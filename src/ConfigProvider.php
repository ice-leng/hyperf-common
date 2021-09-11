<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Lengbin\Hyperf\Common;

use Hyperf\Utils\Coroutine;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'annotations'  => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'class_map'          => [
                        Coroutine::class => BASE_PATH . '/vendor/lengbin/hyperf-common/src/ClassMap/Coroutine.php'
                    ],
                ],
            ],
            'commands' => [

            ],
            'listeners' => [
            ],
            'publish'      => [
                [
                    'id'          => 'common',
                    'description' => 'The config for common.',
                ],
            ],
        ];
    }
}
