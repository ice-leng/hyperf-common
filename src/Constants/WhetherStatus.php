<?php

namespace Lengbin\Hyperf\Common\Constants;

use Lengbin\ErrorCode\AbstractEnum;

/**
 * 基础状态
 */
class WhetherStatus extends AbstractEnum
{
    /**
     * @Message("是")
     */
    const YES = 1;

    /**
     * @Message("否")
     */
    const NO = 0;
}
