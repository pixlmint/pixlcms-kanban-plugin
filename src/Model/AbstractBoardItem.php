<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

abstract class AbstractBoardItem
{
    private PicoPage $page;

    public abstract static function init(PicoPage $page): self;

    public abstract static function createNew(PicoPage $page): self;

    public abstract function serialize(): array;

    public function __construct(PicoPage $page)
    {
        $this->page = $page;
    }

    public function __get(string $name)
    {
        return $this->getPage()->$name;
    }

    protected function getPage(): PicoPage
    {
        return $this->page;
    }
}