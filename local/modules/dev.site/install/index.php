<?

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeLanguageTable;
use Bitrix\Iblock\TypeTable;

class dev_site extends CModule
{
    const MODULE_ID = 'dev.site';

    public $MODULE_ID = 'dev.site',
        $MODULE_VERSION,
        $MODULE_VERSION_DATE,
        $MODULE_NAME = 'Тренировочный модуль',
        $PARTNER_NAME = 'dev';

    public const CODE = 'LOG',
        IBLOCK_TYPE_ID = 'logging';

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . 'version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    function InstallFiles($arParams = array())
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallFiles();
        $this->InstallLogIBlock();
    }

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
        $this->UnInstallLogIBlock();

    }

    function InstallLogIBlock()
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        if (TypeTable::getByPrimary(self::IBLOCK_TYPE_ID)->fetch() === false) {
            $blockTypeResult = TypeTable::add([
                'ID' => self::IBLOCK_TYPE_ID,
                'SECTIONS' => 'Y',
                'SORT' => 100,
            ]);

            if (!$blockTypeResult) {
                return false;
            }

            $names = [
                'ru' => ['Логирование', 'Разделы', 'Элементы'],
                'en' => ['Logging', 'Sections', 'Elements'],
            ];

            foreach ($names as $languageId => [$name, $sectionsName, $elementsName])
            {
                $languageResult = TypeLanguageTable::add([
                    'IBLOCK_TYPE_ID' => self::IBLOCK_TYPE_ID,
                    'LANGUAGE_ID' => $languageId,
                    'NAME' => $name,
                    'SECTIONS_NAME' => $sectionsName,
                    'ELEMENTS_NAME' => $elementsName,
                ]);

                if (!$languageResult->isSuccess())
                {
                    return false;
                }
            }
        }

        // Проверяем существование инфоблока
        $rsIBlock = IblockTable::getList([
            'filter' => [
                '=CODE' => self::CODE,
                '=IBLOCK_TYPE_ID' => self::IBLOCK_TYPE_ID,
            ],
        ])->fetch();

        if (!$rsIBlock) {
            $iblock = new \CIBlock();

            $result = $iblock->Add([
                'ACTIVE' => 'Y',
                'NAME' => 'Логи',
                'CODE' => self::CODE,
                'API_CODE' => self::CODE,
                'IBLOCK_TYPE_ID' => self::IBLOCK_TYPE_ID,
                'LID' => ['s1'],
                'SORT' => 500,
                'GROUP_ID' => [
                    1 => 'X',
                    2 => 'D',
                    6 => 'R',
                ],
            ]);

            if (!$result) {
                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    function UnInstallLogIBlock($arParams = [])
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        // Ищем инфоблок по CODE и типу
        $iBlock = \CIBlock::GetList(
            [],
            [
                '=CODE' => self::CODE,
                '=IBLOCK_TYPE_ID' => self::IBLOCK_TYPE_ID,
            ]
        );

        $rsIBlock = $iBlock->Fetch();

        if ($rsIBlock) {
            // Удаляем инфоблок
            if (!\CIBlock::Delete($rsIBlock['ID'])) {
                return false;
            }

            return true;
        }

        // Удаляем тип инфоблока
        $obBlocktype = new \CIBlockType();

        if (!$obBlocktype->Delete(self::IBLOCK_TYPE_ID)) {
            return false;
        }

        return true;
    }
}
