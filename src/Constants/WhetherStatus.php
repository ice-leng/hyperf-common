<?php

namespace Lengbin\Hyperf\Common\Constants;

use Lengbin\ErrorCode\AbstractEnum;
use Lengbin\ErrorCode\Annotation\EnumMessage;

/**
 * 基础状态
 */
class WhetherStatus extends AbstractEnum
{
    /**
     * @Message("是")
     */
    #[EnumMessage("是")]
    const YES = 1;

    /**
     * @Message("否")
     */
    #[EnumMessage("否")]
    const NO = 0;
}
