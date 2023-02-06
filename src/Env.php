<?php

namespace pjpawel\LightApi;

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
    public static function getConfigFromEnv(string $dir, string $defaultConfigFile = 'config.php'): array
    {
        $config = [];
        $dir .= DIRECTORY_SEPARATOR;
        $files = scandir($dir);
        if (in_array($defaultConfigFile, $files)) {
            $config = self::loadConfigFile($dir . $defaultConfigFile);
        }
        foreach (self::ENV_FILES as $file) {
            if (in_array($file, $files)) {
                $config = array_merge_recursive(self::loadConfigFile($dir . $file), $config);
            }
        }
        return $config;
    }

    /**
     * Method to get configuration array form a file
     *
     * @param string $filePath
     * @return array
     */
    public static function loadConfigFile(string $filePath): array
    {
        return require $filePath;
    }

}