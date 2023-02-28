<?php

namespace pjpawel\LightApi\Component;

use Exception;

class Serializer
{

    private string $serializedDir;
    public bool $serializeOnDestruct = false;
    /**
     * @var array<string,object>
     */
    public array $serializedObjects = [];

    public function __construct(string $serializedDir)
    {
        $this->serializedDir = $serializedDir;
    }

    public function loadSerialized(): bool
    {
        if (!is_dir($this->serializedDir)) {
            $this->serializeOnDestruct = true;
            return false;
        }
        try {
            $ini = ini_get('error_reporting');
            if ($ini === false) {
                $ini = -1;
            }
            error_reporting(E_ERROR);
            foreach (array_diff(scandir($this->serializedDir), ['.', '..']) as $file) {
                $fileName = $this->serializedDir . DIRECTORY_SEPARATOR . $file;
                if (!is_file($fileName)) {
                    continue;
                }
                $serialized = file_get_contents($fileName);
                if ($serialized === false) {
                    throw new Exception('Cannot load serialized object in ' . $fileName);
                }
                $this->serializedObjects[$this->makeClassNameFromFileName($file)] = unserialize($serialized);
            }
            error_reporting((int) $ini);
            return true;
        } catch (Exception $e) {
            error_reporting((int) $ini);
            error_log($e->getMessage());
            $this->serializeOnDestruct = true;
            return false;
        }
    }

    /**
     * @param array<string,object> $objects
     * @return void
     */
    public function makeSerialization(array $objects): void
    {
        if (!is_dir($this->serializedDir)) {
            mkdir($this->serializedDir, 0777, true);
        }
        foreach ($objects as $name => $object) {
            file_put_contents($this->makeFileNameFromClassName($name), serialize($object));
        }
    }

    private function makeFileNameFromClassName(string $className): string
    {
        return $this->serializedDir . DIRECTORY_SEPARATOR . str_replace('\\', '--', $className);
    }

    private function makeClassNameFromFileName(string $fileName): string
    {
        return str_replace('--', '\\', $fileName);
    }











}