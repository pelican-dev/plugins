<?php

return [
    'navigation' => [
        'workshop_mods' => 'Workshop Mods',
    ],
    'labels' => [
        'mod_id' => 'Mod ID',
        'mod_name' => 'Mod Name',
        'version' => 'Version',
        'subscribers' => 'Subscribers',
        'downloads' => 'Downloads',
        'rating' => 'Rating',
        'config_path' => 'Config Path',
        'installed_mods' => 'Installed Mods',
    ],
    'actions' => [
        'add_mod' => 'Add Mod',
        'browse_workshop' => 'Browse Workshop',
        'edit_config' => 'Edit Config',
        'remove' => 'Remove',
    ],
    'form' => [
        'mod_id_helper' => 'Enter the mod ID (GUID) from the Bohemia Workshop. Example: 5965550F24A0C152',
        'mod_name_helper' => 'A friendly name for the mod',
        'version_placeholder' => 'Optional - leave empty for latest',
    ],
    'modals' => [
        'remove_mod_heading' => 'Remove Mod',
        'remove_mod_description' => 'Are you sure you want to remove ":name" from your server\'s mod list?',
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
