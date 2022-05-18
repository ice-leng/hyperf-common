<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Commands\CodeGenerator;

use Lengbin\Common\BaseObject;

class ClassInfo extends BaseObject
{
    // 名称
    public string $name;

    // 命名空间
    public string $namespace;

    // 文件
    public string $file;
}