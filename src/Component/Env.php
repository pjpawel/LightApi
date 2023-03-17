<?php

namespace pjpawel\LightApi\Component;

use pjpawel\LightApi\Exception\ProgrammerException;

class Env
{

    public const ENV_FILES = [
        'env.local.php',
        'env.php'
    ];

    /**
     * Get configuration from files in given directories
     *
     * @param string $dir
     * @param string $defaultConfigFile
     * @return array
     */
    public function getConfigFromEnv(string $dir, string $defaultConfigFile = 'config.php'): array
    {
        $config = [];
        $dir .= DIRECTORY_SEPARATOR;
        $files = scandir($dir);
        if (in_array($defaultConfigFile, $files)) {
            $config = $this->loadConfigFile($dir . $defaultConfigFile);
        }
        foreach (self::ENV_FILES as $file) {
            if (in_array($file, $files)) {
                $config = array_merge_recursive($this->loadConfigFile($dir . $file), $config);
            }
        }
        return $config;
    }

    /**
     * @param array $classConfig
     * @return object
     * @throws ProgrammerException|\ReflectionException
     */
    public function createClassFromConfig(array $classConfig): object
    {
        if (!isset($classConfig['class'])) {
            throw new ProgrammerException('Cannot create class from config');
        }
        return (new \ReflectionClass($classConfig['class']))->newInstanceArgs($classConfig['args'] ?? []);
    }

    /**
     * Method to get configuration array form a file
     *
     * @param string $filePath
     * @return array
     */
    public function loadConfigFile(string $filePath): array
    {
        return require $filePath;
    }

}