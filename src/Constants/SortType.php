<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Constants;

use Lengbin\ErrorCode\AbstractEnum;
use Lengbin\ErrorCode\Annotation\EnumMessage;

/**
 * 排序方式
 * @method static SortType UNKNOWN()
 * @method static SortType ASC()
 * @method static SortType DESC()
 */
class SortType extends AbstractEnum
{
    /**
     * @Message("")
     */
    #[EnumMessage(message: "")]
    public const UNKNOWN = '';

    /**
     * @Message("正序")
     */
    #[EnumMessage(message: "正序")]
    public const ASC = 'asc';

    /**
     * @Message("倒序")
     */
    #[EnumMessage(message: "倒序")]
    public const DESC = 'desc';
}
