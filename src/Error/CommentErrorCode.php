<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Error;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class CommentErrorCode extends AbstractConstants
{
    /**
     * @Message("Success")
     */
    const SUCCESS = '0';

    /**
     * @Message("系统错误")
     */
    const SERVER_ERROR = 'F-000-000-500';

    /**
     * @Message("错误的请求参数")
     */
    const INVALID_PARAMS = 'F-000-000-400';

    /**
     * @Message("无效权限")
     */
    const INVALID_PERMISSION = 'F-000-000-402';

    /**
     * @Message("请重新登录")
     */
    const TOKEN_EXPIRED = 'F-000-000-401';

    /**
     * @Message("请重新登录")
     */
    const INVALID_TOKEN = 'F-000-000-403';


}
