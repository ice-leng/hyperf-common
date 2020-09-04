<?php

namespace Lengbin\Hyperf\Common\Exception\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
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
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var FormatterInterface
     */
    protected $formatter;

    public function __construct(ConfigInterface $config,
        StdoutLoggerInterface $logger,
        LoggerFactory $loggerFactory,
        HttpResponseInterface $response,
        FormatterInterface $formatter)
    {
        $this->logger = $logger;
        $this->response = $response;
        $this->config = $config;
        $this->formatter = $formatter;
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
        if ($this->config->get('app_env', 'prod') === 'dev') {
            $this->logger->error($this->formatter->format($throwable));
        }
        $this->loggerFactory->error($this->formatter->format($throwable));
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->formatLog($throwable);
        return $this->response->fail($throwable->getCode(), $throwable->getMessage());
    }
}
