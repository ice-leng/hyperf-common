<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\HttpServer\Response as BaseResponse;
use Lengbin\Helper\YiiSoft\StringHelper;
use Lengbin\Hyperf\Common\Error\CommentErrorCode;
use Psr\Http\Message\ResponseInterface;

class Response extends BaseResponse
{
    /**
     * @param null $params
     *
     * @return ResponseInterface
     */
    public function success($params = null)
    {
        if (StringHelper::isEmpty($params)) {
            $params = new \stdClass();
        }
        $data = [
            "code"    => CommentErrorCode::SUCCESS,
            "result"  => $params,
            "message" => CommentErrorCode::SUCCESS()->getMessage(),
        ];
        return $this->json($data);
    }

    /**
     * @param string      $code
     * @param string|null $message
     *
     * @return ResponseInterface
     */
    public function fail(string $code, ?string $message = null)
    {
        if (StringHelper::isEmpty($message)) {
            $message = CommentErrorCode::byValue($code)->getMessage();
        }
        $data = [
            "code"    => $code,
            "result"  => new \stdClass(),
            "message" => $message,
        ];
        return $this->json($data);
    }
}
