<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обучение сотрудников");
?>Text here....<?$APPLICATION->IncludeComponent(
	"bitrix:learning.course.list", 
	".default", 
	[
		"CHECK_PERMISSIONS" => "Y",
		"COURSES_PER_PAGE" => "20",
		"COURSE_DETAIL_TEMPLATE" => "/edu/course.php?COURSE_ID=#COURSE_ID#&INDEX=Y",
		"SET_TITLE" => "Y",
		"SORBY" => "SORT",
		"SORORDER" => "ASC",
		"COMPONENT_TEMPLATE" => ".default"
	],
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>