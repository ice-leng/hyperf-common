<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\HttpServer\Response as BaseResponse;
use Lengbin\Helper\YiiSoft\StringHelper;
use Lengbin\Hyperf\Common\Error\CommentErrorCode;
use stdClass;

class Response extends BaseResponse
{
    public function success($params = null)
    {
        if (StringHelper::isEmpty($params)) {
            $params = new stdClass();
        }
        $data = [
            "code"    => CommentErrorCode::SUCCESS,
            "result"  => $params,
            "message" => CommentErrorCode::SUCCESS()->getMessage(),
        ];
        return $this->json($data);
    }

    public function fail(string $code, string $message)
    {
        $data = [
            "code"    => $code,
            "result"  => new stdClass(),
            "message" => $message,
        ];
        return $this->json($data);
    }
}
