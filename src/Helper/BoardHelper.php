<?php

namespace PixlMint\KanbanPlugin\Helper;

use Nacho\Contracts\PageManagerInterface;
use Nacho\Helpers\PageManager;
use Nacho\Models\PicoPage;
use PixlMint\CMS\Helpers\CMSConfiguration;
use PixlMint\KanbanPlugin\Model\AbstractBoardItem;
use PixlMint\KanbanPlugin\Model\AbstractContainerBoardItem;
use PixlMint\KanbanPlugin\Model\Board;
use PixlMint\KanbanPlugin\Model\Card;
use PixlMint\KanbanPlugin\Model\CardList;
use PixlMint\KanbanPlugin\Repository\BoardItemUidMapRepository;

class BoardHelper
{
    private PageManagerInterface $pageManager;
    private BoardItemUidMapRepository $boardItemUidMapRepository;

    public function __construct(PageManagerInterface $pageManager, BoardItemUidMapRepository $boardItemUidMapRepository)
    {
        $this->pageManager = $pageManager;
        $this->boardItemUidMapRepository = $boardItemUidMapRepository;
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

        $this->pageManager->readPages();
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
        $card = Card::createNew($cardPage);
        $list->insert($card);
        $this->storeChanges($list);

        return $card;
    }

    public function moveCard(string $targetListUid, string $cardUid)
    {
        /** @var AbstractContainerBoardItem $list */
        $list = $this->getBoardItemFromUid($targetListUid);
        $card = $this->getBoardItemFromUid($cardUid);
        $tmpOriginalParent = $this->pageManager->getPage($card->meta->parentPath);
        $originalParent = CardList::init($tmpOriginalParent);
        $originalParent->untrackCard($card->id);
        $this->storeChanges($originalParent);
        $success = $this->pageManager->move($card->id, $list->id);
        $mapItem = $this->boardItemUidMapRepository->getEntryByUid($card->getUid());

        $splMyId = explode('/', $card->id);
        $myId = array_pop($splMyId);
        $newId = $list->id . '/' . $myId;

        $mapItem->setBoardItem($newId);
        if (!$success) {
            throw new \Exception("An error occurred while attempting to move $cardUid into $targetListUid");
        }
        PageManager::$INCLUDE_PAGE_TREE = true;
        $this->pageManager->readPages();
        $list = $this->getBoardItemFromUid($targetListUid);
        $card = $this->getBoardItemFromUid($cardUid);
        $list->insert($card);
        PageManager::$INCLUDE_PAGE_TREE = false;
    }

    public function deleteCard(string $cardUid)
    {
        $card = $this->getBoardItemFromUid($cardUid);

        $tmpParent = $this->pageManager->getPage($card->meta->parentPath);
        $parent = CardList::init($tmpParent);
        $parent->untrackCard($card->id);

        $this->pageManager->delete($card->id);
        PageManager::$INCLUDE_PAGE_TREE = true;
        $this->pageManager->readPages();
        PageManager::$INCLUDE_PAGE_TREE = false;
    }

    private function getBoardItemFromUid(string $uid): AbstractBoardItem
    {
        $item = $this->boardItemUidMapRepository->getEntryByUid($uid);
        $itemId = $item->getBoardItem();
        $page = $this->pageManager->getPage($itemId);
        switch ($page->meta->kind) {
            case 'board':
                return Board::init($page);
            case 'list':
                return CardList::init($page);
            case 'card':
                return Card::init($page);
        }

        throw new \Exception('No matching item found for page with id ' . $page->id);
    }

    private function storeChanges(AbstractBoardItem $boardItem): void
    {
        if ($boardItem instanceof AbstractContainerBoardItem) {
            foreach ($boardItem->getItems() as $childItem) {
                $this->storeChanges($childItem);
            }
        }

        $uidMapItem = $this->boardItemUidMapRepository->getEntryByUid($boardItem->getUid());
        $uidMapItem->setBoardItem($boardItem->id);
        $this->boardItemUidMapRepository->set($uidMapItem);

        $pageId = $boardItem->id;
        $content = $boardItem->raw_content ?? "";
        $meta = $boardItem->meta->toArray();
        $this->pageManager->editPage($pageId, $content, $meta);
    }
}
