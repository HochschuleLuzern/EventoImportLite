<?php declare(strict_types=1);

namespace EventoImportLite\import\data_management;

use EventoImportLite\communication\api_models\EventoUser;
use EventoImportLite\import\data_management\ilias_core_service\IliasUserServices;
use EventoImportLite\import\data_management\repository\IliasEventoUserRepository;
use EventoImportLite\communication\api_models\EventoUserShort;
use EventoImportLite\config\DefaultUserSettings;
use EventoImportLite\communication\EventoUserPhotoImporter;
use EventoImportLite\import\data_management\repository\model\IliasEventoUser;
use EventoImportLite\import\Logger;

class UserManager
{
    private IliasUserServices $ilias_user_service;
    private IliasEventoUserRepository $evento_user_repo;
    private DefaultUserSettings $default_user_settings;
    private Logger $logger;

    public function __construct(IliasUserServices $ilias_user_service, IliasEventoUserRepository $evento_user_repo, DefaultUserSettings $default_user_settings, Logger $logger)
    {
        $this->ilias_user_service = $ilias_user_service;
        $this->evento_user_repo = $evento_user_repo;
        $this->default_user_settings = $default_user_settings;
        $this->logger = $logger;
    }

    public function createAndSetupNewIliasUser(EventoUser $evento_user) : \ilObjUser
    {
        $ilias_user_object = $this->ilias_user_service->createNewIliasUserObject();

        $ilias_user_object = $this->setUserValuesFromEventoUserObject($ilias_user_object, $evento_user);

        $ilias_user_object->create();
        $ilias_user_object->saveAsNew(false);

        $this->setUserDefaultSettings($ilias_user_object, $this->default_user_settings);
        $this->setForcedUserSettings($ilias_user_object, $this->default_user_settings);

        $this->evento_user_repo->addNewEventoIliasUserByEventoUser($evento_user, $ilias_user_object, IliasEventoUserRepository::TYPE_HSLU_AD);

        return $ilias_user_object;
    }

    public function updateSettingsForExistingUser(\ilObjUser $ilias_user)
    {
        $this->setForcedUserSettings($ilias_user, $this->default_user_settings);
    }

    public function importAndSetUserPhoto(\ilObjUser $ilias_user, int $evento_id, EventoUserPhotoImporter $photo_importer) : void
    {
        if ($this->ilias_user_service->userHasPersonalPicture($ilias_user->getId())) {
            return;
        }

        try {
            $photo_import = $photo_importer->fetchUserPhotoDataById($evento_id);

            if (!is_null($photo_import)
                && $photo_import->getHasPhoto()
                && $photo_import->getImgData()
                && strlen($photo_import->getImgData()) > 10
            ) {
                $this->ilias_user_service->saveEncodedPersonalPictureToUserProfile($ilias_user->getId(), $photo_import->getImgData());
            }
        } catch (\Exception $e) {
            $this->logger->logException('Photo Import', 'Exception on importing User Photo. EventoID = ' . $evento_id
                . ', IliasUserId = ' . $ilias_user->getId() . ', Exception Message = ' . $e->getMessage());
        }
    }

    public function synchronizeIliasUserWithEventoRoles(\ilObjUser $user, array $imported_evento_roles) : void
    {
        $this->ilias_user_service->assignUserToRole($user->getId(), $this->default_user_settings->getDefaultUserRoleId());

        // Set ilias roles according to given evento roles
        foreach ($this->default_user_settings->getEventoCodeToIliasRoleMapping() as $evento_role_code => $ilias_role_id) {
            if (in_array($evento_role_code, $imported_evento_roles)) {
                $this->ilias_user_service->assignUserToRole($user->getId(), $ilias_role_id);
            } else {
                $this->ilias_user_service->deassignUserFromRole($user->getId(), $ilias_role_id);
            }
        }
    }

    public function registerEventoUserAsDelivered(EventoUser $evento_user)
    {
        $this->evento_user_repo->registerUserAsDelivered($evento_user->getEventoId());
    }

    public function updateIliasUserFromEventoUser(\ilObjUser $ilias_user, EventoUser $evento_user)
    {
        $changed_user_data = [];
        if ($ilias_user->getFirstname() != $evento_user->getFirstName()) {
            $changed_user_data['first_name'] = [
                'old' => $ilias_user->getFirstname(),
                'new' => $evento_user->getFirstName()
            ];
            $ilias_user->setFirstname($evento_user->getFirstName());
        }

        if ($ilias_user->getlastname() != $evento_user->getLastName()) {
            $changed_user_data['last_name'] = [
                'old' => $ilias_user->getFirstname(),
                'new' => $evento_user->getFirstName()
            ];
            $ilias_user->setLastname($evento_user->getLastName());
        }

        $received_gender_char = $this->convertEventoToIliasGenderChar($evento_user->getGender());
        if ($ilias_user->getGender() != $received_gender_char) {
            $changed_user_data['gender'] = [
                'old' => $ilias_user->getGender(),
                'new' => $received_gender_char
            ];
            $ilias_user->setGender($received_gender_char);
        }

        $mail_list = $evento_user->getEmailList();
        if (isset($mail_list[0]) && ($ilias_user->getSecondEmail() != $mail_list[0])) {
            $changed_user_data['second_mail'] = [
                'old' => $ilias_user->getSecondEmail(),
                'new' => $mail_list[0]
            ];
            $ilias_user->setSecondEmail($mail_list[0]);
        }

        if ($ilias_user->getMatriculation() != ('Evento:' . $evento_user->getEventoId())) {
            $changed_user_data['matriculation'] = [
                'old' => $ilias_user->getMatriculation(),
                'new' => 'Evento:' . $evento_user->getEventoId()
            ];
            $ilias_user->setMatriculation('Evento:' . $evento_user->getEventoId());
        }

        if ($ilias_user->getAuthMode() != $this->default_user_settings->getAuthMode()) {
            $changed_user_data['auth_mode'] = [
                'old' => $ilias_user->getAuthMode(),
                'new' => $this->default_user_settings->getAuthMode()
            ];
            $ilias_user->setAuthMode($this->default_user_settings->getAuthMode());
        }

        if (!$ilias_user->getActive()) {
            $changed_user_data['active'] = [
                'old' => false,
                'new' => true
            ];
            $ilias_user->setActive(true);
        }

        if (count($changed_user_data) > 0) {
            $ilias_user->update();
        }

        return $changed_user_data;
    }

    private function setUserValuesFromEventoUserObject(\ilObjUser $ilias_user, EventoUser $evento_user) : \ilObjUser
    {
        $ilias_user->setLogin($evento_user->getLoginName());
        $ilias_user->setFirstname($evento_user->getFirstName());
        $ilias_user->setLastname($evento_user->getLastName());
        $ilias_user->setGender($this->convertEventoToIliasGenderChar($evento_user->getGender()));
        $ilias_user->setEmail($evento_user->getEmailList()[0]);
        $ilias_user->setTitle($ilias_user->getFullname());
        $ilias_user->setDescription($ilias_user->getEmail());
        $ilias_user->setMatriculation('Evento:' . $evento_user->getEventoId());
        $ilias_user->setExternalAccount($evento_user->getEventoId() . '@hslu.ch');
        $ilias_user->setAuthMode($this->default_user_settings->getAuthMode());

        return $ilias_user;
    }

    private function setForcedUserSettings(\ilObjUser $ilias_user, DefaultUserSettings $user_settings)
    {
        $ilias_user->setActive(true);

        // Reset login attempts over night -> needed since login attempts are limited to 8
        $ilias_user->setLoginAttempts(0);

        // Set user time limits
        $ilias_user->setTimeLimitUnlimited(true);

        /*
            The old import had the $user->setPasswd method two times called. One time within an if-statement and another time without
            Code snipped of if-statement below:
                if ($user_settings->isAuthModeLDAP()) {
                    $user->setPasswd('');
                }

            Since the second call without an if-statement makes this block useless, it is not in the code anymore
        */
        $ilias_user->setPasswd('', IL_PASSWD_PLAIN);

        // profil is always public for registered users
        $ilias_user->setPref(
            'public_profile',
            $user_settings->isProfilePublic()  ? 'y' : 'n'
        );

        // profil picture is always public for registered users, but it can be changed by the profile owner
        $ilias_user->setPref(
            'public_upload',
            $user_settings->isProfilePicturePublic()  ? 'y' : 'n'
        );

        $ilias_user->update();
        $ilias_user->writePrefs();
    }

    private function setUserDefaultSettings(\ilObjUser $ilias_user_object, DefaultUserSettings $user_settings)
    {
        $ilias_user_object->setActive(true);
        $ilias_user_object->setTimeLimitUnlimited(true);

        $ilias_user_object->setAuthMode($user_settings->getAuthMode());

        // Set default prefs
        $ilias_user_object->setPref(
            'hits_per_page',
            (string) $user_settings->getDefaultHitsPerPage()
        ); //100 hits per page
    }

    private function convertEventoToIliasGenderChar(string $evento_gender_char) : string
    {
        switch (strtolower($evento_gender_char)) {
            case 'f':
                return 'f';
            case 'm':
                return 'm';
            case 'x':
            default:
                return 'n';
        }
    }

    public function deleteEventoUserToIliasUserConnection(int $evento_id)
    {
        $this->evento_user_repo->deleteEventoIliasUserConnectionByEventoId($evento_id);
    }

    public function getExistingIliasUserObjectById(int $ilias_user_id) : \ilObjUser
    {
        return $this->ilias_user_service->getExistingIliasUserObjectById($ilias_user_id);
    }

    public function getIliasEventoUserByEventoId(int $evento_id) : IliasEventoUser
    {
        return $this->evento_user_repo->getIliasEventoUserByEventoId($evento_id);
    }

    public function getIliasUserIdByEventoId(int $evento_id) : ?int
    {
        return $this->evento_user_repo->getIliasUserIdByEventoId($evento_id);
    }

    public function getIliasUserIdByEventoUserShort(EventoUserShort $evento_user) : ?int
    {
        $ilias_user_id = $this->evento_user_repo->getIliasUserIdByEventoId($evento_user->getEventoId());
        if (is_null($ilias_user_id)) {
            $edu_user = $this->ilias_user_service->searchEduUserByEmail($evento_user->getEmailAddress());
            if (!is_null($edu_user)) {
                $ilias_user_id = (int) $edu_user->getId();
                $this->evento_user_repo->addNewEventoIliasUserByEventoUserShort($evento_user, $edu_user, IliasEventoUserRepository::TYPE_EDU_ID);
            }
        }

        return $ilias_user_id;
    }

    public function getIliasEventoUserForEventoUser(EventoUserShort $evento_user) : ?IliasEventoUser
    {
        return $this->evento_user_repo->getIliasEventoUserByEventoId($evento_user->getEventoId());
    }
}
