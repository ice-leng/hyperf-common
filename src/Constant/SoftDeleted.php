<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Constant;

use Lengbin\Hyperf\ErrorCode\BaseEnum;

class SoftDeleted extends BaseEnum
{
    /**
     * @Message("正常")
     */
    const ENABLE = 1;

    /**
     * @Message("删除")
     */
    const DISABLE = 2;
}
