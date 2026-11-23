<?php

namespace Dev\Site\Handlers;


use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Type\DateTime;

class Iblock
{
    public static function addLog(array &$fields)
    {
        $logIblockId = IblockTable::resolveIdByCode("LOG");

        if ($logIblockId === null) {
            throw new \RuntimeException('Инфоблок не найден');
        }

        $targetIblockId = (int)$fields['IBLOCK_ID'];

        if (!$targetIblockId) {
            return false;
        }

        if ($targetIblockId === $logIblockId) {
            return false;
        }

        $targetIblock = IblockTable::getByPrimary($targetIblockId)->fetch();

        if (!$targetIblock) {
            return false;
        }
        // Ищем раздел в логирующем инфоблоке
        $section = SectionTable::getRow([
            'filter' => [
                '=IBLOCK_ID' => $logIblockId,
                '=CODE' => $targetIblock['CODE'],
            ],
            'select' => ['ID', 'NAME', 'CODE'],
        ]);
        // Если раздел не найден, создаем его
        if (!$section) {
            $bs = new \CIBlockSection();
            $loggerSectionId = $bs->Add([
                'IBLOCK_ID' => $logIblockId,
                'ACTIVE' => 'Y',
                'NAME' => $targetIblock['NAME'],
                'CODE' => $targetIblock['CODE'],
            ]);

            if (!$loggerSectionId) {
                return false;
            }
        } else {
            $loggerSectionId = $section['ID'];
        }

        $sectionIds = $fields['IBLOCK_SECTION'] ?? [];
        $sectionId = reset($sectionIds) ?: 0;

        $previewText = $targetIblock['NAME'] . '->' . self::getSectionPathRecursive($sectionId) . '->' . $fields['NAME'];

        // Добавляем элемент лога, привязывая его к найденному/созданному разделу
        $el = new \CIBlockElement();
        $logElementFields = [
            'IBLOCK_ID' => $logIblockId,
            'IBLOCK_SECTION_ID' => $loggerSectionId,
            'NAME' => $fields['ID'],
            'ACTIVE' => 'Y',
            'ACTIVE_FROM' => new DateTime(),
            'PREVIEW_TEXT' => $previewText,
        ];

        $logElementId = $el->Add($logElementFields);

        if (!$logElementId) {
            return false;
        }

        return $logElementId;
    }

    private static function getSectionPath(int $sectionId): string
    {
        if ($sectionId === 0) {
            return '';
        }

        $current = SectionTable::getByPrimary($sectionId, [
            'select' => ['LEFT_MARGIN', 'RIGHT_MARGIN', 'NAME'],
        ])
            ->fetch();

        if (!$current) {
            throw new \RuntimeException('Раздел не найден');
        }

        $path = SectionTable::query()
            ->setSelect(['ID', 'NAME', 'CODE'])
            ->where('LEFT_MARGIN', '<=', $current['LEFT_MARGIN'])
            ->where('RIGHT_MARGIN', '>=', $current['RIGHT_MARGIN'])
            ->setOrder(['LEFT_MARGIN' => 'ASC'])
            ->fetchAll();

        return implode('_', array_column($path, 'NAME'));
    }


    /**
     * 1.7. (Дополнительно) Сделать поиск имен разделов рекурсивным.
     */
    private static function getSectionPathRecursive(int $sectionId): string
    {
        if ($sectionId === 0) {
            return '';
        }

        $section = SectionTable::getRow([
            'filter' => ['=ID' => $sectionId],
            'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID'],
        ]);

        if (!$section) {
            return '';
        }

        $parent = self::getSectionPathRecursive((int)$section['IBLOCK_SECTION_ID']);

        return $parent === ''
            ? $section['NAME']
            : $parent . '_' . $section['NAME'];
    }

    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $iQuality = 95;
        $iWidth = 1000;
        $iHeight = 1000;
        /*
         * Получаем пользовательские свойства
         */
        $dbIblockProps = \Bitrix\Iblock\PropertyTable::getList(array(
            'select' => array('*'),
            'filter' => array('IBLOCK_ID' => $arFields['IBLOCK_ID'])
        ));
        /*
         * Выбираем только свойства типа ФАЙЛ (F)
         */
        $arUserFields = [];
        while ($arIblockProps = $dbIblockProps->Fetch()) {
            if ($arIblockProps['PROPERTY_TYPE'] == 'F') {
                $arUserFields[] = $arIblockProps['ID'];
            }
        }
        /*
         * Перебираем и масштабируем изображения
         */
        foreach ($arUserFields as $iFieldId) {
            foreach ($arFields['PROPERTY_VALUES'][$iFieldId] as &$file) {
                if (!empty($file['VALUE']['tmp_name'])) {
                    $sTempName = $file['VALUE']['tmp_name'] . '_temp';
                    $res = \CAllFile::ResizeImageFile(
                        $file['VALUE']['tmp_name'],
                        $sTempName,
                        array("width" => $iWidth, "height" => $iHeight),
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        false,
                        $iQuality);
                    if ($res) {
                        rename($sTempName, $file['VALUE']['tmp_name']);
                    }
                }
            }
        }

        if ($arFields['CODE'] == 'brochures') {
            $RU_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_RU');
            $EN_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_EN');
            if ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID || $arFields['IBLOCK_ID'] == $EN_IBLOCK_ID) {
                \CModule::IncludeModule('iblock');
                $arFiles = [];
                foreach ($arFields['PROPERTY_VALUES'] as $id => &$arValues) {
                    $arProp = \CIBlockProperty::GetByID($id, $arFields['IBLOCK_ID'])->Fetch();
                    if ($arProp['PROPERTY_TYPE'] == 'F' && $arProp['CODE'] == 'FILE') {
                        $key_index = 0;
                        while (isset($arValues['n' . $key_index])) {
                            $arFiles[] = $arValues['n' . $key_index++];
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'L' && $arProp['CODE'] == 'OTHER_LANG' && $arValues[0]['VALUE']) {
                        $arValues[0]['VALUE'] = null;
                        if (!empty($arFiles)) {
                            $OTHER_IBLOCK_ID = $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? $EN_IBLOCK_ID : $RU_IBLOCK_ID;
                            $arOtherElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => $OTHER_IBLOCK_ID,
                                    'CODE' => $arFields['CODE']
                                ], false, false, ['ID'])
                                ->Fetch();
                            if ($arOtherElement) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arOtherElement['ID'], $OTHER_IBLOCK_ID, $arFiles, 'FILE');
                            }
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'E') {
                        $elementIds = [];
                        foreach ($arValues as &$arValue) {
                            if ($arValue['VALUE']) {
                                $elementIds[] = $arValue['VALUE'];
                                $arValue['VALUE'] = null;
                            }
                        }
                        if (!empty($arFiles && !empty($elementIds))) {
                            $rsElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => \Only\Site\Helpers\IBlock::getIblockID('PRODUCTS', 'CATALOG_' . $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? '_RU' : '_EN'),
                                    'ID' => $elementIds
                                ], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
                            while ($arElement = $rsElement->Fetch()) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arElement['ID'], $arElement['IBLOCK_ID'], $arFiles, 'FILE');
                            }
                        }
                    }
                }
            }
        }
    }

}
