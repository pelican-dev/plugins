<?php

return [
    'title' => 'Crew',
    'username' => 'Crew Name',
    'email' => 'Message Scroll (Email)',
    'assign_all' => 'Assign all crew',
    'invite_user' => 'Recruit Crew Member',
    'action' => 'Send Invitation',
    'remove' => 'Remove Crew Member',
    'edit' => 'Edit Crew Member',
    'editing' => 'Editing :user',
    'delete' => 'Ban Crew Member',
    'notification_add' => 'Crew Member Invited!',
    'notification_edit' => 'Crew Updated!',
    'notification_delete' => 'Crew Member Banished!',
    'notification_failed' => 'Failed to recruit crew member!',

    'permissions' => [
        'title' => 'Crew Powers',

        'activity_title' => 'Ship Logs',
        'activity_desc' => 'Powers that control access to the ship activity logs.',

        'startup_title' => 'Boot-Up',
        'startup_desc' => 'Powers that control access to the ship boot-up settings.',

        'settings_title' => 'Ship Settings',
        'settings_desc' => 'Powers that allow changes to ship configuration.',

        'control_title' => 'Control',
        'control_desc' => 'Powers that allow control over ship power state or sending commands.',

        'user_title' => 'Crew Management',
        'user_desc' => 'Powers that allow managing other crew members on the ship. No crew member may edit their own role or grant powers they do not already possess.',

        'file_title' => 'Files',
        'file_desc' => 'Powers that control access to the ship file system.',

        'allocation_title' => 'Harbor Slots',
        'allocation_desc' => 'Powers that control port allocations for the ship.',

        'database_title' => 'Treasure Databases',
        'database_desc' => 'Powers that control access to ship database management.',

        'backup_title' => 'Treasure Backups',
        'backup_desc' => 'Powers that allow creation and management of ship backups.',

        'schedule_title' => 'Voyage Schedule',
        'schedule_desc' => 'Powers that control access to ship scheduling.',

        'startup_read' => 'Allows a crew member to view boot-up variables for the ship.',
        'startup_update' => 'Allows a crew member to modify boot-up variables for the ship.',
        'startup_docker_image' => 'Allows a crew member to change the Docker image used by the ship.',

        'settings_rename' => 'Allows a crew member to rename the ship.',
        'settings_description' => 'Allows a crew member to change the ship description.',
        'settings_reinstall' => 'Allows a crew member to trigger a full ship reinstall.',
        'settings_change_icon' => 'Allows a crew member to change the ship icon.',

        'activity_read' => 'Allows a crew member to view ship activity logs.',

        'websocket_connect' => 'Allows a crew member to access the ship websocket.',

        'control_console' => 'Allows a crew member to send commands to the ship console.',
        'control_start' => 'Allows a crew member to start the ship.',
        'control_stop' => 'Allows a crew member to stop the ship.',
        'control_restart' => 'Allows a crew member to restart the ship.',
        'control_kill' => 'Allows a crew member to force stop the ship.',

        'user_create' => 'Allows a crew member to create new crew accounts.',
        'user_read' => 'Allows a crew member to view other crew members on the ship.',
        'user_update' => 'Allows a crew member to modify other crew members.',
        'user_delete' => 'Allows a crew member to remove other crew members.',

        'file_create' => 'Allows a crew member to create new files and folders.',
        'file_read' => 'Allows a crew member to view folders but not file contents.',
        'file_read_content' => 'Allows a crew member to view and download file contents.',
        'file_update' => 'Allows a crew member to modify files and folders.',
        'file_delete' => 'Allows a crew member to delete files and folders.',
        'file_archive' => 'Allows a crew member to create and extract file archives.',
        'file_sftp' => 'Allows a crew member to perform file actions using SFTP.',

        'allocation_read' => 'Allows a crew member to view all port allocations for the ship. Primary allocation is always visible.',
        'allocation_update' => 'Allows a crew member to change the primary allocation and add notes.',
        'allocation_delete' => 'Allows a crew member to remove an allocation from the ship.',
        'allocation_create' => 'Allows a crew member to add new allocations to the ship.',

        'database_create' => 'Allows a crew member to create new databases.',
        'database_read' => 'Allows a crew member to view ship databases.',
        'database_update' => 'Allows a crew member to modify databases.',
        'database_delete' => 'Allows a crew member to delete databases.',
        'database_view_password' => 'Allows a crew member to view database passwords.',

        'schedule_create' => 'Allows a crew member to create new schedules.',
        'schedule_read' => 'Allows a crew member to view schedules.',
        'schedule_update' => 'Allows a crew member to modify schedules.',
        'schedule_delete' => 'Allows a crew member to delete schedules.',

        'backup_create' => 'Allows a crew member to create backups.',
        'backup_read' => 'Allows a crew member to view backups.',
        'backup_delete' => 'Allows a crew member to delete backups.',
        'backup_download' => 'Allows a crew member to download backups. Warning: grants access to all ship files.',
        'backup_restore' => 'Allows a crew member to restore backups. Warning: may overwrite all ship files.',

        'mount_desc' => 'Powers that control mount management for the ship.',
        'mount_read' => 'Allows a crew member to view available mounts.',
        'mount_update' => 'Allows a crew member to enable or disable mounts.',
    ],
];
