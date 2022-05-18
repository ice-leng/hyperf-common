<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request;

use Lengbin\Hyperf\Common\Commands\CodeGenerator\ClassInfo;

class GeneratorDetailRequest extends BaseGeneratorRequest
{

    public function getFilename(): string
    {
        return $this->modelInfo->name . 'DetailRequest';
    }

    public function buildClass(ClassInfo $class, array $results = []): string
    {
        $stub = file_get_contents(dirname(__DIR__, 2) . '/stubs/Request/DetailRequest.stub');
        $this->replaceNamespace($stub, $class->namespace)
            ->replaceClass($stub, $class->name)
            ->replace($stub, '%CONDITION%', $results['requestCondition']->name)
            ->replace($stub, '%SEARCH%', $results['requestSearch']->name);
        return $stub;
    }
}