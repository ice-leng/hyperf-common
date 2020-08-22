<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use SwooleX\Constants\SoftDeleted;

class BaseModel
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
    public function update(array $attributes = [], array $options = [])
    {
        $attributes = $this->parseAttributes($attributes);
        return parent::update($attributes, $options);
    }

    /**
     * 删除
     * @return bool
     */
    public function softDelete(array $options = []): bool
    {
        $this->enable = SoftDeleted::DISABLE;
        return $this->save($options);
    }

    /**
     * @param string           $key
     * @param string|int|array $value
     * @param string[]         $field
     * @param string|null      $deleteFiledName
     *
     * @return Builder|object|null
     */
    public static function findOne(string $key, $value, $field = ['*'], ?string $deleteFiledName = 'enable'): ?self
    {
        $query = self::query();
        if (!empty($deleteFiledName)) {
            $query->where([$deleteFiledName => SoftDeleted::ENABLE]);
        }

        if (is_array($value)) {
            $query->whereIn($key, $value);
        } else {
            $query->where($key, $value);
        }

        return $query->first($field);
    }

    /**
     * 多条件
     *
     * @param array       $conditions 如果是 string 表示走 主键
     * @param array       $field
     * @param string|null $deleteFiledName
     *
     * @return Builder|static|object|null
     */
    public static function findOneCondition(array $conditions, $field = ['*'], ?string $deleteFiledName = 'enable'): ?self
    {
        $query = self::query();

        if (!empty($deleteFiledName)) {
            $query->where([$deleteFiledName => SoftDeleted::ENABLE]);
        }

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        return $query->first($field);
    }

    /**
     * 多查询
     *
     * @param array       $conditions
     * @param array       $field
     * @param string|null $deleteFiledName
     *
     * @return Builder|static|object|null
     */
    public static function findAllCondition(array $conditions, $field = ['*'], ?string $deleteFiledName = 'enable'): ?Collection
    {
        $query = self::query();
        if (!empty($deleteFiledName)) {
            $query->where([$deleteFiledName => SoftDeleted::ENABLE]);
        }
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
        return $query->get($field);
    }

    /**
     * 条件删除
     *
     * @param array       $conditions
     * @param string|null $deleteFiledName
     *
     * @return int
     */
    public static function softDeleteCondition(array $conditions, ?string $deleteFiledName = 'enable'): ?int
    {
        $query = self::query();
        if (!empty($deleteFiledName)) {
            $query->where([$deleteFiledName => SoftDeleted::ENABLE]);
        }

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->update([$deleteFiledName => SoftDeleted::DISABLE]);
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
