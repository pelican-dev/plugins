<?php

return [
    'template' => 'Backup Template|Backup Templates',

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
