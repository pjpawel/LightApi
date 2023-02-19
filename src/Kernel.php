<?php

namespace pjpawel\LightApi;

use Exception;
use pjpawel\LightApi\Command\CommandLoader;
use pjpawel\LightApi\Component\Logger\SimpleLogger\SimpleLogger;
use pjpawel\LightApi\Component\Serializer;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Endpoint\EndpointsLoader;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;
use Psr\Log\LoggerInterface;

class Kernel
{

    public const VERSION_MAJOR = 0;
    public const VERSION_MINOR = 1;
    public const VERSION_PATCH = 0;
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
    private CommandLoader $commandLoader;
    private ContainerLoader $containerLoader;
    private Serializer $serializer;
    private LoggerInterface $kernelLogger;


    public function __construct(array $config)
    {
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
            $this->commandLoader = $this->serializer->serializedObjects[CommandLoader::class];
            $this->containerLoader = $this->serializer->serializedObjects[ContainerLoader::class];
            return;
        }
        $this->endpointsLoader = new EndpointsLoader($config['controllers']);
        $this->commandLoader = new CommandLoader($config['commands']);
        $this->containerLoader = new ContainerLoader($config['container']);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function handleRequest(Request $request): Response
    {
        if ($this->endpointsLoader->loaded === false) {
            $this->endpointsLoader->load($this->projectDir);
        }
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
        if ($this->commandLoader->loaded === false) {
            $this->commandLoader->load();
        }
        /*if ($commandName === null){
            $commandName = $this->commandLoader->getCommandNameFromServer();
        }
        $command = $this->commandLoader->getCommand($commandName, $this->containerLoader);
        $command->prepare();*/
        return 1;//$command->run();
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
                CommandLoader::class => $this->commandLoader
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


    /**
     * Get kernel version in string format
     *
     * @return string
     */
    public function getVersion(): string
    {
        return sprintf('%s.%s.%s', self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_PATCH);
    }

}