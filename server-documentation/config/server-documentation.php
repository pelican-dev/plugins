<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for document queries. Set to 0 to disable.
    |
    */

    // Cache TTL for server document queries (in seconds)
    'cache_ttl' => env('SERVER_DOCS_CACHE_TTL', 300),

    // Cache TTL for navigation badge count (in seconds)
    'badge_cache_ttl' => env('SERVER_DOCS_BADGE_CACHE_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Version History Settings
    |--------------------------------------------------------------------------
    |
    | Configure document version history behavior.
    |
    */

    // Number of versions to keep per document (0 = unlimited)
    'versions_to_keep' => env('SERVER_DOCS_VERSIONS_TO_KEEP', 50),

    // Automatically prune old versions on save
    'auto_prune_versions' => env('SERVER_DOCS_AUTO_PRUNE', false),

    /*
    |--------------------------------------------------------------------------
    | Import Settings
    |--------------------------------------------------------------------------
    |
    | Configure markdown import behavior.
    |
    */

    // Maximum file size for markdown imports (in KB)
    'max_import_size' => env('SERVER_DOCS_MAX_IMPORT_SIZE', 512),

    // Allow raw HTML in markdown imports (security risk if enabled)
    'allow_html_import' => env('SERVER_DOCS_ALLOW_HTML_IMPORT', false),

    /*
    |--------------------------------------------------------------------------
    | Permissions Settings
    |--------------------------------------------------------------------------
    |
    | Configure permission behavior.
    |
    */

    // Require explicit document permissions instead of inheriting from server permissions
    'explicit_permissions' => env('SERVER_DOCS_EXPLICIT_PERMISSIONS', false),

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | Configure audit logging for document operations.
    |
    */

    // Log channel for audit events (use 'single', 'daily', or a custom channel)
    'audit_log_channel' => env('SERVER_DOCS_AUDIT_LOG_CHANNEL', 'single'),
];
