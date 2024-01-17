<?php declare(strict_types=1);

use EventoImportLite\communication\EventoUserImporter;
use EventoImportLite\communication\ImporterIterator;
use EventoImportLite\communication\request_services\RestClientService;
use EventoImportLite\communication\EventoEventImporter;
use EventoImportLite\communication\EventoUserPhotoImporter;
use EventoImportLite\import\Logger;
use EventoImportLite\import\ImportTaskFactory;
use EventoImportLite\config\ConfigurationManager;
use EventoImportLite\communication\EventoEmployeeImporter;

class ilEventoImportLiteDailyImportCronJob extends ilCronJob
{
    public const ID = "crevlite_daily_import";

    private ilEventoImportLitePlugin $cp;
    private ImportTaskFactory $import_factory;
    private ConfigurationManager $config_manager;
    private Logger $logger;

    public function __construct(
        \ilEventoImportLitePlugin $cp,
        ImportTaskFactory $import_factory,
        ConfigurationManager $config_manager,
        Logger $logger
    ) {
        $this->cp = $cp;
        $this->import_factory = $import_factory;
        $this->config_manager = $config_manager;
        $this->logger = $logger;
    }

    public function getId() : string
    {
        return self::ID;
    }

    public function hasAutoActivation() : bool
    {
        return false;
    }

    public function hasFlexibleSchedule(): bool
    {
        return true;
    }

    public function getDefaultScheduleType(): int
    {
        return self::SCHEDULE_TYPE_DAILY;
    }

    public function getDefaultScheduleValue(): int
    {
        return 0;
    }

    public function run(): \ilCronJobResult
    {
        try {
            $api_settings = $this->config_manager->getApiConfiguration();

            $data_source = new RestClientService(
                $api_settings->getUrl(),
                $api_settings->getTimeoutAfterRequest(),
                $api_settings->getApikey(),
                $api_settings->getApiSecret()
            );

            $import_users = $this->import_factory->buildUserImport(
                new EventoUserImporter(
                    $data_source,
                    new ImporterIterator($api_settings->getPageSize()),
                    $this->logger,
                    $api_settings->getTimeoutFailedRequest(),
                    $api_settings->getMaxRetries()
                ),
                new EventoUserPhotoImporter(
                    $data_source,
                    $api_settings->getTimeoutFailedRequest(),
                    $api_settings->getMaxRetries(),
                    $this->logger
                )
            );

            $import_events = $this->import_factory->buildEventImport(
                new EventoEventImporter(
                    $data_source,
                    new ImporterIterator($api_settings->getPageSize()),
                    $this->logger,
                    $api_settings->getTimeoutFailedRequest(),
                    $api_settings->getMaxRetries()
                )
            );

            $import_users->run();
            $import_events->run();

            return new ilEventoImportLiteResult(ilCronJobResult::STATUS_OK, 'Cron job terminated successfully.');
        } catch (Exception $e) {
            return new ilEventoImportLiteResult(ilCronJobResult::STATUS_CRASHED, 'Cron job crashed: ' . $e->getMessage());
        }
    }

    public function getTitle(): string
    {
        return $this->cp->txt('daily_import_cj_title');
    }

    public function getDescription(): string
    {
        return $this->cp->txt('daily_import_cj_desc');
    }

    public function isManuallyExecutable() : bool
    {
        return true;
    }

    public function hasCustomSettings() : bool
    {
        return true;
    }

    public function addCustomSettingsToForm(ilPropertyFormGUI $form) : void
    {
        $this->config_manager->form()->fillFormWithApiConfig($form);
        $this->config_manager->form()->fillFormWithUserImportConfig($form);
        $this->config_manager->form()->fillFormWithEventLocationConfig($form);
        $this->config_manager->form()->fillFormWithEventConfig($form);
    }

    public function saveCustomSettings(ilPropertyFormGUI $form) : bool
    {
        return $this->config_manager->form()->saveApiConfigFromForm($form)
            && $this->config_manager->form()->saveUserConfigFromForm($form)
            && $this->config_manager->form()->saveEventLocationConfigFromForm($form)
            && $this->config_manager->form()->saveEventConfigFromForm($form);
    }
}
