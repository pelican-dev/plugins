<?php

return [
    'title' => 'Panel Installer, Arrr!',
    'requirements' => [
        'title' => 'Ship Requirements',
        'sections' => [
            'version' => [
                'title' => 'PHP Version',
                'or_newer' => ':version or newer, matey',
                'content' => 'Yer PHP Version be :version.',
            ],
            'extensions' => [
                'title' => 'PHP Extensions',
                'good' => 'All required PHP Extensions be installed and seaworthy.',
                'bad' => 'The followin\' PHP Extensions be missin\': :extensions',
            ],
            'permissions' => [
                'title' => 'Folder Permissions',
                'good' => 'All folders have the proper permissions, matey.',
                'bad' => 'The followin\' folders have incorrect permissions: :folders',
            ],
        ],
        'exception' => 'Some requirements be missin\'',
    ],
    'environment' => [
        'title' => 'Environment',
        'fields' => [
            'app_name' => 'Ship Name',
            'app_name_help' => 'This be the name o\' yer Panel.',
            'app_url' => 'Ship URL',
            'app_url_help' => 'This be the address ye use ter access yer Panel.',
            'account' => [
                'section' => 'Captain\'s Account',
                'email' => 'E-Mail',
                'username' => 'Captain Name',
                'password' => 'Secret Passphrase',
            ],
        ],
    ],
    'database' => [
        'title' => 'Database',
        'driver' => 'Database Driver',
        'driver_help' => 'The driver used fer the panel database. We recommend "SQLite", matey.',
        'fields' => [
            'host' => 'Database Host',
            'host_help' => 'The host o\' yer database. Make certain it be reachable.',
            'port' => 'Database Port',
            'port_help' => 'The port o\' yer database.',
            'path' => 'Database Path',
            'path_help' => 'The path ter yer .sqlite file relative ter the database hold.',
            'name' => 'Database Name',
            'name_help' => 'The name o\' the panel database.',
            'username' => 'Database Username',
            'username_help' => 'The name o\' yer database sailor.',
            'password' => 'Database Password',
            'password_help' => 'The password o\' yer database sailor. May be left empty.',
        ],
        'exceptions' => [
            'connection' => 'Failed ter connect ter the database',
            'migration' => 'Migrations be failed, matey',
        ],
    ],
    'egg' => [
        'title' => 'Eggs',
        'no_eggs' => 'No Eggs Available Aboard',
        'background_install_started' => 'Egg Installation Hoisted',
        'background_install_description' => 'Installation o\' :count eggs has been queued and will continue below deck.',
        'exceptions' => [
            'failed_to_update' => 'Failed ter update the egg manifest',
            'no_eggs' => 'No eggs be available fer installation at this time.',
            'installation_failed' => 'Failed ter install the selected eggs. Please import \'em manually after installation via the egg manifest.',
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
            'host_help' => 'The host o\' yer Redis server. Ensure it be reachable.',
            'port' => 'Redis Port',
            'port_help' => 'The port o\' yer Redis server.',
            'username' => 'Redis Username',
            'username_help' => 'The name o\' yer Redis sailor. May be empty.',
            'password' => 'Redis Password',
            'password_help' => 'The password fer yer Redis sailor. May be empty.',
        ],
        'exception' => 'Failed ter connect ter Redis',
    ],
    'queue' => [
        'title' => 'Queue',
        'driver' => 'Queue Driver',
        'driver_help' => 'The driver used fer handlin\' queues. We recommend "Database".',
        'fields' => [
            'done' => 'Aye, I have completed both steps below.',
            'done_validation' => 'Ye must complete both steps before continuin\'!',
            'crontab' => 'Run the followin\' command ter set up yer crontab. Note that <code>www-data</code> be yer webserver sailor. On some systems this name may differ!',
            'service' => 'Ter set up the queue worker service, simply run the followin\' command.',
        ],
    ],
    'exceptions' => [
        'write_env' => 'Could not write ter the .env file',
        'migration' => 'Could not run the migrations',
        'create_user' => 'Could not create the captain\'s account',
    ],
    'finish' => 'Set Sail!',
];
