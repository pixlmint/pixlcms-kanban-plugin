<?php

namespace PixlMint\KanbanPlugin\Model;

use Nacho\Models\PicoPage;

class Board extends AbstractContainerBoardItem
{
    public static function init(PicoPage $page): Board
    {
        $lists = [];
        if (is_array($page->children)) {
            foreach ($page->meta->board['lists'] as $listId) {
                foreach ($page->children as $list) {
                    if ($listId === $list->id) {
                        $lists[] = CardList::init($list);
                    }
                }
            }
        }
        $board = new Board($page);
        $board->updateList($lists);

        return $board;
    }

    public function serialize(): array
    {
        return $this->getPage()->toArray();
    }

    public static function createNew(PicoPage $page): self
    {
        $board = new Board($page);
        $page->meta->board = ['lists' => []];
        $page->meta->kind = 'board';

        return $board;
    }

    public function getLists(): array
    {
        return $this->getItems();
    }

    protected function updateMeta(): void
    {
        $page = $this->getPage();
        if (!key_exists('board', $page->meta->toArray())) {
            $page->meta->board = [];
        }
        if (!key_exists('lists', $page->meta->board)) {
            $page->meta->board['lists'] = [];
        }
        foreach ($this->getLists() as $list) {
            $b = $page->meta->getAdditionalValues()->get('board');
            if (is_null($b['lists'])) {
                $b['lists'] = [];
            }
            if (!in_array($list->id, $b['lists'])) {
                $b['lists'][] = $list->id;
            }
            $page->meta->getAdditionalValues()->set('board', $b);
        }
    }
}