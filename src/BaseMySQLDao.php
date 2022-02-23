<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/3
 * Time:  1:20 上午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Lengbin\Common\Entity\Page;

class BaseMySQLDao
{
    public function output(Builder $query, Page $page): array
    {
        $output = [];
        if ($page->total) {
            $sql = sprintf("select count(*) as count from (%s) as b", $query->toSql());
            $output['total'] = Db::selectOne($sql, $query->getBindings())->count;
        }

        if (!$page->all) {
            $query->forPage($page->page, $page->pageSize);
            $output['page'] = $page->page;
            $output['page_size'] = $page->pageSize;
        }

        $output['list'] = $query->get()->toArray();
        return $output;
    }

    /**
     * @param Builder $model
     * @param string  $field
     * @param array   $data [start, end]
     */
    public function betweenTime(Builder $model, string $field, array $data)
    {
        $model->where(function (Builder $builder) use ($field, $data) {
            if ($data['start'] > 0) {
                $builder->where($field, '>=', $data['start']);
            }
            if ($data['end'] > 0) {
                $builder->where($field, '<', $data['end']);
            }
        });
    }
}
