<?php declare(strict_types = 1);

namespace EventoImportLite\administration;

use EventoImportLite\communication\api_models\ApiDataModelBase;
use EventoImportLite\communication\request_services\RestClientService;
use EventoImportLite\communication\EventoUserImporter;
use EventoImportLite\communication\EventoEventImporter;
use EventoImportLite\communication\EventoUserPhotoImporter;
use EventoImportLite\communication\EventoAdminImporter;
use EventoImportLite\communication\request_services\RequestClientService;
use EventoImportLite\config\ImporterApiSettings;

class EventoImportLiteApiTester
{
    private \ilSetting $settings;
    private \ilDBInterface $db;

    public function __construct(\ilSetting $settings, \ilDBInterface $db)
    {
        $this->settings = $settings;
        $this->db = $db;
    }

    public function fetchDataRecord(string $cmd, int $id) : ?ApiDataModelBase
    {
        $api_importer_settings = new ImporterApiSettings($this->settings);
        $iterator = new \EventoImportLite\communication\ImporterIterator($api_importer_settings->getPageSize());
        $logger = new \EventoImportLite\import\Logger($this->db);

        $request_client = $this->buildDataSource($api_importer_settings);

        if ($cmd == 'user') {
            $importer = new EventoUserImporter(
                $request_client,
                $iterator,
                $logger,
                $api_importer_settings->getTimeoutAfterRequest(),
                $api_importer_settings->getMaxRetries()
            );
            return $importer->fetchUserDataRecordById($id);
        } else {
            if ($cmd == 'event') {
                $importer = new EventoEventImporter(
                    $request_client,
                    $iterator,
                    $logger,
                    $api_importer_settings->getTimeoutAfterRequest(),
                    $api_importer_settings->getMaxRetries()
                );
                return $importer->fetchEventDataRecordById($id);
            } else {
                if ($cmd == 'photo') {
                    $importer = new EventoUserPhotoImporter(
                        $request_client,
                        $api_importer_settings->getTimeoutAfterRequest(),
                        $api_importer_settings->getMaxRetries(),
                        $logger
                    );
                    return $importer->fetchUserPhotoDataById($id);
                } else {
                    if ($cmd == 'admin') {
                        $importer = new EventoAdminImporter(
                            $request_client,
                            $logger,
                            $api_importer_settings->getTimeoutAfterRequest(),
                            $api_importer_settings->getMaxRetries()
                        );
                        return $importer->fetchEventAdminDataRecordById($id);
                    }
                }
            }
        }
    }

    public function fetchDataSet(string $cmd, int $skip, int $take) : array
    {
        $api_importer_settings = new ImporterApiSettings($this->settings);
        $iterator = new \EventoImportLite\communication\ImporterIterator($api_importer_settings->getPageSize());
        $logger = new \EventoImportLite\import\Logger($this->db);

        $request_client = $this->buildDataSource($api_importer_settings);

        if ($cmd == 'user') {
            $importer = new EventoUserImporter(
                $request_client,
                $iterator,
                $logger,
                $api_importer_settings->getTimeoutAfterRequest(),
                $api_importer_settings->getMaxRetries()
            );
            return $importer->fetchSpecificUserDataSet($skip, $take);
        } else {
            if ($cmd == 'event') {
                $importer = new EventoEventImporter(
                    $request_client,
                    $iterator,
                    $logger,
                    $api_importer_settings->getTimeoutAfterRequest(),
                    $api_importer_settings->getMaxRetries()
                );
                return $importer->fetchSpecificEventDataSet($skip, $take);
            }
        }
    }

    public function fetchParameterlessDataset() : array
    {
        $api_importer_settings = new ImporterApiSettings($this->settings);
        $logger = new \EventoImportLite\import\Logger($this->db);

        $data_source = $this->buildDataSource($api_importer_settings);

        $importer = new EventoAdminImporter(
            $data_source,
            $logger,
            $api_importer_settings->getTimeoutAfterRequest(),
            $api_importer_settings->getMaxRetries()
        );
        return $importer->fetchAllIliasAdmins();
    }

    private function buildDataSource(ImporterApiSettings $api_importer_settings) : RequestClientService
    {
        return new RestClientService(
            $api_importer_settings->getUrl(),
            $api_importer_settings->getTimeoutAfterRequest(),
            $api_importer_settings->getApikey(),
            $api_importer_settings->getApiSecret()
        );
    }
}
