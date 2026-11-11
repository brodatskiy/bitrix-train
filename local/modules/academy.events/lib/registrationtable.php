<?php

namespace Academy\Events;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\EnumField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Fields\Validators\RangeValidator;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Validation\Validator\EmailValidator;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields\FieldError;
use Bitrix\Main\UserTable;

Loc::loadMessages(__FILE__);

class RegistrationTable extends DataManager
{
    public const STATUS_NEW = 'NEW';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public static function getTableName(): string
    {
        return 'academy_events_registration';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('EVENT_ID'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_EVENT_ID'))
                ->addValidator(new RangeValidator(1)),

            (new IntegerField('USER_ID'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_USER_ID')),

            (new Reference('USER', UserTable::class, Join::on('this.USER_ID', 'ref.ID'))),
            (new OneToMany('PARTICIPANTS', ParticipantTable::class, 'REGISTRATION')),
            (new ManyToMany('MASTERCLASSES', MasterclassTable::class))
                ->configureTableName('academy_events_registration_masterclass'),
            (new StringField('NAME'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_NAME'))
                ->addValidator(new LengthValidator(2, 100)),

            (new StringField('EMAIL'))
                ->configureRequired()
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_EMAIL'))
                ->addValidator(new LengthValidator(null, 255))
                ->addValidator([static::class, 'validateEmail']),

            (new IntegerField('SEATS'))
                ->configureDefaultValue(1)
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_SEATS'))
                ->addValidator(new RangeValidator(1, 5)),

            (new EnumField('STATUS'))
                ->configureValues([self::STATUS_NEW, self::STATUS_CONFIRMED, self::STATUS_CANCELLED])
                ->configureDefaultValue(self::STATUS_NEW)
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_STATUS')),

            (new DatetimeField('CREATED_AT'))
                ->configureDefaultValueNow()
                ->configureTitle(Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_CREATED_AT')),
        ];
    }

    public static function validateEmail($value): bool|string
    {
        $result = (new EmailValidator(strict: true))->validate((string)$value);

        if ($result->isSuccess())
        {
            return true;
        }

        return Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_EMAIL_INVALID');
    }

    public static function onBeforeAdd(Event $event): EventResult
    {
        $result = new EventResult();
        $fields = $event->getParameter('fields');

        $modified = [];
        if (isset($fields['EMAIL']))
        {
            $modified['EMAIL'] = mb_strtolower(trim($fields['EMAIL']));
        }
        if (isset($fields['NAME']))
        {
            $modified['NAME'] = trim($fields['NAME']);
        }
        $result->modifyFields($modified);

        if (isset($fields['STATUS']) && $fields['STATUS'] !== self::STATUS_NEW)
        {
            $result->addError(new FieldError(
                $event->getEntity()->getField('STATUS'),
                Loc::getMessage('ACADEMY_EVENTS_REGISTRATION_STATUS_ON_ADD')
            ));
        }

        return $result;
    }
}