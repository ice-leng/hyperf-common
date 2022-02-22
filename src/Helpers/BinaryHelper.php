<?php
/**
 * Created by PhpStorm.
 * Date:  2022/2/21
 * Time:  4:27 PM
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Helpers;

class BinaryHelper
{
    /**
     * 2的 n次方 的 和
     * @param array $items
     *
     * @return int
     */
    public function sum(array $items): int
    {
        $value = 0;
        foreach ($items as $item) {
            $value += $this->pow($item);
        }
        return $value;
    }

    /**
     * 获得二进制  2的 n次方
     * @param int $value
     *
     * @return int
     */
    public function pow(int $value): int
    {
        return pow(2, $value);
    }

    /**
     * 解析 2的 n次方 的 和
     * @param array $items
     * @param int   $value
     *
     * @return array
     */
    public function resolver(array $items, int $value): array
    {
        // 14 = 8 + 4 + 2
        $data = [];
        foreach ($items as $item) {
            $pow = $this->pow($item);
            if ($pow > $value) {
                continue;
            }
            $data[] = $item;
            $value -= $pow;
        }
        return $data;
    }
}
