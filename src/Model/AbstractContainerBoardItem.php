<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

abstract class AbstractContainerBoardItem extends AbstractBoardItem
{
    /** @var array|CardList[]|Card[] */
    private array $childItems = [];

    public abstract static function init(PicoPage $page): AbstractContainerBoardItem;

    protected abstract function updateMeta(): void;

    public function insert(AbstractBoardItem $child, ?int $position = null): int
    {
        if (is_null($position)) {
            $position = count($this->childItems);
        }
        array_splice($this->childItems, $position, 0, [$child]);

        $this->updateMeta();

        return $position;
    }

    public function getItems(): array
    {
        return $this->childItems;
    }

    public function updateList(array $childItems)
    {
        $this->childItems = $childItems;
    }
}