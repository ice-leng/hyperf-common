<?php

namespace Lengbin\Hyperf\Common\Helper;

use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Redis\Redis;
use Hyperf\Contract\ConfigInterface;
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
        return ApplicationContext::getContainer();
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

    /**
     * request set attribute
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return RequestInterface
     */
    public static function setRequestAttribute($key, $value): RequestInterface
    {
        return Context::override(RequestInterface::class, function (RequestInterface $request) use ($key, $value) {
            return $request->withAttribute($key, $value);
        });
    }

    /**
     * 是否为开发环境
     * @return bool
     */
    public static function isDev(): bool
    {
        return self::getConfig()->get('app_env', 'prod') === 'dev';
    }

}
