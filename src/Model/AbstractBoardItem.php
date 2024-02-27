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

    public function getUid(): ?string
    {
        if (!key_exists('uid', (array)$this->page->meta) || !$this->page->meta->uid) {
            $this->generateUid();
        }

        return $this->page->meta->uid;
    }

    protected function getPage(): PicoPage
    {
        return $this->page;
    }

    private function generateUid(): void
    {
        $this->page->meta->uid = uniqid();
    }
}