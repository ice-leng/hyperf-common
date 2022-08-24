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
use Lengbin\Hyperf\Common\Constants\SortType;

trait MySQLDaoTrait
{
    protected string $softDeleted = 'enable';

    abstract public function modelClass(): string;

    abstract protected function handleSearch(Builder $query, array $search, array $condition, array $sort): array;

    protected function appendTableName(array $data, string $tableName): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (!str_contains($key, '.')) {
                $key = "{$tableName}.{$key}";
            }
            $result[$key] = $value;
        }
        return $result;
    }

    protected function handleQuery(array $condition, array $search, array $field = ['*'], array $sort = []): Builder
    {
        $model = make($this->modelClass());
        $query = $model->newQuery();
        [$query, $search, $condition, $sort] = $this->handleSearch($query, $search, $condition, $sort);

        $groupBy = ArrayHelper::remove($search, '_groupBy');
        if ($groupBy) {
            $query->groupBy($groupBy);
        }

        $sort[$model->getKeyName()] = SortType::ASC;

        $forExcludePk = false;
        foreach ($condition as $with => $whether) {
            if (!$whether) {
                continue;
            }
            switch ($with) {
                case '_leftJoin':
                    $sort = $this->appendTableName($sort, $model->getTableName());
                    $search = $this->appendTableName($search, $model->getTableName());
                    break;
                case "_exceptPk":
                    $forExcludePk = true;
                    break;
                case "_forUpdate";
                    $query->lockForUpdate();
                    break;
                default:
                    if ($whether == 1) {
                        if (str_starts_with($with, 'with_')) {
                            $query->with(substr($with, 5));
                        } elseif (str_starts_with($with, 'with')) {
                            $query->with(lcfirst(substr($with, 4)));
                        }
                    }
                    break;
            }
        }

        $query->select($field);
        foreach ($sort as $column => $sortType) {
            if (empty($sortType)) {
                continue;
            }
            $query->orderBy($column, $sortType);
        }

        return $this->modelClass()::buildQuery($search, $query, $forExcludePk);
    }

    public function getList(array $condition, array $search, array $sort, Page $page, array $field = ['*']): array
    {
        $query = $this->handleQuery($condition, $search, $field, $sort);
        return $this->output($query, $page);
    }

    protected function appendTime(array $data, array $columns = [$this->modelClass()::CREATED_AT, $this->modelClass()::UPDATED_AT]): array
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
        $data = $this->appendTime($data, [$this->modelClass()::UPDATED_AT]);
        $ret = $this->modelClass()::insertOrUpdate($data);
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

    public function modify(array $condition, array $search, array $data): int
    {
        $forExcludePk = boolval($condition['_exceptPk'] ?? false);
        return $this->modelClass()::updateCondition($search, $data, $forExcludePk);
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
        $query = $this->handleQuery($condition, $search, $field);
        $model = $query->first();
        return $model ? $model->toArray() : [];
    }
}
