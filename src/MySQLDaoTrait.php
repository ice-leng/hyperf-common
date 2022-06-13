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
    protected string $softDeleted = 'enable';

    abstract public function modelClass(): string;

    abstract protected function handleSearch(Builder $query, array &$search, array $condition): Builder;

    protected function handleQuery(array $condition, array $search, array $field = ['*'], bool $forExcludePk = false): Builder
    {
        $model = make($this->modelClass());
        $query = $model->newQuery()->select($field);

        $query->orderBy("{$model->getTableName()}.{$model->getKeyName()}");

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

        $query = $this->handleSearch($query, $search, $condition);
        return $this->modelClass()::buildQuery($search, $query, $forExcludePk);
    }

    public function getList(array $condition, array $search, array $sort, Page $page, array $field = ['*']): array
    {
        $query = $this->handleQuery($condition, $search, $field);

        foreach ($sort as $column => $sortType) {
            $query->orderBy($column, $sortType);
        }

        return $this->output($query, $page);
    }

    protected function appendTime(array $data, array $columns = ['create_at', 'update_at']): array
    {
        $now = time();
        foreach ($data as $key => $item) {
            foreach ($columns as $column) {
                if (!ArrayHelper::keyExists($item, $column)) {
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

    public function create(array $condition, array $data): array
    {
        if (empty($data)) {
            return [];
        }

        if (ArrayHelper::isValidValue($condition, '_insert')) {
            return $this->batchInsert($data);
        }
        if (ArrayHelper::isValidValue($condition, '_update')) {
            return $this->batchUpdate($data);
        }

        $model = (make($this->modelClass()))->fill($data);
        $ret = $model->save();
        return $ret ? $model->toArray() : [];
    }

    public function modify(array $search, array $data): int
    {
        return $this->modelClass()::updateCondition($search, $data);
    }

    public function remove(array $condition, array $search): int
    {
        if (empty($search)) {
            return 0;
        }
        $forceDelete = boolval($condition['_delete'] ?? false);
        return $this->modelClass()::removeCondition($search, $forceDelete, $this->softDeleted);
    }

    public function detail(array $condition, array $search, array $field = ['*']): array
    {
        $forUpdate = boolval($condition['_forUpdate'] ?? false);
        $forExcludePk = boolval($condition['_exceptPk'] ?? false);

        $groupBy = ArrayHelper::remove($search, '_groupBy');
        $query = $this->handleQuery($condition, $search, $field, $forExcludePk);
        if ($groupBy) {
            $query->groupBy($groupBy);
        }

        if ($forUpdate) {
            $query->lockForUpdate();
        }
        $model = $query->first($field);

        if (!$model) {
            return [];
        }
        return $model->toArray();
    }
}
