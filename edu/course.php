<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Курс");
?>Text here....<?$APPLICATION->IncludeComponent(
	"bitrix:learning.course",
	"",
	Array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHECK_PERMISSIONS" => "Y",
		"COURSE_ID" => $_REQUEST["COURSE_ID"],
		"PAGE_NUMBER_VARIABLE" => "PAGE",
		"PAGE_WINDOW" => "10",
		"PATH_TO_USER_PROFILE" => "/company/personal/user/#USER_ID#/",
		"SEF_MODE" => "N",
		"SET_TITLE" => "Y",
		"SHOW_TIME_LIMIT" => "Y",
		"TESTS_PER_PAGE" => "20",
		"VARIABLE_ALIASES" => Array("CHAPTER_ID"=>"CHAPTER_ID","COURSE_ID"=>"COURSE_ID","FOR_TEST_ID"=>"FOR_TEST_ID","GRADEBOOK"=>"GRADEBOOK","INDEX"=>"INDEX","LESSON_ID"=>"LESSON_ID","SELF_TEST_ID"=>"SELF_TEST_ID","TEST_ID"=>"TEST_ID","TEST_LIST"=>"TEST_LIST","TYPE"=>"TYPE")
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>