<?php

return [
    'template' => 'Backup Template|Backup Templates',

    'permissions' => [
        'group' => 'Berechtigungen zum Verwalten von Backup-Vorlagen für diesen Server.',
        'create' => 'Erlaubt es einem Benutzer, Backup-Vorlagen für diesen Server zu erstellen, zu bearbeiten und zu löschen.',
    ],

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
