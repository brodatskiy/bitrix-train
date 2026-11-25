<?php

use Bitrix\Main\EventManager;

EventManager::getInstance()->registerEventHandlerCompatible(
    'iblock',
    'OnAfterIBlockElementAdd',
    'dev.site',
    \Dev\Site\Handlers\Iblock::class,
    'addLog',
);
EventManager::getInstance()->registerEventHandlerCompatible(
    'iblock',
    'OnAfterIBlockElementUpdate',
    'dev.site',
    \Dev\Site\Handlers\Iblock::class,
    'addLog'
);