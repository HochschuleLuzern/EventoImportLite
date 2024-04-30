<?php declare(strict_types=1);

namespace EventoImportLite\config;

use ILIAS\DI\RBACServices;
use ilSelectInputGUI;
use ilNumberInputGUI;
use ilFormSectionHeaderGUI;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilUriInputGUI;
use ilCheckboxInputGUI;
use ilTextInputGUI;
use ilRadioOption;
use ilAuthUtils;
use EventoImportLite\config\locations\BaseLocationConfiguration;
use EventoImportLite\config\locations\RepositoryLocationSeeker;
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
    const LANG_HEADER_USER_ADDITIONAL_ROLE_MAPPING = 'additional_user_roles_mapping';
    const LANG_ROLE_DELETE_FROM_ADMIN_ON_REMOVAL = 'delete_from_admins_on_removal';
    const LANG_ROLE_DELETE_FROM_ADMIN_ON_REMOVAL_DESC = 'delete_from_admins_on_removal_desc';
    const LANG_ROLE_TRACK_REMOVAL_CUSTOM_FIELD = 'track_removal_custom_field';
    const LANG_ROLE_TRACK_REMOVAL_CUSTOM_FIELD_DESC = 'track_removal_custom_field_desc';
    const LANG_ROLE_MAPPING_TO = 'maps_to';
    const LANG_ROLE_MAPPING_TO_DESC = 'maps_to_desc';
    const LANG_HEADER_USER_FOLLOW_UP_ROLE_MAPPING = 'follow_up_role_mapping';
    const LANG_HEADER_USER_FOLLOW_UP_ROLE_MAPPING_DESC = 'follow_up_role_mapping_desc';
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
    const FORM_USER_GLOBAL_ROLE_ = 'crevlite_global_role_';
    const FORM_USER_EVENTO_ROLE_MAPPED_TO_ = 'crevlite_map_from_';
    const FORM_USER_EVENTO_ROLE_DELETE_FROM_ADMIN_ON_REMOVAL_ = 'crevlite_delete_admin_on_removal_from_';
    const FORM_USER_EVENTO_ROLE_TRACK_REMOVAL_CUSTOM_FIELD_FOR_ = 'crevlite_track_removal_custom_field_for_';
    const FORM_USER_FOLLOW_UP_ROLE_FOR_ = 'crevlite_follow_up_role_for_';
    const FORM_DEPARTEMTNS = 'crevlite_departments';
    const FORM_KINDS = 'crevlite_kinds';
    const FORM_EVENT_OBJECT_OWNER = 'crevlite_object_owner';
    const FORM_EVENT_OPT_OWNER_ROOT = 'crevlite_object_owner_root';
    const FORM_EVENT_OPT_OWNER_CUSTOM_USER = 'crevlite_object_owner_custom';
    const FORM_EVENT_OPT_OWNER_CUSTOM_ID = 'crevlite_object_owner_custom_id';
    const FORM_EVENT_AUTO_CREATE = 'crevlite_event_auto_create';
    const FORM_EVENT_REMOVE_PARTICIPANTS = 'crevlite_remove_participants';
    const CONF_EVENT_REMOVE_PARTICIPANTS = 'crevlite_remove_participants';

    private DefaultUserSettings $default_user_settings;
    private DefaultEventSettings $default_event_settings;
    private ImporterApiSettings $importer_api_settings;
    private BaseLocationConfiguration $event_locations;
    private RepositoryLocationSeeker $location_seeker;
    private \ilEventoImportLitePlugin $cp;
    private \ilLanguage $lng;
    private RBACServices $rbac;

    public function __construct(
        DefaultUserSettings $default_user_settings,
        DefaultEventSettings $default_event_settings,
        ImporterApiSettings $importer_api_settings,
        BaseLocationConfiguration $event_locations,
        RepositoryLocationSeeker $location_seeker,
        \ilEventoImportLitePlugin $plugin,
        \ilLanguage $lng,
        RBACServices $rbac
    ) {
        global $DIC;
        $this->default_user_settings = $default_user_settings;
        $this->default_event_settings = $default_event_settings;
        $this->importer_api_settings = $importer_api_settings;
        $this->event_locations = $event_locations;
        $this->location_seeker = $location_seeker;
        $this->lng = $lng;

        $this->cp = $plugin;
        $this->rbac = $rbac;
        $this->lng = $DIC->language();
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
        $ws_item->setValue($this->importer_api_settings->getUrl());
        $form->addItem($ws_item);

        $ws_item = new ilTextInputGUI(
            $this->cp->txt(self::LANG_API_AUTH_KEY),
            self::FORM_API_AUTH_KEY
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_AUTH_KEY_DESC));
        $ws_item->setRequired(false);
        $ws_item->setValue($this->importer_api_settings->getApikey());
        $form->addItem($ws_item);

        $ws_item = new ilTextInputGUI(
            $this->cp->txt(self::LANG_API_AUTH_SECRET),
            self::FORM_API_AUTH_SECRET
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_AUTH_SECRET_DESC));
        $ws_item->setRequired(false);
        $ws_item->setValue($this->importer_api_settings->getApiSecret());
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_PAGE_SIZE),
            self::FORM_API_PAGE_SIZE
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_PAGE_SIZE_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue((string) $this->importer_api_settings->getPageSize());
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_MAX_PAGES),
            self::FORM_API_MAX_PAGES
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_MAX_PAGES_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue((string) $this->importer_api_settings->getMaxPages());
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_TIMEOUT_AFTER_REQUEST),
            self::FORM_API_TIMEOUT_AFTER_REQUEST
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_TIMEOUT_AFTER_REQUEST_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue((string) $this->importer_api_settings->getTimeoutAfterRequest());
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_TIMEOUT_FAILED_REQUEST),
            self::FORM_API_TIMEOUT_FAILED_REQUEST
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_TIMEOUT_FAILED_REQUEST_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue((string) $this->importer_api_settings->getTimeoutFailedRequest());
        $form->addItem($ws_item);

        $ws_item = new ilNumberInputGUI(
            $this->cp->txt(self::LANG_API_MAX_RETRIES),
            self::FORM_API_MAX_RETRIES
        );
        $ws_item->setInfo($this->cp->txt(self::LANG_API_MAX_RETRIES_DESC));
        $ws_item->setRequired(true);
        $ws_item->allowDecimals(false);
        $ws_item->setValue((string) $this->importer_api_settings->getMaxRetries());
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
        $auth_modes = \ilAuthUtils::_getActiveAuthModes();
        $options = [];
        foreach ($auth_modes as $auth_name => $auth_key) {
            if ($auth_name == 'default') {
                $name = $this->lng->txt('auth_' . $auth_name) . " (" . $this->lng->txt('auth_' . \ilAuthUtils::_getAuthModeName($auth_key)) . ")";
            } else {
                $name = \ilAuthUtils::getAuthModeTranslation($auth_key, $auth_name);
            }
            $options[$auth_name] = $name;
        }
        $ws_item->setOptions($options);
        $ws_item->setValue($this->default_user_settings->getAuthMode());
        $form->addItem($ws_item);

        $section = new ilFormSectionHeaderGUI();
        $section->setTitle($this->cp->txt(self::LANG_HEADER_USER_ADDITIONAL_ROLE_MAPPING));
        $form->addItem($section);

        $global_roles = $this->rbac->review()->getGlobalRoles();
        $role_mapping = array_flip($this->default_user_settings->getEventoCodeToIliasRoleMapping());
        $track_removal_custom_fields_mapping = $this->default_user_settings->getTrackRemovalCustomFieldsMapping();
        $delete_from_admin_when_removed_role_array = $this->default_user_settings->getDeleteFromAdminWhenRemovedFromRoleMapping();

        $custom_fields = \ilUserDefinedFields::_getInstance();
        $available_custom_fields = [0 => '--'];
        foreach ($custom_fields->getDefinitions() as $definition) {
            if ($definition['field_type'] === (string) UDF_TYPE_TEXT) {
                $available_custom_fields[$definition['field_id']] = $definition['field_name'];
            }
        }


        foreach ($global_roles as $role_id) {
            $role_title = \ilObject::_lookupTitle($role_id);
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

            $track_role_removal_custom_field = new \ilSelectInputGUI(
                $this->cp->txt(self::LANG_ROLE_TRACK_REMOVAL_CUSTOM_FIELD),
                self::FORM_USER_EVENTO_ROLE_TRACK_REMOVAL_CUSTOM_FIELD_FOR_ . $role_id
            );
            $track_role_removal_custom_field->setOptions($available_custom_fields);
            $track_role_removal_custom_field->setInfo($this->cp->txt(self::LANG_ROLE_TRACK_REMOVAL_CUSTOM_FIELD_DESC));
            $track_role_removal_custom_field->setValue($track_removal_custom_fields_mapping[$role_id] ?? 0);


            $delete_from_admin_input = new \ilCheckboxInputGUI(
                $this->cp->txt(self::LANG_ROLE_DELETE_FROM_ADMIN_ON_REMOVAL),
                self::FORM_USER_EVENTO_ROLE_DELETE_FROM_ADMIN_ON_REMOVAL_ . $role_id
            );
            $delete_from_admin_input->setInfo($this->cp->txt(self::LANG_ROLE_DELETE_FROM_ADMIN_ON_REMOVAL_DESC));

            if (isset($role_mapping[$role_id])) {
                $ws_item->setChecked(true);
                $mapping_input->setValue((string) $role_mapping[$role_id]);
                $delete_from_admin_input->setChecked(in_array($role_id, $delete_from_admin_when_removed_role_array));
            } else {
                $ws_item->setChecked(false);
            }

            $ws_item->addSubItem($mapping_input);
            $ws_item->addSubItem($track_role_removal_custom_field);
            $ws_item->addSubItem($delete_from_admin_input);



            $form->addItem($ws_item);
        }

        $section = new ilFormSectionHeaderGUI();
        $section->setTitle($this->cp->txt(self::LANG_HEADER_USER_FOLLOW_UP_ROLE_MAPPING));
        $section->setInfo($this->cp->txt(self::LANG_HEADER_USER_FOLLOW_UP_ROLE_MAPPING_DESC));
        $form->addItem($section);

        $follow_up_role_mapping = $this->default_user_settings->getFollowUpRoleMapping();
        foreach (array_keys($role_mapping) as $role_id) {
            $role_title = \ilObject::_lookupTitle($role_id);
            $options = [
                0 => $this->lng->txt('none')
            ];
            foreach($global_roles as $global_role_id) {
                if ($global_role_id === $role_id) {
                    continue;
                }
                $options[$global_role_id] = \ilObject::_lookupTitle($global_role_id);
            }
            $ws_item = new \ilSelectInputGUI(
                $role_title,
                self::FORM_USER_FOLLOW_UP_ROLE_FOR_ . "$role_id"
            );
            $ws_item->setOptions($options);
            $ws_item->setValue(0);
            if (array_key_exists($role_id, $follow_up_role_mapping)) {
                $ws_item->setValue($follow_up_role_mapping[$role_id]);
            }
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

        $departments = new ilTextInputGUI($this->cp->txt(self::LANG_DEPARTMENTS), self::FORM_DEPARTEMTNS);
        $departments->setMulti(true, false, true);
        $departments->setValue($this->event_locations->getDepartmentLocationList());
        $form->addItem($departments);

        $kinds = new ilTextInputGUI($this->cp->txt(self::LANG_KINDS), self::FORM_KINDS);
        $kinds->setMulti(true, false, true);
        $kinds->setValue($this->event_locations->getKindLocationList());
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
        $remove_participants->setChecked($this->default_event_settings->get(self::CONF_EVENT_REMOVE_PARTICIPANTS, '1') == true);
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
        $custom_user_id->setValue((string) $this->default_event_settings->getDefaultObjectOwnerId());
        $option->addSubItem($custom_user_id);

        $radio->addOption($option);
        $radio_value = $this->default_event_settings->getDefaultObjectOwnerId() === SYSTEM_USER_ID ?
            self::FORM_EVENT_OPT_OWNER_ROOT : self::FORM_EVENT_OPT_OWNER_CUSTOM_USER;
        $radio->setValue($radio_value);

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
        $this->importer_api_settings->setUrl($form->getInput(self::FORM_API_URI));
        $this->importer_api_settings->setApiKey($form->getInput(self::FORM_API_AUTH_KEY));
        $this->importer_api_settings->setApiSecret($form->getInput(self::FORM_API_AUTH_SECRET));
        $this->importer_api_settings->setPageSize(
            intval($form->getInput(self::FORM_API_PAGE_SIZE))
        );
        $this->importer_api_settings->setMaxPages(
            intval($form->getInput(self::FORM_API_MAX_PAGES))
        );
        $this->importer_api_settings->setTimeoutAfterRequest(
            intval($form->getInput(self::FORM_API_TIMEOUT_AFTER_REQUEST))
        );
        $this->importer_api_settings->setTimeoutFailedRequest(
            intval($form->getInput(self::FORM_API_TIMEOUT_FAILED_REQUEST))
        );
        $this->importer_api_settings->setMaxRetries(
            intval($form->getInput(self::FORM_API_MAX_RETRIES))
        );
        $this->importer_api_settings->saveCurrentConfigurationToSettings();

        return true;
    }

    public function saveUserConfigFromForm(ilPropertyFormGUI $form) : bool
    {
        $this->default_user_settings->setAuthMode($form->getInput(self::FORM_USER_AUTH_MODE));
        $this->default_user_settings->saveCurrentConfigurationToSettings();

        $global_roles = $this->rbac->review()->getGlobalRoles();
        $role_mapping = [];
        $delete_admin_on_removal_from_role = [];
        $follow_up_role_mapping = [];

        foreach ($global_roles as $role_id) {
            $check_box = $form->getInput(self::FORM_USER_GLOBAL_ROLE_ . $role_id);
            if ($check_box !== '1') {
                continue;
            }

            $mapped_role_input = $form->getInput(self::FORM_USER_EVENTO_ROLE_MAPPED_TO_ . $role_id);
            $track_removal_custom_field = $form->getInput(self::FORM_USER_EVENTO_ROLE_TRACK_REMOVAL_CUSTOM_FIELD_FOR_ . $role_id);
            $delete_from_admin_on_removal = $form->getInput(self::FORM_USER_EVENTO_ROLE_DELETE_FROM_ADMIN_ON_REMOVAL_ . $role_id);

            if (in_array($mapped_role_input, $role_mapping)) {
                return false;
            }

            $role_mapping[$mapped_role_input] = $role_id;

            $follow_up_role_mapping[$role_id] = intval($form->getInput(self::FORM_USER_FOLLOW_UP_ROLE_FOR_ . $role_id) ?? '0');

            $track_removal_custom_field_mapping[$role_id] = $track_removal_custom_field;

            if ($delete_from_admin_on_removal === '1') {
                $delete_admin_on_removal_from_role[] = $role_id;
            }
        }

        $this->default_user_settings->setEventoCodeToIliasRoleMapping($role_mapping);
        $this->default_user_settings->setDeleteFromAdminWhenRemovedFromRoleMapping($delete_admin_on_removal_from_role);
        $this->default_user_settings->setTrackRemovalCustomFieldsMapping($track_removal_custom_field_mapping);
        $this->default_user_settings->setFollowUpRoleMapping($follow_up_role_mapping);
        $this->default_user_settings->saveCurrentConfigurationToSettings();

        return true;
    }

    public function saveEventLocationConfigFromForm(ilPropertyFormGUI $form) : bool
    {

        $this->event_locations->setDepartmentLocationList($form->getInput(self::FORM_DEPARTEMTNS));
        $this->event_locations->setKindLocationList($form->getInput(self::FORM_KINDS));
        $this->event_locations->saveCurrentConfigurationToSettings();
        return true;
    }

    public function saveEventConfigFromForm(ilPropertyFormGUI $form) : bool
    {
        $this->default_event_settings->set(self::CONF_EVENT_REMOVE_PARTICIPANTS, $form->getInput(self::FORM_EVENT_REMOVE_PARTICIPANTS));

        $event_auto_create = new EventAutoCreateConfiguration($this->settings);
        $event_auto_create->setAndSaveConfiguredEvents($form->getInput(self::FORM_EVENT_AUTO_CREATE));

        $input_object_owner = $form->getInput(self::FORM_EVENT_OBJECT_OWNER);
        switch ($input_object_owner) {
            case self::FORM_EVENT_OPT_OWNER_ROOT:
                $this->default_event_settings->setDefaultObjectOwnerId(6);
                break;

            case self::FORM_EVENT_OPT_OWNER_CUSTOM_USER:
                $this->default_event_settings->setDefaultObjectOwnerId(
                    intval($form->getInput(self::FORM_EVENT_OPT_OWNER_CUSTOM_ID))
                );
                break;
        }

        $this->default_event_settings->saveCurrentConfigurationToSettings();
        return true;
    }
}
