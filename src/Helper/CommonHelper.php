<?php

namespace Lengbin\Hyperf\Common\Helper;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Utils\Context;
use Hyperf\Redis\Redis;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class CommonHelper
{
    /**
     * container
     *
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        return Context::get(ContainerInterface::class);
    }

    /**
     * config
     *
     * @return ConfigInterface
     */
    public static function getConfig(): ConfigInterface
    {
        return self::getContainer()->get(ConfigInterface::class);
    }

    /**
     * redis
     *
     * @return Redis
     */
    public static function getRedis(): Redis
    {
        return CommonHelper::getContainer()->get(\Redis::class);
    }

    /**
     * request
     * @return RequestInterface
     */
    public static function getRequest(): RequestInterface
    {
        return CommonHelper::getContainer()->get(RequestInterface::class);
    }

    /**
     * Response
     * @return ResponseInterface
     */
    public static function getResponse(): ResponseInterface
    {
        return CommonHelper::getContainer()->get(ResponseInterface::class);
    }

}
