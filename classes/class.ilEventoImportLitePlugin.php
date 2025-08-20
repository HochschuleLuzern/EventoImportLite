<?php declare(strict_types = 1);

use EventoImportLite\import\Logger;
use EventoImportLite\import\ImportTaskFactory;
use EventoImportLite\config\ConfigurationManager;
use EventoImportLite\config\CronConfigForm;
use EventoImportLite\config\DefaultUserSettings;
use EventoImportLite\config\DefaultEventSettings;
use EventoImportLite\config\ImporterApiSettings;
use EventoImportLite\config\locations\BaseLocationConfiguration;
use EventoImportLite\config\locations\RepositoryLocationSeeker;
use EventoImportLite\config\local_roles\LocalVisitorRoleManager;
use EventoImportLite\config\local_roles\LocalVisitorRoleFactory;
use EventoImportLite\config\local_roles\LocalVisitorRoleRepository;

/**
 * Copyright (c) 2017 Hochschule Luzern
 *
 * This file is part of the NotifyOnCronFailure-Plugin for ILIAS.

 * NotifyOnCronFailure-Plugin for ILIAS is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.

 * NotifyOnCronFailure-Plugin for ILIAS is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with NotifyOnCronFailure-Plugin for ILIAS.  If not,
 * see <http://www.gnu.org/licenses/>.
 */

/**
 * Class ilEventoImportLitePlugin
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 */

class ilEventoImportLitePlugin extends ilCronHookPlugin
{
    const ID = 'crevlite';
    const PLUGIN_NAME = "EventoImportLite";

    public function __construct(
        \ilDBInterface $db,
        \ilComponentRepositoryWrite $component_repository
    ) {
        parent::__construct($db, $component_repository, self::ID);
    }

    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }
    
    /**
     * @var ilCronJob[]
     */
    protected static $cron_job_instances;
    
    /**
     * @return  ilCronJob[]
     */
    public function getCronJobInstances() : array
    {
        $this->loadCronJobInstance();
        
        return array_values(self::$cron_job_instances);
    }
    
    /**
     * @return  ilCronJob or false on failure
     */
    public function getCronJobInstance($a_job_id): \ilCronJob
    {
        $this->loadCronJobInstance();
        return self::$cron_job_instances[$a_job_id];
    }

    protected function loadCronJobInstance()
    {
        /** @var ILIAS\DI\Container $DIC */
        global $DIC;
        $lng = $DIC['lng'];
        $rbac = $DIC->rbac();
        $tree = $DIC['tree'];
        $settings = new ilSetting('crevlite');

        //This is a workaround to avoid problems with missing templates
        if (!method_exists($DIC, 'ui') || !method_exists($DIC->ui(), 'factory') || !isset($DIC['ui.factory'])) {
            ilInitialisation::initUIFramework($DIC);
            ilStyleDefinition::setCurrentStyle('Desktop');
        }

        if (!isset(self::$cron_job_instances)) {
            ;
            $cron_config = new CronConfigForm(
                new DefaultUserSettings($settings),
                new DefaultEventSettings($settings),
                new ImporterApiSettings($settings),
                new BaseLocationConfiguration($settings),
                new RepositoryLocationSeeker($tree, 1),
                $this,
                $lng,
                $rbac
            );
            $config_manager = new ConfigurationManager($cron_config, $settings, $this->db, $tree);
            $import_factory = new ImportTaskFactory($config_manager, $this->db, $tree, $rbac);
            $logger = new Logger($this->db);

            self::$cron_job_instances[ilEventoImportLiteDailyImportCronJob::ID] = new ilEventoImportLiteDailyImportCronJob(
                $this,
                $import_factory,
                $config_manager,
                $logger
            );
        }
    }

    protected function beforeUninstall(): bool
    {
        $drop_table_list = [
            \EventoImportLite\db\IliasEventoUserTblDef::TABLE_NAME,
            \EventoImportLite\db\IliasEventoEventsTblDef::TABLE_NAME,
            \EventoImportLite\db\IliasParentEventTblDef::TABLE_NAME,
            \EventoImportLite\db\IliasEventLocationsTblDef::TABLE_NAME,
            \EventoImportLite\db\IliasEventoEventMembershipsTblDef::TABLE_NAME,
            Logger::TABLE_LOG_USERS,
            Logger::TABLE_LOG_EVENTS,
            Logger::TABLE_LOG_MEMBERSHIPS
        ];


        foreach ($drop_table_list as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->dropTable($table);
            }
        }

        return true;
    }


    public function getPluginInfo(): ilPluginInfo
    {
        return parent::getPluginInfo();
    }

    public function getComponentInfo(): ilComponentInfo
    {
        return $this->getPluginInfo()->getComponent();
    }

    public function getPluginSlotInfo(): ilPluginSlotInfo
    {
        return $this->getPluginInfo()->getPluginSlot();
    }

    /**
     * Send Info Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */
    public static function sendInfo($a_info = "", $a_keep = false)
    {
        global $DIC;

        if (isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("info", $a_info, $a_keep);
        }
    }

    /**
     * Send Failure Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */
    public static function sendFailure($a_info = "", $a_keep = false)
    {
        global $DIC;

        if (isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("failure", $a_info, $a_keep);
        }
    }

    /**
     * Send Question to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static	*/
    public static function sendQuestion($a_info = "", $a_keep = false)
    {
        global $DIC;

        if (isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("question", $a_info, $a_keep);
        }
    }

    /**
     * Send Success Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */
    public static function sendSuccess($a_info = "", $a_keep = false)
    {
        global $DIC;

        /** @var ilTemplate $tpl */
        if (isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("success", $a_info, $a_keep);
        }
    }
}
