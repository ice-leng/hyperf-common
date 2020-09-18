<?php

namespace Lengbin\Hyperf\Common\Component\Sort;

use Lengbin\Helper\Component\BaseObject;

/**
 * Class Sort
 *
 * $sort = [
 *     'attributes' = [
 *          'name' => [
 *              'asc' => ['name' => SORT_ASC],
 *              'desc' => ['name' => SORT_DESC],
 *              'default' => SORT_DESC,
 *          ];
 *     ];
 * ];
 */
class Sort extends BaseObject
{

    /**
     * 是否支持 多个排序
     */
    private $enableMultiSort = false;

    /**
     * @var SortAttribute[]
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $separator = ',';

    /**
     * eg: name,-age
     *
     * @var string
     */
    private $sort;

    private $direction = [
        SORT_ASC  => 'ASC',
        SORT_DESC => 'DESC',
    ];

    /**
     * @param bool $enableMultiSort
     *
     * @return $this
     */
    public function setEnableMultiSort(bool $enableMultiSort): self
    {
        $this->enableMultiSort = $enableMultiSort;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnableMultiSort(): bool
    {
        return $this->enableMultiSort;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function setSort(string $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return string
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array|SortAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * 获得排序
     * @return array
     */
    protected function getAttributeOrders(): array
    {
        $data = [];
        $attributeConfig = $this->getAttributes();
        $attributes = [];
        if (!StringHelper::isEmpty($this->getSort())) {
            if ($this->getEnableMultiSort()) {
                $attributes = explode($this->getSeparator(), $this->getSort());
            } else {
                $attributes = [$this->getSort()];
            }
        }
        foreach ($attributes as $attribute) {
            $descending = false;
            if (strncmp($attribute, '-', 1) === 0) {
                $descending = true;
                $attribute = substr($attribute, 1);
            }

            if (isset($attributeConfig[$attribute])) {
                $data[$attribute] = $descending ? SORT_DESC : SORT_ASC;
                if (!$this->getEnableMultiSort()) {
                    return $data;
                }
            }
        }
        return $data;
    }

    /**
     * 获得 排序
     *
     * @return array
     */
    public function getOrders(): array
    {
        $orders = [];
        $attributeOrders = $this->getAttributeOrders();
        $attributeConfig = $this->getAttributes();
        foreach ($attributeOrders as $attribute => $direction) {
            $definition = $attributeConfig[$attribute];
            $columns = $direction === SORT_ASC ? $definition->getAsc() : $definition->getDesc();
            if (is_array($columns)) {
                foreach ($columns as $name => $dir) {
                    $orders[$name] = $this->direction[$dir];
                }
            } else {
                $orders[] = $columns;
            }
        }
        return $orders;
    }

    /**
     * 生成
     *
     * @param string $attribute
     * @param int    $default
     *
     * @return SortAttribute
     */
    public static function generateSortAttribute(string $attribute, int $default = SORT_ASC): SortAttribute
    {
        $data = [
            'asc'     => [$attribute => SORT_ASC],
            'desc'    => [$attribute => SORT_DESC],
            'default' => $default,
        ];
        return new SortAttribute($data);
    }

    /**
     *
     */
    public function buildOrderBy(): string
    {
        $orders = [];
        foreach ($this->getOrders() as $name => $direction) {
            $orders[] = $name . ' ' . $direction;
        }

        return implode(', ', $orders);
    }
}
