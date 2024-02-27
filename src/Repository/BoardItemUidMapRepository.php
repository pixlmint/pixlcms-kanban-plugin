<?php

namespace PixlMint\KanbanPlugin\Repository;

use Nacho\ORM\AbstractRepository;
use Nacho\ORM\ModelInterface;
use Nacho\ORM\TemporaryModel;
use PixlMint\KanbanPlugin\Model\BoardItemUidMap;

class BoardItemUidMapRepository extends AbstractRepository
{
    private bool $overrideIsDataChanged = false;

    public static function getDataName(): string
    {
        return 'board-item-uid-map';
    }

    protected static function getModel(): string
    {
        return BoardItemUidMap::class;
    }

    /**
     * @param ModelInterface|BoardItemUidMap $newData
     */
    public function set(ModelInterface $newData): void
    {
        $data = $this->getData();
        if (key_exists($newData->getUid(), $data)) {
            $this->overrideIsDataChanged = true;
            return;
        }

        $data[$newData->getUid()] = $newData;
        $this->setData($data);
        $this->overrideIsDataChanged = true;
    }

    public function isDataChanged(): bool
    {
        return $this->overrideIsDataChanged;
    }

    public function getEntryByUid(string $uid): BoardItemUidMap
    {
        if (!key_exists($uid, $this->getData())) {
            $this->set(new BoardItemUidMap($uid, ''));
        }
        $data = $this->getData();

        if (is_array($data[$uid])) {
            $data[$uid] = $this->myInitialiseObject($uid);
            $this->setData($data);
        }

        return $this->getData()[$uid];
    }

    private function myInitialiseObject(string $uid): ModelInterface
    {
        $dt = $this->getData()[$uid];
        $dt['uid'] = $uid;
        return BoardItemUidMap::init(new TemporaryModel($dt), 0);
    }
}