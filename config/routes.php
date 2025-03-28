<?php

use PixlMint\KanbanPlugin\Controller\BoardController;

return [
    [
        'route' => '/api/board/load',
        'controller' => BoardController::class,
        'function' => 'loadBoard',
    ],
    [
        'route' => '/api/board/create',
        'controller' => BoardController::class,
        'function' => 'createBoard',
    ],
    [
        'route' => '/api/board/list/create',
        'controller' => BoardController::class,
        'function' => 'createList',
    ],
    [
        'route' => '/api/board/list/card/create',
        'controller' => BoardController::class,
        'function' => 'createCard',
    ],
    [
        'route' => '/api/board/move-card',
        'controller' => BoardController::class,
        'function' => 'moveCard',
    ],
    [
        'route' => '/api/board/delete-card',
        'controller' => BoardController::class,
        'function' => 'deleteCard',
    ],
];
