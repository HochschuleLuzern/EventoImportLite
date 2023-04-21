<?php declare(strict_types = 1);

namespace EventoImportLite\import\action\event;

use EventoImportLite\communication\api_models\EventoEvent;
use EventoImportLite\import\data_management\repository\model\IliasEventoParentEvent;
use EventoImportLite\import\data_management\MembershipManager;
use EventoImportLite\import\data_management\repository\model\IliasEventoEvent;
use EventoImportLite\import\Logger;
use EventoImportLite\import\data_management\EventManager;

class EventActionFactory
{
    private EventManager $event_manager;
    private MembershipManager $membership_manager;
    private Logger $logger;

    public function __construct(
        EventManager $event_manager,
        MembershipManager $membership_manager,
        Logger $logger
    ) {
        $this->event_manager = $event_manager;
        $this->membership_manager = $membership_manager;
        $this->logger = $logger;
    }

    public function createSingleEvent(EventoEvent $evento_event, int $destination_ref_id) : CreateSingleEvent
    {
        return new CreateSingleEvent(
            $evento_event,
            $this->event_manager,
            $this->membership_manager,
            $this->logger,
        );
    }

    public function createEventWithParent(EventoEvent $evento_event, int $destination_ref_id) : CreateEventWithParent
    {
        return new CreateEventWithParent(
            $evento_event,
            $this->event_manager,
            $this->membership_manager,
            $this->logger,
        );
    }

    public function createEventInParentEvent(EventoEvent $evento_event, IliasEventoParentEvent $parent_event) : CreateEventInParentEvent
    {
        return new CreateEventInParentEvent(
            $evento_event,
            $parent_event,
            $this->event_manager,
            $this->membership_manager,
            $this->logger,
        );
    }

    public function updateExistingEvent(EventoEvent $evento_event, IliasEventoEvent $ilias_event) : UpdateExistingEvent
    {
        return new UpdateExistingEvent(
            $evento_event,
            $ilias_event,
            $this->event_manager,
            $this->membership_manager,
            $this->logger,
        );
    }

    public function convertSingleEventToMultiGroupEvent(EventoEvent $evento_event, IliasEventoEvent $ilias_event)
    {
        return new ConvertSingleEventToMultiGroupEvent(
            $evento_event,
            $ilias_event,
            $this->event_manager,
            $this->membership_manager,
            $this->logger
        );
    }

    public function markExistingIliasObjAsEvent(
        EventoEvent $evento_event,
        \ilContainer $ilias_obj
    ) : MarkExistingIliasObjAsEvent {
        return new MarkExistingIliasObjAsEvent(
            $evento_event,
            $ilias_obj,
            $this->event_manager,
            $this->membership_manager,
            $this->logger,
        );
    }

    public function unmarkDeletedIliasObject(EventoEvent $evento_event, IliasEventoEvent $ilias_event) : UnmarkDeletedIliasObject
    {
        return new UnmarkDeletedIliasObject(
            $evento_event,
            $ilias_event,
            $this->event_manager,
            $this->membership_manager,
            $this->logger
        );
    }

    public function reportNonIliasEvent(EventoEvent $evento_event) : ReportEventImportDatasetWithoutAction
    {
        return new ReportEventImportDatasetWithoutAction(
            Logger::CREVENTO_MA_NON_ILIAS_EVENT,
            $evento_event->getEventoId(),
            null,
            $evento_event->getDecodedApiData(),
            $this->logger
        );
    }

    public function reportUnknownLocationForEvent(EventoEvent $evento_event) : ReportEventImportDatasetWithoutAction
    {
        return new ReportEventImportDatasetWithoutAction(
            Logger::CREVENTO_MA_EVENT_LOCATION_UNKNOWN,
            $evento_event->getEventoId(),
            null,
            $evento_event->getDecodedApiData(),
            $this->logger
        );
    }

    public function deleteSingleCourseEvent(IliasEventoEvent $ilias_evento_event) : DeleteSingleCourseEvent
    {
        return new DeleteSingleCourseEvent(
            $ilias_evento_event,
            $this->event_manager,
            $this->logger
        );
    }

    public function deleteGroupEventInCourse(IliasEventoEvent $ilias_evento_event) : DeleteGroupEventInCourse
    {
        return new DeleteGroupEventInCourse(
            $ilias_evento_event,
            $this->event_manager,
            $this->logger
        );
    }

    public function deleteEventGroupWithParentEventCourse(IliasEventoEvent $ilias_evento_event, IliasEventoParentEvent $ilias_evento_parent_event) : DeleteEventGroupWithParentEventCourse
    {
        return new DeleteEventGroupWithParentEventCourse(
            $ilias_evento_event,
            $ilias_evento_parent_event,
            $this->event_manager,
            $this->logger
        );
    }

    public function unmarkExistingIliasObjFromEventoEvents(IliasEventoEvent $ilias_evento_event) : UnmarkExistingIliasObjFromEventoEvents
    {
        return new UnmarkExistingIliasObjFromEventoEvents(
            $ilias_evento_event,
            $this->event_manager,
            $this->logger
        );
    }
}
