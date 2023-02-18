<?php

namespace pjpawel\LightApi\Component;

use Exception;

class Serializer
{

    private const SEPARATOR = '->';
    private const SINGLE_FILE_NAME = 'serialized';

    private string $serializedDir;
    private bool $serializeOnDestruct = false;
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
        $serializedFile = $this->serializedDir . DIRECTORY_SEPARATOR . self::SINGLE_FILE_NAME;
        if (!is_file($serializedFile)) {
            $this->serializeOnDestruct = true;
            return false;
        }
        try {
            $ini = ini_get('error_reporting');
            if ($ini === false) {
                $ini = -1;
            }
            error_reporting(E_ERROR);
            $allSerializedObjects = file_get_contents($serializedFile);
            error_reporting($ini);
            if ($allSerializedObjects === false) {
                throw new Exception('Cannot load serialized objects');
            }
            foreach (explode(PHP_EOL, $allSerializedObjects) as $serializedObject) {
                if ($serializedObject === '') {
                    continue;
                }
                $serialization = explode(self::SEPARATOR, $serializedObject);
                $this->serializedObjects[$serialization[0]] = unserialize($serialization[1]);
            }
            return true;
        } catch (Exception $e) {
            error_reporting($ini);
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
        if (!$this->serializeOnDestruct) {
            return;
        }
        /** @var string[] $serializedObjects */
        $serializedObjects = [];
        foreach ($objects as $name => $object) {
            $serializedObjects[] = $name . self::SEPARATOR . serialize($object) . PHP_EOL;
        }
        if (!is_dir($this->serializedDir)) {
            mkdir($this->serializedDir, 0777, true);
        }
        file_put_contents(
            $this->serializedDir . DIRECTORY_SEPARATOR . self::SINGLE_FILE_NAME,
            implode('', $serializedObjects));
    }











}