<?php declare(strict_types = 1);

namespace EventoImportLite\config;

class DefaultEventSettings
{
    private int $default_object_owner_id;
    private int $default_sort_mode;
    private int $default_sort_direction;
    private bool $default_online_status;
    private bool $remove_participants_on_membership_sync;
    private int $default_sort_new_items_order;
    private int $default_sort_new_items_position;

    public function __construct(\ilSetting $settings)
    {
        $this->default_object_owner_id = (int) $settings->get(CronConfigForm::CONF_EVENT_OWNER_ID, 6);
        $this->default_sort_mode = \ilContainer::SORT_MANUAL;
        $this->default_sort_new_items_order = \ilContainer::SORT_NEW_ITEMS_ORDER_CREATION;
        $this->default_sort_new_items_position = \ilContainer::SORT_NEW_ITEMS_POSITION_BOTTOM;
        $this->default_sort_direction = \ilContainer::SORT_DIRECTION_ASC;
        $this->default_online_status = true;
        $this->remove_participants_on_membership_sync = $settings->get(CronConfigForm::CONF_EVENT_REMOVE_PARTICIPANTS, '1') == 1;
    }

    public function getDefaultObjectOwnerId() : int
    {
        return $this->default_object_owner_id;
    }

    public function getDefaultSortMode() : int
    {
        return $this->default_sort_mode;
    }

    public function getDefaultSortNewItemsOrder()
    {
        return $this->default_sort_new_items_order;
    }

    public function getDefaultSortNewItemsPosition()
    {
        return $this->default_sort_new_items_position;
    }

    public function getDefaultSortDirection() : int
    {
        return $this->default_sort_direction;
    }

    public function isDefaultOnline() : bool
    {
        return $this->default_online_status;
    }

    public function getRemoveParticipantsOnMembershipSync() : bool
    {
        return $this->remove_participants_on_membership_sync;
    }
}
