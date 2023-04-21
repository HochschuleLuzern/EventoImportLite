<?php declare(strict_types = 1);

namespace EventoImportLite\import\action;

use EventoImportLite\import\data_management\ilias_core_service\IliasUserServices;
use EventoImportLite\import\action\user\UserActionFactory;
use EventoImportLite\communication\api_models\EventoUser;
use EventoImportLite\import\data_management\repository\IliasEventoUserRepository;

class UserImportActionDecider
{
    private IliasUserServices $ilias_user_service;
    private UserActionFactory $action_factory;
    private IliasEventoUserRepository $evento_user_repo;

    public function __construct(IliasUserServices $ilias_user_service, IliasEventoUserRepository $evento_user_repo, UserActionFactory $action_factory)
    {
        $this->ilias_user_service = $ilias_user_service;
        $this->evento_user_repo = $evento_user_repo;
        $this->action_factory = $action_factory;
    }

    private function addUserToEventoIliasMappingTable(
        EventoUser $evento_user,
        int $ilias_user_id
    ) {
        $ilias_user = $this->ilias_user_service->getExistingIliasUserObjectById($ilias_user_id);
        $this->evento_user_repo->addNewEventoIliasUserByEventoUser($evento_user, $ilias_user, IliasEventoUserRepository::TYPE_HSLU_AD);
    }

    private function getExternalAccountStringForEventoUser(EventoUser $evento_user)
    {
        return $evento_user->getEventoId()."@hslu.ch";
    }

    public function determineImportAction(EventoUser $evento_user) : EventoImportLiteAction
    {
        $matched_user_id = $this->evento_user_repo->getIliasUserIdByEventoId($evento_user->getEventoId());

        if (!is_null($matched_user_id)) {
            $current_login_of_matched_user = $this->ilias_user_service->getLoginByUserId($matched_user_id);

            // Check if login of delivered user has changed AND the changed login name is already taken
            if ($current_login_of_matched_user != $evento_user->getLoginName()
                && $this->ilias_user_service->getUserIdByLogin($evento_user->getLoginName()) > 0
            ) {
                $id_of_user_to_rename = $this->ilias_user_service->getUserIdByLogin($evento_user->getLoginName());
                $user_to_rename = $this->ilias_user_service->getExistingIliasUserObjectById($id_of_user_to_rename);
                return $this->action_factory->buildRenameExistingAndUpdateDeliveredAction(
                    $evento_user,
                    $matched_user_id,
                    $user_to_rename,
                    'login'
                );
            }

            return $this->action_factory->buildUpdateAction($evento_user, $matched_user_id);
        }

        return $this->matchToIliasUsersAndDetermineAction($evento_user);
    }

    private function matchToIliasUsersAndDetermineAction(EventoUser $evento_user) : EventoImportLiteAction
    {
        $ilias_user_id = null;

        try {
            $ilias_user_id = $this->ilias_user_service->searchUserIdByExternalAccount(
                $this->getExternalAccountStringForEventoUser($evento_user)
            );
        } catch (\ilEventoImportLiteDuplicateAccountException $e) {
            return $this->action_factory->buildReportConflict($evento_user, $e->getMessage());
        }


        if (is_null($ilias_user_id)) {
            return $this->action_factory->buildCreateAction($evento_user);
        } else {
            $this->addUserToEventoIliasMappingTable($evento_user, $ilias_user_id);
            return $this->action_factory->buildUpdateAction($evento_user, $ilias_user_id);
        }
    }
}
