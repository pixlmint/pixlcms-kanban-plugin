<?php

namespace PixlMint\KanbanPlugin\Helper;

use Nacho\Contracts\PageManagerInterface;
use Nacho\Helpers\PageManager;

class BoardHelper
{
    private PageManagerInterface $pageManager;

    public function __construct(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    public function loadBoard(string $boardId)
    {
        $originalIncludePageTree = PageManager::$INCLUDE_PAGE_TREE;
        PageManager::$INCLUDE_PAGE_TREE = true;

        $boardPage = $this->pageManager->getPage($boardId);

        PageManager::$INCLUDE_PAGE_TREE = $originalIncludePageTree;
    }
}