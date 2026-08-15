<?php

return [
    'database_limit' => (int) env('UCS_DEFAULT_DATABASE_LIMIT', 0),
    'allocation_limit' => (int) env('UCS_DEFAULT_ALLOCATION_LIMIT', 0),
    'backup_limit' => (int) env('UCS_DEFAULT_BACKUP_LIMIT', 0),

    'default_user_cpu' => (int) env('UCS_DEFAULT_USER_CPU', 0),
    'default_user_memory' => (int) env('UCS_DEFAULT_USER_MEMORY', 0),
    'default_user_disk' => (int) env('UCS_DEFAULT_USER_DISK', 0),

    'max_cpu' => (int) env('UCS_MAX_CPU', 0),
    'max_memory' => (int) env('UCS_MAX_MEMORY', 0),
    'max_disk' => (int) env('UCS_MAX_DISK', 0),

    'can_users_update_servers' => (bool) env('UCS_CAN_USERS_UPDATE_SERVERS', true),
    'can_users_delete_servers' => (bool) env('UCS_CAN_USERS_DELETE_SERVERS', false),

    'deployment_tags' => env('UCS_DEPLOYMENT_TAGS', 'user_creatable_servers'),
    'deployment_ports' => env('UCS_DEPLOYMENT_PORTS', ''),
    'allowed_eggs' => env('UCS_ALLOWED_EGGS', ''),
];
