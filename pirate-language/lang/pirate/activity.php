<?php

return [
    'auth' => [
        'fail' => 'Failed ter log in, matey',
        'success' => 'Logged aboard',
        'password-reset' => 'Password reset, arrr',
        'checkpoint' => 'Two-factor authentication requested, ye scallywag',
        'recovery-token' => 'Used two-factor recovery token',
        'token' => 'Solved the two-factor challenge',
        'ip-blocked' => 'Blocked request from unlisted IP address fer <b>:identifier</b>',
        'sftp' => [
            'fail' => 'Failed SFTP log in, matey',
        ],
    ],
    'user' => [
        'account' => [
            'username-changed' => 'Changed username from <b>:old</b> ter <b>:new</b>',
            'email-changed' => 'Changed email from <b>:old</b> ter <b>:new</b>',
            'password-changed' => 'Changed the secret passphrase',
        ],
        'api-key' => [
            'create' => 'Forged a new API key <b>:identifier</b>',
            'delete' => 'Tossed API key <b>:identifier</b> overboard',
        ],
        'ssh-key' => [
            'create' => 'Added SSH key <b>:fingerprint</b> ter the account',
            'delete' => 'Removed SSH key <b>:fingerprint</b> from the account',
        ],
        'two-factor' => [
            'create' => 'Enabled two-factor auth, matey',
            'delete' => 'Disabled two-factor auth',
        ],
    ],
    'server' => [
        'console' => [
            'command' => 'Executed "<b>:command</b>" aboard the server',
        ],
        'power' => [
            'start' => 'Started the server',
            'stop' => 'Stopped the server',
            'restart' => 'Restarted the server',
            'kill' => 'Sent the server process ter Davy Jones\' Locker',
        ],
        'backup' => [
            'download' => 'Downloaded the <b>:name</b> backup',
            'delete' => 'Deleted the <b>:name</b> backup',
            'restore' => 'Restored the <b>:name</b> backup (deleted files: <b>:truncate</b>)',
            'restore-complete' => 'Finished restorin\' the <b>:name</b> backup',
            'restore-failed' => 'Failed ter restore the <b>:name</b> backup',
            'start' => 'Started a new backup <b>:name</b>',
            'complete' => 'Marked the <b>:name</b> backup as complete',
            'fail' => 'Marked the <b>:name</b> backup as failed',
            'lock' => 'Locked the <b>:name</b> backup chest',
            'unlock' => 'Unlocked the <b>:name</b> backup chest',
            'rename' => 'Renamed backup from "<b>:old_name</b>" ter "<b>:new_name</b>"',
        ],
        'database' => [
            'create' => 'Created a new database <b>:name</b>',
            'rotate-password' => 'Rotated the password fer database <b>:name</b>',
            'delete' => 'Deleted database <b>:name</b>',
        ],
        'file' => [
            'compress' => 'Packed up <b>:directory:files</b>|Packed up <b>:count</b> files in <b>:directory</b>',
            'read' => 'Peeked at the contents o\' <b>:file</b>',
            'copy' => 'Made a copy o\' <b>:file</b>',
            'create-directory' => 'Created directory <b>:directory:name</b>',
            'decompress' => 'Unpacked <b>:file</b> in <b>:directory</b>',
            'delete' => 'Scuttled <b>:directory:files</b>|Scuttled <b>:count</b> files in <b>:directory</b>',
            'download' => 'Downloaded <b>:file</b>',
            'pull' => 'Hauled a remote file from <b>:url</b> ter <b>:directory</b>',
            'rename' => 'Moved/Renamed <b>:from</b> ter <b>:to</b>|Moved/Renamed <b>:count</b> files in <b>:directory</b>',
            'write' => 'Penned new contents into <b>:file</b>',
            'upload' => 'Began uploadin\' a file',
            'uploaded' => 'Uploaded <b>:directory:file</b>',
        ],
        'sftp' => [
            'denied' => 'Blocked SFTP access due ter lack o\' permissions',
            'create' => 'Created <b>:files</b>|Created <b>:count</b> new files',
            'write' => 'Modified the contents o\' <b>:files</b>|Modified the contents o\' <b>:count</b> files',
            'delete' => 'Deleted <b>:files</b>|Deleted <b>:count</b> files',
            'create-directory' => 'Created the <b>:files</b> directory|Created <b>:count</b> directories',
            'rename' => 'Renamed <b>:from</b> ter <b>:to</b>|Renamed or moved <b>:count</b> files',
        ],
        'allocation' => [
            'create' => 'Added <b>:allocation</b> ter the server',
            'notes' => 'Updated the notes fer <b>:allocation</b> from "<b>:old</b>" ter "<b>:new</b>"',
            'primary' => 'Set <b>:allocation</b> as the primary server allocation',
            'delete' => 'Deleted the <b>:allocation</b> allocation',
        ],
        'schedule' => [
            'create' => 'Created the <b>:name</b> schedule',
            'update' => 'Updated the <b>:name</b> schedule',
            'execute' => 'Manually executed the <b>:name</b> schedule',
            'delete' => 'Deleted the <b>:name</b> schedule',
        ],
        'task' => [
            'create' => 'Created a new "<b>:action</b>" task fer the <b>:name</b> schedule',
            'update' => 'Updated the "<b>:action</b>" task fer the <b>:name</b> schedule',
            'delete' => 'Deleted the "<b>:action</b>" task fer the <b>:name</b> schedule',
        ],
        'settings' => [
            'rename' => 'Renamed the server from "<b>:old</b>" ter "<b>:new</b>"',
            'description' => 'Changed the server description from "<b>:old</b>" ter "<b>:new</b>"',
            'reinstall' => 'Reinstalled the server',
        ],
        'startup' => [
            'edit' => 'Changed the <b>:variable</b> variable from "<b>:old</b>" ter "<b>:new</b>"',
            'image' => 'Updated the Docker Image fer the server from <b>:old</b> ter <b>:new</b>',
            'command' => 'Updated the Startup Command fer the server from <b>:old</b> ter <b>:new</b>',
        ],
        'subuser' => [
            'create' => 'Added <b>:email</b> as a deckhand',
            'update' => 'Updated the deckhand permissions fer <b>:email</b>',
            'delete' => 'Cast <b>:email</b> off the crew',
        ],
        'mount' => [
            'update' => 'Updated the mounts fer the server',
        ],
        'crashed' => 'The server be sunk!',
    ],
];
