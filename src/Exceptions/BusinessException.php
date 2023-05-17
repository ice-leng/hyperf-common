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
use Lengbin\Hyperf\ErrorCode\BaseEnum;
use Throwable;

class BusinessException extends ServerException
{
    public function __construct(int|BaseEnum $code, ?string $message = null, array $replace = [], ?Throwable $previous = null)
    {
        if ($code instanceof BaseEnum) {
            if (empty($message)) {
                $message = $code->getMessage($replace);
            }
            $code = $code->getValue();
        }

        if (empty($message)) {
            try {
                $config = \Hyperf\Config\config('errorCode', []);
                $class = $config['classNamespace'] . '\\' . $config['classname'];
                $message = $class::byValue($code)->getMessage($replace);
            } catch (Throwable $exception) {
                $message = $exception->getMessage();
            }
        }
        parent::__construct($message, $code, $previous);
    }
}
