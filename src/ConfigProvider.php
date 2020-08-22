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

use Lengbin\Hyperf\Common\Framework\Request;
use Lengbin\Hyperf\Common\Framework\Response;

use Hyperf\HttpServer\Request as BaseRequest;
use Hyperf\HttpServer\Response as BaseResponse;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                BaseRequest::class  => Request::class,
                BaseResponse::class => Response::class,
            ],
            'annotations'  => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
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
