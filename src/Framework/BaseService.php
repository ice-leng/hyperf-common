<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\Database\Model\Builder;
use Hyperf\Utils\Str;
use Lengbin\Helper\Util\RegularHelper;
use Lengbin\Helper\YiiSoft\StringHelper;
use Lengbin\Hyperf\Common\Component\Sort\Sort;
use Lengbin\Hyperf\Common\Entity\PageEntity;
use Lengbin\Hyperf\Common\Helper\CommonHelper;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Lengbin\Hyperf\Common\Framework\Exception\MethodNotImplException;

class BaseService
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * page
     *
     * @param Builder    $query
     * @param PageEntity $pageEntity
     *
     * @return array
     */
    public function page(Builder $query, PageEntity $pageEntity): array
    {
        $pageSize = $pageEntity->getPageSize();
        $total = $query->count();
        $list = $query->forPage($pageEntity->getPage(), $pageSize)->get()->toArray();
        return [
            'list'      => $list,
            'pageSize'  => $pageSize,
            'total'     => $total,
            'totalPage' => ceil($total / $pageSize),
        ];
    }

    /**
     * default
     *
     * @return Sort
     */
    private function getDefaultSort(): Sort
    {
        return new Sort([
            'attributes' => [
                'create_at' => Sort::generateSortAttribute('create_at', SORT_DESC),
            ],
            'sort'       => '-create_at',
        ]);
    }

    /**
     * order by
     *
     * @param Builder   $query
     * @param Sort|null $sort
     */
    public function orderBy(Builder $query, ?Sort $sort = null): void
    {
        $sort = $sort ?? $this->getDefaultSort();
        foreach ($sort->getOrders() as $column => $direction) {
            $query->orderBy($column, $direction);
        }
    }

    /**
     * @param               $data
     * @param callable|null $call
     *
     * @return array
     */
    public function toArray(array $data, ?callable $call = null): array
    {
        $item = [];
        $results = isset($data['list']) ? $data['list'] : $data;
        foreach ($results as $key => $result) {
            $change = is_null($call) ? $result : call_user_func($call, $result);
            if (is_null($change)) {
                unset($item[$key]);
            } else {
                $item[$key] = $change;
            }
        }
        if (isset($data['list'])) {
            $data['list'] = $item;
        } else {
            $data = $item;
        }
        return $data;
    }

    /**
     * 收集数据
     *
     * @param array         $data
     * @param \Closure|null $call
     *
     * @return array
     */
    public function collection(array $data, ?\Closure $call = null): array
    {
        $item = [];
        $results = isset($data['list']) ? $data['list'] : $data;
        foreach ($results as $key => $result) {
            $item[$key] = is_null($call) ? $result : call_user_func($call, $result);
        }
        return $item;
    }

    /**
     * list
     *
     * @param array           $params
     * @param array           $field
     * @param PageEntity|null $pageEntity
     *
     * @return mixed
     */
    public function getList(array $params = [], array $field = ['*'], ?PageEntity $pageEntity = null): array
    {
        throw new MethodNotImplException();
    }

    /**
     * create
     *
     * @param array $params
     *
     * @return mixed
     */

    public function create(array $params): array
    {
        throw new MethodNotImplException();
    }

    /**
     * update
     *
     * @param array $params
     *
     * @return mixed
     */

    public function update(array $params): array
    {
        throw new MethodNotImplException();
    }

    /**
     * detail
     *
     * @param array $params
     * @param array $columns
     *
     * @return mixed
     */
    public function detail(array $params, array $columns = ['*']): array
    {
        throw new MethodNotImplException();
    }

    /**
     * delete
     *
     * @param array $params
     *
     * @return mixed
     */
    public function remove(array $params): int
    {
        throw new MethodNotImplException();
    }

    /**
     * 图片地址
     *
     * @param string $path
     *
     * @return string
     */
    public function imageUrl($path): string
    {
        if (RegularHelper::checkUrl($path)) {
            return $path;
        }
        $imageUrl = CommonHelper::getConfig()->get('image_url');
        if (StringHelper::isEmpty($imageUrl)) {
            $uri = CommonHelper::getRequest()->getUri();
            $imageUrl = $uri->getScheme() . '://' . $uri->getAuthority();
        }
        return $imageUrl . $path;
    }
}
