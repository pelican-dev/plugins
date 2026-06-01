<?php

return [
    'title' => 'Captain\'s Settings',
    'save_success' => 'Settings saved to the ship\'s log',
    'save_failed' => 'Failed to save ship settings',

    'navigation' => [
        'general' => 'General Orders',
        'captcha' => 'Anti-Spam Cannons (Captcha)',
        'mail' => 'Messenger Mail',
        'backup' => 'Treasure Backups',
        'oauth' => 'OAuth Gates',
        'misc' => 'Miscellaneous Loot',
    ],

    'general' => [
        'app_name' => 'Ship Name',
        'app_logo' => 'Ship Flag (Logo)',
        'app_logo_help' => 'Place logo in the public hold. Leave blank to use ship name instead.',
        'app_favicon' => 'Small Flag (Favicon)',
        'app_favicon_help' => 'Place favicon in the public hold.',

        'debug_mode' => 'Debug Mode',
        'navigation' => 'Navigation Style',
        'default_navigation' => 'Default Navigation Type',
        'sidebar' => 'Side Deck',
        'topbar' => 'Top Deck',
        'mixed' => 'Mixed Rigging',

        'unit_prefix' => 'Unit Prefix',
        'decimal_prefix' => 'Decimal Prefix (MB/GB)',
        'binary_prefix' => 'Binary Prefix (MiB/GiB)',

        '2fa_requirement' => 'Two-Factor Lock',
        'not_required' => 'Not Required',
        'admins_only' => 'Only for Officers',
        'all_users' => 'For All Crew',

        'trusted_proxies' => 'Trusted Sea Gates (Proxies)',
        'trusted_proxies_help' => 'Add trusted IPs or ranges',
        'clear' => 'Clear the List',
        'set_to_cf' => 'Set to Cloudflare Fleet',

        'display_width' => 'Display Width',
        'avatar_provider' => 'Crew Avatar Provider',
        'uploadable_avatars' => 'Allow crew to upload their own flags?',
    ],

    'captcha' => [
        'enable' => 'Enable Cannons',
        'disable' => 'Disable Cannons',
        'info_label' => 'Info',
        'info' => 'Generate keys in your <u><a href="https://developers.cloudflare.com/turnstile/get-started/#get-a-sitekey-and-secret-key" target="_blank">Cloudflare Control Room</a></u>. A Cloudflare account be required.',
        'site_key' => 'Site Key',
        'secret_key' => 'Secret Key',
        'verify' => 'Verify Domain?',
    ],

    'mail' => [
        'mail_driver' => 'Messenger System',
        'test_mail' => 'Send Test Message',
        'test_mail_sent' => 'Test message sent',
        'test_mail_failed' => 'Test message failed',

        'from_settings' => 'Sender Settings',
        'from_settings_help' => 'Set sender name and address used in messages.',
        'from_address' => 'From Address',
        'from_name' => 'From Name',

        'smtp' => [
            'smtp_title' => 'SMTP Sea Mail Configuration',
            'host' => 'Host',
            'port' => 'Port',
            'username' => 'Username',
            'password' => 'Password',
            'scheme' => 'Scheme',
        ],

        'mailgun' => [
            'mailgun_title' => 'Mailgun Post System',
            'domain' => 'Domain',
            'secret' => 'Secret',
            'endpoint' => 'Endpoint',
        ],
    ],

    'backup' => [
        'backup_driver' => 'Backup Vault System',
        'throttle' => 'Backup Limits',
        'throttle_help' => 'Limit how many backups can be made per time. Set to 0 to disable.',

        'limit' => 'Limit',
        'period' => 'Period',
        'seconds' => 'Seconds',

        's3' => [
            's3_title' => 'S3 Treasure Storage',
            'default_region' => 'Default Region',
            'access_key' => 'Access Key ID',
            'secret_key' => 'Secret Access Key',
            'bucket' => 'Treasure Bucket',
            'endpoint' => 'Endpoint',
            'use_path_style_endpoint' => 'Use Path-Style Routing',
        ],
    ],

    'oauth' => [
        'enable' => 'Enable Gate',
        'enable_schema' => 'Enable :schema Gate',
        'disable' => 'Disable Gate',

        'client_id' => 'Client ID',
        'client_secret' => 'Client Secret',
        'redirect' => 'Redirect URL',
        'web_api_key' => 'Web API Key',
        'base_url' => 'Base URL',

        'display_name' => 'Display Name',
        'auth_url' => 'Authorization Callback URL',

        'create_missing_users' => 'Auto Create Missing Crew?',
        'link_missing_users' => 'Auto Link Missing Crew?',
    ],

    'misc' => [
        'auto_allocation' => [
            'title' => 'Automatic Port Assignment',
            'helper' => 'Allow crew to create allocations from the client deck.',
            'question' => 'Allow crew to create allocations?',

            'create_new' => 'Create new allocations if none exist?',
            'create_new_help' => 'If enabled, new ports are forged. If disabled, only unused ports are assigned.',

            'start' => 'Start Port',
            'end' => 'End Port',
        ],

        'mail_notifications' => [
            'title' => 'Messenger Notifications',
            'helper' => 'Control which messages are sent to crew.',

            'account_created' => 'Account Created',
            'added_to_server' => 'Added to Ship',
            'removed_from_server' => 'Removed from Ship',
            'server_installed' => 'Ship Installed',
            'server_reinstalled' => 'Ship Rebuilt',
            'backup_completed' => 'Backup Completed',
        ],

        'connections' => [
            'title' => 'Connections',
            'helper' => 'Timeouts for sea requests.',
            'request_timeout' => 'Request Timeout',
            'connection_timeout' => 'Connection Timeout',
            'seconds' => 'Seconds',
        ],

        'activity_log' => [
            'title' => 'Ship Logs',
            'helper' => 'Control how long logs are kept and whether officer actions are recorded.',
            'prune_age' => 'Prune Age',
            'days' => 'Days',
            'log_admin' => 'Hide officer activity?',
        ],

        'api' => [
            'title' => 'API Cannon Control',
            'helper' => 'Rate limits for API usage.',
            'client_rate' => 'Client API Limit',
            'app_rate' => 'Application API Limit',
            'rpm' => 'Requests per Minute',
        ],

        'server' => [
            'title' => 'Ships',
            'helper' => 'Ship settings',
            'edit_server_desc' => 'Allow crew to edit descriptions?',
            'console_font_upload' => 'Console Font Upload',
            'console_font_hint' => 'Only *.ttf fonts allowed. Monospace preferred.',
        ],

        'webhook' => [
            'title' => 'Sea Hooks (Webhooks)',
            'helper' => 'Control webhook log cleanup.',
            'prune_age' => 'Prune Age',
            'days' => 'Days',
        ],
    ],
];
