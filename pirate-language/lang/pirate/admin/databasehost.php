<?php

return [
    'nav_title' => 'Treasure Vault Keepers',
    'model_label' => 'Vault Keeper',
    'model_label_plural' => 'Vault Keepers',
    'table' => [
        'database' => 'Treasure Vault',
        'name' => 'Keeper\'s Name',
        'host' => 'Harbor',
        'port' => 'Dock',
        'name_helper' => 'Leavin\' this blank will generate a random keeper name',
        'username' => 'Keeper\'s Handle',
        'password' => 'Secret Code',
        'remote' => 'Ships Allowed',
        'remote_helper' => 'Which ships can access this vault. Leave blank to allow all ships.',
        'max_connections' => 'Max Ship Visits',
        'created_at' => 'Built On',
        'connection_string' => 'JDBC Route Map',
    ],
    'error' => 'Trouble reachin\' the keeper',
    'host' => 'Keeper\'s Harbor',
    'host_help' => 'The harbor location that should be used when attemptin\' to reach this MySQL keeper to build new vaults.',
    'port' => 'Keeper\'s Dock',
    'port_help' => 'The dock where MySQL keeper be waitin\'.',
    'max_database' => 'Max Vaults',
    'max_databases_help' => 'The maximum number of vaults this keeper can guard. If the limit be reached, no new vaults can be built. Blank means no limit.',
    'display_name' => 'Keeper\'s Display Name',
    'display_name_help' => 'The harbor mark that should be shown to the crew.',
    'username' => 'Keeper\'s Handle',
    'username_help' => 'The username of an account that has enough permissions to create new users and databases on the system.',
    'password' => 'Password',
    'password_help' => 'The password fer the database user.',
    'linked_nodes' => 'Linked Harbors',
    'linked_nodes_help' => 'This settin\' only defaults to this vault keeper when addin\' a vault to a ship at the selected Harbor.',
    'connection_error' => 'Error connectin\' to vault keeper',
    'no_database_hosts' => 'No Vault Keepers',
    'no_nodes' => 'No Harbors',
    'delete_help' => 'Database Host Has Databases',
    'unlimited' => 'Unlimited',
    'anywhere' => 'Anywhere',

    'rotate' => 'Rotate',
    'rotate_password' => 'Rotate Password',
    'rotated' => 'Password Rotated',
    'rotate_error' => 'Password Rotation Failed',
    'databases' => 'Databases',

    'setup' => [
        'preparations' => 'Preparations',
        'database_setup' => 'Database Setup',
        'panel_setup' => 'Panel Setup',

        'note' => 'Currently, only MySQL/ MariaDB databases be supported fer database hosts!',
        'different_server' => 'Are the panel and the database <i>not</i> on the same server?',

        'database_user' => 'Database User',
        'cli_login' => 'Use <code>mysql -u root -p</code> to access mysql cli.',
        'command_create_user' => 'Command to create the user',
        'command_assign_permissions' => 'Command to assign permissions',
        'cli_exit' => 'To exit mysql cli run <code>exit</code>.',
        'external_access' => 'External Access',
        'allow_external_access' => '
                                    <p>Chances are ye\'ll need to allow external access to this MySQL instance in order to allow servers to connect to it.</p>
                                    <br>
                                    <p>To do this, open <code>my.cnf</code>, which varies in location dependin\' on yer OS and how MySQL was installed. Ye can type find <code>/etc -iname my.cnf</code> to locate it.</p>
                                    <br>
                                    <p>Open <code>my.cnf</code>, add text below to the bottom of the file and save it:<br>
                                    <code>[mysqld]<br>bind-address=0.0.0.0</code></p>
                                    <br>
                                    <p>Restart MySQL/ MariaDB to apply these changes. This will override the default MySQL configuration, which by default will only accept requests from localhost. Updatin\' this will allow connections on all interfaces, and thus, external connections. Make sure to allow the MySQL port (default 3306) in yer firewall.</p>
                                ',
    ],
];
