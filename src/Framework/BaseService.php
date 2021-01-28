<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\Database\Model\Builder;
use Lengbin\Common\Component\Entity\PageEntity;
use Lengbin\Common\Component\Sort\Sort;
use Lengbin\Helper\Util\RegularHelper;
use Lengbin\Helper\YiiSoft\StringHelper;
use Lengbin\Hyperf\Common\Helper\CommonHelper;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
use Lengbin\Hyperf\Common\Exception\MethodNotImplException;

class BaseService
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

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
            'page'      => $pageEntity->getPage(),
            'pageSize'  => $pageSize,
            'total'     => $total,
        ];
    }

    /**
     * page
     *
     * @param array      $params
     * @param PageEntity $pageEntity
     *
     * @return array
     */
    public function pageByArray(array $params, PageEntity $pageEntity): array
    {
        $total = count($params);
        $pageSize = $pageEntity->getPageSize();
        $offset = ($pageEntity->getPage() - 1) * $pageSize;
        $params = array_values($params);
        $list = array_slice($params, $offset, $pageSize);
        return [
            'list'      => $list,
            'page'      => $pageEntity->getPage(),
            'pageSize'  => $pageSize,
            'total'     => $total,
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
            'attributes'   => [
                'create_at',
            ],
            'defaultOrder' => ['create_at' => SORT_DESC],
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
     * @param array    $data
     * @param callable $call
     *
     * @return array
     */
    public function toArray(array $data, callable $call): array
    {
        $item = [];
        $results = isset($data['list']) ? $data['list'] : $data;
        foreach ($results as $key => $result) {
            $item[$key] = call_user_func($call, $result);
        }
        if (isset($data['list'])) {
            $data['list'] = $item;
        } else {
            $data = $item;
        }
        return $data;
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
     * @param array $field
     *
     * @return mixed
     */
    public function detail(array $params, array $field = ['*']): array
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
    public function imageUrl(string $path): string
    {
        if (RegularHelper::checkUrl($path)) {
            return $path;
        }
        $imageUrl = $this->config->get('image_url');
        if (StringHelper::isEmpty($imageUrl)) {
            $uri = CommonHelper::getRequest()->getUri();
            $imageUrl = $uri->getScheme() . '://' . $uri->getAuthority();
        }
        return $imageUrl . $path;
    }
}
