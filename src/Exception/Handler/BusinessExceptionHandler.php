<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Lengbin\Hyperf\Common\Exception\BusinessException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class BusinessExceptionHandler extends ExceptionHandler
{
    use ExceptionHandlerTrait;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        $this->formatLog($throwable);
        return $this->response->fail((string)$throwable->getCode());
    }

    /**
     * Determine if the current exception handler should handle the exception,.
     *
     * @return bool
     *              If return true, then this exception handler will handle the exception,
     *              If return false, then delegate to next handler
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}
