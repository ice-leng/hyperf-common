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
namespace Lengbin\Hyperf\Common\Exceptions;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class BusinessException extends ServerException
{
    public function __construct(int $code, string $message = null, array $replace = [], Throwable $previous = null)
    {
        if (empty($message)) {
            $config = config('errorCode', []);
            $class = $config['classNamespace'] . '\\' . $config['classname'];
            $message = $class::byValue($code)->getMessage($replace);
        }
        parent::__construct($message, $code, $previous);
    }
}
