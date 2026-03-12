<?php

return [
    'template' => 'Backup Template|Backup Templates',

    'fields' => [
        'name' => 'Name',
        'ignored' => 'Ignorierte Dateien und Ordner',
        'ignored_help' => 'Verwenden Sie einen Pfad pro Zeile, der dem Pelican-Backup-Ignore-Format entspricht.',
    ],

    'backup_form' => [
        'template' => 'Ignorier-Vorlage',
        'template_placeholder' => 'Keine Vorlage ausgewählt',
        'template_help' => 'Verwenden Sie eine Vorlage, um ignorierte Pfade automatisch auszufüllen.',
    ],
];
