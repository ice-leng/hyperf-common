<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\HttpServer\Request as BaseRequest;

class Request extends BaseRequest
{
    /**
     * 真实 ip
     *
     * @param string $headerName
     *
     * @return string
     */
    public function getClientIp($headerName = 'x-real-ip'): string
    {
        $client = $this->getServerParams();
        $clientAddress = $client['remote_addr'];
        $xri = $this->getHeader($headerName);
        $xff = $this->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) {
                    $clientAddress = $list[0];
                }
            }
        }
        return $clientAddress;
    }
}
