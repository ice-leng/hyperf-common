<?php

declare (strict_types=1);

namespace Lengbin\Hyperf\Common\Entity\Traits;

use Lengbin\Hyperf\Common\Constants\SoftDeleted;

trait EnableIdentifier
{
    protected int $enable = SoftDeleted::ENABLE;
}
