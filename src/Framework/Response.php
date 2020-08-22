<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\HttpServer\Response as BaseResponse;
use Lengbin\Helper\YiiSoft\StringHelper;
use Lengbin\Hyperf\Common\Constant\ErrorCode;

class Response extends BaseResponse
{
    /**
     * @param null|mixed $params
     *
     */
    public function success($params = null)
    {
        if (StringHelper::isEmpty($params)) {
            $params = new \stdClass();
        }
        $data = [
            "code"    => ErrorCode::SUCCESS,
            "result"  => $params,
            "message" => ErrorCode::getMessage(ErrorCode::SUCCESS),
        ];
        return $this->json($data);
    }

    /**
     * @param string      $code
     * @param string|null $message
     */
    public function fail(string $code, ?string $message = null)
    {
        if (StringHelper::isEmpty($message)) {
            $message = ErrorCode::getMessage($code);
        }
        $data = [
            "code"    => $code,
            "result"  => new \stdClass(),
            "message" => $message,
        ];
        return $this->json($data);
    }
}
