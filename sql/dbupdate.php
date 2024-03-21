<#1>
<?php
/** @var ilDBInterface $ilDB */
$ilDB->createTable('srms_config', [
    'namespace' => [
        'type' => 'text',
        'length' => 64,
        'notnull' => true,
    ],
    'config_key' => [
        'type' => 'text',
        'length' => 128,
        'notnull' => true,
    ],
    'value' => [
        'type' => 'text',
        'length' => 4000,
        'notnull' => true,
        'default' => ''
    ],
    'type' => [
        'type' => 'integer',
        'length' => 8,
        'notnull' => true,
    ],
]);
$ilDB->addPrimaryKey('srms_config', ['namespace', 'config_key']);
?>
<#2>
<?php
/** @var ilDBInterface $ilDB */
$ilDB->createTable('srms_object_config', [
    'workflow_id' => [
        'type' => 'text',
        'length' => 64,
        'notnull' => true,
    ],
    'context_ref_id' => [
        'type' => 'integer',
        'length' => 8,
        'notnull' => true,
    ],
    'config_data' => [
        'type' => 'clob',
        'notnull' => false,
    ]
]);
$ilDB->addPrimaryKey(
    'srms_object_config',
    ['workflow_id', 'context_ref_id']
);
?>
<#3>
<?php
/** @var ilDBInterface $ilDB */
$ilDB->createTable('srms_object_mode', [
    'workflow_id' => [
        'type' => 'text',
        'length' => 64,
        'notnull' => true,
    ],
    'context_ref_id' => [
        'type' => 'integer',
        'length' => 8,
        'notnull' => true,
    ],
    'mode_id' => [
        'type' => 'integer',
        'length' => 8,
        'notnull' => true,
    ]
]);
$ilDB->addPrimaryKey(
    'srms_object_mode',
    ['workflow_id', 'context_ref_id', 'mode_id']
);
?>
<#4>
<?php
// migrate existing RUN_AS_DIFF (8) modes to sync_mode (4)
$ilDB->manipulateF(
    'UPDATE srms_object_mode SET mode_id = %s WHERE mode_id = %s',
    ['integer', 'integer'],
    [64, 8]
);
?>
<#5>
<?php
// migrate other modes
$ilDB->manipulateF(
    'UPDATE srms_object_mode SET mode_id = %s WHERE mode_id = %s',
    ['integer', 'integer'],
    [4, 2]
);
$ilDB->manipulateF(
    'UPDATE srms_object_mode SET mode_id = %s WHERE mode_id = %s',
    ['integer', 'integer'],
    [16, 1]
);
?>
