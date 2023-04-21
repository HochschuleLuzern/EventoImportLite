<?php declare(strict_types = 1);

namespace EventoImportLite\import\action;

abstract class ReportDatasetWithoutAction implements EventoImportLiteAction
{
    protected int $log_info_code;
    protected array $log_data;
    protected \EventoImportLite\import\Logger $logger;

    public function __construct(int $log_info_code, array $log_data, \EventoImportLite\import\Logger $logger)
    {
        $this->log_info_code = $log_info_code;
        $this->log_data = $log_data;
        $this->logger = $logger;
    }
}
