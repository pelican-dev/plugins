<?php

return [
    'user_resource_limits' => 'Benutzer Ressourcen Limit|Benutzer Ressourcen Limits',
    'user' => 'Benutzer|Benutzer',
    'cpu' => 'CPU',
    'memory' => 'RAM',
    'disk' => 'Speicherplatz',
    'server_limit' => 'Server Limit',
    'no_limit' => 'Kein Limit',
    'unlimited' => 'Unbegrenzt',
    'hint_unlimited' => '0 bedeutet Unbegrenzt',
    'name' => 'Server Name',
    'egg' => 'Egg',
    'left' => 'übrig',
    'variables' => 'Startup Variablen',

    'create_server' => 'Server erstellen',

    'modals' => [
        'delete_server_confirm' => 'Bist du sicher, dass du diesen Server löschen willst?',
        'delete_server_warning' => 'Dieser Vorgang kann nicht rückgängig gemacht werden und alle Daten gehen unwiderruflich verloren.',
        'delete_server' => 'Server löschen',
    ],

    'notifications' => [
        'server_resources_updated' => 'Server Ressourcen Limits aktualisiert',
        'might_need_restart' => 'Um die neuen Ressourcen Limits vollständig zu nutzen, ist möglicherweise ein Neustart des Servers erforderlich.',
        'manual_restart_needed' => 'Bitte starte deinen Server manuell neu, um die neuen Ressourcen Limits zu übernehmen.',

        'server_deleted' => 'Server gelöscht',
        'server_deleted_success' => 'Der Server wurde erfolgreich gelöscht.',
        'server_delete_error' => 'Konnte Server nicht löschen',

        'server_creation_failed' => 'Konnte Server nicht erstellen',
        'no_viable_node_found' => 'Es wurde keine verfügbare Node gefunden. Bitte wende dich an einen Panel Admin.',
        'no_viable_allocation_found' => 'Es wurde keine verfügbare Allocation gefunden. Bitte wende dich an einen Panel Admin.',
        'unknown_server_creation_error' => 'Unbekannter Fehler. Bitte wende dich an einen Panel Admin.',
    ],
];
