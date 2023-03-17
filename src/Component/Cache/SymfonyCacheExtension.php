<?php

namespace pjpawel\LightApi\Component\Cache;

use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Component\Extension\Extension;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Container\Definition\AliasDefinition;
use pjpawel\LightApi\Container\Definition\ClassDefinition;
use pjpawel\LightApi\Container\Definition\Definition;
use Symfony\Contracts\Cache\CacheInterface;

class SymfonyCacheExtension extends Extension
{

    /*
     * id => [
     *     class => '',
     *     args => []
     * ]
     */

    public function registerServices(ContainerLoader $container): void
    {
        $setKernelCache = !$container->has('kernel.cache');
        /** @var array<string, Definition> $definitions */
        $definitions = [];
        foreach ($this->config as $id => $adapterConfig) {
            $definitions[$id] =  new ClassDefinition($adapterConfig['class'], $adapterConfig['args'] ?? []);
            if ($setKernelCache) {
                $definitions['kernel.cache'] = new AliasDefinition(CacheInterface::class, '@' . $id);
                $setKernelCache = false;
            }
        }
        $container->addDefinitions($definitions);
    }

    public function registerCommands(CommandsLoader $commandLoader): void
    {
        $commandLoader->registerCommand('cache:clear', SymfonyCacheClearCommand::class);
        $commandLoader->registerCommand('cache:warmup', SymfonyCacheWarmupCommand::class);
    }

}