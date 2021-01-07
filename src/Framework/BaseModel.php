<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Model\Model;
use Lengbin\Hyperf\Common\Constant\SoftDeleted;

class BaseModel extends Model
{
    const CREATED_AT = 'create_at';

    const UPDATED_AT = 'update_at';

    protected $dateFormat = 'U';

    /**
     * 表前缀
     * @return string
     */
    public static function getTablePrefix(): string
    {
        return self::query()->getConnection()->getTablePrefix();
    }

    /**
     * 静态方法 获取 表名
     *
     * @param bool $isFull
     *
     * @return string
     */
    public static function getTableName(bool $isFull = false): string
    {
        $model = new static();
        $tableName = $model->getTable();
        if ($isFull) {
            $tableName = self::getTablePrefix() . $tableName;
        }
        return $tableName;
    }

    /**
     * 过滤 null 字段 的值
     *
     * @param array $attributes
     *
     * @return array
     */
    private function parseAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $value) {
            if (is_null($value)) {
                unset($attributes[$key]);
            }
        }
        return $attributes;
    }

    /**
     * 添加
     *
     * @param array $attributes
     * @param array $options
     *
     * @return bool
     */
    public function insert(array $attributes, array $options = []): bool
    {
        $attributes = $this->parseAttributes($attributes);
        $this->fill($attributes);
        return $this->save($options);
    }

    /**
     *
     * 更新
     *
     * @param array $attributes
     * @param array $options
     *
     * @return bool
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        $attributes = $this->parseAttributes($attributes);
        return parent::update($attributes, $options);
    }

    /**
     * 删除
     *
     * @param string $deleteFiledName
     * @param array  $options
     *
     * @return bool
     */
    public function softDelete(string $deleteFiledName = 'enable', array $options = []): bool
    {
        $this->$deleteFiledName = SoftDeleted::DISABLE;
        return $this->save($options);
    }

    /**
     * @param string           $key
     * @param string|int|array $value
     * @param string[]         $field
     * @param string|null      $deleteFiledName
     *
     * @return object|null|static
     */
    public static function findOne(string $key, $value, $field = ['*'], ?string $deleteFiledName = 'enable'): ?self
    {
        return self::findOneCondition([$key => $value], $field, $deleteFiledName);
    }

    /**
     * @param Builder $query
     * @param array   $conditions
     *
     * @return Builder
     */
    protected static function condition(Builder $query, array $conditions = []): Builder
    {
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        return $query;
    }

    /**
     * 多条件
     *
     * @param array       $conditions
     * @param array       $field
     * @param string|null $deleteFiledName
     *
     * @return static|object|null
     */
    public static function findOneCondition(array $conditions, $field = ['*'], ?string $deleteFiledName = 'enable'): ?self
    {
        $query = self::query();
        if (!empty($deleteFiledName)) {
            $conditions[$deleteFiledName] = SoftDeleted::ENABLE;
        }
        return self::condition($query, $conditions)->first($field);
    }

    /**
     * 多查询
     *
     * @param array       $conditions
     * @param array       $field
     * @param string|null $deleteFiledName
     *
     * @return Collection|static[]
     */
    public static function findAllCondition(array $conditions, $field = ['*'], ?string $deleteFiledName = 'enable'): Collection
    {
        $query = self::query();
        if (!empty($deleteFiledName)) {
            $conditions[$deleteFiledName] = SoftDeleted::ENABLE;
        }
        return self::condition($query, $conditions)->get($field);
    }

    /**
     * 条件删除
     *
     * @param array       $conditions
     * @param string|null $deleteFiledName
     *
     * @return int
     */
    public static function softDeleteCondition(array $conditions, ?string $deleteFiledName = 'enable'): int
    {
        $query = self::query();
        if (!empty($deleteFiledName)) {
            $conditions[$deleteFiledName] = SoftDeleted::ENABLE;
        }
        return self::condition($query, $conditions)->update([$deleteFiledName => SoftDeleted::DISABLE]);
    }

    /**
     * 条件更新
     *
     * @param array $conditions
     * @param array $update
     *
     * @return int
     */
    public static function updateCondition(array $conditions, array $update): int
    {
        $query = self::query();
        if (!empty($deleteFiledName)) {
            $conditions[$deleteFiledName] = SoftDeleted::ENABLE;
        }
        return self::condition($query, $conditions)->update($update);
    }

    /**
     * format created_at
     *
     * @param $value
     *
     * @return false|string
     */
    public function getCreateAtAttribute($value): string
    {
        return date('Y-m-d H:i:s', (int)$value);
    }

    /**
     * format updated_at
     *
     * @param $value
     *
     * @return false|string
     */
    public function getUpdateAtAttribute($value): string
    {
        return date('Y-m-d H:i:s', (int)$value);
    }

    /**
     * @return int
     */
    public function getCreateAt(): int
    {
        return (int)$this->attributes[$this->getCreatedAtColumn()];
    }

    /**
     * @return int
     */
    public function getUpdateAt(): int
    {
        return (int)$this->attributes[$this->getUpdatedAtColumn()];
    }
}
