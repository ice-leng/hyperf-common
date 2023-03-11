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

namespace Lengbin\Hyperf\Common\Exceptions\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Lengbin\Hyperf\Common\Constants\Errors\CommonError;
use Lengbin\Hyperf\Common\Entity\Traits\ExceptionFormatTrait;
use Lengbin\Hyperf\Common\Exceptions\BusinessException;
use Lengbin\Hyperf\Common\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    use ExceptionFormatTrait;

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @var Response
     */
    protected $response;

    public function __construct(StdoutLoggerInterface $logger, Response $response)
    {
        $this->logger = $logger;
        $this->response = $response;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if (!$throwable instanceof NotFoundHttpException) {
            $this->logger->error($this->formatException($throwable));
        }

        if ($throwable instanceof BusinessException) {
            return $this->response->fail($throwable->getCode(), $throwable->getMessage());
        }

        $systemError = new BusinessException(CommonError::SERVER_ERROR());
        return $this->response->fail($systemError->getCode(), $systemError->getMessage());
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
