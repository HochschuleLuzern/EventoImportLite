<?php declare(strict_types = 1);

namespace EventoImportLite\import\action\event;

use EventoImportLite\communication\api_models\EventoEvent;
use EventoImportLite\import\data_management\repository\model\IliasEventoEvent;
use EventoImportLite\import\data_management\MembershipManager;
use EventoImportLite\import\Logger;
use EventoImportLite\import\data_management\EventManager;

class UpdateExistingEvent implements EventImportAction
{
    private EventoEvent $evento_event;
    private IliasEventoEvent $ilias_event;
    private EventManager $event_manager;
    private MembershipManager $membership_manager;
    private Logger $logger;
    private int $log_code;

    public function __construct(EventoEvent $evento_event, IliasEventoEvent $ilias_event, EventManager $event_manager, MembershipManager $membership_manager, Logger $logger)
    {
        $this->evento_event = $evento_event;
        $this->ilias_event = $ilias_event;
        $this->event_manager = $event_manager;
        $this->membership_manager = $membership_manager;
        $this->logger = $logger;
        $this->log_code = Logger::CREVENTO_MA_SUBS_UPDATED;
    }

    public function executeAction() : void
    {
        $this->event_manager->registerEventoEventAsDelivered($this->evento_event);

        $this->membership_manager->syncMemberships($this->evento_event, $this->ilias_event);

        $this->logger->logEventImport(
            $this->log_code,
            $this->evento_event->getEventoId(),
            $this->ilias_event->getRefId(),
            ['api_data' => $this->evento_event->getDecodedApiData()]
        );
    }
}
