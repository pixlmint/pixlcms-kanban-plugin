<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Contracts\ArrayableInterface;
use Nacho\ORM\AbstractModel;
use Nacho\ORM\ModelInterface;
use Nacho\ORM\TemporaryModel;

class BoardItemUidMap extends AbstractModel implements ArrayableInterface, ModelInterface
{
    private string $uid;
    private string $boardItem;

    public function __construct(string $uid, string $boardItem)
    {
        $this->uid = $uid;
        $this->boardItem = $boardItem;
    }

    public static function init(TemporaryModel $data, int $id): ModelInterface
    {
        return new BoardItemUidMap($data->get('uid'), $data->get('boardItem'));
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string The Pico ID of the entry
     */
    public function getBoardItem(): string
    {
        return $this->boardItem;
    }

    /**
     * @param string $boardItem The Pico ID of the entry
     */
    public function setBoardItem(string $boardItem): void
    {
        $this->boardItem = $boardItem;
    }

    public function toArray(): array
    {
        return [
            'boardItem' => $this->boardItem,
        ];
    }
}