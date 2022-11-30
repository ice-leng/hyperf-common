<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Helpers;

use Hyperf\Redis\Pool\PoolFactory;
use Hyperf\Utils\Coordinator\Constants;
use Hyperf\Utils\Coordinator\CoordinatorManager;
use Hyperf\Utils\Coroutine;
use Lengbin\Common\AbstractRedisLock;

class RedisLockHelper extends AbstractRedisLock
{
    /**
     * @var PoolFactory
     */
    private $poolFactory;

    public function __construct(PoolFactory $poolFactory, )
    {
        $this->poolFactory = $poolFactory;
        $this->setRedisPoolName();
    }

    /**
     * 根据Redis链接池配置名称数组生成独立Redis实例.
     * @param array $poolName
     * @return $this
     */
    public function setRedisPoolName(array $poolName = ['default']): static
    {
        if (!empty($poolName)) {
            $instances = $this->getInstances();
            foreach ($poolName as $row) {
                if (!isset($instances[$row])) {
                    $instances[$row] = $this->poolFactory->getPool($row)->get();
                }
            }
            $this->setInstances($instances);
            $quorum = min(count($instances), (count($instances) / 2 + 1));
            $this->setQuorum($quorum);
        }

        return $this;
    }

    /**
     * 如果担心请求保持锁阶段进程出现重启或退出情况
     * @param array $lock
     * @param callable|null $call
     * @return mixed|null
     */
    public function unlockForCoroutine(array $lock, ?callable $call = null)
    {
        //to release lock when server receive exit sign
        Coroutine::create(function () use ($lock) {
            $exited = CoordinatorManager::until(Constants::WORKER_EXIT)->yield($lock['validity']);
            $exited && $this->unlock($lock);
        });
        $result = null;
        if (!is_null($call)) {
            $result = call_user_func($call);
        }
        $this->unlock($lock);
        return $result;
    }
}