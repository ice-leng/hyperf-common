<?php

declare(strict_types=1);

namespace SwooleX\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class SoftDeleted extends AbstractConstants
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
