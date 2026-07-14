<?php

return [
    'token' => env('SUBDOMAINS_CLOUDFLARE_TOKEN', env('CLOUDFLARE_TOKEN')),
    'subdomain_blacklist' => env('SUBDOMAINS_BLACKLIST', 'www,panel,node,billing,client'),
];
