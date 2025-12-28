<?php

return [
    'no_domains' => 'No Domains',
    'domain' => 'Domain|Domains',

    'no_subdomains' => 'No Subdomains',
    'subdomain' => 'Subdomain|Subdomains',
    'limit' => 'Limit',
    'change_limit' => 'Change Limit',
    'limit_changed' => 'Limit changed',
    'limit_reached' => 'Subdomain Limit Reached',
    'create_subdomain' => 'Create Subdomain',

    'name' => 'Name',

    'srv_record' => 'SRV Record',
    'srv_record_help' => 'Enable this option to create a SRV record instead of an A or AAAA record.',

    'srv_target' => 'SRV Target',
    'srv_target_help' => 'The hostname that SRV records point to (for example: play.example.com).',

    'errors' => [
        'srv_target_missing' => 'Cannot enable SRV record because the selected domain does not have an SRV target set.',
    ],

    'api_token' => 'Cloudflare API Token',
    'api_token_help' => 'The token needs to have read permissions for Zone.Zone and write for Zone.Dns. For better security you can also set the "Zone Resources" to exclude certain domains and add the panel ip to the "Client IP Adress Filtering".',

    'notifications' => [
        'dns_created' => 'DNS record created on Cloudflare',
        'dns_updated' => 'DNS record updated on Cloudflare',
        'dns_deleted' => 'DNS record deleted from Cloudflare',
        'dns_action_failed' => 'Cloudflare DNS action failed',
        'zone_request_failed' => 'Cloudflare zone request failed',
        'zone_request_succeeded' => 'Cloudflare zone request succeeded',
    ],
];
