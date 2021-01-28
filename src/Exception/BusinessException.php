<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Exception;

use Hyperf\Server\Exception\ServerException;
use Lengbin\Hyperf\Common\Error\CommentErrorCode;
use Lengbin\Hyperf\Common\Helper\CommonHelper;

class BusinessException extends ServerException
{

    /**
     * @var string
     */
    private $realCode;

    public function __construct($code, array $replace = [], string $message = null, \Throwable $previous = null)
    {
        if (is_null($message)) {
            $config = CommonHelper::getConfig()->get('errorCode', []);
            $class = $config ? $config['classNamespace'] . '\\' . $config['classname'] : CommentErrorCode::class;
            $message = $class::byValue($code)->getMessage($replace);
        }
        $this->realCode = $code;
        parent::__construct($message, 0, $previous);
    }

    public function getRealCode(): string
    {
        return $this->realCode;
    }
}
