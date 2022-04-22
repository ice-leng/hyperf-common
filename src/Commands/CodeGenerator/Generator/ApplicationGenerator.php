<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator;

use Lengbin\Hyperf\Common\Commands\CodeGenerator\ClassInfo;

abstract class ApplicationGenerator extends AbstractGenerator
{
    public function run(array $results = [])
    {
        $data = [];
        foreach ($this->config->applications as $application) {
            $context = [];
            foreach ($results as $key => $result) {
                if ($result instanceof ClassInfo) {
                    $context[$key] = $result;
                } else {
                    if (str_starts_with($key, 'entity_')) {
                        foreach ($result as $k => $v) {
                            $context[$k] = $v[$application] ?? [];
                        }
                    } else {
                        $context[$key] = $result[$application] ?? [];
                    }
                }
            }
            $context['_application'] = $application;
            $data[$application] = $this->handle($context, ucfirst($application));
        }
        return $data;
    }

    public function handle(array $results, string $application): ClassInfo
    {
        $class = $this->getClassInfo($application);
        if (!file_exists($class->file)) {
            $this->mkdir($class->file);
            file_put_contents($class->file, $this->buildClass($class, $results));
        }
        return $class;
    }

}