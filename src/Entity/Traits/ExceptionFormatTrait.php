<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Entity\Traits;

use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Throwable;

trait ExceptionFormatTrait
{
    private ?FormatterInterface $_formatter = null;

    private function getFormatter(): FormatterInterface
    {
        if (is_null($this->_formatter)) {
            $this->_formatter = make(FormatterInterface::class);
        }
        return $this->_formatter;
    }

    public function formatException(Throwable $throwable): string
    {
        return $this->getFormatter()->format($throwable);
    }
}