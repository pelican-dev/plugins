<?php

return [
    'user' => [
        'search_users' => 'Enter a Captain Name, Sailor ID, or Messenger Address',
        'select_search_user' => 'ID of the sailor ter cast overboard (Enter \'0\' ter search again)',
        'deleted' => 'Sailor successfully removed from the Panel.',
        'confirm_delete' => 'Be ye certain ye wish ter remove this sailor from the Panel?',
        'no_users_found' => 'No sailors were found fer the search term provided.',
        'multiple_found' => 'Multiple crew members matched the search. Unable ter proceed because o\' the --no-interaction flag.',
        'ask_admin' => 'Be this sailor an Admiral?',
        'ask_email' => 'Messenger Address',
        'ask_username' => 'Captain Name',
        'ask_password' => 'Secret Passphrase',
        'ask_password_tip' => 'If ye wish ter create an account with a random passphrase sent by messenger bird, rerun this command (CTRL+C) and pass the `--no-password` flag.',
        'ask_password_help' => 'Passphrases must be at least 8 characters long and contain at least one capital letter and one number.',
        '2fa_help_text' => 'This command will disable two-factor authentication fer a sailor\'s account if it be enabled. This should only be used fer account recovery when a sailor be locked out. If this be not yer intent, press CTRL+C and abandon ship.',
        '2fa_disabled' => 'Two-Factor Authentication has been disabled fer :email.',
    ],
    'schedule' => [
        'output_line' => 'Dispatchin\' a job fer the first task in `:schedule` (:id).',
    ],
    'maintenance' => [
        'deleting_service_backup' => 'Scuttlin\' service backup file :file.',
    ],
    'server' => [
        'rebuild_failed' => 'Rebuild request fer ":name" (#:id) on node ":node" failed with error: :message',
        'reinstall' => [
            'failed' => 'Reinstallation request fer ":name" (#:id) on node ":node" failed with error: :message',
            'confirm' => 'Ye be about ter reinstall a whole fleet o\' servers. Do ye wish ter continue?',
        ],
        'power' => [
            'confirm' => 'Ye be about ter perform a :action upon :count servers. Do ye wish ter continue?',
            'action_failed' => 'Power command fer ":name" (#:id) on node ":node" failed with error: :message',
        ],
    ],
    'environment' => [
        'mail' => [
            'ask_smtp_host' => 'SMTP Host (e.g. smtp.gmail.com)',
            'ask_smtp_port' => 'SMTP Port',
            'ask_smtp_username' => 'SMTP Username',
            'ask_smtp_password' => 'SMTP Secret',
            'ask_mailgun_domain' => 'Mailgun Domain',
            'ask_mailgun_endpoint' => 'Mailgun Endpoint',
            'ask_mailgun_secret' => 'Mailgun Secret',
            'ask_mandrill_secret' => 'Mandrill Secret',
            'ask_postmark_username' => 'Postmark API Key',
            'ask_driver' => 'Which courier shall deliver yer messages?',
            'ask_mail_from' => 'Messenger address that all messages shall originate from',
            'ask_mail_name' => 'Name that messages should appear ter come from',
            'ask_encryption' => 'Encryption method ter employ',
        ],
    ],
];
