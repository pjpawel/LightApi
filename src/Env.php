<?php

namespace pjpawel\LightApi;

class Env
{

    public const ENV_FILES = [
        'env.local.php',
        'env.php'
    ];

    /**
     * @param string $dir
     * @param string $defaultConfigFile
     * @return array
     */
    public static function getConfigFromEnv(string $dir, string $defaultConfigFile = 'config.php'): array
    {
        $config = [];
        $files = scandir($dir);
        if (in_array($defaultConfigFile, $files)) {
            $config = self::loadConfigFile($defaultConfigFile);
        }
        foreach (self::ENV_FILES as $file) {
            if (in_array($file, $files)) {
                $config = array_merge_recursive(self::loadConfigFile($file), $config);
            }
        }
        return $config;
    }

    public static function loadConfigFile(string $filePath): array
    {
        return require $filePath;
    }

}