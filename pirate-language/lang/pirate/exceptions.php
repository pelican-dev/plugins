<?php

return [
    'daemon_connection_failed' => 'There be a storm brewin\' while tryin\' to parley with the daemon, resultin\' in a HTTP/:code response. This scallywag error has been logged in the captain\'s log.',
    'node' => [
        'servers_attached' => 'A node can only be sent to Davy Jones\' Locker if no servers be chained to it.',
        'error_connecting' => 'Trouble makin\' contact with :node, arrr!',
        'daemon_off_config_updated' => 'The daemon\'s config <strong>has been updated</strong>, but there was a squall tryin\' to update the config file on the daemon itself. Ye\'ll need to update the config.yml file by hand to make the changes take hold.',
    ],
    'allocations' => [
        'server_using' => 'This allocation be already claimed by a server. Ye can only delete it if no server be assigned.',
        'too_many_ports' => 'Tryin\' to add over 1000 ports in one go ain\'t supported, matey.',
        'invalid_mapping' => 'The map given for port :port be as wrong as a drunk sailor\'s compass and can\'t be processed.',
        'cidr_out_of_range' => 'CIDR notation only allows masks from /25 to /32 — no more, no less!',
        'port_out_of_range' => 'Ports must be between 1024 and 65535 to sail these waters.',
    ],
    'egg' => [
        'delete_has_servers' => 'Ye can\'t toss an Egg overboard if there be active servers hatchin\' from it.',
        'invalid_copy_id' => 'The Egg ye picked to copy a script from either don\'t exist or be copyin\' from another script itself.',
        'has_children' => 'This Egg be a parent to other Eggs. Ye must send them to the deep before ye delete this one.',
    ],
    'variables' => [
        'env_not_unique' => 'The environment variable :name must be unique for this Egg\'s domain.',
        'reserved_name' => 'The environment variable :name be protected — no assignin\' allowed!',
        'bad_validation_rule' => 'The validation rule ":rule" be no good for this ship\'s application.',
    ],
    'importer' => [
        'json_error' => 'There was trouble parsing the JSON treasure map: :error.',
        'file_error' => 'The JSON file provided be nothin\' but bilge water.',
        'invalid_json_provided' => 'The JSON file be not in a form the ship can recognize.',
    ],
    'subusers' => [
        'editing_self' => 'Ye can\'t be editin\' yer own subuser account, savvy?',
        'user_is_owner' => 'Ye can\'t add the server\'s captain as a subuser, no matter how much grog ye offer.',
        'subuser_exists' => 'A matey with that email be already listed as a subuser for this server.',
    ],
    'databases' => [
        'delete_has_databases' => 'Ye can\'t scuttle a database host server while it still has active databases tied to it.',
    ],
    'tasks' => [
        'chain_interval_too_long' => 'The longest waitin\' time fer a chained task be 15 minutes, no more!',
    ],
    'locations' => [
        'has_nodes' => 'Ye can\'t delete a location if it\'s got active nodes anchored there.',
    ],
    'users' => [
        'is_self' => 'Ye can\'t send yerself to the briny deep by deletin\' yer own user account.',
        'has_servers' => 'Ye can\'t delete a user while they\'ve got servers sailin\' under their flag. Sink their servers first, then come back.',
        'node_revocation_failed' => 'Failed to revoke keys on <a href=":link">Harbor #:node</a>. :error',
    ],
    'deployment' => [
        'no_viable_nodes' => 'No nodes meetin\' the demands for automatic deployment could be found in these waters.',
        'no_viable_allocations' => 'No allocations fit fer automatic deployment were found, arr!',
    ],
    'api' => [
        'resource_not_found' => 'The treasure ye seek does not exist on this server.',
    ],
    'mount' => [
        'servers_attached' => 'A mount must be free of servers before it can be sent to the depths.',
    ],
    'server' => [
        'marked_as_failed' => 'This server\'s installation voyage ain\'t finished yet, try again later, matey.',
    ],
];
