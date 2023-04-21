<?php declare(strict_types=1);

namespace EventoImportLite\communication;

use EventoImportLite\communication\request_services\RequestClientService;
use EventoImportLite\import\Logger;

/**
 * Copyright (c) 2017 Hochschule Luzern
 * This file is part of the EventoImportLite-Plugin for ILIAS.
 * EventoImportLite-Plugin for ILIAS is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * EventoImportLite-Plugin for ILIAS is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with EventoImportLite-Plugin for ILIAS.  If not,
 * see <http://www.gnu.org/licenses/>.
 */

/**
 * Class ilEventoImportLiteer
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 */
abstract class EventoImporterBase
{
    protected RequestClientService $data_source;
    protected int $seconds_before_retry;
    protected int $max_retries;
    protected Logger $evento_logger;
    protected bool $has_more_data;

    public function __construct(
        RequestClientService $data_source,
        int $seconds_before_retry,
        int $max_retries,
        Logger $logger
    ) {
        $this->data_source = $data_source;
        $this->seconds_before_retry = $seconds_before_retry;
        $this->max_retries = $max_retries;
        $this->evento_logger = $logger;
    }

    public function hasMoreData() : bool
    {
        return $this->has_more_data;
    }
}
