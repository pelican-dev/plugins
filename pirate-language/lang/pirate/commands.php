<?php

return [
    'appsettings' => [
        'comment' => [
            'author' => 'Provide the email address that eggs exported by this Panel should be sent from, matey. It ought ter be a valid email address.',
            'url' => 'The application URL MUST begin with https:// or http:// dependin\' on whether ye be usin\' SSL. If ye forget the scheme, yer emails and other messages may point sailors to the wrong port.',
            'timezone' => "The timezone should match one o\' PHP's supported timezones. If ye be unsure, consult the chart at https://php.net/manual/en/timezones.php.",
        ],
        'redis' => [
            'note' => 'Ye have selected the Redis driver fer one or more options. Please provide proper connection details below. In most cases, the default values will serve ye well unless ye have altered yer setup.',
            'comment' => 'By default, a Redis server runs with the username "default" and no password, as it be local and hidden from the outside seas. If that be the case, simply press enter without enterin\' a value.',
            'confirm' => 'It seems a :field be already defined fer Redis. Would ye like ter change it?',
        ],
    ],
    'database_settings' => [
        'DB_HOST_note' => 'It be highly recommended not ter use "localhost" as yer database host, as we\'ve seen many socket troubles from those waters. If ye wish ter use a local connection, chart a course fer "127.0.0.1" instead.',
        'DB_USERNAME_note' => 'Usin\' the root account fer MySQL connections be greatly frowned upon, and this application forbids it entirely. Ye must create a dedicated MySQL user fer this software.',
        'DB_PASSWORD_note' => 'It appears ye already have a MySQL connection password set. Would ye like ter change it?',
        'DB_error_2' => 'Yer connection credentials have NOT been saved, matey. Ye must provide valid connection information before proceedin\'.',
        'go_back' => 'Turn back and try again',
    ],
    'make_node' => [
        'name' => 'Enter a short identifier ter distinguish this node from the others in the fleet',
        'description' => 'Enter a description ter identify the node',
        'scheme' => 'Please enter either https fer SSL or http fer an unsecured connection',
        'fqdn' => 'Enter a domain name (e.g. node.example.com) ter connect ter the daemon. An IP address may only be used if SSL be not enabled fer this node',
        'public' => 'Should this node be open ter the public seas? Markin\' it private will prevent automatic deployments ter this node.',
        'behind_proxy' => 'Be yer FQDN sailin\' behind a proxy?',
        'maintenance_mode' => 'Should maintenance mode be hoistin\' its colors?',
        'memory' => 'Enter the maximum amount o\' memory',
        'memory_overallocate' => 'Enter the amount o\' memory ter overallocate. -1 disables checkin\', and 0 prevents creatin\' new servers',
        'disk' => 'Enter the maximum amount o\' disk space',
        'disk_overallocate' => 'Enter the amount o\' disk space ter overallocate. -1 disables checkin\', and 0 prevents creatin\' new servers',
        'cpu' => 'Enter the maximum amount o\' CPU power',
        'cpu_overallocate' => 'Enter the amount o\' CPU ter overallocate. -1 disables checkin\', and 0 prevents creatin\' new servers',
        'upload_size' => 'Enter the maximum file size fer uploads',
        'daemonListen' => 'Enter the daemon listenin\' port',
        'daemonConnect' => 'Enter the daemon connection port (this may be the same as the listenin\' port)',
        'daemonSFTP' => 'Enter the daemon SFTP listenin\' port',
        'daemonSFTPAlias' => 'Enter the daemon SFTP alias (may be left empty)',
        'daemonBase' => 'Enter the base folder',
        'success' => 'Successfully created a new node named :name with the ID :id, matey',
    ],
    'node_config' => [
        'error_not_exist' => 'The selected node be nowhere on the charts.',
        'error_invalid_format' => 'Invalid format specified. Valid options be yaml and json.',
    ],
    'key_generate' => [
        'error_already_exist' => 'It appears ye already have an application encryption key configured. Continuin\' will overwrite that key and may corrupt any existing encrypted data. DO NOT PROCEED UNLESS YE KNOW WHAT YE BE DOIN\'.',
        'understand' => 'I understand the consequences o\' runnin\' this command and accept full responsibility fer the loss o\' encrypted data.',
        'continue' => 'Are ye certain ye wish ter continue? Changin\' the application encryption key WILL CAUSE DATA LOSS.',
    ],
    'schedule' => [
        'process' => [
            'no_tasks' => 'There be no scheduled tasks awaitin\' execution aboard any server.',
            'error_message' => 'An error was encountered while processin\' Schedule: :schedules',
        ],
    ],
];
