<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\HttpServer\Request as BaseRequest;
use Lengbin\Hyperf\Common\Framework\Exception\MethodNotImplException;

class Request extends BaseRequest
{
    /**
     * 真实 ip
     *
     * @param string $headerName
     *
     * @return mixed|string
     *
     * @throws MethodNotImplException
     */
    public function clientRealIP($headerName = 'x-real-ip')
    {
        throw new MethodNotImplException();
    }
}
