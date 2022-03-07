<?php
/**
 * Created by PhpStorm.
 * Date:  2021/9/2
 * Time:  4:34 下午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Common;

use Hyperf\Database\Model\Collection;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;
use Lengbin\Hyperf\Common\Constants\SoftDeleted;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Query\Expression;
use Hyperf\Database\Query\Grammars\Grammar;
use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Utils\Arr;

abstract class BaseModel extends Model
{
    const CREATED_AT = 'create_at';

    const UPDATED_AT = 'update_at';

    protected $dateFormat = 'U';

    /**
     * @param array       $conditions
     * @param string|null $softDeleted
     *
     * @return Builder
     */
    public static function buildQuery(array $conditions, ?string $softDeleted = 'enable'): Builder
    {
        $model = new static();
        $query = $model->newQuery();
        if (ArrayHelper::isIndexed($conditions)) {
            $query->where($conditions);
        } else {
            foreach ($conditions as $key => $value) {
                if (is_null($value)) {
                    continue;
                }
                if (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        if (!empty($softDeleted)) {
            $query->where($softDeleted, SoftDeleted::ENABLE);
        }

        return $query;
    }

    /**
     * @param array       $conditions
     * @param array       $field
     * @param bool        $forUpdate
     * @param string|null $softDeleted
     *
     * @return null|BaseModel|object|static
     */
    public static function findOneCondition(array $conditions, array $field = ['*'], bool $forUpdate = false, ?string $softDeleted = 'enable'): ?self
    {
        $query = self::buildQuery($conditions, $softDeleted);
        if ($forUpdate) {
            $query->lockForUpdate();
        }
        return $query->first($field);
    }

    /**
     * @param array       $conditions
     * @param array       $field
     * @param bool        $forUpdate
     * @param string|null $softDeleted
     *
     * @return Collection|static[]
     */
    public static function findCondition(array $conditions, array $field = ['*'], bool $forUpdate = false, ?string $softDeleted = 'enable')
    {
        $query = self::buildQuery($conditions, $softDeleted);
        if ($forUpdate) {
            $query->lockForUpdate();
        }
        return $query->get($field);
    }

    /**
     * @param array       $condition
     * @param array       $data
     * @param string|null $softDeleted
     *
     * @return int
     */
    public static function updateCondition(array $condition, array $data, ?string $softDeleted = 'enable'): int
    {
        $query = static::buildQuery($condition);
        return $query->update($data);
    }

    /**
     * @param array       $condition
     * @param bool        $forceDelete
     * @param string|null $softDeleted
     *
     * @return int
     */
    public static function removeCondition(array $condition, bool $forceDelete = false, ?string $softDeleted = 'enable'): int
    {
        $query = static::buildQuery($condition, $softDeleted);
        if ($forceDelete) {
            return $query->delete();
        } else {
            return $query->update([
                $softDeleted => SoftDeleted::DISABLE,
            ]);
        }
    }

    /**
     * insert or update a record
     *
     * @param array $values
     * @param array $column
     *
     * @return bool
     */
    public static function insertOrUpdate(array $values, array $column)
    {
        if (empty($column)) {
            $column = array_keys($values);
        }
        $value = [];
        foreach ($column as $item) {
            $value[$item] = Db::raw("values(`{$item}`)");
        }
        $model = new static();
        $connection = $model->getConnection();   // 数据库连接
        $builder = $model->newQuery()->getQuery();   // 查询构造器
        $grammar = $builder->getGrammar();  // 语法器
        // 编译插入语句
        $insert = $grammar->compileInsert($builder, $values);
        // 编译重复后更新列语句。
        $update = $model->compileUpdateColumns($grammar, $value);
        // 构造查询语句
        $query = $insert . ' on duplicate key update ' . $update;
        // 组装sql绑定参数
        $bindings = $model->prepareBindingsForInsertOrUpdate($values, $value);
        // 执行数据库查询
        return $connection->insert($query, $bindings);
    }

    /**
     * Compile all of the columns for an update statement.
     *
     * @param Grammar $grammar
     * @param array   $values
     *
     * @return string
     */
    private function compileUpdateColumns(Grammar $grammar, array $values)
    {
        return collect($values)->map(function ($value, $key) use ($grammar) {
            return $grammar->wrap($key) . ' = ' . $grammar->parameter($value);
        })->implode(', ');
    }

    /**
     * Prepare the bindings for an insert or update statement.
     *
     * @param array $values
     * @param array $value
     *
     * @return array
     */
    private function prepareBindingsForInsertOrUpdate(array $values, array $value)
    {
        // Merge array of bindings
        $bindings = array_merge_recursive($values, [$value]);
        // Remove all of the expressions from a list of bindings.
        return array_values(array_filter(Arr::flatten($bindings, 1), function ($binding) {
            return !$binding instanceof Expression;
        }));
    }

    protected function asJson($value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function fromDateTime($value): string
    {
        return strval($this->asTimestamp($value));
    }
}
