<?php declare(strict_types = 1);

namespace EventoImportLite\communication;

use EventoImportLite\communication\generic_importers\SingleDataRecordImport;
use EventoImportLite\communication\request_services\RequestClientService;
use EventoImportLite\communication\api_models\EventoUserPhoto;
use EventoImportLite\import\Logger;

class EventoUserPhotoImporter extends EventoImporterBase
{
    use SingleDataRecordImport;

    private string $fetch_data_record_method;

    public function __construct(
        RequestClientService $data_source,
        int $seconds_before_retry,
        int $max_retries,
        Logger $logger
    ) {
        parent::__construct($data_source, $seconds_before_retry, $max_retries, $logger);

        $this->fetch_data_record_method = 'GetPhotoById';
    }

    public function fetchUserPhotoDataById(int $user_evento_id) : ?EventoUserPhoto
    {
        $api_data = $this->fetchDataRecordById(
            $this->data_source,
            $this->fetch_data_record_method,
            $user_evento_id,
            $this->seconds_before_retry,
            $this->max_retries
        );


        return !is_null($api_data) ? new EventoUserPhoto($api_data) : null;
    }
}
