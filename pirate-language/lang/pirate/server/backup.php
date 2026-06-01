<?php

return [
    'title' => 'Backup Chests',
    'empty' => 'No Backup Chests Found',
    'size' => 'Size',
    'created_at' => 'Created On',
    'status' => 'Status',
    'is_locked' => 'Lock Status',
    'backup_status' => [
        'in_progress' => 'Under Way',
        'successful' => 'Voyage Successful',
        'failed' => 'Voyage Failed',
    ],
    'actions' => [
        'create' => [
            'title' => 'Create Backup Chest',
            'limit' => 'Backup Chest Limit Reached',
            'created' => ':name forged',
            'notification_success' => 'Backup Chest Created Successfully',
            'notification_fail' => 'Failed ter Create Backup Chest',
            'name' => 'Name',
            'ignored' => 'Ignored Files & Folders',
            'locked' => 'Locked Away?',
            'lock_helper' => 'Prevents this backup chest from bein\' scuttled until it be explicitly unlocked.',
        ],
        'lock' => [
            'lock' => 'Lock',
            'unlock' => 'Unlock',
        ],
        'download' => 'Download',
        'rename' => [
            'title' => 'Rename',
            'new_name' => 'Backup Chest Name',
            'notification_success' => 'Backup Chest Renamed Successfully',
        ],
        'restore' => [
            'title' => 'Restore',
            'helper' => 'Yer server will be brought to a halt. Ye will not be able ter control its power, access the file hold, or create additional backup chests until the restoration voyage be complete.',
            'delete_all' => 'Scuttle all files before restorin\' the backup chest?',
            'notification_started' => 'Restorin\' Backup Chest',
            'notification_success' => 'Backup Chest Restored Successfully',
            'notification_fail' => 'Backup Chest Restoration Failed',
            'notification_fail_body_1' => 'This server be not currently in a state that permits restorin\' a backup chest.',
            'notification_fail_body_2' => 'This backup chest cannot be restored at this time: it either be unfinished or failed.',
        ],
        'delete' => [
            'title' => 'Scuttle Backup Chest',
            'description' => 'Do ye wish ter scuttle :backup?',
            'notification_success' => 'Backup Chest Scuttled',
            'notification_fail' => 'Could not scuttle backup chest',
            'notification_fail_body' => 'Connection ter the node failed, matey. Try again.',
        ],
    ],
];
