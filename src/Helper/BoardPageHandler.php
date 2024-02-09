<?php

namespace PixlMint\KanbanPlugin\Helper;

use Nacho\Contracts\PageHandler;
use Nacho\Models\PicoPage;

class BoardPageHandler implements PageHandler
{
    public function setPage(PicoPage $page): void
    {
        // TODO: Implement setPage() method.
    }

    public function renderPage(): string
    {
        // TODO: Implement renderPage() method.
    }

    public function handleUpdate(string $url, string $newContent, array $newMeta): PicoPage
    {
        // TODO: Implement handleUpdate() method.
    }

    public function handleDelete(): void
    {
        // TODO: Implement handleDelete() method.
    }
}