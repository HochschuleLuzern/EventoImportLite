<?php declare(strict_types = 1);

namespace EventoImportLite\import;

use EventoImportLite\communication\EventoUserImporter;
use EventoImportLite\import\action\UserImportActionDecider;
use EventoImportLite\import\data_management\ilias_core_service\IliasUserServices;
use ILIAS\DI\RBACServices;
use EventoImportLite\import\data_management\repository\IliasEventoUserRepository;
use EventoImportLite\communication\EventoUserPhotoImporter;
use EventoImportLite\import\action\user\UserActionFactory;
use EventoImportLite\communication\EventoEventImporter;
use EventoImportLite\import\action\EventImportActionDecider;
use EventoImportLite\import\action\event\EventActionFactory;
use EventoImportLite\import\data_management\ilias_core_service\IliasEventObjectService;
use EventoImportLite\import\data_management\MembershipManager;
use EventoImportLite\import\data_management\repository\IliasEventoEventMembershipRepository;
use EventoImportLite\import\data_management\repository\IliasEventoEventObjectRepository;
use EventoImportLite\config\locations\EventLocationsRepository;
use EventoImportLite\import\data_management\ilias_core\MembershipablesEventInTreeSeeker;
use EventoImportLite\config\EventLocations;
use EventoImportLite\import\data_management\EventManager;
use EventoImportLite\import\data_management\UserManager;
use EventoImportLite\config\ConfigurationManager;
use EventoImportLite\config\locations\RepositoryLocationSeeker;
use EventoImportLite\config\locations\EventLocationCategoryBuilder;

class ImportTaskFactory
{
    private ConfigurationManager $config_manager;
    private \ilDBInterface $db;
    private RBACServices $rbac;
    private Logger $logger;
    private \ilTree $tree;

    public function __construct(ConfigurationManager $config_manager, \ilDBInterface $db, \ilTree $tree, RBACServices $rbac)
    {
        $this->config_manager = $config_manager;
        $this->db = $db;
        $this->tree = $tree;
        $this->rbac = $rbac;
        $this->logger = new Logger($db);
    }

    public function buildUserImport(EventoUserImporter $user_importer, EventoUserPhotoImporter $user_photo_importer) : UserImportTask
    {
        $user_settings = $this->config_manager->getDefaultUserConfiguration();
        $ilias_user_service = new IliasUserServices($user_settings, $this->db, $this->rbac);
        $evento_user_repo = new IliasEventoUserRepository($this->db);

        return new UserImportTask(
            $user_importer,
            new UserImportActionDecider(
                $ilias_user_service,
                $evento_user_repo,
                new UserActionFactory(
                    new UserManager(
                        $ilias_user_service,
                        $evento_user_repo,
                        $user_settings,
                        $this->logger
                    ),
                    $user_photo_importer,
                    $this->logger
                )
            ),
            $ilias_user_service,
            $evento_user_repo,
            $this->logger
        );
    }

    public function buildEventImport(EventoEventImporter $event_importer) : EventAndMembershipImportTask
    {
        $event_settings = $this->config_manager->getDefaultEventConfiguration();
        $user_settings = $this->config_manager->getDefaultUserConfiguration();
        $event_obj_service = new IliasEventObjectService($event_settings, $this->db, $this->tree, $this->rbac);
        $evento_event_obj_repo = new IliasEventoEventObjectRepository($this->db);
        $event_locations = new EventLocations(
            new EventLocationsRepository($this->db),
            new RepositoryLocationSeeker($this->tree, 1),
            new EventLocationCategoryBuilder()
        );
        $membership_manager = new MembershipManager(
            new MembershipablesEventInTreeSeeker($this->tree),
            new IliasEventoEventMembershipRepository($this->db),
            new UserManager(
                new IliasUserServices($user_settings, $this->db, $this->rbac),
                new IliasEventoUserRepository($this->db),
                $user_settings,
                $this->logger
            ),
            new \ilFavouritesManager(),
            $this->logger,
            $this->rbac,
            new \DateTimeImmutable(),
            $event_settings->getRemoveParticipantsOnMembershipSync()
        );

        $event_manager = new EventManager(
            $event_obj_service,
            $evento_event_obj_repo,
            $event_locations,
            $membership_manager,
            $this->config_manager->getEventAutoCreationRepo()
        );

        return new EventAndMembershipImportTask(
            $event_importer,
            new EventImportActionDecider(
                $event_manager,
                new EventActionFactory(
                    $event_manager,
                    $membership_manager,
                    $this->logger
                ),
                $event_locations
            ),
            $this->logger
        );
    }
}
