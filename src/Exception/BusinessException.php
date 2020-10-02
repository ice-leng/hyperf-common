<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Exception;

use Hyperf\Server\Exception\ServerException;
use Lengbin\Hyperf\Common\Error\CommentErrorCode;

class BusinessException extends ServerException
{
    public function __construct($code, string $message = null, \Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = CommentErrorCode::byValue($code)->getMessage();
        }

        parent::__construct($message, $code, $previous);
    }
}
