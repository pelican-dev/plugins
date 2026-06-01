<?php

return [
    'nav_title' => 'Ports of the Fleet',
    'model_label' => 'Port',
    'model_label_plural' => 'Ports of the Fleet',

    'create' => 'Raise a New Port',

    'tabs' => [
        'overview' => 'Overview',
        'basic_settings' => 'Basic Charts',
        'advanced_settings' => 'Advanced Charts',
        'config_file' => 'Ship Configuration Scroll',
        'diagnostics' => 'Ship Diagnostics',
    ],

    'table' => [
        'health' => 'Ship Health',
        'reachable' => 'Reachable',
        'name' => 'Name',
        'address' => 'Address',
        'public' => 'Public',
        'servers' => 'Ships',
        'alias' => 'Alias',
        'ip' => 'IP',
        'egg' => 'Egg',
        'owner' => 'Captain',
        'allocation_notes' => 'Notes',
        'no_notes' => 'No notes',
    ],

    'node_info' => 'Port Information',
    'wings_version' => 'Wings Version',
    'cpu_threads' => 'Cannon Threads (CPU)',
    'architecture' => 'Ship Architecture',
    'kernel' => 'Kernel',
    'unknown' => 'Unknown',
    'latest' => '(Latest: :version)',

    'node_uuid' => 'Port UUID',
    'node_id' => 'Port ID',

    'ip_address' => 'IP Address',
    'ip_help' => 'Usually yer ship\'s public IP unless ye be port forwardin\', matey.',
    'alias_help' => 'Optional name to help ye remember this port.',

    'refresh' => 'Refresh the Charts',
    'custom_ip' => 'Enter Custom IP',
    'domain' => 'Domain Name',
    'ssl_ip' => 'Consider usin\' a domain name instead of an IP',
    'fqdn_ssl' => 'Yer panel be secured with SSL, so yer ports must be too.',
    'dns_error' => 'No valid DNS records found for that domain.',
    'valid' => 'Valid Sea Route (DNS)',
    'invalid' => 'Broken Sea Route (DNS)',

    'port' => 'Port',
    'ports' => 'Ports',
    'port_help' => 'If behind Cloudflare, set daemon port to 8443 for websocket magic over SSL.',
    'connect_port' => 'Connection Port',
    'connect_port_help' => 'Port used to reach Wings. May differ if using a reverse proxy.',
    'listen_port' => 'Listening Port',
    'listen_port_help' => 'Wings listens on this port.',

    'display_name' => 'Display Name',
    'ssl' => 'Sail with SSL',
    'panel_on_ssl' => 'Yer panel uses SSL, so yer daemon must too.',
    'ssl_help' => 'An IP address cannot use SSL magic.',

    'tags' => 'Markings',
    'upload_limit' => 'Cargo Upload Limit',
    'upload_limit_help' => 'Maximum file size allowed in the file hold.',

    'sftp_port' => 'SFTP Port',
    'sftp_alias' => 'SFTP Alias',
    'sftp_alias_help' => 'Display name for SFTP address. Leave blank to use FQDN.',

    'daemon_base' => 'Daemon Base Hold',
    'daemon_base_help' => 'Where ship data be stored.',

    'use_for_deploy' => 'Use for Deployments?',
    'maintenance_mode' => 'Under Maintenance',
    'maintenance_mode_help' => 'If enabled, crew cannot access ships on this port.',

    'cpu' => 'CPU',
    'cpu_limit' => 'CPU Limit',
    'memory' => 'Memory',
    'memory_limit' => 'Memory Limit',
    'disk' => 'Disk',
    'disk_limit' => 'Disk Limit',

    'unlimited' => 'Unlimited',
    'limited' => 'Limited',
    'overallocate' => 'Overallocate',

    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'yes' => 'Aye',
    'no' => 'Nay',

    'instructions' => 'Instructions',
    'instructions_help' => 'Save this scroll as config.yml in yer daemon\'s root hold.',

    'auto_deploy' => 'Auto Deploy Command',
    'auto_question' => 'Choose between Standalone or Docker rigging.',
    'auto_label' => 'Type',
    'standalone' => 'Standalone',
    'docker' => 'Docker',

    'auto_command' => 'To auto-configure this port, run:',
    'reset_token' => 'Reset Authorization Token',
    'token_reset' => 'Daemon token has been reset.',
    'reset_help' => 'Resetting this token voids all old access. Use with caution, matey.',

    'no_nodes' => 'No Ports Found',
    'none' => 'None',

    'cpu_chart' => 'CPU - :cpu% of :max%',
    'memory_chart' => 'Memory - :used of :total',
    'disk_chart' => 'Storage - :used of :total',

    'used' => 'Used',
    'unused' => 'Unused',

    'node_has_servers' => 'Port Still Has Ships Docked',
    'create_allocation' => 'Create Allocation',
    'primary_allocation' => 'Primary Allocation',

    'databases' => 'Databases',
    'backups' => 'Backups',

    'error_connecting' => 'Error connectin\' to :node',
    'error_connecting_description' => 'Could not auto-update Wings config. Ye must update it manually.',

    'allocation' => 'Allocation',

    'diagnostics' => [
        'header' => 'Port Diagnostics',
        'include_endpoints' => 'Include Endpoints',
        'include_endpoints_hint' => 'Will reveal panel URLs in logs (not for faint-hearted pirates).',
        'include_logs' => 'Include Logs',
        'include_logs_hint' => 'Includes recent ship logs for investigation.',
        'run_diagnostics' => 'Run Diagnostics',
        'upload_to_pelican' => 'Send Logs Ashore',
        'logs_pulled' => 'Logs Retrieved!',
        'logs_uploaded' => 'Logs Sent!',
        'upload_failed' => 'Failed to Send Logs',
        'view_logs' => 'View Logs',
        'pull' => 'Pull',
        'upload' => 'Upload',
        'clear' => 'Clear',
        '404' => 'Diagnostic scroll not found. Make sure Wings be up to date.',
    ],

    'cloudflare_issue' => [
        'title' => 'Cloudflare Trouble',
        'body' => 'Yer node cannot be reached by Cloudflare\'s magic shields.',
    ],

    'bulk_update_ip' => 'Update IP Addresses',
    'bulk_update_ip_description' => 'Replace old IP with new across all allocations. Useful when ports move.',
    'update_ip' => 'Update IP',
    'old_ip' => 'Old IP',
    'new_ip' => 'New IP',

    'no_allocations_to_update' => 'No matching allocations found',
    'ip_updated' => 'Successfully updated :count of :total allocations',
    'ip_update_failed' => ':count allocations failed to update',
];
