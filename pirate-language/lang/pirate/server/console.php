<?php

return [
    'title' => 'Captain\'s Console',
    'command' => 'Issue a command, matey...',
    'command_blocked' => 'Server Be Offline...',
    'command_blocked_title' => 'Ye cannot issue commands while the server be offline',
    'open_in_admin' => 'Open in Admiral Mode',
    'power_actions' => [
        'start' => 'Set Sail',
        'stop' => 'Drop Anchor',
        'restart' => 'Turn Her Around',
        'kill' => 'Scuttle',
        'kill_tooltip' => 'This may lead ter data corruption and/or lost treasure!',
    ],
    'labels' => [
        'cpu' => 'CPU',
        'memory' => 'Memory',
        'network' => 'Network',
        'disk' => 'Cargo Hold',
        'name' => 'Name',
        'status' => 'Status',
        'address' => 'Address',
        'unavailable' => 'Beyond Reach',
    ],
    'status' => [
        'created' => 'Built',
        'starting' => 'Hoistin\' Sails',
        'running' => 'Sailin\'',
        'restarting' => 'Changing Course',
        'exited' => 'Docked',
        'paused' => 'Adrift',
        'dead' => 'Sent ter Davy Jones',
        'removing' => 'Scuttlin\'',
        'stopping' => 'Droppin\' Anchor',
        'offline' => 'Offline',
        'missing' => 'Lost at Sea',
    ],
    'websocket_error' => [
        'title' => 'Could not establish a websocket connection, matey!',
        'body' => 'Check yer browser console fer more details.',
    ],
];
