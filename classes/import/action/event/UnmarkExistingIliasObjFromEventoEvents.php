<?php declare(strict_types=1);

namespace EventoImportLite\import\action\event;

use EventoImportLite\import\data_management\repository\model\IliasEventoEvent;
use EventoImportLite\import\Logger;
use EventoImportLite\import\data_management\EventManager;

class UnmarkExistingIliasObjFromEventoEvents implements EventDeleteAction
{
    private IliasEventoEvent $ilias_evento_event;
    private EventManager $event_manager;
    private Logger $logger;

    private int $log_info_code;

    public function __construct(
        IliasEventoEvent $ilias_evento_event,
        EventManager $event_manager,
        Logger $logger
    ) {
        $this->ilias_evento_event = $ilias_evento_event;
        $this->event_manager = $event_manager;
        $this->logger = $logger;

        $this->log_info_code = Logger::CREVENTO_MA_UNMARK_EVENT;
    }

    public function executeAction() : void
    {
        $this->event_manager->removeIliasEventoEventConnection($this->ilias_evento_event);

        $this->logger->logEventImport(
            $this->log_info_code,
            $this->ilias_evento_event->getEventoEventId(),
            $this->ilias_evento_event->getRefId(),
            []
        );
    }
}
