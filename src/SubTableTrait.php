<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common;

use Lengbin\Hyperf\Common\Helpers\RedisLockHelper;
use Lengbin\SubDatabase\Enums\SubTableMode;
use Lengbin\SubDatabase\SubTable\AbstractSubTable;
use Lengbin\SubDatabase\SubTable\Mode\SubTableHash;
use Lengbin\SubDatabase\SubTable\SubTableFactory;

trait SubTableTrait
{
    private ?SubTableFactory $_subTableFactory = null;
    private ?AbstractSubTable $_subTableDate = null;
    private ?SubTableHash $_subTableHash = null;
    private ?RedisLockHelper $_redisLock = null;
    private ?string $_tableName = null;

    private array $_hashTable = [];

    private function _getSubTableFactory(): SubTableFactory
    {
        if (!$this->_subTableFactory) {
            $this->_subTableFactory = make(SubTableFactory::class);
        }
        return $this->_subTableFactory;
    }

    private function _getTable(): string
    {
        if (!$this->_tableName) {
            $this->_tableName = $this->getModel()->getTable();
        }
        return $this->_tableName;
    }

    private function _getSubTableHash(): SubTableHash
    {
        if (!$this->_subTableHash) {
            $this->_subTableHash = $this->_getSubTableFactory()
                ->make(SubTableMode::HASH())
                ->setSlices($this->getSubTableSlices())
                ->setTable($this->_getTable());
        }
        return $this->_subTableHash;
    }

    private function _getSubTableDate(): AbstractSubTable
    {
        if (!$this->_subTableDate) {
            $this->_subTableDate = $this->_getSubTableFactory()
                ->make(SubTableMode::DATE())
                ->setTable($this->_getTable());
        }
        return $this->_subTableDate;
    }

    private function _getRedisLock(): RedisLockHelper
    {
        if (!$this->_redisLock) {
            $this->_redisLock = make(RedisLockHelper::class);
        }
        return $this->_redisLock;
    }

    public function getSubTableSlices(): int
    {
        return 16;
    }

    public function getSubTableHash(string $key): string
    {
        $subTableHash = $this->_getSubTableHash()->setKey($key);
        $subTable = $subTableHash->getSubTable();
        if (!in_array($subTable, $this->_hashTable)) {
            $this->_createSubTable($subTableHash, $subTable);
            $this->_hashTable[] = $subTable;
        }
        return $subTable;
    }

    // 毫秒
    public function getSubTableTimestamp(): int
    {
        return 1000;
    }

    public function getSubTableDate(string $key): string
    {
        $subTableData = $this->_getSubTableDate()->setKey($key);
        $subTable = $subTableData->getSubTable();
        $lock = $this->_getRedisLock()->lock($subTable, $this->getSubTableTimestamp());
        if ($lock) {
            $this->_createSubTable($subTableData, $subTable);
        }
        return $subTable;
    }

    private function _createSubTable(AbstractSubTable $subTable, string $table): bool
    {
        $connection = $this->getModel()->getConnection();
        $pdo = $connection->getPdo();
        $subTable->setTablePrefix($connection->getTablePrefix());
        return $subTable->createSubTable($pdo, $connection->getDatabaseName(), $table);
    }

    public function getSubTable(string $key): string
    {
        $subTableData = $this->_getSubTableHash()->setKey($key);
        $subTable = $subTableData->getSubTable();
        if (!in_array($subTable, $this->_hashTable)) {
            $this->_createSubTable($subTableData, $subTable);
            $this->_hashTable[] = $subTable;
        }
        return $subTable;
    }
}