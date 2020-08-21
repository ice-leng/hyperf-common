<?php

namespace Lengbin\Hyperf\Common\Component\Sort;

use Lengbin\Helper\BaseObject;
use Lengbin\Hyperf\Common\Component\Sort\Exception\MethodNotAllowedException;

/**
 * Class Sort
 *
 * $sort = [
 *      'name' => [
 *          'asc' => ['name' => SORT_ASC],
 *          'desc' => ['name' => SORT_DESC],
 *          'default' => SORT_DESC,
 *      ];
 * ];
 *
 * // demo2
 * [
 *     'name' => [
 *         'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
 *         'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
 *         'default' => SORT_DESC,
 *     ],
 * ]
 *
 */
class SortAttribute extends BaseObject
{
    /**
     * @var array
     */
    private $asc = [];

    /**
     * @var array
     */
    private $desc = [];

    /**
     * @var int
     */
    private $default = SORT_ASC;

    /**
     * @var array
     */
    private $allowSort = [SORT_ASC, SORT_DESC];

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setDefault(int $sort): self
    {
        if (!in_array($sort, $this->allowSort)) {
            throw new MethodNotAllowedException();
        }
        $this->default = $sort;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault(): int
    {
        return $this->default;
    }

    /**
     * @param array $asc
     *
     * @return $this
     */
    public function setAsc(array $asc): self
    {
        $this->asc = $asc;
        return $this;
    }

    /**
     * @return array
     */
    public function getAsc(): array
    {
        return $this->asc;
    }

    /**
     * @param array $desc
     *
     * @return $this
     */
    public function setDesc(array $desc): self
    {
        $this->desc = $desc;
        return $this;
    }

    /**
     * @return array
     */
    public function getDesc(): array
    {
        return $this->desc;
    }
}
