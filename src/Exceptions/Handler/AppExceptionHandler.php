<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Lengbin\Hyperf\Common\Exceptions\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Logger\LoggerFactory;
use Lengbin\Hyperf\Common\Constants\Errors\CommonError;
use Lengbin\Hyperf\Common\Exceptions\AbstractException;
use Lengbin\Hyperf\Common\Exceptions\FrameworkException;
use Lengbin\Hyperf\Common\Http\Response;
use Lengbin\Hyperf\Common\Logs\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(LoggerFactory $logger, Response $response)
    {
        $this->logger = $logger->get('throw-error');
        $this->response = $response;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $msg = sprintf("%s: %s(%s) in %s:%s\nStack trace:\n%s",
            get_class($throwable),
            $throwable->getMessage(),
            $throwable->getCode(),
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getTraceAsString()
        );
        $this->logger->debug($msg);

        $message = null;
        if (config('app_env', 'dev') === 'dev') {
            $message = $msg;
        }

        if ($throwable instanceof AbstractException) {
            return $this->response->fail($throwable->getRealCode(), $message ?? $throwable->getMessage());
        }

        $serverError = CommonError::SERVER_ERROR();
        $systemError = new FrameworkException($serverError->getValue());
        return $this->response->fail($systemError->getRealCode(), $message ?? $serverError->getMessage());
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
