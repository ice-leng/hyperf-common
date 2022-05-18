<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Commands\CodeGenerator;

class ModelInfo extends ClassInfo
{
    // 字段
    public array $columns;

    // 模块
    public string $module;

    // 主键
    public string $pk;

    // 表的备注
    public string $comment;
}