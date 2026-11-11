<?php

namespace Academy\Events;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;

class RegistrationMasterclassTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'academy_events_registration_masterclass';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('REGISTRATION_ID'))
                ->configurePrimary(),

            (new IntegerField('MASTERCLASS_ID'))
                ->configurePrimary(),
        ];
    }
}