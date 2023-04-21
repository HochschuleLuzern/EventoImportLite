<?php declare(strict_types=1);

namespace EventoImportLite\config;

use ILIAS\DI\RBACServices;
use ilSelectInputGUI;
use ilNumberInputGUI;
use ilSetting;
use ilFormSectionHeaderGUI;
use ilPropertyFormGUI;
use ilLDAPServer;
use ilRadioGroupInputGUI;
use ilTextAreaInputGUI;
use ilUriInputGUI;
use ilCheckboxInputGUI;
use ilTextInputGUI;
use ilRadioOption;
use ilAuthUtils;
use ilObject;
use EventoImportLite\config\locations\BaseLocationConfiguration;
use EventoImportLite\config\event_auto_create\EventAutoCreateConfiguration;

/**
 * Class ilEventoImportLiteCronConfig
 * This class is used to separate the config part for the cron-job from the executing class (ilEventoImportLiteImport)
 */
class CronConfigForm
{
    const LANG_HEADER_API_SETTINGS = 'api_settings';
    const LANG_API_URI = 'api_uri';
    const LANG_API_URI_DESC = 'api_uri_desc';
    const LANG_API_AUTH_KEY = 'auth_key';
    const LANG_API_AUTH_KEY_DESC = 'auth_key_desc';
    const LANG_API_AUTH_SECRET = 'auth_secret';
    const LANG_API_AUTH_SECRET_DESC = 'auth_secret_desc';
    const LANG_API_PAGE_SIZE = 'api_page_size';
    const LANG_API_PAGE_SIZE_DESC = 'api_page_size_desc';
    const LANG_API_MAX_PAGES = 'api_max_pages';
    const LANG_API_MAX_PAGES_DESC = 'api_max_pages_desc';
    const LANG_API_TIMEOUT_AFTER_REQUEST = 'api_timeout_after_request';
    const LANG_API_TIMEOUT_AFTER_REQUEST_DESC = 'api_timeout_after_request_desc';
    const LANG_API_TIMEOUT_FAILED_REQUEST = 'api_timeout_failed_request';
    const LANG_API_TIMEOUT_FAILED_REQUEST_DESC = 'api_timeout_failed_request_desc';
    const LANG_API_MAX_RETRIES = 'api_max_retries';
    const LANG_API_MAX_RETRIES_DESC = 'api_max_retries_desc';
    const LANG_HEADER_USER_SETTINGS = 'user_import_settings';
    const LANG_USER_AUTH_MODE = 'user_auth_mode';
    const LANG_USER_AUTH_MODE_DESC = 'user_auth_mode_desc';
    const LANG_DEFAULT_USER_ROLE = 'default_user_role';
    const LANG_DEFAULT_USER_ROLE_DESC = 'default_user_role_desc';
    const LANG_HEADER_USER_ADDITIONAL_ROLE_MAPPING = 'additional_user_roles_mapping';
    const LANG_ROLE_MAPPING_TO = 'maps_to';
    const LANG_ROLE_MAPPING_TO_DESC = 'maps_to_desc';
    const LANG_HEADER_EVENT_LOCATIONS = 'location_settings';
    const LANG_DEPARTMENTS = 'location_departments';
    const LANG_KINDS = 'location_kinds';
    const LANG_HEADER_EVENT_SETTINGS = 'event_import_settings';
    const LANG_EVENT_OBJECT_OWNER = 'object_owner';
    const LANG_EVENT_OBJECT_OWNER_DESC = 'object_owner_desc';
    const LANG_EVENT_OPT_OWNER_ROOT = 'owner_root_user';
    const LANG_EVENT_OPT_OWNER_CUSTOM_USER = 'owner_custom_user';
    const LANG_EVENT_OPT_OWNER_CUSTOM_ID = 'object_owner_id';
    const LANG_EVENT_AUTO_CREATE = 'event_auto_create_input';
    const LANG_EVENT_AUTO_CREATE_DESC = 'event_auto_create_input_desc';
    const LANG_EVENT_REMOVE_PARTICIPANTS = 'event_remove_participants';
    const LANG_EVENT_REMOVE_PARTICIPANTS_DESC = 'event_remove_participants_desc';

    const FORM_API_URI = 'crevlite_api_uri';
    const FORM_API_AUTH_KEY = 'crevlite_api_auth_key';
    const FORM_API_AUTH_SECRET = 'crevlite_api_auth_secret';
    const FORM_API_PAGE_SIZE = 'crevlite_api_page_size';
    const FORM_API_MAX_PAGES = 'crevlite_api_max_pages';
    const FORM_API_TIMEOUT_AFTER_REQUEST = 'crevlite_api_timeout_after_request';
    const FORM_API_TIMEOUT_FAILED_REQUEST = 'crevlite_api_timeout_failed_request';
    const FORM_API_MAX_RETRIES = 'crevlite_api_max_retries';
    const FORM_USER_AUTH_MODE = 'crevlite_user_auth_mode';
    const FORM_USER_IMPORT_ACC_DURATION = 'crevlite_user_import_acc_duration';
    const FORM_USER_MAX_ACC_DURATION = 'crevlite_user_max_acc_duration';
    const FORM_USER_CHANGED_MAIL_SUBJECT = 'crevlite_user_changed_mail_subject';
    const FORM_USER_CHANGED_MAIL_BODY = 'crevlite_user_changed_mail_body';
    const FORM_DEFAULT_USER_ROLE = 'crevlite_default_user_role';
    const FORM_USER_GLOBAL_ROLE_ = 'crevlite_global_role_';
    const FORM_USER_EVENTO_ROLE_MAPPED_TO_ = 'crevlite_map_from_';
    const FORM_DEPARTEMTNS = 'crevlite_departments';
    const FORM_KINDS = 'crevlite_kinds';
    const FORM_EVENT_OBJECT_OWNER = 'crevlite_object_owner';
    const FORM_EVENT_OPT_OWNER_ROOT = 'crevlite_object_owner_root';
    const FORM_EVENT_OPT_OWNER_CUSTOM_USER = 'crevlite_object_owner_custom';
    const FORM_EVENT_OPT_OWNER_CUSTOM_ID = 'crevlite_object_owner_custom_id';
    const FORM_EVENT_AUTO_CREATE = 'crevlite_event_auto_create';
    const FORM_EVENT_REMOVE_PARTICIPANTS = 'crevlite_remove_participants';

    const CONF_API_URI = 'crevlite_api_uri';
    const CONF_API_AUTH_KEY = 'crevlite_api_auth_key';
    const CONF_API_AUTH_SECRET = 'crevlite_api_auth_secret';
    const CONF_API_PAGE_SIZE = 'crevlite_api_page_size';
    const CONF_API_MAX_PAGES = 'crevlite_api_max_pages';
    const CONF_API_TIMEOUT_AFTER_REQUEST = 'crevlite_api_timeout_after_request';
    const CONF_API_TIMEOUT_FAILED_REQUEST = 'crevlite_api_timeout_failed_request';
    const CONF_API_MAX_RETRIES = 'crevlite_api_max_retries';
    const CONF_USER_AUTH_MODE = 'crevlite_ilias_auth_mode';
    const CONF_USER_IMPORT_ACC_DURATION = 'crevlite_user_import_acc_duration';
    const CONF_USER_MAX_ACC_DURATION = 'crevlite_user_max_acc_duration';
    const CONF_USER_CHANGED_MAIL_SUBJECT = 'crevlite_email_account_changed_subject';
    const CONF_USER_CHANGED_MAIL_BODY = 'crevlite_email_account_changed_body';
    const CONF_USER_STUDENT_ROLE_ID = 'crevlite_student_role_id';
    const CONF_DEFAULT_USER_ROLE = 'crevlite_default_user_role';
    const CONF_ROLES_ILIAS_EVENTO_MAPPING = 'crevlite_roles_ilias_evento_mapping';
    const CONF_EVENT_OWNER_ID = 'crevlite_object_owner_id';
    const CONF_EVENT_OBJECT_OWNER = 'crevlite_object_owner';
    const CONF_EVENT_REMOVE_PARTICIPANTS = 'crevlite_remove_participants';

    private \ilSetting $settings;
    private \ilEventoImportLitePlugin $cp;
    private RBACServices $rbac;

    public function __construct(ilSetting $settings, \ilEventoImportLitePlugin $plugin, RBACServices $rbac)
    {
        $this->settings = $settings;
        $this->cp = $plugin;
        $this->rbac = $rbac;
    }

    public function fillFormWithApiConfig(ilPropertyFormGUI $form)
    {
        /***************************
         * API Settings
         ***************************/
        $header = new ilFormSectionHeaderGUI();
        $header->setTitle($this->cp->txt(self::LANG_HEADER_API_SETTINGS));
        $form->addItem($header);

        $ws_item = new ilUriInputGUI(
            $this->cp->txt(self::LANG_API_URI),
            self::FORM_API_URI
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_URI_DESC));
        $ws_item->setRequired(true);
        $ws_item->setValue($this->settings->get(self::CONF_API_URI, ''));
        $form->addItem($ws_item);

        $ws_item = new ilTextInputGUI(
            $this->cp->txt(self::LANG_API_AUTH_KEY),
            self::FORM_API_AUTH_KEY
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_AUTH_KEY_DESC));
        $ws_item->setRequired(false);
        $ws_item->setValue($this->settings->get(self::CONF_API_AUTH_KEY, ''));
        $form->addItem($ws_item);

        $ws_item = new ilTextInputGUI(
            $this->cp->txt(self::LANG_API_AUTH_SECRET),
            self::FORM_API_AUTH_SECRET
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_AUTH_SECRET_DESC));
        $ws_item->setRequired(false);
        $ws_item->setValue($this->settings->get(self::CONF_API_AUTH_SECRET, ''));
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_PAGE_SIZE),
            self::FORM_API_PAGE_SIZE
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_PAGE_SIZE_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue($this->settings->get(self::CONF_API_PAGE_SIZE, '0'));
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_MAX_PAGES),
            self::FORM_API_MAX_PAGES
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_MAX_PAGES_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue($this->settings->get(self::CONF_API_MAX_PAGES, '0'));
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_TIMEOUT_AFTER_REQUEST),
            self::FORM_API_TIMEOUT_AFTER_REQUEST
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_TIMEOUT_AFTER_REQUEST_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue($this->settings->get(self::CONF_API_TIMEOUT_AFTER_REQUEST, ''));
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_TIMEOUT_FAILED_REQUEST),
            self::FORM_API_TIMEOUT_FAILED_REQUEST
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_TIMEOUT_FAILED_REQUEST_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue($this->settings->get(self::CONF_API_TIMEOUT_FAILED_REQUEST, ''));
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_MAX_RETRIES),
            self::FORM_API_MAX_RETRIES
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_MAX_RETRIES_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue($this->settings->get(self::CONF_API_MAX_RETRIES, ''));
        $form->addItem($ws_item);
    }

    public function fillFormWithUserImportConfig(ilPropertyFormGUI $form)
    {
        /***************************
         * User Import Settings
         ***************************/
        $header = new ilFormSectionHeaderGUI();
        $header->setTitle($this->cp->txt(self::LANG_HEADER_USER_SETTINGS));
        $form->addItem($header);

        $ws_item = new ilSelectInputGUI(
            $this->cp->txt(self::LANG_USER_AUTH_MODE),
            self::FORM_USER_AUTH_MODE
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_USER_AUTH_MODE_DESC));
        $auth_modes = ilAuthUtils::_getAllAuthModes();
        $options = [];
        foreach ($auth_modes as $auth_mode => $auth_name) {
            if (ilLDAPServer::isAuthModeLDAP($auth_mode)) {
                $server = ilLDAPServer::getInstanceByServerId(ilLDAPServer::getServerIdByAuthMode($auth_mode));
                if ($server->isActive()) {
                    $options[$auth_name] = $auth_name;
                }
            } else {
                if ($this->settings->get($auth_name . '_active') || $auth_mode == AUTH_LOCAL) {
                    $options[$auth_name] = $auth_name;
                }
            }
        }
        $ws_item->setOptions($options);
        $ws_item->setValue($this->settings->get(self::CONF_USER_AUTH_MODE));
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_DEFAULT_USER_ROLE),
            self::FORM_DEFAULT_USER_ROLE
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_DEFAULT_USER_ROLE_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue($this->settings->get(self::CONF_DEFAULT_USER_ROLE));
        $form->addItem($ws_item);

        $section = new ilFormSectionHeaderGUI();
        $section->setTitle($this->cp->txt(self::LANG_HEADER_USER_ADDITIONAL_ROLE_MAPPING));
        $form->addItem($section);

        $global_roles = $this->rbac->review()->getGlobalRoles();
        $globale_roles_settings = $this->settings->get(self::CONF_ROLES_ILIAS_EVENTO_MAPPING, null);
        $role_mapping = !is_null($globale_roles_settings) ? json_decode($globale_roles_settings, true) : null;
        if (is_null($role_mapping) || !is_array($role_mapping)) {
            $role_mapping = [];
        }

        foreach ($global_roles as $role_id) {
            $role_title = ilObject::_lookupTitle($role_id);
            $ws_item = new ilCheckboxInputGUI(
                $role_title,
                self::FORM_USER_GLOBAL_ROLE_ . "$role_id"
            );
            $ws_item->setValue('1');

            $mapping_input = new ilNumberInputGUI(
                $this->cp->txt(self::LANG_ROLE_MAPPING_TO),
                self::FORM_USER_EVENTO_ROLE_MAPPED_TO_ . $role_id
            );
            $mapping_input->allowDecimals(false);
            $mapping_input->setRequired(true);
            $mapping_desc = sprintf($this->cp->txt(self::LANG_ROLE_MAPPING_TO_DESC), $role_title);
            $mapping_input->setInfo($mapping_desc);

            if (isset($role_mapping[$role_id])) {
                $ws_item->setChecked(true);
                $mapping_input->setValue($role_mapping[$role_id]);
            } else {
                $ws_item->setChecked(false);
            }

            $ws_item->addSubItem($mapping_input);
            $form->addItem($ws_item);
        }
    }

    public function fillFormWithEventLocationConfig(ilPropertyFormGUI $form)
    {
        /***************************
         * Event Location Settings
         ***************************/
        $header = new ilFormSectionHeaderGUI();
        $header->setTitle($this->cp->txt(self::LANG_HEADER_EVENT_LOCATIONS));
        $form->addItem($header);

        $locations = new BaseLocationConfiguration($this->settings);

        $departments = new ilTextInputGUI($this->cp->txt(self::LANG_DEPARTMENTS), self::FORM_DEPARTEMTNS);
        $departments->setMulti(true, false, true);
        $departments->setValue($locations->getDepartmentLocationList());
        $form->addItem($departments);

        $kinds = new ilTextInputGUI($this->cp->txt(self::LANG_KINDS), self::FORM_KINDS);
        $kinds->setMulti(true, false, true);
        $kinds->setValue($locations->getKindLocationList());
        $form->addItem($kinds);
    }

    public function fillFormWithEventConfig(ilPropertyFormGUI $form)
    {
        /***************************
         * Event Import Settings
         ***************************/
        $header = new ilFormSectionHeaderGUI();
        $header->setTitle($this->cp->txt(self::LANG_HEADER_EVENT_SETTINGS));
        $form->addItem($header);


        $remove_participants = new \ilCheckboxInputGUI(
             $this->cp->txt(self::LANG_EVENT_REMOVE_PARTICIPANTS),
            self::FORM_EVENT_REMOVE_PARTICIPANTS
        );
        $remove_participants->setInfo($this->cp->txt(self::LANG_EVENT_REMOVE_PARTICIPANTS_DESC));
        $remove_participants->setChecked($this->settings->get(self::CONF_EVENT_REMOVE_PARTICIPANTS, '1') == true);
        $form->addItem($remove_participants);

        $radio = new ilRadioGroupInputGUI(
            $this->cp->txt(self::LANG_EVENT_OBJECT_OWNER),
            self::FORM_EVENT_OBJECT_OWNER //'crevlite_object_owner'
        );
        $radio->setInfo($this->cp->txt(self::LANG_EVENT_OBJECT_OWNER_DESC));

        $option = new ilRadioOption(
            $this->cp->txt(self::LANG_EVENT_OPT_OWNER_ROOT),
            self::FORM_EVENT_OPT_OWNER_ROOT
        );
        $radio->addOption($option);

        $option = new ilRadioOption(
            $this->cp->txt(self::LANG_EVENT_OPT_OWNER_CUSTOM_USER),
            self::FORM_EVENT_OPT_OWNER_CUSTOM_USER //'custom_user'
        );
        $custom_user_id = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_EVENT_OPT_OWNER_CUSTOM_ID),
            self::FORM_EVENT_OPT_OWNER_CUSTOM_ID// 'crevlite_object_owner_id'
        );
        $custom_user_id->allowDecimals(false);
        $custom_user_id->setValue($this->settings->get(self::CONF_EVENT_OWNER_ID, ''));
        $option->addSubItem($custom_user_id);

        $radio->addOption($option);
        $radio->setValue($this->settings->get(self::CONF_EVENT_OBJECT_OWNER, self::FORM_EVENT_OPT_OWNER_ROOT));

        $form->addItem($radio);

        $auto_create_config = new EventAutoCreateConfiguration($this->settings);

        $event_auto_create_input = new ilTextInputGUI($this->cp->txt(self::LANG_EVENT_AUTO_CREATE), self::FORM_EVENT_AUTO_CREATE);
        $event_auto_create_input->setInfo($this->cp->txt(self::LANG_EVENT_AUTO_CREATE_DESC));
        $event_auto_create_input->setMulti(true, false, true);
        $event_auto_create_input->setValue($auto_create_config->getConfiguredEvents());
        $form->addItem($event_auto_create_input);
    }

    public function saveApiConfigFromForm(ilPropertyFormGUI $form) : bool
    {
        $form_input_correct = true;

        $this->getTextInputAndSaveIfNotNull($form, self::FORM_API_URI, self::CONF_API_URI);
        $this->getTextInputAndSaveIfNotNull($form, self::FORM_API_AUTH_KEY, self::CONF_API_AUTH_KEY);
        $this->getTextInputAndSaveIfNotNull($form, self::FORM_API_AUTH_SECRET, self::CONF_API_AUTH_SECRET);
        $this->getIntegerInputAndSaveIfNotNull($form, self::FORM_API_PAGE_SIZE, self::CONF_API_PAGE_SIZE);
        $this->getIntegerInputAndSaveIfNotNull($form, self::FORM_API_MAX_PAGES, self::CONF_API_MAX_PAGES);
        $this->getIntegerInputAndSaveIfNotNull(
            $form,
            self::FORM_API_TIMEOUT_AFTER_REQUEST,
            self::CONF_API_TIMEOUT_AFTER_REQUEST
        );
        $this->getIntegerInputAndSaveIfNotNull(
            $form,
            self::FORM_API_TIMEOUT_FAILED_REQUEST,
            self::CONF_API_TIMEOUT_FAILED_REQUEST
        );
        $this->getIntegerInputAndSaveIfNotNull($form, self::FORM_API_MAX_RETRIES, self::CONF_API_MAX_RETRIES);

        return $form_input_correct;
    }

    public function saveUserConfigFromForm(ilPropertyFormGUI $form) : bool
    {
        $form_input_correct = true;

        if ($form->getInput(self::FORM_USER_AUTH_MODE) != null) {
            $this->settings->set(self::CONF_USER_AUTH_MODE, $form->getInput(self::FORM_USER_AUTH_MODE));
        }

        $this->getIntegerInputAndSaveIfNotNull(
            $form,
            self::FORM_USER_IMPORT_ACC_DURATION,
            self::CONF_USER_IMPORT_ACC_DURATION
        );
        $this->getIntegerInputAndSaveIfNotNull(
            $form,
            self::FORM_USER_MAX_ACC_DURATION,
            self::CONF_USER_MAX_ACC_DURATION
        );
        $this->getTextInputAndSaveIfNotNull(
            $form,
            self::FORM_USER_CHANGED_MAIL_SUBJECT,
            self::CONF_USER_CHANGED_MAIL_SUBJECT
        );
        $this->getTextInputAndSaveIfNotNull(
            $form,
            self::FORM_USER_CHANGED_MAIL_BODY,
            self::CONF_USER_CHANGED_MAIL_BODY
        );

        $this->getIntegerInputAndSaveIfNotNull($form, self::FORM_DEFAULT_USER_ROLE, self::CONF_DEFAULT_USER_ROLE);

        $global_roles = $this->rbac->review()->getGlobalRoles();
        $role_mapping = [];
        $save_global_role_mapping = true;

        foreach ($global_roles as $role_id) {
            $check_box = $form->getInput(self::FORM_USER_GLOBAL_ROLE_ . $role_id);
            if ($check_box == '1') {
                $mapped_role_input = (int) $form->getInput(self::FORM_USER_EVENTO_ROLE_MAPPED_TO_ . $role_id);
                if (!is_null($mapped_role_input) && !in_array($mapped_role_input, $role_mapping)) {
                    $role_mapping[$role_id] = $mapped_role_input;
                } elseif (in_array($mapped_role_input, $role_mapping)) {
                    $form_input_correct = false;
                    $save_global_role_mapping = false;
                }
            }
        }
        if ($save_global_role_mapping) {
            $this->settings->set(self::CONF_ROLES_ILIAS_EVENTO_MAPPING, json_encode($role_mapping));
        }

        return $form_input_correct;
    }

    public function saveEventLocationConfigFromForm(ilPropertyFormGUI $form) : bool
    {
        $form_input_correct = true;

        $location_settings = new BaseLocationConfiguration($this->settings);

        $location_settings->setDepartmentLocationList($form->getInput(self::FORM_DEPARTEMTNS));
        $location_settings->setKindLocationList($form->getInput(self::FORM_KINDS));

        $location_settings->saveCurrentConfigurationToSettings();

        return $form_input_correct;
    }

    public function saveEventConfigFromForm(ilPropertyFormGUI $form) : bool
    {
        $form_input_correct = true;

        $this->settings->set(self::CONF_EVENT_REMOVE_PARTICIPANTS, $form->getInput(self::FORM_EVENT_REMOVE_PARTICIPANTS));

        $event_auto_create = new EventAutoCreateConfiguration($this->settings);
        $event_auto_create->setAndSaveConfiguredEvents($form->getInput(self::FORM_EVENT_AUTO_CREATE));

        $input_object_owner = $form->getInput(self::FORM_EVENT_OBJECT_OWNER);
        switch ($input_object_owner) {
            case self::FORM_EVENT_OPT_OWNER_ROOT:
                $this->settings->set(self::CONF_EVENT_OBJECT_OWNER, self::FORM_EVENT_OPT_OWNER_ROOT);
                $this->settings->set(self::CONF_EVENT_OWNER_ID, 6);
                break;

            case self::FORM_EVENT_OPT_OWNER_CUSTOM_USER:
                $input_user_id = (int) $form->getInput(self::FORM_EVENT_OPT_OWNER_CUSTOM_ID);
                $this->settings->set(self::CONF_EVENT_OBJECT_OWNER, self::FORM_EVENT_OPT_OWNER_CUSTOM_USER);
                $this->settings->set(self::CONF_EVENT_OWNER_ID, $input_user_id);
                break;
        }

        return $form_input_correct;
    }

    private function getTextInputAndSaveIfNotNull(ilPropertyFormGUI $form, string $input_field, string $conf_key)
    {
        $value = $form->getInput($input_field);
        if (!is_null($value)) {
            $this->settings->set($conf_key, $value);
        }
    }

    private function getIntegerInputAndSaveIfNotNull(ilPropertyFormGUI $form, string $input_field, string $conf_key)
    {
        $value = (int) $form->getInput($input_field);
        if (!is_null($value)) {
            $this->settings->set($conf_key, $value);
        }
    }
}
