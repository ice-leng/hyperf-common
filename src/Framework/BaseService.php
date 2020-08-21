<?php

namespace Lengbin\Hyperf\Common\Framework;

use Hyperf\Database\Model\Builder;
use Hyperf\Utils\Str;
use Lengbin\Helper\Util\RegularHelper;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;

class BaseService
{

    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param int|null $ttl 过期时间， 如果为 0 表示 不设置过期时间， null 表示redis配置的过期时间
     *
     * @return int|null
     */
    public function getRedisTtl(?int $ttl = null): ?int
    {
        if (is_null($ttl)) {
            $config = Config::getInstance()->getConf("REDIS");
            $ttl = ArrayHelper::get($config, 'ttl', '3600');
            [$min, $max] = ArrayHelper::get($config, 'random', [1, 60]);
            return $ttl + rand($min, $max);
        }

        if ($ttl === 0) {
            return null;
        }

        return $ttl;
    }

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
        $list = $query->forPage($pageEntity->getPage(), $pageSize)->get();
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
     * 获得 di
     *
     * @param string $name
     *
     * @return callable|string|null
     * @throws Throwable
     */
    public function getDi(string $name)
    {
        return Di::getInstance()->get($name);
    }

    /**
     * list
     *
     * @param array           $columns
     * @param array           $condition
     * @param PageEntity|null $pageEntity
     *
     * @return mixed
     */
    public function getList(array $condition = [], array $columns = ['*'], ?PageEntity $pageEntity = null): array
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
     * @param array $condition
     * @param array $columns
     *
     * @return mixed
     */
    public function detail(array $condition, array $columns = ['*']): array
    {
        throw new MethodNotImplException();
    }

    /**
     * delete
     *
     * @param array $condition
     *
     * @return mixed
     */
    public function remove(array $condition): int
    {
        throw new MethodNotImplException();
    }

    /**
     * @param string      $key
     * @param string|null $prefix
     *
     * @return string
     */
    private function getCacheKey(string $key, ?string $prefix = null): string
    {
        $mc = Config::getInstance()->getConf("SERVER_NAME");
        $service = Str::snake(Str::pluralStudly(class_basename(get_called_class())));
        if (!is_null($prefix)) {
            $key = $prefix . ':' . $key;
        }
        return sprintf('mc:%s:s:%s:%s', $mc, $service, $key);
    }

    /**
     * 通过key获得缓存
     *
     * @param string        $key
     * @param \Closure|null $call
     * @param string|null   $prefix
     * @param int|null      $ttl 过期时间， 如果为 0 表示 不设置过期时间， null 表示redis配置的过期时间
     *
     * @return mixed
     * @throws Throwable
     */
    public function getCacheByKey(string $key, ?\Closure $call = null, ?string $prefix = null, ?int $ttl = null)
    {
        $redis = $this->getRedis();
        $k = $this->getCacheKey($key, $prefix);
        $data = $redis->get($k);
        if (!is_null($data)) {
            return $data;
        }
        if (!is_null($call)) {
            $ttl = $this->getRedisTtl($ttl);
            $data = call_user_func($call, $key);
        }

        if (StringHelper::isEmpty($data)) {
            $data = [];
            $ttl = 60;
        }
        $data = is_object($data) ? $data->toArray() : $data;
        $redis->set($k, json_encode($data), $ttl);
        return $data;
    }

    /**
     * @param array         $keys
     * @param \Closure|null $call
     * @param string|null   $prefix
     * @param int|null      $ttl
     *
     * @return array
     * @throws Throwable
     */
    public function getCacheByKeys(array $keys, ?\Closure $call = null, ?string $prefix = null, ?int $ttl = null): array
    {
        if (count($keys) === 0) {
            return [];
        }

        $redis = $this->getRedis();

        $ks = [];
        foreach ($keys as $key) {
            $ks[] = $this->getCacheKey($key, $prefix);
        }
        /* @var array $data */
        $data = $redis->mGet($ks);

        $output = [];
        $missed = [];
        foreach ($data as $index => $item) {
            if (is_null($item)) {
                $key = $keys[$index];
                $missed[$index] = $key;
                continue;
            }
            $output[$index] = json_decode($item, true);
        }
        $models = [];
        if (!is_null($call)) {
            $models = call_user_func($call, $missed);
            $models = $models ?? [];
        }

        foreach ($models as $index => $model) {
            $model = is_object($model) ? $model->toArray() : $model;
            $output[$index] = $model;
            $targetIntersectKey = $this->getCacheKey($keys[$index], $prefix);
            $redis->set($targetIntersectKey, json_encode($model), $ttl);
        }
        return $output;
    }

    /**
     * @param             $keys
     * @param string|null $prefix
     *
     * @return bool|string
     * @throws Throwable
     */
    public function removeCacheByKey($keys, ?string $prefix = null): bool
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $ks = [];
        foreach ($keys as $key) {
            $ks[] = $this->getCacheKey($key, $prefix);
        }
        return $this->getRedis()->del(...$ks);
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
        // todo
        return RegularHelper::checkUrl($path) ? $path : $path;
    }
}
