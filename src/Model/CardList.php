<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

class CardList extends AbstractContainerBoardItem
{

    public static function init(PicoPage $page): AbstractContainerBoardItem
    {
        $cards = [];
        foreach ($page->children as $list) {
            $cards[] = Card::init($list);
        }
        $list = new CardList($page);
        $list->updateList($cards);

        return $list;
    }
}