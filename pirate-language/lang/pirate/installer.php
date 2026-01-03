<?php

return [
    'title' => 'Ship\'s Panel Setup, Matey!',
    'requirements' => [
        'title' => 'Ship\'s Requirements',
        'sections' => [
            'version' => [
                'title' => 'PHP Version',
                'or_newer' => ':version or a newer vessel',
                'content' => 'Yer PHP Version be :version.',
            ],
            'extensions' => [
                'title' => 'PHP Rigging',
                'good' => 'All needed PHP rigging be properly installed.',
                'bad' => 'These here PHP rigging pieces be missin\': :extensions',
            ],
            'permissions' => [
                'title' => 'Cargo Hold Permissions',
                'good' => 'All holds have the proper access rights.',
                'bad' => 'These here holds have wrong permissions: :folders',
            ],
        ],
        'exception' => 'Some ship requirements be missin\', arr!',
    ],
    'environment' => [
        'title' => 'Ship\'s Environment',
        'fields' => [
            'app_name' => 'Ship\'s Name',
            'app_name_help' => 'This will be the name of yer fine ship.',
            'app_url' => 'Harbor Location',
            'app_url_help' => 'This be the sea route ye\'ll use to reach yer ship.',
            'account' => [
                'section' => 'Captain\'s Account',
                'email' => 'Message Bottle',
                'username' => 'Pirate Name',
                'password' => 'Secret Code',
            ],
        ],
    ],
    'database' => [
        'title' => 'Database',
        'driver' => 'Database Driver',
        'driver_help' => 'The driver used fer the panel database. We recommend "SQLite".',
        'fields' => [
            'host' => 'Database Host',
            'host_help' => 'The host of yer database. Make sure it be reachable.',
            'port' => 'Database Port',
            'port_help' => 'The port of yer database.',
            'path' => 'Database Path',
            'path_help' => 'The path of yer .sqlite file relative to the database folder.',
            'name' => 'Database Name',
            'name_help' => 'The name of the panel database.',
            'username' => 'Database Username',
            'username_help' => 'The name of yer database user.',
            'password' => 'Database Password',
            'password_help' => 'The password of yer database user. Can be empty.',
        ],
        'exceptions' => [
            'connection' => 'Database connection failed',
            'migration' => 'Migrations failed',
        ],
    ],
    'session' => [
        'title' => 'Session',
        'driver' => 'Session Driver',
        'driver_help' => 'The driver used fer storin\' sessions. We recommend "Filesystem" or "Database".',
    ],
    'cache' => [
        'title' => 'Cache',
        'driver' => 'Cache Driver',
        'driver_help' => 'The driver used fer cachin\'. We recommend "Filesystem".',
        'fields' => [
            'host' => 'Redis Host',
            'host_help' => 'The host of yer redis server. Make sure it be reachable.',
            'port' => 'Redis Port',
            'port_help' => 'The port of yer redis server.',
            'username' => 'Redis Username',
            'username_help' => 'The name of yer redis user. Can be empty',
            'password' => 'Redis Password',
            'password_help' => 'The password fer yer redis user. Can be empty.',
        ],
        'exception' => 'Redis connection failed',
    ],
    'queue' => [
        'title' => 'Queue',
        'driver' => 'Queue Driver',
        'driver_help' => 'The driver used fer handlin\' queues. We recommend "Database".',
        'fields' => [
            'done' => 'I have done both steps below.',
            'done_validation' => 'Ye need to do both steps before continuin\'!',
            'crontab' => 'Run the followin\' command to set up yer crontab. Note that <code>www-data</code> be yer webserver user. On some systems this username might be different!',
            'service' => 'To setup the queue worker service ye simply have to run the followin\' command.',
        ],
    ],
    'exceptions' => [
        'write_env' => 'Could not write to .env file',
        'migration' => 'Could not run migrations',
        'create_user' => 'Could not create admin user',
    ],
    'next_step' => 'Next Step',
    'finish' => 'Finish',
];
