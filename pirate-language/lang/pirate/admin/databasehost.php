<?php

return [
    'nav_title' => 'Database Havens',
    'model_label' => 'Database Haven',
    'model_label_plural' => 'Database Havens',

    'table' => [
        'database' => 'Database',
        'name' => 'Name o\' the Haven',
        'host' => 'Harbor (Host)',
        'port' => 'Dock Port',
        'name_helper' => 'Leave blank and a random name will be forged for ye',
        'username' => 'Crew Username',
        'password' => 'Secret Key (Password)',
        'remote' => 'Allowed From',
        'remote_helper' => 'Where ships may connect from. Leave blank to allow all seas.',
        'max_connections' => 'Max Ships (Connections)',
        'created_at' => 'Created At',
        'connection_string' => 'JDBC Sea Route String',
    ],

    'error' => 'Failed to connect to the harbor',
    'host' => 'Harbor',
    'host_help' => 'The IP or domain used by the panel to reach this MySQL harbor and forge new databases.',
    'port' => 'Dock Port',
    'port_help' => 'The port where MySQL be runnin\' on this harbor.',

    'max_database' => 'Max Treasures (Databases)',
    'max_databases_help' => 'The max number o\' databases allowed on this harbor. If reached, no more loot can be stored. Leave blank for infinite booty.',

    'display_name' => 'Name on the Map',
    'display_name_help' => 'The IP or domain shown to the end user sailor.',

    'username' => 'Database Crew Name',
    'username_help' => 'The username of a powerful account that can create new users and databases.',

    'password' => 'Secret Password',
    'password_help' => 'The password for the database account.',

    'linked_nodes' => 'Linked Ports (Nodes)',
    'linked_nodes_help' => 'This setting only applies when placing databases on selected nodes.',

    'connection_error' => 'Failed to connect to database harbor',
    'no_database_hosts' => 'No Database Havens Found',
    'no_nodes' => 'No Ports Found',
    'delete_help' => 'This harbor still holds treasures (databases)',
    'unlimited' => 'Unlimited',
    'anywhere' => 'Anywhere',

    'rotate' => 'Spin the Key',
    'rotate_password' => 'Rotate Secret Password',
    'rotated' => 'Password Successfully Rotated',
    'rotate_error' => 'Failed to Rotate the Secret Key',
    'databases' => 'Treasure Vaults',

    'setup' => [
        'preparations' => 'Preparations',
        'database_setup' => 'Database Setup',
        'panel_setup' => 'Captain\'s Panel Setup',

        'note' => 'Only MySQL / MariaDB treasure vaults be supported for now, matey!',

        'different_server' => 'Is the panel and database <i>not</i> on the same ship?',

        'database_user' => 'Database Crew Member',
        'cli_login' => 'Use <code>mysql -u root -p</code> to enter the command cabin.',
        'command_create_user' => 'Command to create a crew member',
        'command_assign_permissions' => 'Command to grant permissions',
        'cli_exit' => 'To leave the command cabin run <code>exit</code>.',

        'external_access' => 'Outer Sea Access',

        'allow_external_access' => '
            <p>Ye\'ll likely need to open the harbor gates so outside ships can connect to this MySQL instance.</p>
            <br>
            <p>To do so, open <code>my.cnf</code>, which may be hidden in different places dependin\' on yer system. Use <code>find /etc -iname my.cnf</code> to locate it.</p>
            <br>
            <p>Add this to the bottom o\' the scroll:<br>
            <code>[mysqld]<br>bind-address=0.0.0.0</code></p>
            <br>
            <p>Restart MySQL / MariaDB so the changes take effect. By default it only listens to localhost, but this lets it accept outside ships from all seas. Don\'t forget to open port 3306 in yer firewall cannons.</p>
        ',
    ],
];
