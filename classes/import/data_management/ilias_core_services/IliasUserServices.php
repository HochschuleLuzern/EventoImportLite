<?php declare(strict_types = 1);

namespace EventoImportLite\import\data_management\ilias_core_service;

use ILIAS\DI\RBACServices;
use EventoImportLite\config\DefaultUserSettings;

/**
 * Class IliasUserServices
 *
 * This class is a take on encapsulation all the "User" specific functionality from the rest of the import. Things like
 * searching an ILIAS-Object by title or saving something event-specific to the DB should go through this class.
 *
 * @package EventoImportLite\import\db
 */
class IliasUserServices
{
    private DefaultUserSettings $user_settings;
    private \ilDBInterface $db;
    private RBACServices $rbac_services;
    private \ilRbacReview $rbac_review;
    private \ilRbacAdmin $rbac_admin;
    private ?int $student_role_id;

    public function __construct(DefaultUserSettings $user_settings, \ilDBInterface $db, RBACServices $rbac_services)
    {
        $this->user_settings = $user_settings;
        $this->db = $db;
        $this->rbac_services = $rbac_services;
        $this->rbac_review = $rbac_services->review();
        $this->rbac_admin = $rbac_services->admin();

        $this->student_role_id = null;
    }

    /*
     * Get / Create ILIAS User objects
     */

    public function createNewIliasUserObject() : \ilObjUser
    {
        return new \ilObjUser();
    }

    public function getExistingIliasUserObjectById(int $user_id) : \ilObjUser
    {
        return new \ilObjUser($user_id);
    }

    /*
     * Search for ILIAS User IDs by criteria
     */

    public function getUserIdsByEmailAddresses(array $email_adresses)
    {
        $user_lists = array();

        // For each mail given in the adress array...
        foreach ($email_adresses as $email_adress) {

            // ... get all user ids in which a user has this email
            foreach ($this->getUserIdsByEmailAddress($email_adress) as $ilias_id_by_mail) {
                if (!in_array($ilias_id_by_mail, $user_lists)) {
                    $user_lists[] = (int) $ilias_id_by_mail;
                }
            }
        }

        return $user_lists;
    }

    public function getUserIdsByEmailAddress(string $mail_address) : array
    {
        /* The user ids from ilObjUser::getUserIdsByEmail() are returned as string instead of int. Since we use strict_type
        int his plugin, this throws a TypeError when ever an id from this array is passed to a method which expects an argument
        with the type of int. */
        $ids = [];
        foreach (\ilObjUser::getUserIdsByEmail($mail_address) as $user_id) {
            $ids[] = (int) $user_id;
        }

        return $ids;
    }

    public function getUserIdByLogin(string $login_name)
    {
        return \ilObjUser::getUserIdByLogin($login_name);
    }

    public function getLoginByUserId(int $user_id)
    {
        return \ilObjUser::_lookupLogin($user_id);
    }

    public function getUserIdsByEventoId(int $evento_id) : array
    {
        $list = array();

        $query = "SELECT usr_id FROM usr_data WHERE matriculation = " . $this->db->quote("Evento:$evento_id", \ilDBConstants::T_TEXT);
        $result = $this->db->query($query);
        while ($user_record = $this->db->fetchAssoc($result)) {
            $list[] = (int) $user_record["usr_id"];
        }

        return $list;
    }

    public function searchUserIdByExternalAccount(string $external_account) : ?int
    {
        $user_id = null;
        $user_list = [];

        $query = "SELECT usr_id FROM usr_data WHERE ext_account = " . $this->db->quote($external_account, \ilDBConstants::T_TEXT);
        $result = $this->db->query($query);
        while ($user_record = $this->db->fetchAssoc($result)) {
            $user_id = (int) $user_record["usr_id"];
            $user_list[] = $user_id;

        }

        if (count($user_list) > 1) {
            throw new \ilEventoImportLiteDuplicateAccountException(
                $external_account,
                $user_list,
                'Multiple users for same external account: ' . implode(', ', $user_list)
            );
        }

        return $user_id;
    }

    public function searchEduUserByEmail(string $mail_address) : ?\ilObjUser
    {
        $user_ids = $this->getUserIdsByEmailAddress($mail_address);

        $found_user_obj = null;
        foreach ($user_ids as $user_id) {
            $user_obj = $this->getExistingIliasUserObjectById($user_id);
            if (stristr($user_obj->getExternalAccount(), '@eduid.ch') !== false) {
                $found_user_obj = $this->getExistingIliasUserObjectById($user_id);
            }
        }

        return $found_user_obj;
    }

    /*
     * User and role specific methods
     */

    public function assignUserToRole(int $user_id, int $role_id) : void
    {
        if (!$this->rbac_review->isAssigned($user_id, $role_id)) {
            $this->rbac_admin->assignUser($role_id, $user_id);
        }
    }

    public function deassignUserFromRole(int $user_id, int $role_id)
    {
        if ($this->rbac_review->isAssigned($user_id, $role_id)) {
            $this->rbac_admin->deassignUser($role_id, $user_id);
        }
    }

    public function userWasStudent(\ilObjUser $ilias_user_object) : bool
    {
        // TODO: Implement config for this
        if (is_null($this->student_role_id)) {
            $this->student_role_id = $this->user_settings->getStudentRoleId();
            if (is_null($this->student_role_id)) {
                return false;
            }
        }

        return $this->rbac_services->review()->isAssigned($ilias_user_object->getId(), $this->student_role_id);
    }

    /*
     *
     */

    public function userHasPersonalPicture(int $ilias_user_id) : bool
    {
        $personal_picturpath = \ilObjUser::_getPersonalPicturePath($ilias_user_id, "small", false);

        return strpos($personal_picturpath, 'data:image/svg+xml') === false;
    }

    public function saveEncodedPersonalPictureToUserProfile(int $ilias_user_id, string $encoded_image_string) : void
    {
        try {
            $tmp_file = \ilUtil::ilTempnam();
            imagepng(
                imagecreatefromstring(
                    base64_decode(
                        $encoded_image_string
                    )
                ),
                $tmp_file,
                0
            );
            \ilObjUser::_uploadPersonalPicture($tmp_file, $ilias_user_id);
        } catch (\Exception $e) {
            global $DIC;
            $DIC->logger()->root()->log('Evento Import: Exception on Photo Upload: ' . print_r($e, true), \ilLogLevel::ERROR);
        } finally {
            if (isset($tmp_file)) {
                unlink($tmp_file);
            }
        }
    }

    public function setMailPreferences(int $user_id, int $incoming_type)
    {
        $mail_options = new \ilMailOptions($user_id);
        $mail_options->setIncomingType($incoming_type);
        $mail_options->updateOptions();
    }
}
