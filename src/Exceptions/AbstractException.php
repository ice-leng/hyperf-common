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
    public function __construct($code, string $message = null, array $replace = [], Throwable $previous = null)
    {
        if (empty($message)) {
            $config = config('errorCode', []);
            $class = $config['classNamespace'] . '\\' . $config['classname'];
            $message = $class::byValue($code)->getMessage($replace);
        }
        parent::__construct($message, $code, $previous);
    }

    abstract public function getRealCode();

    public function formatCode()
    {
        if (is_string($this->code)) {
            return $this->code;
        }
        $code = str_pad((string)$this->code, 9, "0", STR_PAD_LEFT);
        return implode('-', str_split($code, 3));
    }
}
