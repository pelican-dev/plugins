<?php

return [
    'user_resource_limits' => 'Brukerreessursgrense|Brukerressursgrenser',
    'user' => 'Bruker|Brukere',
    'cpu' => 'CPU',
    'memory' => 'Minne',
    'disk' => 'Diskplass',
    'server_limit' => 'Servergrense',
    'no_limit' => 'Ingen grense',
    'unlimited' => 'Ubegrenset',
    'hint_unlimited' => '0 betyr ubegrenset',
    'name' => 'Servernavn',
    'egg' => 'Egg',
    'left' => 'igjen',
    'variables' => 'Oppstartsvariabler',

    'create_server' => 'Opprett server',

    'modals' => [
        'delete_server_confirm' => 'Er du sikker på at du vil slette denne serveren?',
        'delete_server_warning' => 'Denne handlingen kan ikke angres og all data vil gå tapt permanent.',
        'delete_server' => 'Slett server',
    ],

    'notifications' => [
        'server_resources_updated' => 'Serverressursgrenser oppdatert',
        'resource_limit_reached' => 'De forespurte ressursene overstiger tilgjengelig bruker- eller UCS-allokering.',
        'might_need_restart' => 'For å bruke de nye ressursgrensene kan det være nødvendig å starte serveren på nytt.',
        'manual_restart_needed' => 'Vennligst start serveren på nytt, manuelt, for å bruke de nye ressursgrensene.',

        'server_deleted' => 'Server slettet',
        'server_deleted_success' => 'Serveren ble slettet.',
        'server_delete_error' => 'Kunne ikke slette server',

        'server_creation_failed' => 'Kunne ikke opprette server',
        'no_viable_node_found' => 'Ingen tilgjengelig node ble funnet. Vennligst kontakt paneladministratoren.',
        'no_viable_allocation_found' => 'Ingen tilgjengelig allokering ble funnet. Vennligst kontakt paneladministratoren.',
        'unknown_server_creation_error' => 'Ukjent feil. Vennligst kontakt paneladministratoren.',
    ],
];
