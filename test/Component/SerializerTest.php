<?php

namespace pjpawel\LightApi\Test\Component;

use pjpawel\LightApi\Component\Serializer;
use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Test\resources\classes\Logger;

/**
 * @covers \pjpawel\LightApi\Component\Serializer
 */
class SerializerTest extends TestCase
{

    private const SERIALIZED_DIR = __DIR__ . '/../../var/cache';

    /**
     * @covers \pjpawel\LightApi\Component\Serializer
     */
    public function test__construct(): void
    {
        $serializer = new Serializer(self::SERIALIZED_DIR);
        $this->assertTrue(is_a($serializer, Serializer::class));
    }

    /**
     * @covers \pjpawel\LightApi\Component\Serializer::loadSerialized
     */
    public function testLoadSerializedEmpty(): void
    {
        $this->removeSerializedDir();
        $serializer = new Serializer(self::SERIALIZED_DIR);
        $loaded = $serializer->loadSerialized();
        $this->assertFalse($loaded);
        $this->assertEquals([], $serializer->serializedObjects);
    }

    /**
     * @covers \pjpawel\LightApi\Component\Serializer::loadSerialized
     */
    public function testLoadSerializedNotEmpty(): void
    {
        $this->removeSerializedDir();
        $serializer = new Serializer(self::SERIALIZED_DIR);
        $loaded = $serializer->loadSerialized();
        $this->assertFalse($loaded);
        $this->assertEquals([], $serializer->serializedObjects);
    }

    /**
     * @covers \pjpawel\LightApi\Component\Serializer::makeSerialization
     */
    public function testMakeSerialization(): void
    {
        $this->removeSerializedDir();
        $container = new ContainerLoader();
        $serializer = new Serializer(self::SERIALIZED_DIR);
        $this->assertFalse($serializer->loadSerialized());
        $serializer->makeSerialization([ContainerLoader::class => $container]);
        $this->assertDirectoryExists(self::SERIALIZED_DIR);


        $serializerNew = new Serializer(self::SERIALIZED_DIR);
        $serializerNew->loadSerialized();
        $containerNew = $serializerNew->serializedObjects[ContainerLoader::class];
        $this->assertTrue(is_a($containerNew, ContainerLoader::class));
        $this->assertEquals($container, $containerNew);
    }

    private function removeSerializedDir(): void
    {
        if (!is_dir(self::SERIALIZED_DIR)) {
            return;
        }
        foreach (array_diff(scandir(self::SERIALIZED_DIR), ['.', '..']) as $file) {
            unlink(self::SERIALIZED_DIR . DIRECTORY_SEPARATOR . $file);
        }
        rmdir(self::SERIALIZED_DIR);
    }
}
