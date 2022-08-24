<?php

namespace Lengbin\Hyperf\Common\Constants;

use Lengbin\ErrorCode\AbstractEnum;
use Lengbin\ErrorCode\Annotation\EnumMessage;

/**
 * 是否
 * @method static WhetherStatus YES()
 * @method static WhetherStatus NO()
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
