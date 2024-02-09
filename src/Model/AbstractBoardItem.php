<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

abstract class AbstractBoardItem
{
    private PicoPage $page;

    public abstract static function init(PicoPage $page): AbstractBoardItem;

    public function __construct(PicoPage $page)
    {

    }

    protected function getPage(): PicoPage
    {
        return $this->page;
    }
}