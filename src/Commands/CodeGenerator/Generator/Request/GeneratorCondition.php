<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request;

use Lengbin\Hyperf\Common\Commands\CodeGenerator\ClassInfo;

class GeneratorCondition extends BaseGeneratorRequest
{
    public function getFilename(): string
    {
        return $this->modelInfo->name . 'Condition';
    }

    public function buildClass(ClassInfo $class, array $results = []): string
    {
        $stub = file_get_contents(dirname(__DIR__, 2) . '/stubs/Request/Condition.stub');
        $this->replaceNamespace($stub, $class->namespace)
            ->replaceClass($stub, $class->name);
        return $stub;
    }
}