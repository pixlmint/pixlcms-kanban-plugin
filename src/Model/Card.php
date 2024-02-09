<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

class Card extends AbstractBoardItem
{
    public static function init(PicoPage $page): AbstractBoardItem
    {
        return new Card($page);
    }
}