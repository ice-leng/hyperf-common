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
use Lengbin\Hyperf\Common\Http\Response;
use Lengbin\Hyperf\Common\Logs\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Contract\StdoutLoggerInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ResponseInterface::class                     => Response::class,
                Hyperf\HttpServer\Response::class            => Response::class,
                StdoutLoggerInterface::class                 => LoggerFactory::class,
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
