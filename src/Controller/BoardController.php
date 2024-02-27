<?php

namespace PixlMint\KanbanPlugin\Controller;

use Nacho\Contracts\PageManagerInterface;
use Nacho\Contracts\RequestInterface;
use Nacho\Contracts\Response;
use Nacho\Controllers\AbstractController;
use Nacho\Models\HttpMethod;
use Nacho\Models\HttpResponseCode;
use PixlMint\CMS\Helpers\CustomUserHelper;
use PixlMint\KanbanPlugin\Helper\BoardHelper;

class BoardController extends AbstractController
{
    private BoardHelper $boardHelper;
    private PageManagerInterface $pageManager;

    public function __construct(BoardHelper $boardHelper, PageManagerInterface $pageManager)
    {
        parent::__construct();
        $this->boardHelper = $boardHelper;
        $this->pageManager = $pageManager;
    }

    public function loadBoard(RequestInterface $request): Response
    {
        if (!key_exists('board', $request->getBody())) {
            return $this->json(['message' => 'No Board ID defined'], 400);
        }

        $board = $this->boardHelper->loadBoard($request->getBody()['board']);

        if (!$board) {
            return $this->json(['message' => 'Unable to find board with id '. $request->getBody()['board']], HttpResponseCode::NOT_FOUND);
        }

        return $this->json([
            'board' => $board->serialize(),
        ]);
    }

    public function createBoard(RequestInterface $request): Response
    {
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You are not authenticated'], 401);
        }
        if (strtoupper($request->requestMethod) !== HttpMethod::POST) {
            return $this->json(['message' => 'only post requests allowed'], HttpResponseCode::METHOD_NOT_ALLOWED);
        }
        if (!key_exists('parentPage', $request->getBody()) || !key_exists('name', $request->getBody())) {
            return $this->json(['message' => 'No Parent ID or board name defined'], HttpResponseCode::BAD_REQUEST);
        }

        $parentPageId = $request->getBody()['parentPage'];
        $boardName = $request->getBody()['name'];

        $parentPage = $this->pageManager->getPage($parentPageId);

        if (!$parentPage) {
            return $this->json(['message' => 'Unable to find page with id ' . $parentPageId], HttpResponseCode::NOT_FOUND);
        }

        $board = $this->boardHelper->createBoard($parentPage, $boardName);

        return $this->json([
            'message' => 'Board Successfully created',
            'boardId' => $board->serialize()['id'],
        ]);
    }

    public function createList(RequestInterface $request): Response
    {
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You are not authenticated'], 401);
        }
        if (strtoupper($request->requestMethod) !== HttpMethod::POST) {
            return $this->json(['message' => 'only post requests allowed'], HttpResponseCode::METHOD_NOT_ALLOWED);
        }
        if (!key_exists('boardId', $request->getBody()) || !key_exists('name', $request->getBody())) {
            return $this->json(['message' => 'No boardId or list name defined'], HttpResponseCode::BAD_REQUEST);
        }

        $boardId = $request->getBody()['boardId'];
        $listName = $request->getBody()['name'];

        $board = $this->boardHelper->loadBoard($boardId);

        if (!$board) {
            return $this->json(['message' => 'Unable to find board with id ' . $boardId], HttpResponseCode::NOT_FOUND);
        }

        $list = $this->boardHelper->createList($board, $listName);

        return $this->json([
            'message' => 'List Successfully created',
            'listId' => $list->serialize()['id'],
        ]);
    }

    public function createCard(RequestInterface $request): Response
    {
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You are not authenticated'], 401);
        }
        if (strtoupper($request->requestMethod) !== HttpMethod::POST) {
            return $this->json(['message' => 'only post requests allowed'], HttpResponseCode::METHOD_NOT_ALLOWED);
        }
        if (!key_exists('listId', $request->getBody()) || !key_exists('name', $request->getBody())) {
            return $this->json(['message' => 'No listId or card name defined'], HttpResponseCode::BAD_REQUEST);
        }

        $listId = $request->getBody()['listId'];
        $cardName = $request->getBody()['name'];

        $list = $this->boardHelper->loadList($listId);

        if (!$list) {
            return $this->json(['message' => 'Unable to find list with id ' . $listId], HttpResponseCode::NOT_FOUND);
        }

        $card = $this->boardHelper->createCard($list, $cardName);
        $list = $this->boardHelper->loadList($listId);

        return $this->json([
            'message' => 'Card Successfully created',
            'cardId' => $card->serialize()['id'],
            'list' => $list->serialize(),
        ]);
    }

    public function moveCard(RequestInterface $request): Response
    {
        if (!$this->isGranted(CustomUserHelper::ROLE_EDITOR)) {
            return $this->json(['message' => 'You are not authenticated'], 401);
        }
        if (strtoupper($request->requestMethod) !== HttpMethod::PUT) {
            return $this->json(['message' => 'only put requests allowed'], HttpResponseCode::METHOD_NOT_ALLOWED);
        }
        if (!key_exists('targetListUid', $request->getBody()) || !key_exists('cardUid', $request->getBody())) {
            return $this->json(['message' => 'No listId or card name defined'], HttpResponseCode::BAD_REQUEST);
        }

        $targetListUid = $request->getBody()['targetListUid'];
        $cardUid = $request->getBody()['cardUid'];

        $this->boardHelper->moveCard($targetListUid, $cardUid);

        return $this->json(['message' => 'Success']);
    }
}