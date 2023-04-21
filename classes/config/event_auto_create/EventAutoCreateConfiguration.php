<?php declare(strict_types=1);

namespace EventoImportLite\config\event_auto_create;

class EventAutoCreateConfiguration
{
    private const CONF_KEY_EVENT_AUTO_CREATE_LIST = 'crevlite_event_auto_create';

    private array $configured_events = [];

    private \ilSetting $settings;

    public function __construct(\ilSetting $settings)
    {
        $this->settings = $settings;

        $json = $this->settings->get(self::CONF_KEY_EVENT_AUTO_CREATE_LIST, null);

        if (is_null($json)) {
            $this->configured_events = [];
        } else {
            $configured_events = json_decode($json, true);

            if (!is_null($configured_events) && is_array($configured_events)) {
                $this->configured_events = $configured_events;
            } else {
                $this->configured_events = [];
            }
        }
    }

    public function getConfiguredEvents() : array
    {
        return $this->configured_events;
    }

    public function setAndSaveConfiguredEvents(array $configured_events)
    {
        $processed_list = [];
        foreach ($configured_events as $event) {
            if (is_string($event)) {
                $event = trim($event);
                if(strlen($event) > 3) {
                    $processed_list[] = $event;
                }
            }
        }

        $this->configured_events = $processed_list;
        $config_value = json_encode($this->configured_events);
        $this->settings->set(self::CONF_KEY_EVENT_AUTO_CREATE_LIST, $config_value);
    }
}