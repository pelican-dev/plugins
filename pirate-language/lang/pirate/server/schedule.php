<?php

return [
    'title' => 'Ship\'s Orders',
    'new' => 'New Order',
    'edit' => 'Edit Order',
    'save' => 'Save Order',
    'delete' => 'Scuttle Order',
    'import' => 'Import Order',
    'export' => 'Export Order',
    'name' => 'Name',
    'cron' => 'Cron',
    'status' => 'Status',
    'schedule_status' => [
        'inactive' => 'Inactive',
        'processing' => 'Under Way',
        'active' => 'Active',
    ],
    'no_tasks' => 'No Tasks',
    'run_now' => 'Execute Now',
    'online_only' => 'Only While Sailin\'',
    'last_run' => 'Last Voyage',
    'next_run' => 'Next Voyage',
    'never' => 'Never',
    'cancel' => 'Belay That',

    'only_online' => 'Only when the Vessel be Sailin\'?',
    'only_online_hint' => 'Only execute this order when the vessel be runnin\'.',
    'enabled' => 'Enable Order?',
    'enabled_hint' => 'This order shall be carried out automatically if enabled.',

    'cron_body' => 'Keep in mind, matey, that the cron values below always use UTC.',
    'cron_timezone' => 'Next voyage in yer timezone (:timezone): <b>:next_run</b>',

    'invalid' => 'Invalid',

    'time' => [
        'minute' => 'Minute',
        'hour' => 'Hour',
        'day' => 'Day',
        'week' => 'Week',
        'month' => 'Month',
        'day_of_month' => 'Day o\' the Month',
        'day_of_week' => 'Day o\' the Week',

        'hourly' => 'Every Hour',
        'daily' => 'Daily',
        'weekly_mon' => 'Weekly (Monday)',
        'weekly_sun' => 'Weekly (Sunday)',
        'monthly' => 'Monthly',
        'every_min' => 'Every x Minutes',
        'every_hour' => 'Every x Hours',
        'every_day' => 'Every x Days',
        'every_week' => 'Every x Weeks',
        'every_month' => 'Every x Months',
        'every_day_of_week' => 'Every x Day o\' the Week',

        'every' => 'Every',
        'minutes' => 'Minutes',
        'hours' => 'Hours',
        'days' => 'Days',
        'months' => 'Months',

        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ],

    'tasks' => [
        'title' => 'Crew Duties',
        'create' => 'Create Duty',
        'limit' => 'Duty Limit Reached',
        'action' => 'Action',
        'payload' => 'Cargo',
        'no_payload' => 'No Cargo',
        'time_offset' => 'Time Offset',
        'first_task' => 'First Duty',
        'seconds' => 'Second|Seconds',
        'continue_on_failure' => 'Continue if the Duty Fails',

        'actions' => [
            'title' => 'Action',
            'power' => [
                'title' => 'Issue Ship Command',
                'action' => 'Ship Command',
                'start' => 'Set Sail',
                'stop' => 'Drop Anchor',
                'restart' => 'Change Course',
                'kill' => 'Scuttle',
            ],
            'command' => [
                'title' => 'Issue Command',
                'command' => 'Command',
            ],
            'backup' => [
                'title' => 'Create Backup Chest',
                'files_to_ignore' => 'Files ter Ignore',
            ],
            'delete_files' => [
                'title' => 'Scuttle Files',
                'files_to_delete' => 'Files ter Scuttle',
            ],
        ],
    ],

    'notification_invalid_cron' => 'The cron data provided does not chart a valid course',

    'import_action' => [
        'file' => 'File',
        'url' => 'URL',
        'schedule_help' => 'This should be the raw .json file (schedule-daily-restart.json)',
        'url_help' => 'URLs must point directly ter the raw .json file',
        'add_url' => 'New URL',
        'import_failed' => 'Import Failed',
        'import_success' => 'Import Successful',
    ],
];
