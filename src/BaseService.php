<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/3
 * Time:  1:17 上午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common;

use Lengbin\Common\Entity\Page;

class BaseService
{
    public function toArray($data, callable $handler)
    {
        if (is_object($data)) {
            return call_user_func($handler, $data);
        }

        foreach ($data as $key => $item) {
            $data[$key] = call_user_func($handler, $item);
        }
        return $data;
    }

    /**
     * page
     *
     * @param array $data
     * @param Page  $page
     *
     * @return array
     */
    public function outputForArray(array $data, Page $page): array
    {
        $output = [];
        if ($page->total) {
            $total = count($data);
            $output['total'] = $total;
        }

        $list = $data;
        if (!$page->all) {
            $pageSize = $page->pageSize;
            $offset = ($page->page - 1) * $pageSize;
            $data = array_values($data);
            $output['page'] = $page->page;
            $output['page_size'] = $pageSize;
            $list = array_slice($data, $offset, $pageSize);
        }

        $output['list'] = $list;
        return $output;
    }
}
