<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arResult
 */
//ddebug($arResult);

$hiddenHtml = '';
$inputsHtml = '';
$textareaHtml = '';

foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
//    ddebug($arQuestion);
    $field = $arQuestion['STRUCTURE'][0];
    $inputName = "form_" . $field['FIELD_TYPE'] . "_" . $field['ID'];

    if ($field['FIELD_TYPE'] == 'hidden') {
        $hiddenHtml .= $arQuestion["HTML_CODE"];
    }

    if ($field['FIELD_TYPE'] == 'text' || $field['FIELD_TYPE'] == 'email') {
        ob_start();
        ?>
        <div class="input contact-form__input">
            <label class="input__label" for="<?= $inputName ?>">
                <div class="input__label-text"><?= $arQuestion["CAPTION"] ?><? if ($arQuestion["REQUIRED"] == "Y"): ?>*<? endif; ?></div>
                <input class="input__input" type="<?= $field['FIELD_TYPE'] ?>"
                       id="<?= $inputName ?>" name="<?= $inputName ?>" value=""
                        <?= ($arQuestion["REQUIRED"] == "Y" ? 'required' : '') ?>>

                <? if (isset($arResult["FORM_ERRORS"][$FIELD_SID])) { ?>
                    <div class="input__notification">
                        <?= htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID]) ?>
                    </div>
                <? } ?>
            </label>
        </div>
        <?php
        $inputsHtml .= ob_get_clean();
    }

    if ($field['FIELD_TYPE'] == 'textarea') {
        ob_start();
        ?>
        <div class="input">
            <label class="input__label" for="<?= $inputName ?>">
                <div class="input__label-text"><?= $arQuestion["CAPTION"] ?><? if ($arQuestion["REQUIRED"] == "Y"): ?><?= $arResult["REQUIRED_SIGN"]; ?><? endif; ?></div>
                <textarea class="input__input" id="<?= $inputName ?>" name="<?= $inputName ?>"></textarea>
                <? if (isset($arResult["FORM_ERRORS"][$FIELD_SID])) { ?>
                    <div class="input__notification">
                        <?= htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID]) ?>
                    </div>
                <? } ?>
            </label>
        </div>
        <?php
        $textareaHtml .= ob_get_clean();
    }
}
?>

<div class="contact-form">
    <? if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y") { ?>
        <div class="contact-form__head">
            <div class="contact-form__head-title"><?= $arResult["FORM_TITLE"] ?></div>
            <div class="contact-form__head-text"><?= $arResult["FORM_DESCRIPTION"] ?></div>
        </div>
    <? } ?>

    <?= str_replace('<form', '<form class="contact-form__form"', $arResult["FORM_HEADER"]) ?>

    <?= $hiddenHtml ?>
    <div class="contact-form__form-inputs">
        <?= $inputsHtml ?>
    </div>
    <div class="contact-form__form-message">
        <?= $textareaHtml ?>
    </div>
    <div class="contact-form__bottom">
        <div class="contact-form__bottom-policy">
            Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что
            ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку персональных
            данных&raquo;.
        </div>

        <button class="form-button contact-form__bottom-button"
                type="submit"
                name="web_form_submit"
                value="Y"
                data-success="Отправлено"
                data-error="Ошибка отправки"
                <?= (intval($arResult["F_RIGHT"]) < 10 ? 'disabled="disabled"' : '') ?>>
            <div class="form-button__title">
                <?= htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]); ?>
            </div>
        </button>

    </div>
    <?= $arResult["FORM_FOOTER"] ?>
</div>

