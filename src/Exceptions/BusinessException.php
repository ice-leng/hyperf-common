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
namespace Lengbin\Hyperf\Common\Exceptions;


class BusinessException extends AbstractException
{
    public function getRealCode(): string
    {
        return sprintf('B-%d', $this->formatCode());
    }
}
