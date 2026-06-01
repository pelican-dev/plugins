<?php

return [
    'nav_title' => 'Sea Eggs',
    'model_label' => 'Egg',
    'model_label_plural' => 'Sea Eggs',

    'tabs' => [
        'configuration' => 'Ship Configuration',
        'process_management' => 'Crew Process Control',
        'egg_variables' => 'Egg Variables (Sea Runes)',
        'install_script' => 'Hatching Script',
    ],

    'import' => [
        'file' => 'Scroll File',
        'url' => 'Map URL',
        'icon_url' => 'Flag Icon URL',
        'icon_error' => 'Could not fetch the flag',
        'egg_help' => 'Must be a raw .json/.yaml scroll',
        'url_help' => 'URLs must point directly to raw .json/.yaml scrolls',
        'add_url' => 'Add New Map URL',
        'import_failed' => 'Hatch Failed',
        'import_success' => 'Hatch Successful',
        'import_result' => 'Hatched :success of :total eggs',
        'imported_eggs' => 'Hatched: :eggs',
        'failed_import_eggs' => 'Failed Hatch: :eggs',
        'github' => 'GitHub Sea Archive',
        'refresh' => 'Refresh the Nest',
        'import_icon' => 'Import Flag',
        'delete_icon' => 'Remove Flag',
        'no_local_ip' => 'Local sea routes are forbidden',
        'unsupported_format' => 'Unknown scroll format. Supported: :formats',
        'invalid_url' => 'That map URL be invalid',
        'unknown_extension' => 'Unknown flag type (:extension)',
        'could_not_write' => 'Could not store flag in the hold',
        'icon_deleted' => 'Flag removed',
        'no_icon' => 'No flag provided',
        'icon_updated' => 'Flag updated',
    ],

    'export' => [
        'modal' => 'How would ye like to export this egg, matey :egg ?',
        'as' => 'As .:format scroll',
    ],

    'in_use' => 'In Use',
    'servers' => 'Ships Using This Egg',
    'name' => 'Name',
    'egg_uuid' => 'Egg UUID',
    'egg_id' => 'Egg ID',

    'name_help' => 'A simple name for identifying this egg across the seas.',
    'author' => 'Creator',
    'uuid_help' => 'A globally unique sea mark used by Wings to identify this egg.',
    'author_help' => 'The creator of this egg scroll.',
    'author_help_edit' => 'The creator of this version. Changing upload will change this.',
    'description' => 'Description',
    'description_help' => 'A tale describing this egg for all sailors to see.',

    'add_startup' => 'Add Boot Command',
    'startup_command' => 'Command',
    'startup_commands' => 'Boot Commands',
    'startup_name' => 'Display Name',
    'startup_help' => 'Boot commands available for ships using this egg. First is default.',

    'file_denylist' => 'Forbidden Files',
    'file_denylist_help' => 'Files the crew is not allowed to touch.',

    'features' => 'Ship Features',
    'force_ip' => 'Force Cannon IP',
    'force_ip_help' => 'Forces outgoing traffic to use the ship\'s main cannon IP. Required for certain games. Warning: disables internal ship networking between vessels.',

    'tags' => 'Markings',
    'update_url' => 'Update Sea Route',
    'update_url_help' => 'Must point directly to a raw .json scroll',

    'add_image' => 'Add Docker Beast',
    'docker_images' => 'Docker Beasts',
    'docker_name' => 'Beast Name',
    'docker_uri' => 'Beast URI',
    'docker_help' => 'Docker beasts available for this egg. First is default.',

    'stop_command' => 'Stop Command',
    'stop_command_help' => 'Command used to safely stop the ship. Use ^C for SIGINT.',
    'copy_from' => 'Copy From Another Egg',
    'copy_from_help' => 'Select another egg to inherit settings from.',
    'none' => 'None',

    'start_config' => 'Boot Completion Signs',
    'start_config_help' => 'Values the daemon watches to know when booting is complete.',

    'config_files' => 'Ship Configuration Scrolls',
    'config_files_help' => 'JSON scroll describing files to modify and how.',

    'log_config' => 'Log Scrolls',
    'log_config_help' => 'JSON scroll describing log locations and behavior.',

    'environment_variable' => 'Sea Variable',
    'default_value' => 'Default Value',
    'user_permissions' => 'Crew Permissions',
    'viewable' => 'Viewable',
    'editable' => 'Editable',
    'rules' => 'Rules of the Sea',
    'add_new_variable' => 'Add New Sea Variable',

    'error_unique' => 'A variable with this name already exists in the hold.',
    'error_required' => 'This variable be required, matey.',
    'error_reserved' => 'This variable is reserved by the captain.',

    'script_from' => 'Script Origin',
    'script_container' => 'Script Vessel',
    'script_entry' => 'Script Entry Point',
    'script_install' => 'Hatching Script',

    'no_eggs' => 'No Eggs Found',
    'no_servers' => 'No Ships',
    'no_servers_help' => 'No ships are currently using this egg.',

    'update' => 'Update|Update Selected',
    'updated' => 'Egg updated|:count/:total eggs updated',
    'updated_failed' => ':count failed',
    'updated_skipped' => ':count skipped',
    'update_success' => ':egg updated successfully',
    'update_failed' => 'Failed to update :egg',

    'update_question' => 'Are ye sure ye want to update this egg?|Are ye sure ye want to update these eggs?',
    'update_description' => 'Any changes will be overwritten!|Any changes to these eggs will be overwritten!',

    'no_updates' => 'No updates available for selected eggs',
    'cannot_update' => 'Cannot update :count egg(s)',
    'no_update_url' => 'These eggs have no valid update URL: :eggs',
    'cannot_delete' => 'Cannot delete :count egg(s)',
    'eggs_have_servers' => 'These eggs still have ships attached: :eggs',

    'updated_from' => 'Successfully updated from: :url',
    'update_error' => 'Error: :error',

    'updated_eggs' => 'Updated: :eggs',
    'failed_eggs' => 'Failed: :eggs',
    'skipped_eggs' => 'Skipped: :eggs',
];
