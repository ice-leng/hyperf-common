<?php

declare (strict_types=1);

namespace Lengbin\Hyperf\Common\Entity\Traits;

use Lengbin\Hyperf\Common\Constants\BaseStatus;

trait EnableIdentifier
{
    protected int $enable = BaseStatus::NORMAL;
}
