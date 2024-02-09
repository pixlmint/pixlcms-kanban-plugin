<?php

use PixlMint\KanbanPlugin\Controller\BoardController;

return [
    [
        'route' => '/api/board/load',
        'controller' => BoardController::class,
        'function' => 'loadBoard',
    ],
];