<?php
/**
 * Created by PhpStorm.
 * Date:  2022/4/15
 * Time:  11:35 AM
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common;

use Hyperf\Database\Model\Builder;
use Lengbin\Common\Entity\Page;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;

trait MySQLDaoTrait
{
    abstract public function modelClass(): string;

    abstract protected function handleSearch(Builder $query, array $search, array $condition): Builder;

    public function getList(array $condition, array $search, array $sort, Page $page, array $field = ['*']): array
    {
        $query = $this->modelClass()::buildQuery($search);
        $query->select($field);

        foreach ($condition as $with => $whether) {
            if (!$whether) {
                continue;
            }
            if (str_starts_with($with, 'with_')) {
                $query->with(substr($with, 5));
            } elseif (str_starts_with($with, 'with')) {
                $query->with(lcfirst(substr($with, 4)));
            }
        }

        foreach ($sort as $column => $sortType) {
            $query->orderBy($column, $sortType);
        }

        $model = new $this->modelClass();
        $query->orderBy("{$model->getTableName()}.{$model->getKeyName()}");
        return $this->output($this->handleSearch($query, $search, $condition), $page);
    }

    protected function appendTime(array $data, array $columns = ['create_at', 'update_at']): array
    {
        $now = time();
        foreach ($data as $key => $item) {
            foreach ($columns as $column) {
                if (!ArrayHelper::isValidValue($item, $column)) {
                    $item[$column] = $now;
                }
            }
            $data[$key] = $item;
        }
        return $data;
    }

    public function batchInsert(array $data): array
    {
        $data = $this->appendTime($data);
        $ret = $this->modelClass()::query()->insert($data);
        if (!$ret) {
            return [];
        }
        return $data;
    }

    public function batchUpdate(array $data): array
    {
        $data = $this->appendTime($data, ['update_at']);
        $ret = $this->modelClass()::query()->insertOrUpdate($data);
        if (!$ret) {
            return [];
        }
        return $data;
    }

    public function create(array $data, array $condition = []): array
    {
        if (ArrayHelper::isValidValue($condition, 'for_insert')) {
            return $this->batchInsert($data);
        }
        if (ArrayHelper::isValidValue($condition, 'for_update')) {
            return $this->batchUpdate($data);
        }

        $model = (new $this->modelClass())->fill($data);
        $ret = $model->save();
        return $ret ? $model->toArray() : [];
    }

    public function modify(array $search, array $data): int
    {
        return $this->modelClass()::updateCondition($search, $data);
    }

    public function remove(array $condition, array $search, string $softDeleted = 'enable'): int
    {
        $forceDelete = boolval($condition['for_delete'] ?? false);
        return $this->modelClass()::removeCondition($search, $forceDelete);
    }

    public function detail(array $condition, array $search, array $field = ['*']): array
    {
        $forUpdate = boolval($condition['for_update'] ?? false);
        $forExcludePk = boolval($condition['for_exclude_pk'] ?? false);
        $model = $this->modelClass()::findOne($search, $field, $forExcludePk, $forUpdate);
        if (!$model) {
            return [];
        }
        return $model->toArray();
    }
}
