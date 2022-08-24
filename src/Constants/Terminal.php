<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Common\Constants\Enums;

use Lengbin\ErrorCode\AbstractEnum;
use Lengbin\ErrorCode\Annotation\EnumMessage;

/**
 * 终端
 * @method static Terminal IOS()
 * @method static Terminal ANDROID()
 * @method static Terminal PC()
 * @method static Terminal H5()
 */
class Terminal extends AbstractEnum
{
    #[EnumMessage(message: '苹果')]
    public const IOS = 1;

    #[EnumMessage(message: '安卓')]
    public const ANDROID = 2;

    #[EnumMessage(message: 'pc')]
    public const PC = 3;

    #[EnumMessage(message: 'h5')]
    public const H5 = 4;
}
