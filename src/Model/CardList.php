<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

class CardList extends AbstractContainerBoardItem
{
    public static function init(PicoPage $page): CardList
    {
        $cards = [];
        if (is_array($page->children)) {
            foreach ($page->children as $list) {
                $cards[] = Card::init($list);
            }
        }
        $list = new CardList($page);
        $list->updateList($cards);

        return $list;
    }

    public function serialize(): array
    {
        return $this->getPage()->toArray();
    }

    public function untrackCard(string $cardUid): void
    {
        $children = $this->getPage()->meta->list['cards'];
        if (!is_array($children) || !in_array($cardUid, $children)) {
            return;
        }

        $index = array_search($cardUid, $children);

        array_splice($this->getPage()->meta->list['cards'], $index, 1);
    }

    public static function createNew(PicoPage $page): self
    {
        $list = new CardList($page);
        $page->meta->list = ['cards' => []];
        $page->meta->kind = 'list';

        return $list;
    }

    protected function updateMeta(): void
    {
        $page = $this->getPage();
        $page->meta->list = ['cards' => []];
        foreach ($this->getItems() as $card) {
            $page->meta->list['cards'][] = $card->id;
        }
    }
}