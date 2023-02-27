<?php

namespace pjpawel\LightApi;

use Exception;
use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Component\ClassWalker;
use pjpawel\LightApi\Component\Env;
use pjpawel\LightApi\Component\Logger\SimpleLogger\SimpleLogger;
use pjpawel\LightApi\Component\Serializer;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Endpoint\EndpointsLoader;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;
use Psr\Log\LoggerInterface;

class Kernel
{

    public const VERSION = 001000;
    public const VERSION_DOTTED = '0.1.0';
    /* only for stable version */
    //public const VERSION_END_OF_LIFE = '06/2023';
    //public const VERSION_END_OF_MAINTENANCE = '03/2023';

    private const RUNTIME_DIR = 'var' . DIRECTORY_SEPARATOR;
    private const SERIALIZE_DEFAULT_DIR = self::RUNTIME_DIR . 'cache';
    private const LOGGER_DEFAULT_PATH = self::RUNTIME_DIR . 'log' . DIRECTORY_SEPARATOR . 'app.log';
    private const KERNEL_LOGGER_ALIASES = [
        'logger.kernel',
        'monolog.kernel',
        LoggerInterface::class
    ];

    /**
     * @var string project directory
     */
    public string $projectDir;
    public string $env;
    public bool $debug;
    private EndpointsLoader $endpointsLoader;
    private CommandsLoader $commandLoader;
    private ContainerLoader $containerLoader;
    private Serializer $serializer;
    private LoggerInterface $kernelLogger;


    public function __construct(string $configDir)
    {
        $env = new Env();
        $config = $env->getConfigFromEnv($configDir);
        $this->projectDir = $config['projectDir'];
        $this->env = $config['env'];
        $this->debug = $config['debug'];
        $this->serializer = new Serializer($this->projectDir . DIRECTORY_SEPARATOR .
            ($config['serializeDir'] ?? self::SERIALIZE_DEFAULT_DIR));
        $this->boot($config);
        $this->loadKernelLogger();
    }

    protected function boot(array $config): void
    {
        if (!$this->debug && $this->serializer->loadSerialized()) {
            $this->endpointsLoader = $this->serializer->serializedObjects[EndpointsLoader::class];
            $this->commandLoader = $this->serializer->serializedObjects[CommandsLoader::class];
            $this->containerLoader = $this->serializer->serializedObjects[ContainerLoader::class];
            return;
        }
        $classWalker = new ClassWalker($config['services'] ?? $this->projectDir);
        $this->containerLoader = new ContainerLoader();
        $this->endpointsLoader = new EndpointsLoader();
        $this->commandLoader = new CommandsLoader();
        $classWalker->register($this->containerLoader, $this->endpointsLoader, $this->commandLoader);
        $this->containerLoader->createDefinitionsFromConfig($config['container']);
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
        try {
            $endpoint = $this->endpointsLoader->getEndpoint($request);
        } catch (Exception $e) {
            return $this->endpointsLoader->getErrorResponse($e);
        }
        $this->containerLoader->add(['name' => Request::class, 'args' => [], 'object' => $request]);
        return $endpoint->execute($this->containerLoader, $request);
    }

    /**
     * @param string|null $commandName
     * @return int
     */
    public function handleCommand(?string $commandName = null): int
    {
        if ($commandName === null){
            $commandName = $this->commandLoader->getCommandNameFromServer();
        }
        return $this->commandLoader->runCommandFromName($commandName, $this->containerLoader);
    }

    public function __destruct()
    {
        if ($this->serializer->serializeOnDestruct) {
            if ($this->containerLoader->has(Request::class)) {
                unset($this->containerLoader->definitions[Request::class]);
            }
            $this->serializer->makeSerialization([
                ContainerLoader::class => $this->containerLoader,
                EndpointsLoader::class => $this->endpointsLoader,
                CommandsLoader::class => $this->commandLoader
            ]);
        }
    }

    protected function loadKernelLogger(): void
    {
        foreach (self::KERNEL_LOGGER_ALIASES as $alias) {
            if ($this->containerLoader->has($alias)) {
                $this->containerLoader->get($alias);
                return;
            }
        }
        $this->kernelLogger = new SimpleLogger($this->projectDir . DIRECTORY_SEPARATOR . self::LOGGER_DEFAULT_PATH);
    }
}