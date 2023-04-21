<?php

namespace EventoImportLite\db;

class IliasEventoEventMembershipsTblDef
{
    public const TABLE_NAME = 'crevlite_memberships';

    public const COL_EVENTO_EVENT_ID = 'evento_event_id';
    public const COL_EVENTO_USER_ID = 'evento_user_id';
    public const COL_ROLE_TYPE = 'role_type';

    public const ROLE_ADMIN = 1;
    public const ROLE_MEMBER = 2;
}
