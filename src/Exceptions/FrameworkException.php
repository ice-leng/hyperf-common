<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/3
 * Time:  11:26 上午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Exceptions;

class FrameworkException extends AbstractException
{
    public function getRealCode(): string
    {
        return sprintf('F-%d', $this->formatCode());
    }
}
