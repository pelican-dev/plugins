<?php

return [
    'template' => 'Backup Template|Backup Templates',

    'permissions' => [
        'group' => 'Permissions for managing backup templates for this server.',
        'create' => 'Allows a user to create, edit, and delete backup templates for this server.',
    ],

    'fields' => [
        'name' => 'Name',
        'ignored' => 'Ignored Files and Folders',
        'ignored_help' => 'Use one path per line, matching Pelican backup ignore format.',
    ],

    'backup_form' => [
        'template' => 'Ignore Preset',
        'template_placeholder' => 'No preset selected',
        'template_help' => 'Select a template to auto-fill ignored paths.',
    ],
];
