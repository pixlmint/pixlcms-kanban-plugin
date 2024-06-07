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
        return $this->getPage()->toArray();
    }

    public static function createNew(PicoPage $page): self
    {
        $page->meta->kind = 'card';
        $card = new Card($page);

        return $card;
    }
}