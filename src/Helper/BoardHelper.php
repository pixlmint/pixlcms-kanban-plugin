<?php

namespace PixlMint\KanbanPlugin\Helper;

use Nacho\Contracts\PageManagerInterface;
use Nacho\Helpers\PageManager;
use Nacho\Models\PicoPage;
use PixlMint\KanbanPlugin\Model\AbstractBoardItem;
use PixlMint\KanbanPlugin\Model\AbstractContainerBoardItem;
use PixlMint\KanbanPlugin\Model\Board;
use PixlMint\KanbanPlugin\Model\Card;
use PixlMint\KanbanPlugin\Model\CardList;

class BoardHelper
{
    private PageManagerInterface $pageManager;

    public function __construct(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    public function loadBoard(string $boardId): ?Board
    {
        $originalIncludePageTree = PageManager::$INCLUDE_PAGE_TREE;
        PageManager::$INCLUDE_PAGE_TREE = true;

        $boardPage = $this->pageManager->getPage($boardId);
        if (!$boardPage) {
            return null;
        }
        $board = Board::init($boardPage);

        PageManager::$INCLUDE_PAGE_TREE = $originalIncludePageTree;

        return $board;
    }

    public function loadList(string $listId): ?CardList
    {
        $originalIncludePageTree = PageManager::$INCLUDE_PAGE_TREE;
        PageManager::$INCLUDE_PAGE_TREE = true;

        $listPage = $this->pageManager->getPage($listId);
        if (!$listPage) {
            return null;
        }
        $list = CardList::init($listPage);

        PageManager::$INCLUDE_PAGE_TREE = $originalIncludePageTree;

        return $list;
    }

    public function createBoard(PicoPage $parentPage, string $boardName): Board
    {
        # TODO: If there's already a page with the same name, don't allow creating a board there

        $boardPage = $this->pageManager->create($parentPage->id, $boardName, true);
        $board = Board::createNew($boardPage);
        $this->storeChanges($board);

        return $board;
    }

    public function createList(Board $board, string $listName): CardList
    {
        $listPage = $this->pageManager->create($board->id, $listName, true);
        $list = CardList::createNew($listPage);
        $board->insert($list);
        $this->storeChanges($board);

        return $list;
    }

    public function createCard(CardList $list, string $cardName): Card
    {
        $cardPage = $this->pageManager->create($list->id, $cardName);
        $card = Card::init($cardPage);
        $list->insert($card);
        $this->storeChanges($list);

        return $card;
    }

    private function storeChanges(AbstractBoardItem $boardItem)
    {
        if ($boardItem instanceof AbstractContainerBoardItem) {
            foreach ($boardItem->getItems() as $childItem) {
                $this->storeChanges($childItem);
            }
        }

        $pageId = $boardItem->id;
        $content = $boardItem->raw_content ?? "";
        $meta = (array) $boardItem->meta;
        $this->pageManager->editPage($pageId, $content, $meta);
    }
}