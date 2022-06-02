<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/2
 * Time:  9:37 下午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Middlewares;

use Hyperf\Di\Annotation\Inject;
use Lengbin\Hyperf\Common\Helpers\IpHelper;
use Lengbin\Hyperf\Common\Logs\AppendRequestIdProcessor;
use Hyperf\Snowflake\IdGenerator\SnowflakeIdGenerator;
use Hyperf\Utils\Context;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DebugLogMiddleware implements MiddlewareInterface
{

    #[Inject()]
    protected SnowflakeIdGenerator $idGenerator;

    #[Inject()]
    protected IpHelper $ipHelper;

    protected LoggerFactory $loggerFactory;

    public function __construct()
    {
       $this->loggerFactory = make(LoggerFactory::class);
    }

    /**
     * 记录日志
     */
    protected function logger($data, string $name = 'hyperf'): void
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        $this->loggerFactory->get($name)->info($data);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Context::getOrSet(AppendRequestIdProcessor::REQUEST_ID, $this->idGenerator->generate());

        // 记录请求日志
        $this->logger([
            'user-agent' => $request->getHeaderLine('user-agent'),
            'ip' => $this->ipHelper->getClientIp(),
            'host' => $request->getUri()->getHost(),
            'url' => $request->getUri()->getPath(),
            'post' => $request->getParsedBody(),
            'get' => $request->getQueryParams(),
        ], 'request');

        $response = $handler->handle($request);

        $this->logger($response->getBody()->getContents(), 'response');
        return $response;
    }
}
