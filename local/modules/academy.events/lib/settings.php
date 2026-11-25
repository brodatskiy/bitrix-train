<?php

namespace Academy\Events;

use Bitrix\Main\Config\Option;

class Settings
{
    public const MODULE_ID = 'academy.events';

    public static function getOrganizerEmail(): string
    {
        return (string)Option::get(self::MODULE_ID, 'organizer_email');
    }
}