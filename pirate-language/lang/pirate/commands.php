<?php

return [
    'appsettings' => [
        'comment' => [
            'author' => 'Provide the email address that eggs exported by this Panel should be from. This should be a valid email address.',
            'url' => 'The application URL MUST begin with https:// or http:// dependin\' on if ye be usin\' SSL or not. If ye don\'t include the scheme yer emails and other content will link to the wrong location.',
            'timezone' => "The timezone should match one of PHP\'s supported timezones. If ye be unsure, please reference https://php.net/manual/en/timezones.php.",
        ],
        'redis' => [
            'note' => 'Ye\'ve selected the Redis driver fer one or more options, please provide valid connection information below. In most cases ye can use the defaults provided unless ye have modified yer setup.',
            'comment' => 'By default a Redis server instance has fer username default and no password as it be runnin\' locally and inaccessible to the outside world. If this be the case, simply hit enter without enterin\' a value.',
            'confirm' => 'It seems a :field be already defined fer Redis, would ye like to change it?',
        ],
    ],
    'database_settings' => [
        'DB_HOST_note' => 'It be highly recommended to not use "localhost" as yer database host as we have seen frequent socket connection issues. If ye want to use a local connection ye should be usin\' "127.0.0.1".',
        'DB_USERNAME_note' => "Usin\' the root account fer MySQL connections be not only highly frowned upon, it be also not allowed by this application. Ye\'ll need to have created a MySQL user fer this software.",
        'DB_PASSWORD_note' => 'It appears ye already have a MySQL connection password defined, would ye like to change it?',
        'DB_error_2' => 'Yer connection credentials have NOT been saved. Ye will need to provide valid connection information before proceedin\'.',
        'go_back' => 'Go back and try again',
    ],
    'make_node' => [
        'name' => 'Enter a short identifier used to distinguish this node from others',
        'description' => 'Enter a description to identify the node',
        'scheme' => 'Please either enter https fer SSL or http fer a non-ssl connection',
        'fqdn' => 'Enter a domain name (e.g node.example.com) to be used fer connectin\' to the daemon. An IP address may only be used if ye ain\'t usin\' SSL fer this node',
        'public' => 'Should this node be public? As a note, settin\' a node to private ye will be denyin\' the ability to auto-deploy to this node.',
        'behind_proxy' => 'Be yer FQDN behind a proxy?',
        'maintenance_mode' => 'Should maintenance mode be enabled?',
        'memory' => 'Enter the maximum amount of memory',
        'memory_overallocate' => 'Enter the amount of memory to over allocate by, -1 will disable checkin\' and 0 will prevent creatin\' new servers',
        'disk' => 'Enter the maximum amount of disk space',
        'disk_overallocate' => 'Enter the amount of disk to over allocate by, -1 will disable checkin\' and 0 will prevent creatin\' new server',
        'cpu' => 'Enter the maximum amount of cpu',
        'cpu_overallocate' => 'Enter the amount of cpu to over allocate by, -1 will disable checkin\' and 0 will prevent creatin\' new server',
        'upload_size' => 'Enter the maximum filesize upload',
        'daemonListen' => 'Enter the daemon listenin\' port',
        'daemonConnect' => 'Enter the daemon connectin\' port (can be same as listen port)',
        'daemonSFTP' => 'Enter the daemon SFTP listenin\' port',
        'daemonSFTPAlias' => 'Enter the daemon SFTP alias (can be empty)',
        'daemonBase' => 'Enter the base folder',
        'success' => 'Successfully created a new node with the name :name and has an id of :id',
    ],
    'node_config' => [
        'error_not_exist' => 'The selected node does not exist.',
        'error_invalid_format' => 'Invalid format specified. Valid options be yaml and json.',
    ],
    'key_generate' => [
        'error_already_exist' => 'It appears ye have already configured an application encryption key. Continuin\' with this process with overwrite that key and cause data corruption fer any existin\' encrypted data. DO NOT CONTINUE UNLESS YE KNOW WHAT YE BE DOIN\'.',
        'understand' => 'I understand the consequences of performin\' this command and accept all responsibility fer the loss of encrypted data.',
        'continue' => 'Are ye sure ye wish to continue? Changin\' the application encryption key WILL CAUSE DATA LOSS.',
    ],
    'schedule' => [
        'process' => [
            'no_tasks' => 'There be no scheduled tasks fer servers that need to be run.',
            'error_message' => 'An error was encountered while processin\' Schedule: :schedules',
        ],
    ],
];
