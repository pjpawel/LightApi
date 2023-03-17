<?php

namespace pjpawel\LightApi;

use Exception;
use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Component\ClassWalker;
use pjpawel\LightApi\Component\Env;
use pjpawel\LightApi\Component\Event\EventHandler;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Route\Router;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

class Kernel
{

    public const VERSION = 003000;
    public const VERSION_DOTTED = '0.3.0';
    /* only for stable version */
    //public const VERSION_END_OF_LIFE = '06/2023';
    //public const VERSION_END_OF_MAINTENANCE = '03/2023';

    public const KERNEL_CACHE_NAME = 'kernel.cache';
    public const KERNEL_LOGGER_NAME = 'kernel.logger';
    private const PROPERTIES_TO_CACHE = [
        'containerLoader' => 'kernel.container',
        'router' => 'kernel.router',
        'commandLoader' => 'kernel.command'
    ];

    /**
     * @var string project directory
     */
    public string $projectDir;
    public string $env;
    public bool $debug;
    private Router $router;
    private CommandsLoader $commandLoader;
    private ContainerLoader $containerLoader;
    private ?LoggerInterface $kernelLogger;
    private EventHandler $eventHandler;
    private AbstractAdapter $kernelCache;

    public function __construct(string $configDir)
    {
        $env = new Env();
        $config = $env->getConfigFromEnv($configDir);
        $this->projectDir = $config['projectDir'];
        $this->env = $config['env'];
        $this->debug = $config['debug'];
        $this->kernelCache = $env->createClassFromConfig($config['cache']);
        $this->boot($config);
        $this->ensureContainerHasKernelServices();
        $this->eventHandler->tryTriggering(EventHandler::KERNEL_AFTER_BOOT);
    }

    protected function boot(array $config): void
    {
        $loaded = false;
        if (!$this->debug) {
            $loaded = true;
            foreach (self::PROPERTIES_TO_CACHE as $property => $cacheName) {
                $routerItem = $this->kernelCache->getItem($cacheName);
                if (!$routerItem->isHit()) {
                    $loaded = false;
                    break;
                }
                $this->$property = $routerItem->get();
            }
        }
        if (!$loaded) {
            $classWalker = new ClassWalker($config['services'] ?? $this->projectDir);
            $this->containerLoader = new ContainerLoader();
            $this->router = new Router();
            $this->commandLoader = new CommandsLoader();
            $classWalker->register($this->containerLoader, $this->router, $this->commandLoader);
            $this->containerLoader->createDefinitionsFromConfig($config['container']);
        }
    }

    private function ensureContainerHasKernelServices(): void
    {
        if (!$this->containerLoader->has(EventHandler::class)) {
            $this->containerLoader->add(['name' => EventHandler::class]);
            $this->eventHandler = $this->containerLoader->get(EventHandler::class);
        }
        $this->containerLoader->add(['name' => self::KERNEL_CACHE_NAME, 'object' => $this->kernelCache]);
        if ($this->containerLoader->has(self::KERNEL_LOGGER_NAME)) {
            $this->kernelLogger = $this->containerLoader->get(self::KERNEL_LOGGER_NAME);
        } else {
            $this->kernelLogger = null;
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function handleRequest(Request $request): Response
    {
        $request->logRequest($this->kernelLogger);
        $request->validateIp();
        $this->eventHandler->tryTriggering(EventHandler::KERNEL_BEFORE_REQUEST);
        try {
            $route = $this->router->getRoute($request);
        } catch (Exception $e) {
            return $this->router->getErrorResponse($e);
        }
        $this->containerLoader->add(['name' => Request::class, 'args' => [], 'object' => $request]);
        $response = $route->execute($this->containerLoader, $request);
        $this->eventHandler->tryTriggering(EventHandler::KERNEL_AFTER_REQUEST);
        return $response;
    }

    /**
     * @param string|null $commandName
     * @return int
     */
    public function handleCommand(?string $commandName = null): int
    {
        if ($commandName === null) {
            $commandName = $this->commandLoader->getCommandNameFromServer();
        }
        $this->eventHandler->tryTriggering(EventHandler::KERNEL_BEFORE_COMMAND);
        if (str_starts_with($commandName, 'kernel:')) {
            return $this->commandLoader->runCommandFromName($commandName, $this->containerLoader, $this);
        }
        $code = $this->commandLoader->runCommandFromName($commandName, $this->containerLoader);
        $this->eventHandler->tryTriggering(EventHandler::KERNEL_AFTER_COMMAND);
        return $code;
    }

    public function __destruct()
    {
        $this->eventHandler->tryTriggering(EventHandler::KERNEL_ON_DESTRUCT);
        if (!$this->debug) {
            foreach (self::PROPERTIES_TO_CACHE as $property => $cacheName) {
                $cacheItem = $this->kernelCache->getItem($cacheName);
                $cacheItem->set($this->$property);
                $this->kernelCache->save($cacheItem);
            }
        }
    }
}