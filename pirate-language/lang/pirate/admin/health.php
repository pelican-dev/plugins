<?php

return [
    'title' => 'Ship Health',
    'results_refreshed' => 'Health check results refreshed in the crow\'s nest',
    'checked' => 'Scanned at :time',
    'refresh' => 'Re-scan the Ship',

    'results' => [
        'cache' => [
            'label' => 'Memory Cache',
            'ok' => 'All Good',
            'failed_retrieve' => 'Could not set or retrieve a cache value in the ship\'s memory.',
            'failed' => 'A curse struck the cache system: :error',
        ],

        'database' => [
            'label' => 'Treasure Vault (Database)',
            'ok' => 'All Good',
            'failed' => 'Could not connect to the vault: :error',
        ],

        'debugmode' => [
            'label' => 'Spy Mode (Debug)',
            'ok' => 'Spy mode be OFF',
            'failed' => 'Spy mode expected to be :expected, but instead be :actual',
        ],

        'environment' => [
            'label' => 'Ship Environment',
            'ok' => 'All Good, set to :actual',
            'failed' => 'Environment be :actual, but expected :expected',
        ],

        'nodeversions' => [
            'label' => 'Node Fleet Versions',
            'ok' => 'All ships in the fleet be up to date',
            'failed' => ':outdated/:all ships be running outdated gear',

            'no_nodes_created' => 'No ships built yet',
            'no_nodes' => 'No ships in the fleet',

            'all_up_to_date' => 'All ships current',
            'outdated' => ':outdated/:all outdated vessels',
        ],

        'panelversion' => [
            'label' => 'Captain\'s Panel Version',
            'ok' => 'Panel be up to date',
            'failed' => 'Ye run :currentVersion, but the latest be :latestVersion',

            'up_to_date' => 'Current',
            'outdated' => 'Outdated',
        ],

        'schedule' => [
            'label' => 'Ship Schedule',
            'ok' => 'All Good',
            'failed_last_ran' => 'Last voyage was more than :time minutes ago',
            'failed_not_ran' => 'No voyage has been set sail yet.',
        ],

        'useddiskspace' => [
            'label' => 'Cargo Hold Space',
        ],
    ],

    'checks' => [
        'successful' => 'Ship Be Healthy',
        'failed' => 'Trouble in :checks Systems',
    ],
];
