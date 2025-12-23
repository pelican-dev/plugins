<?php

return [
    'user_resource_limits' => 'User Resource Limit|User Resource Limits',
    'user' => 'User|Users',
    'cpu' => 'CPU',
    'memory' => 'Memory',
    'disk' => 'Disk Space',
    'server_limit' => 'Server Limit',
    'no_limit' => 'No Limit',
    'unlimited' => 'Unlimited',
    'hint_unlimited' => '0 means unlimited',
    'name' => 'Server Name',
    'egg' => 'Egg',
    'left' => 'left',

    'modals' => [
        'delete_server_confirm' => 'Are you sure you want to delete this server?',
        'delete_server_warning' => 'This action cannot be undone and all data will be permanently lost.',
        'delete_server' => 'Delete Server',
    ],

    'notifications' => [
        'server_resources_updated' => 'Server Resource Limits updated',
        'might_need_restart' => 'To fully use the new resource limits a server restart might be required.',
        'manual_restart_needed' => 'Please manually restart your server to apply the new resource limits.',

        'server_deleted' => 'Server Deleted',
        'server_deleted_success' => 'The server has been deleted successfully.',
        'server_delete_error' => 'Could not delete server',

        'server_creation_failed' => 'Could not create server',
        'no_viable_node_found' => 'No viable node was found. Please contact the panel admin.',
        'no_viable_allocation_found' => 'No viable allocation was found. Please contact the panel admin.',
        'unknown_server_creation_error' => 'Unknown error. Please contact the panel admin.',
    ],
];
