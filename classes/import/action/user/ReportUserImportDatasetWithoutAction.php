<?php declare(strict_types = 1);

namespace EventoImportLite\import\action\user;

use EventoImportLite\import\action\ReportDatasetWithoutAction;

class ReportUserImportDatasetWithoutAction extends ReportDatasetWithoutAction implements UserImportAction
{
    private int $evento_id;
    private string $user_name;

    public function __construct(int $log_info_code, int $evento_id, string $user_name, array $error_data, \EventoImportLite\import\Logger $logger)
    {
        $this->evento_id = $evento_id;
        $this->user_name = $user_name;
        parent::__construct($log_info_code, $error_data, $logger);
    }

    public function executeAction() : void
    {
        $this->logger->logUserImport(
            $this->log_info_code,
            $this->evento_id,
            $this->user_name,
            ['api_data' => $this->log_data]
        );
    }
}
