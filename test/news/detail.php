<?php
use Bitrix\Main\Context;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$request = Context::getCurrent()->getRequest();

$APPLICATION->IncludeComponent(
    'bitrix:news.detail',
    '',
    [
        'IBLOCK_TYPE' => 'news',
        'IBLOCK_ID' => '1',
        'ELEMENT_CODE' => $request->getQuery('CODE'),
        'PROPERTY_CODE' => ['FORMAT', 'EVENT_DATE'],
        'IBLOCK_URL' => '/test/news/',
        'SET_TITLE' => 'Y',
        'SET_BROWSER_TITLE' => 'N',
        'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
        'ADD_ELEMENT_CHAIN' => 'Y',
        'SET_STATUS_404' => 'Y',
        'MESSAGE_404' => 'Новость не найдена',
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => '3600',
        'CACHE_GROUPS' => 'Y',
    ]
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
