<?php

return [
    'nav_title' => 'Docking Mounts',
    'model_label' => 'Mount',
    'model_label_plural' => 'Docking Mounts',

    'name' => 'Name',
    'name_help' => 'A unique name used to tell this mount apart from others in the fleet.',

    'source' => 'Source',
    'source_help' => 'Path on the ship\'s hull (host system) to mount into the vessel.',

    'target' => 'Target',
    'target_help' => 'Where this mount be accessible inside the ship\'s hold (container).',

    'read_only' => 'Read Only?',
    'read_only_help' => 'Is this mount locked so no crew can modify it?',

    'description' => 'Description',
    'description_help' => 'A longer tale about this mount.',

    'no_mounts' => 'No Docking Mounts Found',

    'eggs' => 'Eggs Using This Mount',
    'nodes' => 'Ports Using This Mount',

    'user_mountable' => 'Crew-Selectable?',
    'user_mountable_help' => 'Can ordinary crew toggle this mount for their ships?',

    'toggles' => [
        'writable' => 'Writable',
        'read_only' => 'Locked (Read Only)',
        'user_mountable' => 'Crew Allowed',
        'not_user_mountable' => 'Captain Only',
    ],

    'table' => [
        'name' => 'Name',
        'all_eggs' => 'All Eggs',
        'all_nodes' => 'All Ports',
        'read_only' => 'Read Only',
        'user_mountable' => 'Crew Mountable',
    ],
];
