<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

class Board extends AbstractContainerBoardItem
{
    public static function init(PicoPage $page): Board
    {
        $lists = [];
        foreach ($page->children as $list) {
            $lists[] = CardList::init($list);
        }
        $board = new Board($page);
        $board->updateList($lists);

        return $board;
    }

    public function __get(string $name)
    {
        return $this->getPage()->$name;
    }

    public function getLists(): array
    {
        return $this->getItems();
    }
}