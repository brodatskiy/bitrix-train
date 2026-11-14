<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="article-card">
    <div class="article-card__title">
        <? if ((!isset($arParams["DISPLAY_NAME"]) || $arParams["DISPLAY_NAME"] != "N") && $arResult["NAME"]) { ?>
            <h3><?= $arResult["NAME"] ?></h3>
        <? } ?>
    </div>
    <div class="article-card__date">
        <? if ((!isset($arParams["DISPLAY_DATE"]) || $arParams["DISPLAY_DATE"] != "N") && $arResult["DISPLAY_ACTIVE_FROM"]) { ?>
            <span class="news-date-time"><?= $arResult["DISPLAY_ACTIVE_FROM"] ?></span>
        <? } ?>
    </div>
    <div class="article-card__content">
        <div class="article-card__image sticky">
            <? if ((!isset($arParams["DISPLAY_PICTURE"]) || $arParams["DISPLAY_PICTURE"] != "N") && is_array($arResult["DETAIL_PICTURE"])) { ?>
                <img
                        data-object-fit="cover"
                        src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>"
                        width="<?= $arResult["DETAIL_PICTURE"]["WIDTH"] ?>"
                        height="<?= $arResult["DETAIL_PICTURE"]["HEIGHT"] ?>"
                        alt="<?= $arResult["DETAIL_PICTURE"]["ALT"] ?>"
                        title="<?= $arResult["DETAIL_PICTURE"]["TITLE"] ?>"
                />
            <? } ?>
        </div>
        <div class="article-card__text">
            <div class="block-content" data-anim="anim-3">
                <? if ($arResult["DETAIL_TEXT"] <> '') { ?>
                    <?= $arResult["DETAIL_TEXT"]; ?>
                <? } else { ?>
                    <? echo $arResult["PREVIEW_TEXT"]; ?>
                <? } ?>
            </div>
            <a class="article-card__button" href="<?echo $arResult["LIST_PAGE_URL"];?>"><?=GetMessage("NEWS_BACK_TEXT")?></a>
    </div>
</div>