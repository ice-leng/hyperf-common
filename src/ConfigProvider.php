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

use Hyperf\Database\MySqlConnection as HyperfMysqlConnection;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Lengbin\Hyperf\Common\Component\Database\MySqlConnection;
use Lengbin\Hyperf\Common\Component\Database\Visitor\ModelUpdateVisitor;
use Lengbin\Hyperf\Common\Framework\Request;
use Lengbin\Hyperf\Common\Framework\Response;
use Hyperf\Database\Commands\Ast\ModelUpdateVisitor as Visitor;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                RequestInterface::class  => Request::class,
                ResponseInterface::class => Response::class,
                Visitor::class => ModelUpdateVisitor::class,
                HyperfMysqlConnection::class => MySqlConnection::class
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
