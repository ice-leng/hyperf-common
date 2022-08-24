<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Constants;

use Lengbin\ErrorCode\AbstractEnum;
use Lengbin\ErrorCode\Annotation\EnumMessage;

/**
 * 删除
 * @method static SoftDeleted ENABLE()
 * @method static SoftDeleted DISABLE()
 */
class SoftDeleted extends AbstractEnum
{
    /**
     * @Message("正常")
     */
    #[EnumMessage("正常")]
    const ENABLE = 1;

    /**
     * @Message("删除")
     */
    #[EnumMessage("删除")]
    const DISABLE = 0;
}
