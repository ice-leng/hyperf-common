<?php

namespace Lengbin\Hyperf\Common\Exception\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Throwable;

trait ExceptionHandlerTrait
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @var HttpResponseInterface
     */
    protected $response;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    /**
     * @var $config
     */
    protected $config;

    public function __construct(ConfigInterface $config, StdoutLoggerInterface $logger, LoggerFactory $loggerFactory, HttpResponseInterface $response)
    {
        $this->logger = $logger;
        $this->response = $response;
        $this->config = $config;
        $this->loggerFactory = $loggerFactory->get('Exception Trace');
    }

    /**
     * 格式化 错误信息
     *
     * 当是开发环境时
     * 命令行打出日志
     *
     * @param Throwable $throwable
     */
    public function formatLog(Throwable $throwable): void
    {
        $errorMessage = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
        if ($this->config->get('app_env', 'prod') === 'dev') {
            $this->logger->error($errorMessage);
        }
        $this->loggerFactory->error($errorMessage);
        $this->loggerFactory->error($throwable->getTraceAsString());
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->formatLog($throwable);
        return $this->response->fail($throwable->getCode(), $throwable->getMessage());
    }
}
