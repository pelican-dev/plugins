<?php

return [
    'database_limit' => env('UCS_DEFAULT_DATABASE_LIMIT', 0),
    'allocation_limit' => env('UCS_DEFAULT_ALLOCATION_LIMIT', 0),
    'backup_limit' => env('UCS_DEFAULT_BACKUP_LIMIT', 0),

    'can_users_update_servers' => env('UCS_CAN_USERS_UPDATE_SERVERS', true),
    'can_users_delete_servers' => env('UCS_CAN_USERS_DELETE_SERVERS', false),

    'deployment_tags' => env('UCS_DEPLOYMENT_TAGS', 'user_creatable_servers'),
    'deployment_ports' => env('UCS_DEPLOYMENT_PORTS', ''),
];
