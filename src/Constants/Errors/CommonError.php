<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Constants\Errors;

use Lengbin\ErrorCode\Annotation\EnumMessage;
use Lengbin\Hyperf\ErrorCode\BaseEnum;

class CommonError extends BaseEnum
{
    /**
     * @Message("Success")
     */
    #[EnumMessage('Success')]
    const SUCCESS = 0;

    /**
     * @Message("系统错误")
     */
    #[EnumMessage('系统错误')]
    const SERVER_ERROR = 1;

    /**
     * @Message("无效权限")
     */
    #[EnumMessage('无效权限')]
    const INVALID_PERMISSION = 2;

    /**
     * @Message("错误的请求参数")
     */
    #[EnumMessage('错误的请求参数')]
    const INVALID_PARAMS = 3;

    /**
     * @Message("登录已超时")
     */
    #[EnumMessage('登录已超时')]
    const TOKEN_EXPIRED = 4;

    /**
     * @Message("请重新登录")
     */
    #[EnumMessage('请重新登录')]
    const INVALID_TOKEN = 5;
}
