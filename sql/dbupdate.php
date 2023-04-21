<?php
/**
 * Copyright (c) 2017 Hochschule Luzern
 *
 * This file is part of the NotifyOnCronFailure-Plugin for ILIAS.

 * NotifyOnCronFailure-Plugin for ILIAS is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.

 * NotifyOnCronFailure-Plugin for ILIAS is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with EventoImport-Plugin for ILIAS.  If not,
 * see <http://www.gnu.org/licenses/>.
 */
?>
	 
<#1>
<?php
$table_name = \EventoImportLite\db\IliasEventoUserTblDef::TABLE_NAME;
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        \EventoImportLite\db\IliasEventoUserTblDef::COL_EVENTO_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoUserTblDef::COL_ILIAS_USER_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoUserTblDef::COL_LAST_TIME_DELIVERED => array(
            'type' => ilDBConstants::T_TIMESTAMP,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoUserTblDef::COL_ACCOUNT_TYPE => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 15,
            'notnull' => true
        ),

    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, [\EventoImportLite\db\IliasEventoUserTblDef::COL_EVENTO_ID]);
    $ilDB->addUniqueConstraint($table_name, [\EventoImportLite\db\IliasEventoUserTblDef::COL_ILIAS_USER_ID], 'usr');
}
?>
?>
<#2>
<?php

$table_name = \EventoImportLite\db\IliasEventoEventsTblDef::TABLE_NAME;
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_EVENTO_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_EVENTO_TITLE => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 255,
            'notnull' => true,
            'fixed' => false
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_EVENTO_DESCRIPTION => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 128,
            'notnull' => true,
            'fixed' => false
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_EVENTO_TYPE => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 25,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_WAS_AUTOMATICALLY_CREATED => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 1,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_START_DATE => array(
            'type' => ilDBConstants::T_TIMESTAMP,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_END_DATE => array(
            'type' => ilDBConstants::T_TIMESTAMP,
            'notnull' => false
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_LAST_TIME_DELIVERED => array(
            'type' => ilDBConstants::T_TIMESTAMP,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_ILIAS_TYPE => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 4,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_REF_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_OBJ_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_ADMIN_ROLE_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_STUDENT_ROLE_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventsTblDef::COL_PARENT_EVENT_KEY => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 100,
            'notnull' => false
        )
    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, array(\EventoImportLite\db\IliasEventoEventsTblDef::COL_EVENTO_ID));
}

?>
<#3>
<?php

$table_name = \EventoImportLite\db\IliasEventLocationsTblDef::TABLE_NAME;
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        \EventoImportLite\db\IliasEventLocationsTblDef::COL_DEPARTMENT_NAME => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 30,
            'notnull' => true,
            'fixed' => false
        ),
        \EventoImportLite\db\IliasEventLocationsTblDef::COL_EVENT_KIND => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 30,
            'notnull' => true,
            'fixed' => false
        ),
        \EventoImportLite\db\IliasEventLocationsTblDef::COL_YEAR => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 2,
            'notnull' => true,
        ),
        \EventoImportLite\db\IliasEventLocationsTblDef::COL_REF_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        )
    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, array(\EventoImportLite\db\IliasEventLocationsTblDef::COL_DEPARTMENT_NAME, \EventoImportLite\db\IliasEventLocationsTblDef::COL_EVENT_KIND, \EventoImportLite\db\IliasEventLocationsTblDef::COL_YEAR));
}

?>
<#4>
<?php

$table_name = \EventoImportLite\db\IliasEventoEventMembershipsTblDef::TABLE_NAME;
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        \EventoImportLite\db\IliasEventoEventMembershipsTblDef::COL_EVENTO_EVENT_ID => array(
            'type' => 'integer',
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventMembershipsTblDef::COL_EVENTO_USER_ID => array(
            'type' => 'integer',
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasEventoEventMembershipsTblDef::COL_ROLE_TYPE => array(
            'type' => 'integer',
            'length' => 1,
            'notnull' => true
        )
    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, array(\EventoImportLite\db\IliasEventoEventMembershipsTblDef::COL_EVENTO_EVENT_ID,
                                            \EventoImportLite\db\IliasEventoEventMembershipsTblDef::COL_EVENTO_USER_ID));
}


?>
<#5>
<?php

$table_name = \EventoImportLite\db\IliasParentEventTblDef::TABLE_NAME;
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        \EventoImportLite\db\IliasParentEventTblDef::COL_GROUP_UNIQUE_KEY => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 100,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasParentEventTblDef::COL_GROUP_EVENTO_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasParentEventTblDef::COL_TITLE => array(
            'type' => ilDBConstants::T_TEXT,
            'length' => 100,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasParentEventTblDef::COL_REF_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasParentEventTblDef::COL_ADMIN_ROLE_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        ),
        \EventoImportLite\db\IliasParentEventTblDef::COL_STUDENT_ROLE_ID => array(
            'type' => ilDBConstants::T_INTEGER,
            'length' => 8,
            'notnull' => true
        )
    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, array(\EventoImportLite\db\IliasParentEventTblDef::COL_GROUP_UNIQUE_KEY));
}

?>
<#6>
<?php

$table_name = 'crevento_log_users';
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        'evento_id' => array(
            'type' => 'integer',
            'length' => 8,
            'notnull' => true
        ),
        'usrname' => array(
            'type' => 'text',
            'length' => 50,
            'notnull' => true
        ),
        'last_import_data' => array(
            'type' => 'text',
            'length' => 4000,
            'notnull' => false
        ),
        'last_import_date' => array(
            'type' => 'timestamp',
            'notnull' => true
        ),
        'update_info_code' => array(
            'type' => 'integer',
            'length' => 2,
            'notnull' => true
        )
    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, array("evento_id"));
}

$table_name = 'crevento_log_members';
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        'evento_event_id' => array(
            'type' => 'integer',
            'length' => 8,
            'notnull' => true
        ),
        'evento_user_id' => array(
            'type' => 'integer',
            'length' => 8,
            'notnull' => true
        ),
        'role_type' => array(
            'type' => 'integer',
            'length' => 1,
            'notnull' => true
        ),
        'last_import_date' => array(
            'type' => 'timestamp',
            'notnull' => true
        ),
        'update_info_code' => array(
            'type' => 'integer',
            'length' => 2,
            'notnull' => true
        )
    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, array('evento_event_id', 'evento_user_id'));
}

$table_name = 'crevento_log_events';
if (!$ilDB->tableExists($table_name)) {
    $fields = array(
        'evento_id' => array(
            'type' => 'integer',
            'length' => 8,
            'notnull' => true
        ),
        'ref_id' => array(
            'type' => 'integer',
            'length' => 8,
            'notnull' => false
        ),
        'last_import_data' => array(
            'type' => 'text',
            'length' => 4000,
            'notnull' => false
        ),
        'last_import_date' => array(
            'type' => 'timestamp',
            'notnull' => true
        ),
        'update_info_code' => array(
            'type' => 'integer',
            'length' => 2,
            'notnull' => true
        ),
        'last_import_employees' => array(
            'type' => 'text',
            'length' => 4000,
            'notnull' => false
        ),
        'last_import_students' => array(
            'type' => 'text',
            'length' => 4000,
            'notnull' => false
        )
    );

    $ilDB->createTable($table_name, $fields);
    $ilDB->addPrimaryKey($table_name, array("evento_id"));
}

?>