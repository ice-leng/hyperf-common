<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Constants;

use Lengbin\ErrorCode\AbstractEnum;

class SoftDeleted extends AbstractEnum
{
    /**
     * @Message("正常")
     */
    const ENABLE = 1;

    /**
     * @Message("删除")
     */
    const DISABLE = 0;
}