<?php declare(strict_types = 1);

namespace EventoImportLite\import\action\event;

use EventoImportLite\communication\api_models\EventoEvent;
use EventoImportLite\import\data_management\repository\model\IliasEventoParentEvent;
use EventoImportLite\import\data_management\MembershipManager;
use EventoImportLite\import\Logger;
use EventoImportLite\import\data_management\EventManager;

class CreateEventInParentEvent implements EventImportAction
{
    private EventoEvent $evento_event;
    private IliasEventoParentEvent $parent_event;
    private EventManager $event_manager;
    private MembershipManager $membership_manager;
    private Logger $logger;
    private int $log_info_code;

    public function __construct(EventoEvent $evento_event, IliasEventoParentEvent $parent_event, EventManager $event_manager, MembershipManager $membership_manager, Logger $logger)
    {
        $this->evento_event = $evento_event;
        $this->parent_event = $parent_event;
        $this->event_manager = $event_manager;
        $this->membership_manager = $membership_manager;
        $this->logger = $logger;

        $this->log_info_code = Logger::CREVENTO_MA_EVENT_IN_EXISTING_PARENT_EVENT_CREATED;
    }

    public function executeAction() : void
    {
        $ilias_event = $this->event_manager->createSubGroupEvent($this->evento_event, $this->parent_event);

        $this->membership_manager->syncMemberships($this->evento_event, $ilias_event);

        $this->logger->logEventImport(
            $this->log_info_code,
            $this->evento_event->getEventoId(),
            $ilias_event->getRefId(),
            ['api_data' => $this->evento_event->getDecodedApiData()]
        );
    }
}
