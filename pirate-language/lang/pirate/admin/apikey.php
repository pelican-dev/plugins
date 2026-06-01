<?php

return [
    'title' => 'Application Booty Keys',
    'empty' => 'No secret keys found in the hold',
    'whitelist' => 'Trusted Port Addresses (IPv4)',
    'whitelist_help' => 'Booty keys may only work from trusted ports. Enter each address on a new line.',
    'whitelist_placeholder' => 'Example: 127.0.0.1 or 192.168.1.1',
    'description' => 'What this key be for',
    'description_help' => 'A short tale of what this key unlocks.',
    'nav_title' => 'Booty Keys',
    'model_label' => 'Application Booty Key',
    'model_label_plural' => 'Application Booty Keys',

    'table' => [
        'key' => 'Secret Key',
        'description' => 'Tale',
        'last_used' => 'Last Seen in Action',
        'created' => 'Forged',
        'created_by' => 'Forged By',
        'never_used' => 'Never Been Used',
    ],

    'permissions' => [
        'all' => 'Set All Cannons',
        'all_description' => 'Quickly set all powers below to the same rank.',
        'none' => 'No Access',
        'read' => 'Spyglass Only (Read)',
        'read_write' => 'Read & Fire (Write)',
    ],
];
