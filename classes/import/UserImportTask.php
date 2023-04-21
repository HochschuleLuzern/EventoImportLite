<?php declare(strict_types=1);

namespace EventoImportLite\import;

use EventoImportLite\communication\EventoUserImporter;
use EventoImportLite\import\data_management\ilias_core_service\IliasUserServices;
use EventoImportLite\import\action\UserImportActionDecider;
use EventoImportLite\import\data_management\repository\IliasEventoUserRepository;
use EventoImportLite\communication\api_models\EventoUser;

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
class UserImportTask
{
    private EventoUserImporter $evento_importer;
    private UserImportActionDecider $user_import_action_decider;
    private Logger $evento_logger;

    public function __construct(
        EventoUserImporter $importer,
        UserImportActionDecider $user_import_action_decider,
        IliasUserServices $ilias_user_service,
        IliasEventoUserRepository $evento_user_repo,
        Logger $logger
    ) {
        $this->evento_importer = $importer;
        $this->user_import_action_decider = $user_import_action_decider;
        $this->ilias_user_service = $ilias_user_service;
        $this->evento_user_repo = $evento_user_repo;
        $this->evento_logger = $logger;
    }

    public function run() : void
    {
        $this->importUsers();
    }

    private function importUsers() : void
    {
        do {
            try {
                $this->importNextUserPage();
            } catch (\ilEventoImportLiteCommunicationException $e) {
                throw $e;
            } catch (\Exception $e) {
                $this->evento_logger->logException('User Import', $e->getMessage());
            }
        } while ($this->evento_importer->hasMoreData());
    }

    private function importNextUserPage() : void
    {
        foreach ($this->evento_importer->fetchNextUserDataSet() as $data_set) {
            try {
                $evento_user = new EventoUser($data_set);

                $action = $this->user_import_action_decider->determineImportAction($evento_user);
                $action->executeAction();
            } catch (\ilEventoImportLiteApiDataException $e) {
                $data = $e->getApiData();
                if (isset($data[EventoUser::JSON_ID])) {
                    $id = $data[EventoUser::JSON_ID];
                    $evento_id_msg = "Evento ID: $id";
                } else {
                    $evento_id_msg = "Evento ID not given";
                }
                $this->evento_logger->logException('API Data Exception - Importing Event', $evento_id_msg . ' - ' . $e->getMessage());
            } catch (\Exception $e) {
                $this->evento_logger->logException('User Import', $e->getMessage());
            }
        }
    }
}
