<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/3
 * Time:  10:50 上午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common;

use Lengbin\Hyperf\Common\Http\Response;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;

abstract class BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get(RequestInterface::class);
        $this->response = $container->get(Response::class);
    }
}
