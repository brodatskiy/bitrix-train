<?php

use Academy\Events\ParticipantTable;
use Academy\Events\RegistrationTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Text\HtmlFilter;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$APPLICATION->SetTitle('Проверка заявок');

Loader::requireModule('academy.events');

ParticipantTable::add(['REGISTRATION_ID' => 1, 'NAME' => 'Игорь Белов']);

$registration = RegistrationTable::getByPrimary(1, ['select' => ['*', 'PARTICIPANTS']])->fetchObject();
foreach ($registration->getParticipants() as $participant)
{
    echo HtmlFilter::encode($participant->getName()) . '<br>';
}
?>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
?>