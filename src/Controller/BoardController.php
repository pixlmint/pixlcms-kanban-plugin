<?php

namespace PixlMint\KanbanPlugin\Controller;

use Nacho\Contracts\RequestInterface;
use Nacho\Contracts\Response;
use Nacho\Controllers\AbstractController;
use PixlMint\KanbanPlugin\Helper\BoardHelper;

class BoardController extends AbstractController
{
    public function loadBoard(BoardHelper $boardHelper, RequestInterface $request): Response
    {
        if (!key_exists('board', $request->getBody())) {
            return $this->json(['message' => 'No Board ID defined'], 400);
        }
    }
}