<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Response;

use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\ApplicationGenerator;

abstract class BaseGeneratorResponse extends ApplicationGenerator
{
    public function getPath(string $module = ''): string
    {
        $version = ucfirst($this->config->version);
        return parent::getPath("/Entity/Response/{$module}/{$version}/{$this->modelInfo->name}");
    }

    public function isWrite(string $application): bool
    {
        return !is_dir(BASE_PATH . $this->getPath($application));
    }
}