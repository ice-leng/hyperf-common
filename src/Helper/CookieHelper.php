<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Helper;

use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Context;

final class CookieHelper
{

    /**
     * 设置
     *
     * @param string      $name
     * @param string|null $value
     * @param int         $expire
     *
     * @return ResponseInterface
     */
    public static function set(string $name, ?string $value, int $expire = 0): ResponseInterface
    {
        return Context::override(ResponseInterface::class, function (ResponseInterface $response) use ($name, $value, $expire) {
            $cookie = new Cookie($name, $value, $expire);
            return $response->withCookie($cookie);
        });
    }

    /**
     * 获得
     *
     * @param string                $name
     * @param string|null           $default
     * @param RequestInterface|null $request
     *
     * @return mixed
     */
    public static function get(string $name, ?string $default = null, RequestInterface $request = null)
    {
        if ($request === null) {
            $request = Context::get(RequestInterface::class);
        }
        return $request->cookie($name, $default);
    }

    /**
     * 移除
     *
     * @param string $name
     *
     * @return ResponseInterface
     */
    public static function remove(string $name): ResponseInterface
    {
        return self::set($name, '', 1);
    }
}
