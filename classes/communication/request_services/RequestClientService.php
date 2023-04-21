<?php declare(strict_types = 1);

namespace EventoImportLite\communication\request_services;

/**
 * Interface RequestClientService
 * @package EventoImportLite\communication\request_services
 */
interface RequestClientService
{
    public function sendRequest(string $path, array $request_params) : string;
}
