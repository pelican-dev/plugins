<?php

return [
    'daemon_connection_failed' => 'There be an exception while tryin\' ter parley with the daemon, resultin\' in a HTTP/:code response. This mishap has been logged in the captain\'s records.',
    'node' => [
        'servers_attached' => 'A node must have no servers moored to it before it can be sent to Davy Jones\' Locker.',
        'error_connecting' => 'Failed ter establish a connection with :node',
        'daemon_off_config_updated' => 'The daemon configuration <strong>has been updated</strong>, but an error was encountered while attemptin\' ter automatically update the daemon\'s configuration file. Ye must manually update the daemon configuration file (config.yml) fer these changes ter take effect.',
    ],
    'allocations' => [
        'server_using' => 'A server be currently assigned ter this allocation. An allocation may only be scuttled if no server be claimin\' it.',
        'too_many_ports' => 'Chartin\' more than 1000 ports in a single range at once be not supported.',
        'invalid_mapping' => 'The mapping provided fer :port be invalid and could not be processed.',
        'cidr_out_of_range' => 'CIDR notation only permits masks between /25 and /32, matey.',
        'port_out_of_range' => 'Ports in an allocation must be at least 1024 and no greater than 65535.',
    ],
    'egg' => [
        'delete_has_servers' => 'An Egg with active servers hatchin\' from it cannot be deleted from the Panel.',
        'invalid_copy_id' => 'The Egg selected fer copyin\' a script either does not exist or be attemptin\' ter copy from itself.',
        'has_children' => 'This Egg be the parent of one or more other Eggs. Ye must remove those Eggs before deletin\' this one.',
    ],
    'variables' => [
        'env_not_unique' => 'The environment variable :name must be unique ter this Egg.',
        'reserved_name' => 'The environment variable :name be protected and cannot be assigned ter another variable.',
        'bad_validation_rule' => 'The validation rule ":rule" be not recognized by this application.',
    ],
    'importer' => [
        'json_error' => 'An error occurred while readin\' the JSON file: :error.',
        'file_error' => 'The provided JSON file be invalid.',
        'invalid_json_provided' => 'The provided JSON file be not in a format this vessel can recognize.',
    ],
    'subusers' => [
        'editing_self' => 'Ye be not permitted ter edit yer own deckhand account.',
        'user_is_owner' => 'Ye cannot add the ship\'s owner as a deckhand fer this server.',
        'subuser_exists' => 'A sailor with that email address already serves as a deckhand on this server.',
    ],
    'databases' => [
        'delete_has_databases' => 'Cannot remove a database host server that still has active databases anchored ter it.',
    ],
    'tasks' => [
        'chain_interval_too_long' => 'The maximum interval fer a chained task be 15 minutes.',
    ],
    'locations' => [
        'has_nodes' => 'Cannot delete a location that still has active nodes stationed there.',
    ],
    'users' => [
        'is_self' => 'Ye cannot cast yer own account overboard.',
        'has_servers' => 'Cannot delete a sailor with active servers attached ter their account. Scuttle their servers first before proceedin\'.',
        'node_revocation_failed' => 'Failed ter revoke keys on <a href=":link">Node #:node</a>. :error',
    ],
    'deployment' => [
        'no_viable_nodes' => 'No nodes meetin\' the specified requirements fer automatic deployment could be found.',
        'no_viable_allocations' => 'No allocations suitable fer automatic deployment could be found.',
    ],
    'api' => [
        'resource_not_found' => 'The requested resource be nowhere on this server.',
    ],
    'mount' => [
        'servers_attached' => 'A mount must have no servers attached ter it before it can be deleted.',
    ],
    'server' => [
        'marked_as_failed' => 'This server has not yet finished its installation voyage. Please try again later, matey.',
    ],
];
