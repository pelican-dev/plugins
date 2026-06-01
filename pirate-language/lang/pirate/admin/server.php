<?php

return [
    'nav_title' => 'Fleet of Ships',
    'model_label' => 'Ship',
    'model_label_plural' => 'Fleet of Ships',

    'no_servers' => 'No Ships in the Fleet',
    'create' => 'Launch New Ship',

    'ip_address' => 'IP Address',
    'import_icon' => 'Import Flag',
    'delete_icon' => 'Remove Flag',

    'ip_address_helper' => 'Usually yer ship\'s public IP unless ye be port forwardin\'',

    'port' => 'Port',
    'ports' => 'Ports',
    'alias' => 'Alias',
    'alias_helper' => 'Optional name to help ye remember this ship',

    'locked' => 'Locked?',
    'locked_helper' => 'Crew cannot delete locked allocations',
    'lock' => 'Lock',
    'unlock' => 'Unlock',

    'name' => 'Ship Name',
    'external_id' => 'External ID',
    'owner' => 'Captain',
    'description' => 'Description',

    'install_script' => 'Run Launch Script?',
    'start_after' => 'Start After Launch?',

    'yes' => 'Aye',
    'no' => 'Nay',
    'skip' => 'Skip',

    'primary' => 'Primary Port',
    'already_primary' => 'Already Primary',
    'make_primary' => 'Make Primary',

    'startup_cmd' => 'Boot Command',
    'startup_name' => 'Boot Name',
    'default_startup' => 'Default Boot Command',
    'startup_placeholder' => 'Enter custom boot command',

    'variables' => 'Sea Variables',

    'resource_limits' => 'Ship Resources',

    'cpu' => 'CPU',
    'cpu_limit' => 'CPU Limit',
    'cpu_helper' => '100% equals one cannon thread',

    'unlimited' => 'Unlimited',
    'limited' => 'Limited',

    'enabled' => 'Enabled',
    'disabled' => 'Disabled',

    'memory' => 'Memory',
    'memory_limit' => 'Memory Limit',
    'memory_helper' => 'Wings adds overhead so the ship doesn\'t starve when maxed',

    'disk' => 'Cargo Hold Space',
    'disk_limit' => 'Cargo Limit',

    'advanced_limits' => 'Advanced Cannon Controls',

    'cpu_pin' => 'CPU Pinning',
    'threads' => 'Pinned Threads',
    'pin_help' => 'Pin threads like 0 or 2-4',

    'swap' => 'Swap Memory',
    'swap_limit' => 'Swap Limit',
    'oom' => 'Ogre Memory Killer (OOM)',

    'feature_limits' => 'Feature Limits',

    'docker_settings' => 'Docker Settings',
    'docker_image' => 'Docker Beast Image',
    'image_name' => 'Beast Name',
    'primary_allocation' => 'Primary Port',
    'image' => 'Image',
    'image_placeholder' => 'Enter custom beast image',

    'container_labels' => 'Container Markings',

    'title' => 'Title',
    'actions' => 'Actions',
    'console' => 'Console',

    'suspend' => 'Suspend Ship',
    'unsuspend' => 'Unsuspend Ship',
    'reinstall' => 'Rebuild Ship',

    'reinstall_help' => 'This will rebuild the ship using its egg install script.',
    'reinstall_modal_heading' => 'Ye sure ye want to rebuild this ship?',
    'reinstall_modal_description' => '!! This may result in lost booty (data) !!',

    'server_status' => 'Ship Status',
    'view_install_log' => 'View Launch Log',
    'uuid' => 'UUID',
    'node' => 'Port',
    'short_uuid' => 'Short UUID',

    'toggle_install' => 'Toggle Install Status',
    'toggle_install_help' => 'Switch between installed and not installed states.',
    'toggle_install_failed_header' => 'Ship is in failed state',
    'toggle_install_failed_desc' => 'Do ye want to rebuild it?',

    'transfer' => 'Transfer Ship',
    'transfer_help' => 'Move this ship to another port. Experimental—back up yer booty first!',

    'condition' => 'Condition',

    'suspend_all' => 'Suspend All Ships',
    'unsuspend_all' => 'Unsuspend All Ships',

    'select_allocation' => 'Select Port',
    'new_allocation' => 'Create New Port',

    'additional_allocations' => 'Extra Ports',
    'select_additional' => 'Select Extra Ports',

    'no_variables' => 'No sea variables for this egg!',
    'select_egg' => 'Select an egg first!',

    'allocations' => 'Allocations',
    'databases' => 'Databases',
    'no_databases' => 'No treasure databases exist',

    'delete_db' => 'Ye sure ye want to delete :name?',
    'delete_db_heading' => 'Delete Treasure Vault?',

    'backups' => 'Backups',
    'egg' => 'Egg',
    'mounts' => 'Mounts',
    'no_mounts' => 'No mounts on this node',

    'create_database' => 'Create Database',
    'no_db_hosts' => 'No Database Hosts',
    'failed_to_create' => 'Failed to create database',

    'change_egg' => 'Change Egg',
    'new_egg' => 'New Egg',
    'keep_old_variables' => 'Keep old variables if possible?',

    'create_allocation' => 'Create Port',
    'add_allocation' => 'Add Port',

    'view' => 'View',
    'no_log' => 'No Log Found',

    'select_backups' => 'Select Backups',
    'warning_backups' => 'Untransferred backups will be lost at sea.',

    'tabs' => [
        'information' => 'Information',
        'egg_configuration' => 'Egg Configuration',
        'environment_configuration' => 'Environment Configuration',
    ],

    'notifications' => [
        'server_suspension' => 'Ship Suspension',
        'server_suspended' => 'Ship has been suspended',
        'server_already_suspended' => 'Ship already be suspended!',

        'server_suspend_help' => 'Stops the ship and locks the crew out.',
        'server_unsuspend_help' => 'Restores normal ship access.',
        'server_unsuspended' => 'Ship has been unsuspended',

        'error_server_delete' => 'Ship could not be safely destroyed.',
        'error_server_delete_body' => 'Ye may force it if ye dare.',

        'create_failed' => 'Could not launch ship',

        'invalid_port_range' => 'Invalid Port Range',
        'invalid_port_range_body' => 'Yer ports be cursed: :port',

        'too_many_ports' => 'Too many ports!',
        'too_many_ports_body' => 'Limit be :limit ports at once',

        'invalid_port' => 'Invalid port',
        'invalid_port_body' => ':i be outside valid range :portFloor-:portCeil',

        'already_exists' => 'Port already taken',
        'already_exists_body' => ':i already has an allocation',

        'error_connecting' => 'Error connecting to :node',
        'error_connecting_description' => 'Wings sync failed—restart manually.',

        'install_toggled' => 'Install status changed',
        'install_toggle_failed' => 'Could not toggle install',

        'reinstall_started' => 'Rebuild started',
        'reinstall_failed' => 'Rebuild failed',

        'log_failed' => 'Could not fetch install log',

        'transfer_started' => 'Transfer started',
        'transfer_failed' => 'Transfer failed',
        'already_transfering' => 'Ship already being transferred',

        'backup_transfer_failed' => 'Backup transfer failed',
    ],

    'notes' => 'Notes',
    'no_notes' => 'No Notes',
    'none' => 'None',
];
