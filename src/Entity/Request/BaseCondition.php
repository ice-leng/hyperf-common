<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Entity\Request;

use Hyperf\ApiDocs\Annotation\ApiModelProperty;
use Lengbin\Common\BaseObject;

class BaseCondition extends BaseObject
{
    #[ApiModelProperty(value: "是否格式化", hidden: true)]
    public int $_format = 1;

    #[ApiModelProperty(value: "是否不抛异常", hidden: true)]
    public int $_throw = 1;

    #[ApiModelProperty(value: "是否批量添加", hidden: true)]
    public int $_insert = 0;

    #[ApiModelProperty(value: "是否批量更新", hidden: true)]
    public int $_update = 0;

    #[ApiModelProperty(value: "是否强制删除", hidden: true)]
    public int $_delete = 0;

    #[ApiModelProperty(value: "是否悲观锁", hidden: true)]
    public int $_forUpdate = 0;

    #[ApiModelProperty(value: "是否排除主键", hidden: true)]
    public int $_exceptPk = 0;

    #[ApiModelProperty(value: "分表", hidden: true)]
    public string $_subTable = '';
}