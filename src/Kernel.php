<?php

namespace pjpawel\LightApi;

use pjpawel\LightApi\Command\Command;
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

    /**
     * @var string project directory
     */
    public string $projectDir;
    public string $env;
    public bool $debug;
    private EndpointsLoader $endpointsLoader;
    private CommandLoader $commandLoader;
    private ContainerLoader $containerLoader;


    public function __construct(array $config)
    {
        $this->projectDir = $config['projectDir'];
        $this->env = $config['env'];
        $this->debug = $config['debug'];
        $this->endpointsLoader = new EndpointsLoader($config['controllers']);
        $this->commandLoader = new CommandLoader($config['commands']);
        $this->boot($config);
    }

    public function boot(array $config): void
    {
        $this->containerLoader = new ContainerLoader($config['container']);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function handleRequest(Request $request): Response
    {
        if ($this->endpointsLoader->loaded === false) {
            $this->endpointsLoader->load($this->projectDir);
        }
        $request->validateIp();
        $endpoint = $this->endpointsLoader->getEndpoint($request);
        $this->containerLoader->add(['class' => Request::class, 'args' => [], 'object' => $request]);
        return $endpoint->execute($this->containerLoader, $request);
    }

    /**
     * @param string $commandName
     * @return int
     */
    public function handleCommand(string $commandName): int
    {
        if ($this->commandLoader->loaded === false) {
            $this->commandLoader->load();
        }
        $command = $this->commandLoader->getCommand($commandName);
        $command->prepare();
        return $command->execute();
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