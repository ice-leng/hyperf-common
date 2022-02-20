<?php
/**
 * Created by PhpStorm.
 * Date:  2021/11/25
 * Time:  10:30 下午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Helpers;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * ip info
 */
class IpHelper
{
    /**
     * @Inject()
     * @var ClientFactory
     */
    protected ClientFactory $clientFactory;

    /**
     * @param string $ip
     *
     * @return array
     */
    public function getInfoByIp(string $ip): array
    {
        // composer require zx-inc/zxipdb
        $html = '';
        try {
            $url = 'https://www.cip.cc/' . $ip;
            $client = $this->clientFactory->create()->get($url);
            $html = $client->getBody()->getContents();
        } catch (Throwable $exception) {
        }

        preg_match('/<pre>([\s\S]*?)<\/pre>/', $html, $pre);
        if (empty($pre[1])) {
            return ['未知', '未知', '未知',];
        }
        $data = [];
        $results = explode(PHP_EOL, $pre[1]);
        foreach ($results as $result) {
            if (empty($result)) {
                continue;
            }
            [, $value] = explode(":", $result);
            $data[] = trim($value);
        }
        return [$data[1], $data[2], $data[3]];
    }

    /**
     * 真实 ip
     * @param ServerRequestInterface $request
     * @param string                 $headerName
     *
     * @return string
     */
    public function getClientIp(ServerRequestInterface $request, string $headerName = 'x-real-ip'): string
    {
        $client = $request->getServerParams();
        $xri = $request->getHeader($headerName);
        if (!empty($xri)) {
            $clientAddress = $xri[0];
        } else {
            $clientAddress = $client['remote_addr'];
        }
        $xff = $request->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {
                // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {
                // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) {
                    $clientAddress = $list[0];
                }
            }
        }
        return $clientAddress;
    }
}
