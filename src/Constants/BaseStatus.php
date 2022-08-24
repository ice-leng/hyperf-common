<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/3
 * Time:  12:15 上午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Constants;

use Lengbin\ErrorCode\AbstractEnum;
use Lengbin\ErrorCode\Annotation\EnumMessage;

/**
 * 基础状态
 * @method static BaseStatus FROZEN()
 * @method static BaseStatus NORMAL()
 */
class BaseStatus extends AbstractEnum
{
    /**
     * @Message("禁用")
     */
    #[EnumMessage("禁用")]
    const FROZEN = 0;

    /**
     * @Message("正常")
     */
    #[EnumMessage("正常")]
    const NORMAL = 1;
}
