<?php declare(strict_types=1);

namespace EventoImportLite\import\data_management;

use EventoImportLite\import\data_management\ilias_core_service\IliasEventObjectService;
use EventoImportLite\import\data_management\repository\IliasEventoEventObjectRepository;
use EventoImportLite\import\data_management\repository\model\IliasEventoEvent;
use EventoImportLite\communication\api_models\EventoEvent;
use EventoImportLite\import\data_management\repository\model\IliasEventoParentEvent;
use EventoImportLite\config\EventLocations;
use EventoImportLite\config\event_auto_create\EventAutoCreateConfiguration;

class EventManager
{
    private IliasEventObjectService $ilias_obj_service;
    private IliasEventoEventObjectRepository $event_obj_repo;
    private EventLocations $event_locations;
    private MembershipManager $membership_manager;
    private EventAutoCreateConfiguration $event_creation_repo;

    public function __construct(
        IliasEventObjectService $ilias_obj_service,
        IliasEventoEventObjectRepository $event_repo,
        EventLocations $event_locations,
        MembershipManager $membership_manager,
        EventAutoCreateConfiguration $event_creation_repo
    )
    {
        $this->ilias_obj_service = $ilias_obj_service;
        $this->event_obj_repo = $event_repo;
        $this->event_locations = $event_locations;
        $this->membership_manager = $membership_manager;
        $this->event_creation_repo = $event_creation_repo;
    }

    public function createNewSingleCourseEvent(EventoEvent $evento_event) : IliasEventoEvent
    {
        $course_object = $this->ilias_obj_service->createNewCourseObject(
            $evento_event->getName(),
            $evento_event->getDescription(),
            $this->event_locations->getLocationRefIdForEventoEvent($evento_event, false),
        );

        $ilias_evento_event = $this->eventoEventAndIliasObjToIliasEventoEvent($evento_event, $course_object);

        $this->event_obj_repo->addNewEventoIliasEvent($ilias_evento_event);

        return $ilias_evento_event;
    }

    public function createParentEventCourse(EventoEvent $evento_event) : IliasEventoParentEvent
    {
        $course_object = $this->ilias_obj_service->createNewCourseObject(
            $evento_event->getGroupName(),
            $evento_event->getDescription(),
            $this->event_locations->getLocationRefIdForEventoEvent($evento_event, false)
        );

        $parent_event = $this->eventoEventAndIliasObjToParentEvent($evento_event, $course_object);

        $this->event_obj_repo->addNewParentEvent($parent_event);

        return $parent_event;
    }

    public function createSubGroupEvent(EventoEvent $evento_event, IliasEventoParentEvent $parent_event) : IliasEventoEvent
    {
        $event_sub_group = $this->ilias_obj_service->createNewGroupObject(
            $evento_event->getName(),
            $evento_event->getDescription(),
            $parent_event->getRefId()
        );

        $ilias_evento_event = $this->eventoEventAndIliasObjToIliasEventoEvent($evento_event, $event_sub_group);

        $this->event_obj_repo->addNewEventoIliasEvent($ilias_evento_event);

        return $ilias_evento_event;
    }

    public function createIliasObjectAndEventoEventConnection(EventoEvent $evento_event, \ilContainer $ilias_obj) : IliasEventoEvent
    {
        if (!$this->checkIfIliasObjCanBeMarkedAsIliasEventoEvent($ilias_obj)) {
            throw new \Exception('Given object does not fulfill the conditions to be marked as ilias-evento-event');
        }

        $ilias_evento_event = $this->eventoEventAndIliasObjToIliasEventoEvent($evento_event, $ilias_obj);
        $this->event_obj_repo->addNewEventoIliasEvent($ilias_evento_event);

        return $ilias_evento_event;
    }

    public function convertIliasEventoEventToParentEvent(
        EventoEvent $evento_event,
        IliasEventoEvent $ilias_event
    ) : IliasEventoParentEvent {
        $course_obj = $this->ilias_obj_service->getCourseObjectForRefId($ilias_event->getRefId());

        // Rename course obj if it was not renamed by a user
        if ($course_obj->getTitle() == $evento_event->getName()) {
            $course_obj = $this->ilias_obj_service->renameEventObject($course_obj, $evento_event->getGroupName());
        }

        $parent_event = $this->eventoEventAndIliasObjToParentEvent($evento_event, $course_obj);

        $this->event_obj_repo->addNewParentEvent($parent_event);
        $this->event_obj_repo->removeIliasEventoEvent($ilias_event);

        return $parent_event;
    }

    public function removeIliasEventoEventConnection(IliasEventoEvent $ilias_evento_event)
    {
        $this->membership_manager->removeEventoIliasMembershipConnectionsForEvent($ilias_evento_event);
        $this->event_obj_repo->removeIliasEventoEvent($ilias_evento_event);
    }

    public function registerEventoEventAsDelivered(EventoEvent $evento_event)
    {
        $this->event_obj_repo->registerEventAsDelivered($evento_event->getEventoId());
    }

    private function checkIfIliasObjCanBeMarkedAsIliasEventoEvent(\ilContainer $ilias_obj) : bool
    {
        $is_markable_obj = false;

        if ($ilias_obj instanceof \ilObjCourse) {
            $is_markable_obj = true;
        }

        if ($ilias_obj instanceof \ilObjGroup && $this->ilias_obj_service->isGroupObjPartOfACourse($ilias_obj)) {
            $is_markable_obj = true;
        }

        return $is_markable_obj;
    }

    private function eventoEventAndIliasObjToIliasEventoEvent(EventoEvent $evento_event, \ilContainer $ilias_obj) : IliasEventoEvent
    {
        return new IliasEventoEvent(
            $evento_event->getEventoId(),
            $evento_event->getName(),
            $evento_event->getDescription(),
            $evento_event->getType(),
            $this->isEventInCreationWhiteList($evento_event),
            $evento_event->getStartDate(),
            $evento_event->getEndDate(),
            $ilias_obj->getType(),
            (int) $ilias_obj->getRefId(),
            (int) $ilias_obj->getId(),
            (int) $ilias_obj->getDefaultAdminRole(),
            (int) $ilias_obj->getDefaultMemberRole(),
            $evento_event->getGroupUniqueKey()
        );
    }

    private function eventoEventAndIliasObjToParentEvent(EventoEvent $evento_event, \ilObjCourse $course_object)
    {
        return new IliasEventoParentEvent(
            $evento_event->getGroupUniqueKey(),
            $evento_event->getGroupId(),
            $evento_event->getGroupName(),
            (int) $course_object->getRefId(),
            (int) $course_object->getDefaultAdminRole(),
            (int) $course_object->getDefaultMemberRole()
        );
    }

    public function deleteIliasEventoEvent(IliasEventoEvent $ilias_evento_event)
    {
        $this->membership_manager->removeEventoIliasMembershipConnectionsForEvent($ilias_evento_event);
        $this->event_obj_repo->removeIliasEventoEvent($ilias_evento_event);
        $this->ilias_obj_service->removeIliasEventObjectWithSubObjects($ilias_evento_event);
    }

    public function deleteIliasParentEvent(IliasEventoParentEvent $ilias_evento_parent_event)
    {
        $this->event_obj_repo->removeParentEventIfItHasNoChildEvent($ilias_evento_parent_event);
        $this->ilias_obj_service->removeIliasParentEventObject($ilias_evento_parent_event);
    }

    public function searchEventableObjectForEventoEvent(EventoEvent $evento_event) : ?\ilContainer
    {
        return $this->ilias_obj_service->searchEventableIliasObjectByTitle($evento_event->getName());
    }

    public function searchIliasEventoEventByEventoEvent(EventoEvent $evento_event) : ?IliasEventoEvent
    {
        return $this->event_obj_repo->getEventByEventoId($evento_event->getEventoId());
    }

    public function searchParentEventForEventoEvent(EventoEvent $evento_event) : ?IliasEventoParentEvent
    {
        return $this->event_obj_repo->getParentEventbyGroupUniqueKey($evento_event->getGroupUniqueKey());
    }

    public function getParentEventForIliasEventoEvent(IliasEventoEvent $ilias_evento_event) : ?IliasEventoParentEvent
    {
        return $this->event_obj_repo->getParentEventbyGroupUniqueKey($ilias_evento_event->getParentEventKey());
    }

    public function getNumberOfChildEventsForParentEvent(IliasEventoParentEvent $parent_event) : int
    {
        return $this->event_obj_repo->getNumberOfChildEventsForParentEventKey($parent_event);
    }

    public function isIliasObjectToIliasEventoEventStillExisting(IliasEventoEvent $ilias_evento_event) : bool
    {
        return \ilObject::_exists($ilias_evento_event->getRefId(), true, $ilias_evento_event->getIliasType());
    }

    public function isEventInCreationWhiteList(EventoEvent $evento_event) : bool
    {
        $is_in_list = false;

        foreach ($this->event_creation_repo->getConfiguredEvents() as $configured_event_title) {
            if (stristr($evento_event->getName(), $configured_event_title) !== false) {
                return true;
            }
        }

        return $is_in_list;
    }
}
