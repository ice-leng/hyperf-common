<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Entity\Traits;

use Hyperf\Utils\Str;

trait ColumnTrait
{
    public function toSnakeKey(array $result, string $delimiter = '_'): array
    {
        $data = [];
        foreach ($result as $key => $value) {
            $data[Str::snake($key, $delimiter)] = $value;
        }
        return $data;
    }

    public function toCamelKey(array $result): array
    {
        $data = [];
        foreach ($result as $key => $value) {
            $data[Str::camel($key)] = $value;
        }
        return $data;
    }
}