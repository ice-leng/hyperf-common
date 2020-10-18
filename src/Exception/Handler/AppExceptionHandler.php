<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Lengbin\Hyperf\Common\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Lengbin\Hyperf\Common\Error\CommentErrorCode;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    use ExceptionHandlerTrait;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->formatLog($throwable);
        $error = CommentErrorCode::SERVER_ERROR();
        return $this->response->fail($error->getValue(), $error->getMessage());
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
