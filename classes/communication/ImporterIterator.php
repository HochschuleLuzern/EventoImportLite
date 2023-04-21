<?php declare(strict_types=1);

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

namespace EventoImportLite\communication;

/**
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 */
class ImporterIterator
{
    private int $page;
    private int $page_size;

    public function __construct(int $page_size)
    {
        $this->page = 1;
        $this->page_size = $page_size;
    }

    public function nextPage() : int
    {
        return $this->page++;
    }

    public function getPage() : int
    {
        return $this->page;
    }

    public function getPageSize() : int
    {
        return $this->page_size;
    }
}
