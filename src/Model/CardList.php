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
        $list = $this->getPage()->meta->getAdditionalValues()->get('list');
        $children = $list['cards'];
        if (!is_array($children) || !in_array($cardUid, $children)) {
            return;
        }

        $index = array_search($cardUid, $children);

        array_splice($children, $index, 1);
        $list['cards'] = $children;
        $this->getPage()->meta->getAdditionalValues()->set('list', $list);
    }

    public static function createNew(PicoPage $page): self
    {
        $list = new CardList($page);
        $page->meta->getAdditionalValues()->set('list', ['cards' => []]);
        $page->meta->getAdditionalValues()->set('kind', 'list');

        return $list;
    }

    protected function updateMeta(): void
    {
        $page = $this->getPage();
        $cards = array_map(function ($c) {
            return $c->id;
        }, $this->getItems());
        $page->meta->getAdditionalValues()->set('list', ['cards' => $cards]);
    }
}