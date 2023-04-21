<?php declare(strict_types=1);

namespace EventoImportLite\administration;

use EventoImportLite\config\event_auto_create\EventAutoCreateConfiguration;
use EventoImportLite\config\CronConfigForm;

class EventAutoCreationAdminGUI
{
    const FORM_BULK_ADD = 'crevlite_bulk_add';
    const FORM_EVENT_LIST = 'crevlite_event_list';

    private \ilEventoImportLitePlugin $plugin;
    private \ilCtrl $ctrl;
    private \ilSetting $settings;
    private EventAutoCreateConfiguration $event_auto_create_config;
    private $gui_obj;

    public function __construct($gui_obj, \ilEventoImportLitePlugin $plugin, \ilSetting $settings, \ilCtrl $ctrl)
    {
        $this->gui_obj = $gui_obj;
        $this->plugin = $plugin;
        $this->ctrl = $ctrl;

        $this->settings = $settings;
        $this->event_auto_create_config = new EventAutoCreateConfiguration($this->settings);
    }

    public function getHTMLForConfigGUI() : string
    {
        $form = $this->initAddCourseForm();

        $html = $form->getHTML();

        return $html;
    }

    public function saveListForAutoCreatedEvents()
    {
        $form = $this->initAddCourseForm();

        if($form->checkInput()) {
            $final_list = [];

            $text_input_list = $form->getInput(self::FORM_EVENT_LIST);
            foreach ($text_input_list as $event) {
                if (is_string($event)) {
                    $event = trim($event);
                    if (strlen($event) > 4)
                    $final_list[] = $event;
                }
            }

            $text_area_input = $form->getInput(self::FORM_BULK_ADD);
            $bulk_list = explode("\n", $text_area_input);
            foreach ($bulk_list as $event) {
                if (is_string($event)) {
                    $event = trim($event);
                    if (strlen($event) > 4 && !in_array($event, $final_list))
                        $final_list[] = $event;
                }
            }

            $this->event_auto_create_config->setAndSaveConfiguredEvents($final_list);

            return true;
        }

        return false;
    }

    private function initAddCourseForm() : \ilPropertyFormGUI
    {
        $form = new \ilPropertyFormGUI();
        $form->setFormAction($this->ctrl->getFormAction($this->gui_obj));
        $form->addCommandButton('save_event_auto_create_list', 'Save');
        $form->setTitle("List of Events, which should be created automatically");

        $bulk_add = new \ilTextAreaInputGUI("Add multiple courses (copy+paste)", self::FORM_BULK_ADD);
        $bulk_add->setInfo("If you want to add multiple courses, e.g. from an excel sheet, use this text field. On save, all courses will be added to the defined list below");
        $form->addItem($bulk_add);

        $event_auto_create_input = new \ilTextInputGUI("List of courses (same as in cron-config)", self::FORM_EVENT_LIST);
        $event_auto_create_input->setMulti(true, false, true);
        $event_auto_create_input->setValue($this->event_auto_create_config->getConfiguredEvents());
        $form->addItem($event_auto_create_input);

        return $form;
    }
}