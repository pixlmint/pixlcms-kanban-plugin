<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

class Card extends AbstractBoardItem
{
    public static function init(PicoPage $page): self
    {
        return new Card($page);
    }

    public function serialize(): array
    {
        return (array) $this->getPage();
    }

    public static function createNew(PicoPage $page): self
    {
        return self::init($page);
    }
}