<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\HtmlFilter;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
    die();
}

if (!CurrentUser::get()->isAdmin())
{
    return;
}

Loc::loadMessages(__FILE__);

$moduleId = 'academy.events';
$request = Context::getCurrent()->getRequest();
$error = '';

if ($request->isPost() && check_bitrix_sessid())
{
    $email = trim((string)$request->getPost('organizer_email'));

    if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false)
    {
        $error = Loc::getMessage('ACADEMY_EVENTS_OPTIONS_EMAIL_ERROR');
    }
    else
    {
        Option::set($moduleId, 'organizer_email', $email);
    }
}

$formAction = $request->getRequestedPage() . '?mid=' . urlencode($moduleId) . '&lang=' . LANGUAGE_ID;
?>
<?php if ($error !== ''): ?>
    <p class="academy-events-options__error"><?= HtmlFilter::encode($error) ?></p>
<?php endif; ?>
<form method="post" action="<?= HtmlFilter::encode($formAction) ?>">
    <?= bitrix_sessid_post() ?>
    <p>
        <label for="organizer_email"><?= Loc::getMessage('ACADEMY_EVENTS_OPTIONS_EMAIL') ?></label>
        <input
            type="text"
            id="organizer_email"
            name="organizer_email"
            value="<?= HtmlFilter::encode(Option::get($moduleId, 'organizer_email')) ?>"
        >
    </p>
    <input type="submit" value="<?= Loc::getMessage('ACADEMY_EVENTS_OPTIONS_SAVE') ?>">
</form>