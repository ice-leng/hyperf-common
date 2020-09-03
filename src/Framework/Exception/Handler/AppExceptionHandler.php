<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Lengbin\Hyperf\Common\Framework\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    use ExceptionHandlerTrait;

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
