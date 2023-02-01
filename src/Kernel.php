<?php

namespace pjpawel\LightApi;

use Exception;
use pjpawel\LightApi\Command\Command;
use pjpawel\LightApi\Command\CommandLoader;
use pjpawel\LightApi\Endpoint\EndpointsLoader;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;

class Kernel
{

    public const VERSION_MAJOR = 0;
    public const VERSION_MINOR = 1;
    public const VERSION_PATCH = 0;

    public const ENVS = [
        'dev',
        'test',
        'prod'
    ];

    public const REQUIRED_CONFIG_PARAMS = [
        'env',
        
    ];

    public string $env;
    private Request $request;
    private bool $arePathsLoaded = false;
    private EndpointsLoader $endpointsLoader;
    private bool $areCommandsLoaded = false;
    private CommandLoader $commandLoader;


    public function __construct(array $config = [])
    {
        $this->loadConfig($config);
        $this->boot();
        $this->endpointsLoader = new EndpointsLoader($config['controllers']);
        $this->commandLoader = new CommandLoader($config['commands']);
    }

    private function loadEnv(string $env): void
    {
        if (!in_array($env, self::ENVS)) {
            $this->env = $env;
        }
    }

    /**
     * @param array $config
     * @throws Exception
     */
    private function loadConfig(array $config): void
    {
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'env':
                    $this->loadEnv($value);
                    break;
                default:
                    throw new Exception("Config key $key is invalid or not supported");
            }
        }
    }

    public function boot(): void
    {
        //Get dependency Injection
    }

    public function handleRequest(Request $request): Response
    {
        if ($this->arePathsLoaded === false) {
            $this->endpointsLoader->load();
        }
        $request->validateIp();
        $endpoint = $this->endpointsLoader->getPath($request->path);
        $endpoint->load();
        return $endpoint->execute();
    }

    public function getCommand(string $commandName): Command
    {
        if ($this->areCommandsLoaded === false) {
            $this->commandLoader->load();
        }
        return $this->commandLoader->getCommand($commandName);
    }

    private function loadCommands(): void
    {
        
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