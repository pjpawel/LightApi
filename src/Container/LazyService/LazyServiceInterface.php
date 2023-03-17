<?php

namespace pjpawel\LightApi\Container\LazyService;

use Psr\Container\ContainerInterface;

/** @deprecated  */
interface LazyServiceInterface
{

    /**
     * Method will be called after the construct
     * to set container based on the services provided via getAllServices()
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void;

    /**
     * Get all services used in class
     * <internal_id,general_id_of_service>
     *
     * @return array<string,string>
     */
    public static function getAllServices(): array;

}