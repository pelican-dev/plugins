<?php

return [
    'title' => 'Health',
    'results_refreshed' => 'Health check results be updated, aye!',
    'checked' => 'Checked results from :time, matey',
    'refresh' => 'Refresh the lookout',
    'results' => [
        'cache' => [
            'label' => 'Cache',
            'ok' => 'Arr, all be shipshape',
            'failed_retrieve' => "Couldn't set or fetch a cache value in the hold.",
            'failed' => 'A cursed error struck the cache: :error',
        ],
        'database' => [
            'label' => 'Database',
            'ok' => 'Arr, database be connected',
            'failed' => "Couldn't connect to the database port: :error",
        ],
        'debugmode' => [
            'label' => 'Debug Mode',
            'ok' => 'Debug mode be disabled, smooth sailin’',
            'failed' => 'Debug mode was expected to be :expected, but it be :actual instead!',
        ],
        'environment' => [
            'label' => 'Environment',
            'ok' => 'Aye, environment set to :actual',
            'failed' => 'Environment be set to :actual, but we expected :expected',
        ],
        'nodeversions' => [
            'label' => 'Harbor Versions',
            'ok' => 'All Harbors be up-to-date and seaworthy',
            'failed' => ':outdated out o’ :all Harbors be outdated, arr!',
            'no_nodes_created' => 'No Harbors built yet, matey',
            'no_nodes' => 'No Harbors in sight',
            'all_up_to_date' => 'All be shipshape and up-to-date',
            'outdated' => ':outdated out o’ :all be outdated',
        ],
        'panelversion' => [
            'label' => 'Panel Version',
            'ok' => 'Panel be shipshape and up-to-date',
            'failed' => 'Installed version be :currentVersion but latest be :latestVersion, matey!',
            'up_to_date' => 'All caught up',
            'outdated' => 'Old as a barnacle',
        ],
        'schedule' => [
            'label' => 'Schedule',
            'ok' => 'Aye, all good',
            'failed_last_ran' => 'The last time the schedule ran was over :time minutes ago, arr!',
            'failed_not_ran' => "The schedule hasn't set sail yet.",
        ],
        'useddiskspace' => [
            'label' => 'Disk Space',
        ],
    ],
    'checks' => [
        'successful' => 'Fair winds',
        'failed' => 'Davy Jones’ locker',
    ],
];
