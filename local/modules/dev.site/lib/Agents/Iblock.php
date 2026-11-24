<?php

namespace Dev\Site\Agents;


use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\Elements\ElementLogTable;

class Iblock
{
    public const KEEP_COUNT = 10;

    public static function clearOldLogs(): string
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
        }

        $keepCount = 10;


        $rsLastLogElements = ElementLogTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'DESC'],
            'limit' => self::KEEP_COUNT,
        ])->fetchAll();

        $keepIds = array_column($rsLastLogElements, 'ID');

        $rsLogElementToDelete = ElementLogTable::getList([
            'select' => ['ID'],
            'filter' => ['!ID' => $keepIds],
            'order' => ['ID' => 'DESC'],
        ])->fetchAll();

        $toDeleteIds = array_column($rsLogElementToDelete, 'ID');

        foreach ($toDeleteIds as $logId) {
            \CIBlockElement::Delete($logId);
        }

        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
