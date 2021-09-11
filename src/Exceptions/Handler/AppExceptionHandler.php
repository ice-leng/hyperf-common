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

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Lengbin\Hyperf\Common\Constants\Errors\CommentError;
use Lengbin\Hyperf\Common\Exceptions\AbstractException;
use Lengbin\Hyperf\Common\Exceptions\FrameworkException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));

        if ($throwable instanceof AbstractException) {
            return $response->fail($throwable->getRealCode(), $throwable->getMessage());
        }

        $serverError = CommentError::SERVER_ERROR();
        $message = $serverError->getMessage();
        if (config('app_env', 'dev') === 'dev') {
            $message = $throwable->getTraceAsString();
        } else {
            $this->logger->error($throwable->getTraceAsString());
        }

        $systemError = new FrameworkException($serverError->getValue());
        return $response->fail($systemError->getRealCode(), $message);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
