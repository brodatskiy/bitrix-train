<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новости");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"",
        [
                'IBLOCK_TYPE' => 'news',
                'IBLOCK_ID' => '1',
                'NEWS_COUNT' => '10',
                'SORT_BY1' => 'PROPERTY_EVENT_DATE',
                'SORT_ORDER1' => 'ASC',
				"PARENT_SECTION" => $_GET['SECTION_ID'] ?: '',
                'PROPERTY_CODE' => ['FORMAT', 'EVENT_DATE'],
                'DETAIL_URL' => '/test/news/detail.php?CODE=#ELEMENT_CODE#',
                'SET_TITLE' => 'N',
                'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
                'DISPLAY_BOTTOM_PAGER' => 'Y',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '3600',
                'CACHE_GROUPS' => 'Y',
        ]
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>