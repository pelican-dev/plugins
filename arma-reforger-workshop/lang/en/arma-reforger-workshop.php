<?php

return [
    'navigation' => [
        'workshop_mods' => 'Workshop Mods',
        'browse_workshop' => 'Browse Workshop',
    ],
    'titles' => [
        'browse_workshop' => 'Browse Arma Reforger Workshop',
    ],
    'labels' => [
        'mod' => 'Mod',
        'mod_id' => 'Mod ID',
        'mod_name' => 'Mod Name',
        'author' => 'Author',
        'version' => 'Version',
        'subscribers' => 'Subscribers',
        'downloads' => 'Downloads',
        'rating' => 'Rating',
        'type' => 'Type',
        'config_path' => 'Config Path',
        'installed_mods' => 'Installed Mods',
    ],
    'actions' => [
        'add_mod' => 'Add Mod',
        'add_to_server' => 'Add to Server',
        'browse_workshop' => 'Browse Workshop',
        'edit_config' => 'Edit Config',
        'remove' => 'Remove',
        'installed' => 'Installed',
        'view_installed_mods' => 'View Installed Mods',
        'open_in_browser' => 'Open in Browser',
    ],
    'form' => [
        'mod_id_helper' => 'Enter the mod ID (GUID) from the Bohemia Workshop. Example: 5965550F24A0C152',
        'mod_id_placeholder' => '5965550F24A0C152',
        'mod_name_helper' => 'A friendly name for the mod',
        'version_placeholder' => 'Optional - leave empty for latest',
        'mod_id_validation_regex' => 'The mod ID must be a 16-character hexadecimal string.',
    ],
    'modals' => [
        'remove_mod_heading' => 'Remove Mod',
        'remove_mod_description' => 'Are you sure you want to remove ":name" from your server\'s mod list?',
        'add_mod_heading' => 'Add ":name"',
        'add_mod_description' => 'This will add ":name" by :author to your server\'s mod list.',
    ],
    'sections' => [
        'browse_mods' => 'Browse Mods',
        'browse_mods_description' => 'Search and browse mods from the Bohemia Arma Reforger Workshop. Click "Add to Server" to install a mod.',
    ],
    'notifications' => [
        'mod_added' => 'Mod added',
        'mod_added_body' => "':name' has been added to your server configuration.",
        'mod_removed' => 'Mod removed',
        'mod_removed_body' => "':name' has been removed from your server configuration.",
        'failed_to_add' => 'Failed to add mod',
        'failed_to_remove' => 'Failed to remove mod',
        'config_update_failed' => 'Could not update the server configuration.',
    ],
];
