<?php

namespace Academy\Events;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Query\Join;

Loc::loadMessages(__FILE__);

class ParticipantTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'academy_events_participant';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('REGISTRATION_ID'))
                ->configureRequired(),

            (new StringField('NAME'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_PARTICIPANT_NAME'))
                ->addValidator(new LengthValidator(2, 100)),

            (new Reference('REGISTRATION', RegistrationTable::class, Join::on('this.REGISTRATION_ID', 'ref.ID'))),
        ];
    }
}