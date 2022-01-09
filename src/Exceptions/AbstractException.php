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
    private $realCode;

    public function __construct($code, string $message = null, array $replace = [], Throwable $previous = null)
    {
        if (empty($message)) {
            $config = config('errorCode', []);
            $class = $config['classNamespace'] . '\\' . $config['classname'];
            $message = $class::byValue($code)->getMessage($replace);
        }
        if (is_string($code)) {
            $this->realCode = $code;
            $code = 0;
        }
        parent::__construct($message, $code, $previous);
    }

    abstract public function getRealCode();

    public function formatCode()
    {
        if ($this->realCode) {
            return $this->realCode;
        }
        $code = str_pad((string)$this->code, 9, "0", STR_PAD_LEFT);
        return implode('-', str_split($code, 3));
    }
}
