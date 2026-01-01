<?php

return [
    'no_domains' => 'No Domains',
    'domain' => 'Domain|Domains',
    'no_subdomains' => 'No Subdomains',
    'subdomain' => 'Subdomain|Subdomains',
    'name' => 'Name',

    'limit' => 'Limit',
    'change_limit' => 'Change Limit',
    'limit_changed' => 'Limit changed',
    'limit_reached' => 'Subdomain Limit Reached',

    'create_subdomain' => 'Create Subdomain',
    'subdomain_change_limit' => 'Change Subdomain Limit',
    'subdomain_limit' => 'Subdomain Limit',

    'record_type' => 'Record Type',
    'srv_record' => 'SRV Record',
    'srv_record_help' => 'Enable this option to create a SRV record instead of an A or AAAA record.',

    'set_srv_target' => 'Set SRV Target',
    'srv_target' => 'SRV Target',
    'srv_target_help' => 'The hostname that SRV records point to (for example: play.example.com).',
    'srv_target_missing' => 'SRV record is missing from node configuration.',
    'srv_target_confirmation' => 'Changing the SRV target will require all existing subdomains using SRV records to be updated. Are you sure you want to continue?',

    'api_token' => 'Cloudflare API Token',
    'api_token_help' => 'The token needs to have read permissions for Zone.Zone and write for Zone.Dns. For better security you can also set the "Zone Resources" to exclude certain domains and add the panel ip to the "Client IP Adress Filtering".',

    'notifications' => [
        'srv_target_updated_title' => 'SRV target changed successfully.',
        'srv_target_updated' => 'Existing subdomains using SRV records will need to be updated to use the new target.',

        'cloudflare_missing_zone_title' => 'Cloudflare: Missing Zone ID',
        'cloudflare_missing_zone' => 'Cloudflare zone ID is not configured for :domain. Cannot save DNS record for :subdomain.',

        'cloudflare_missing_srv_port_title' => 'Cloudflare: Missing SRV Port',
        'cloudflare_missing_srv_port' => 'SRV port is missing for :server.',

        'cloudflare_missing_srv_target_title' => 'Cloudflare: Missing SRV Target',
        'cloudflare_missing_srv_target' => 'SRV target is missing from :node. ',

        'cloudflare_record_updated_title' => 'Cloudflare: Record Updated',
        'cloudflare_record_updated' => 'Successfully updated :subdomain record to :record_type',

        'cloudflare_missing_ip_title' => 'Cloudflare: Missing IP',
        'cloudflare_missing_ip' => 'Server allocation IP is missing or invalid for :subdomain. Cannot save A/AAAA record.',

        'cloudflare_upsert_failed_title' => 'Cloudflare: Save Failed',
        'cloudflare_upsert_failed' => 'Failed to save record for :subdomain. See logs for details. Errors: :errors',

        'cloudflare_delete_success_title' => 'Cloudflare: Record Deleted',
        'cloudflare_delete_success' => 'Successfully deleted Cloudflare record for :subdomain.',

        'cloudflare_delete_failed_title' => 'Cloudflare: Delete Failed',
        'cloudflare_delete_failed' => 'Failed to delete Cloudflare record for :subdomain. See logs for details. Errors: :errors',

        'cloudflare_zone_fetch_failed' => 'Failed to fetch Cloudflare Zone ID for domain: :domain',
        'cloudflare_domain_saved' => 'Successfully saved domain: :domain',

        'cloudflare_invalid_service_record_type_title' => 'Cloudflare: Unable to determine service type',
        'cloudflare_invalid_service_record_type' => 'Unable to determine service type for SRV record of :subdomain. Please check egg is correctly tagged',
    ],

    'settings_saved' => 'Settings saved',
];
