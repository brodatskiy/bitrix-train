<?php

use Academy\Events\MasterclassTable;
use Academy\Events\ParticipantTable;
use Academy\Events\RegistrationMasterclassTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Academy\Events\RegistrationTable;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
Loc::loadMessages(__FILE__);

class academy_events extends CModule
{
    public $MODULE_ID = 'academy.events';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME = 'Academy';
    public $PARTNER_URI = 'https://example.com';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('ACADEMY_EVENTS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ACADEMY_EVENTS_MODULE_DESCRIPTION');
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallDB();
        $this->InstallFiles();
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = Application::getConnection();
        foreach ($this->getEntityClasses() as $class)
        {
            $entity = $class::getEntity();
            if (!$connection->isTableExists($entity->getDBTableName()))
            {
                $entity->createDbTable();
            }
        }

        return true;
    }

    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = Application::getConnection();
        foreach ($this->getEntityClasses() as $class)
        {
            $tableName = $class::getEntity()->getDBTableName();
            if ($connection->isTableExists($tableName))
            {
                $connection->dropTable($tableName);
            }
        }

        Option::delete($this->MODULE_ID);

        return true;
    }

    private function getEntityClasses(): array
    {
        return [
            RegistrationTable::class,
            ParticipantTable::class,
            MasterclassTable::class,
            RegistrationMasterclassTable::class,
        ];
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/components',
            $_SERVER['DOCUMENT_ROOT'] . '/local/components',
            true,
            true
        );

        return true;
    }

    public function UnInstallFiles()
    {
        Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/local/components/academy/news.list');

        return true;
    }
}