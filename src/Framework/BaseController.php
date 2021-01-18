<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;

class BaseController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    public function success($params = null)
    {
        return $this->response->success($params);
    }

    public function fail(string $code, string $message)
    {
        return $this->response->fail($code, $message);
    }

}
