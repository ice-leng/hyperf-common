<?php

namespace Lengbin\Hyperf\Common\Exception\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Lengbin\Hyperf\Common\Helper\CommonHelper;
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
    protected $loggerFile;

    /**
     * @var ConfigInterface
     */
    protected $config;



    public function __construct(ConfigInterface $config,
        StdoutLoggerInterface $logger,
        LoggerFactory $loggerFactory,
        HttpResponseInterface $response)
    {
        $this->logger = $logger;
        $this->response = $response;
        $this->config = $config;
        $this->loggerFile = $loggerFactory->get('Exception Trace');
    }

    /**
     * 格式化 错误信息
     *
     * 当是开发环境时
     * 命令行打出日志
     *
     * @param Throwable $throwable
     * @param string    $level
     */
    public function formatLog(Throwable $throwable, $level = 'warning'): void
    {
        if (CommonHelper::isDev()) {
            $this->logger->$level(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        }
        $this->loggerFile->$level($throwable->getTraceAsString());
    }
}
