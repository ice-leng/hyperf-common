<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Entity\Request;

use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Lengbin\Common\BaseObject;
use Lengbin\Hyperf\Common\Constants\SortType;

class BaseSort extends BaseObject
{
    #[ApiModelProperty('创建时间排序')]
    public SortType $createAt;
}
