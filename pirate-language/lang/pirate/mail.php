<?php

return [
    'greeting' => 'Ahoy, :name!',

    'account_created' => [
        'body' => 'Ye be receivin\' this message because an account has been created fer ye aboard :app.',
        'username' => 'Captain Name: :username',
        'email' => 'Messenger Address: :email',
        'action' => 'Set Up Yer Account',
    ],

    'added_to_server' => [
        'body' => 'Ye have been added as a deckhand fer the followin\' server, grantin\' ye certain control o\'er the vessel.',
        'server_name' => 'Server Name: :name',
        'action' => 'Board Server',
    ],

    'removed_from_server' => [
        'body' => 'Ye have been removed as a deckhand fer the followin\' server.',
        'server_name' => 'Server Name: :name',
        'action' => 'Visit the Panel',
    ],

    'server_installed' => [
        'body' => 'Yer server has finished its construction voyage and be ready fer service.',
        'server_name' => 'Server Name: :name',
        'action' => 'Log In and Set Sail',
    ],

    'backup_completed' => [
        'body_success' => 'The backup chest was successfully created.',
        'body_failed' => 'The backup creation voyage has failed.',
        'backup_name' => 'Backup Chest: :name',
        'server_name' => 'Server Name: :name',
        'action' => 'View Backup Chests',
    ],

    'mail_tested' => [
        'subject' => 'Panel Test Message, Matey',
        'body' => 'This be a test o\' the Panel mail system. Ye be ready ter set sail!',
    ],
];
