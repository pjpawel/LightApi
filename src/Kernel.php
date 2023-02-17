<?php

namespace pjpawel\LightApi;

use Exception;
use pjpawel\LightApi\Command\CommandLoader;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Endpoint\EndpointsLoader;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;

class Kernel
{

    public const VERSION_MAJOR = 0;
    public const VERSION_MINOR = 1;
    public const VERSION_PATCH = 0;
    private const SERIALIZE_FILE_NAME = 'kernel-serialize.php';

    /**
     * @var string project directory
     */
    public string $projectDir;
    public string $env;
    public bool $debug;
    private string $serializeConfigPath;
    private bool $serializeAfter = false;
    private EndpointsLoader $endpointsLoader;
    private CommandLoader $commandLoader;
    private ContainerLoader $containerLoader;
    //private LoggerInterface $kernelLogger;


    public function __construct(array $config)
    {
        $this->projectDir = $config['projectDir'];
        $this->env = $config['env'];
        $this->debug = $config['debug'];
        $this->boot($config);
    }

    public function boot(array $config): void
    {
        $this->serializeConfigPath = $this->projectDir . DIRECTORY_SEPARATOR .
            ($config['serializeDir'] ?? 'var' . DIRECTORY_SEPARATOR . 'cache') .
            DIRECTORY_SEPARATOR . self::SERIALIZE_FILE_NAME;
        if (!$this->debug && !$this->hasSerializedFile()) {
            try {
                $this->makeLoadersFromSerialized();
                $this->serializeAfter = true;
                return;
            } catch (Exception $e) {
                error_log('Cannot load classes from serialized file');
            }
        }
        $this->makeLoadersFromConfig($config);
    }

    /**
     * @param array $config
     * @return void
     * @throws Exception
     */
    private function makeLoadersFromConfig(array $config): void
    {
        $this->endpointsLoader = new EndpointsLoader($config['controllers']);
        $this->commandLoader = new CommandLoader($config['commands']);
        $this->containerLoader = new ContainerLoader($config['container']);
    }

    private function makeLoadersFromSerialized(): void
    {
        $serializedConfig = require $this->serializeConfigPath;
        $this->endpointsLoader = EndpointsLoader::unserialize($serializedConfig['controllers']);
        $this->commandLoader = CommandLoader::unserialize($serializedConfig['commands']);
        $this->containerLoader = ContainerLoader::unserialize($serializedConfig['container']);
    }

    private function hasSerializedFile(): bool
    {
        if (is_file($this->serializeConfigPath)) {
            return false;
        }
        return true;
    }

    protected function serialize(): void
    {
        $endpointSerialize = var_export($this->endpointsLoader->serialize(), true);
        $commandSerialize = var_export($this->commandLoader->serialize(), true);
        $containerSerialize = var_export($this->containerLoader->serialize(), true);
        $serializeOutput = <<<EOL
            <?php
            return [
                'endpoint' => $endpointSerialize,
                'command => $commandSerialize,
                'container' => $containerSerialize
            ];
            EOL;
        file_put_contents($this->serializeConfigPath, $serializeOutput);
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
        if ($this->serializeAfter) {
            $this->serialize();
        }
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