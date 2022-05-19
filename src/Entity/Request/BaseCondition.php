<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Entity\Request;

use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Lengbin\Common\BaseObject;

class BaseCondition extends BaseObject
{
    #[ApiModelProperty(value: "是否格式化", hidden: true)]
    public bool $_format = true;

    #[ApiModelProperty(value: "是否获取源数据", hidden: true)]
    public bool $_origin = false;

    #[ApiModelProperty(value: "是否不抛异常", hidden: true)]
    public bool $_notThrow = true;

    #[ApiModelProperty(value: "是否批量添加", hidden: true)]
    public bool $_insert = false;

    #[ApiModelProperty(value: "是否批量更新", hidden: true)]
    public bool $_update = false;

    #[ApiModelProperty(value: "是否强制删除", hidden: true)]
    public bool $_delete = false;

    #[ApiModelProperty(value: "是否悲观锁", hidden: true)]
    public bool $_forUpdate = false;

    #[ApiModelProperty(value: "是否排除主键", hidden: true)]
    public bool $_exceptPk = false;
}