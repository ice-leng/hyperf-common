<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/11
 * Time:  7:10 下午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Exceptions;

use Hyperf\Server\Exception\ServerException;
use Throwable;

abstract class AbstractException extends ServerException
{
    public function __construct($code, array $replace = [], string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $config = config('errorCode', []);
            $class = $config['classNamespace'] . '\\' . $config['classname'];
            $message = $class::byValue($code)->getMessage($replace);
        }
        parent::__construct($message, $code, $previous);
    }

    abstract public function getRealCode();

    public function formatCode()
    {
        return implode('-', str_split((string)$this->code, 3));
    }
}
