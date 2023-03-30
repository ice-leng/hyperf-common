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
            if (is_array($value) && isset($value[0])) {
                if (!str_contains($value[0], '.')) {
                    $value[0] = "{$tableName}.{$value[0]}";
                }
            } else {
                if (!str_contains($key, '.')) {
                    $key = "{$tableName}.{$key}";
                }
            }
            $result[$key] = $value;
        }
        return $result;
    }

    protected function getModel(array $condition = []): BaseModel
    {
        $model = new ($this->modelClass());
        $tableName = '';
        if (ArrayHelper::isValidValue($condition, '_subTable_date') && method_exists($this, 'getSubTableDate')) {
            $tableName = $this->getSubTableDate($condition['_subTable_date']);
        }
        if (ArrayHelper::isValidValue($condition, '_subTable_hash') && method_exists($this, 'getSubTableHash')) {
            $tableName = $this->getSubTableHash($condition['_subTable_hash']);
        }
        if (ArrayHelper::isValidValue($condition, '_subTable') && method_exists($this, 'getSubTable')) {
            $tableName = $this->getSubTable($condition['_subTable']);
        }
        if (ArrayHelper::isValidValue($condition, '_table')) {
            $tableName = $condition['_table'];
        }
        if ($tableName) {
            $model->setTable($tableName);
        }
        return $model;
    }

    protected function handleQuery(array $condition, array $search, array $field = ['*'], array $sort = []): Builder
    {
        $model = $this->getModel($condition);
        $query = $model->newQuery();
        [$query, $search, $condition, $sort] = $this->handleSearch($query, $search, $condition, $sort);

        $groupBy = ArrayHelper::remove($search, '_groupBy');
        if ($groupBy) {
            $query->groupBy($groupBy);
        } else {
            if (empty($sort[$model->getKeyName()])) {
                $sort[$model->getKeyName()] = SortType::ASC;
            }
        }

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

        return $model->buildQuery($search, $forExcludePk, $query);
    }

    public function getList(array $condition, array $search, array $sort, Page $page, array $field = ['*']): array
    {
        $query = $this->handleQuery($condition, $search, $field, $sort);
        return $this->output($query, $page);
    }

    protected function appendTime(BaseModel $model, array $data, array $columns = []): array
    {
        if (empty($columns)) {
            $columns = [$model->getCreatedAtColumn(), $model->getUpdatedAtColumn()];
        }
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

    public function batchInsert(BaseModel $model, array $data): array
    {
        $data = $this->appendTime($model, $data);
        $ret = $model->newQuery()->insert($data);
        if (!$ret) {
            return [];
        }
        return $data;
    }

    public function batchUpdate(BaseModel $model, array $data): array
    {
        $data = $this->appendTime($model, $data, [$model->getUpdatedAtColumn()]);
        $ret = $model->insertOrUpdate($data);
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

        $model = $this->getModel($condition);

        if (ArrayHelper::isValidValue($condition, '_insert')) {
            return $this->batchInsert($model, $data);
        }
        if (ArrayHelper::isValidValue($condition, '_update')) {
            return $this->batchUpdate($model, $data);
        }

        $orm = $model->fill($data);
        $ret = $orm->save();
        return $ret ? $orm->toArray() : [];
    }

    public function modify(array $condition, array $search, array $data): int
    {
        $forExcludePk = boolval($condition['_exceptPk'] ?? false);
        return $this->getModel($condition)->updateCondition($search, $data, $forExcludePk);
    }

    public function remove(array $condition, array $search): int
    {
        if (empty($search)) {
            return 0;
        }
        $forceDelete = boolval($condition['_delete'] ?? false);
        return $this->getModel($condition)->removeCondition($search, $forceDelete, $this->softDeleted);
    }

    public function detail(array $condition, array $search, array $field = ['*']): array
    {
        $query = $this->handleQuery($condition, $search, $field);
        $model = $query->first();
        return $model ? $model->toArray() : [];
    }
}
