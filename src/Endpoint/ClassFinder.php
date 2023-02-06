<?php

namespace pjpawel\LightApi\Endpoint;

use Gnugat\NomoSpaco\File\FileRepository;
use Gnugat\NomoSpaco\FqcnRepository;
use Gnugat\NomoSpaco\Token\ParserFactory;

class ClassFinder
{

    /**
     * @param string $dir
     * @return string[]
     */
    public function getAllClassInDir(string $dir): array
    {
        $fqcn = new FqcnRepository(new FileRepository(), new ParserFactory());
        return $fqcn->findIn($dir);
    }

    /**
     * @param string $dir
     * @param string $className
     * @return string[]
     */
    public function getClassInDir(string $dir, string $className): array
    {
        $fqcn = new FqcnRepository(new FileRepository(), new ParserFactory());
        return $fqcn->findInFor($dir, $className);
    }

    public function getAllClassFromNamespace(string $namespacePrefix, array $classes): array
    {
        $searchedClasses = [];
        foreach ($classes as $class) {
            if (str_starts_with($class, $namespacePrefix)) {
                $searchedClasses[] = $class;
            }
        }
        return $searchedClasses;
    }

}