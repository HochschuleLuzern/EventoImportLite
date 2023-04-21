<?php declare(strict_types = 1);

namespace EventoImportLite\import\data_management\repository\model;

class EventMemberships
{
    private int $event_id;
    private int $user_evento_id;
    private int $role_id;

    public function __construct(int $event_ref_id, int $user_evento_id, int $role_id)
    {
        $this->event_id = $event_ref_id;
        $this->user_evento_id = $user_evento_id;
        $this->role_id = $role_id;
    }

    public function getEventId() : int
    {
        return $this->event_id;
    }

    public function getUserEventoId() : int
    {
        return $this->user_evento_id;
    }

    public function getRoleId() : int
    {
        return $this->role_id;
    }
}
