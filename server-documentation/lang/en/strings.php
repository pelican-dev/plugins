<?php

return [
    'navigation' => [
        'documents' => 'Documents',
        'group' => 'Content',
    ],

    'document' => [
        'singular' => 'Document',
        'plural' => 'Documents',
        'title' => 'Title',
        'slug' => 'Slug',
        'content' => 'Content',
        'type' => 'Type',
        'is_global' => 'Global',
        'is_published' => 'Published',
        'sort_order' => 'Sort Order',
        'author' => 'Author',
        'last_edited_by' => 'Last Edited By',
        'version' => 'Version',
    ],

    'types' => [
        'host_admin' => 'Host Admin',
        'host_admin_description' => 'Root Admins only',
        'server_admin' => 'Server Admin',
        'server_admin_description' => 'Server owners + admins with Server Update/Create',
        'server_mod' => 'Server Mod',
        'server_mod_description' => 'Subusers with control permissions',
        'player' => 'Player',
        'player_description' => 'Anyone with server access',
    ],

    'labels' => [
        'all_servers' => 'All Servers',
        'all_servers_helper' => 'Show on all servers (otherwise attach to specific servers below)',
        'published_helper' => 'Unpublished documents are only visible to admins',
        'sort_order_helper' => 'Lower numbers appear first',
    ],

    'form' => [
        'details_section' => 'Document Details',
        'server_assignment' => 'Server Assignment',
        'server_assignment_description' => 'Select which servers should display this document',
        'filter_by_egg' => 'Filter by Egg',
        'all_eggs' => 'All Eggs',
        'assign_to_servers' => 'Assign to Servers',
        'assign_servers_helper' => 'Select servers that should display this document. Leave empty if using "All Servers" toggle above.',
    ],

    'server' => [
        'node' => 'Node',
        'owner' => 'Owner',
    ],

    'table' => [
        'servers' => 'Servers',
        'updated_at' => 'Updated',
        'empty_heading' => 'No documents yet',
        'empty_description' => 'Create your first document to get started.',
    ],

    'permission_guide' => [
        'title' => 'Permission Guide',
        'modal_heading' => 'Document Permission Guide',
        'description' => 'Understanding document visibility',
        'type_controls' => 'controls who can see the document.',
        'all_servers_controls' => 'controls where it appears.',
        'who_can_see' => 'Who Can See',
        'hierarchy_note' => 'Higher tiers can see all docs at their level and below (e.g., Server Admin sees Server Admin, Server Mod, and Player docs).',
        'toggle_title' => 'All Servers Toggle:',
        'toggle_on' => 'On',
        'toggle_on_desc' => 'Document appears on every server',
        'toggle_off' => 'Off',
        'toggle_off_desc' => 'Must attach to specific servers',
        'examples_title' => 'Examples:',
        'example_player_all' => 'Player + All Servers',
        'example_player_all_desc' => 'Welcome guide everyone sees everywhere',
        'example_player_specific' => 'Player + Specific Server',
        'example_player_specific_desc' => 'Rules for one server only',
        'example_admin_all' => 'Server Admin + All Servers',
        'example_admin_all_desc' => 'Company-wide admin procedures',
        'example_mod_specific' => 'Server Mod + Specific Server',
        'example_mod_specific_desc' => 'Mod notes for one server',
    ],

    'messages' => [
        'version_restored' => 'Version :version restored successfully.',
        'no_documents' => 'No documents available.',
        'no_versions' => 'No versions yet.',
    ],

    'versions' => [
        'title' => 'Version History',
        'current_document' => 'Current Document',
        'current_version' => 'Current Version',
        'last_updated' => 'Last Updated',
        'last_edited_by' => 'Last Edited By',
        'version_number' => 'Version',
        'edited_by' => 'Edited By',
        'date' => 'Date',
        'change_summary' => 'Change Summary',
        'preview' => 'Preview',
        'restore' => 'Restore',
        'restore_confirm' => 'Are you sure you want to restore this version? This will create a new version with the restored content.',
        'restored' => 'Version restored successfully.',
    ],

    'server_panel' => [
        'title' => 'Server Documents',
        'no_documents' => 'No documents available',
        'no_documents_description' => 'There are no documents for this server yet.',
        'select_document' => 'Select a document',
        'select_document_description' => 'Choose a document from the list to view its contents.',
        'last_updated' => 'Last updated :time',
        'global' => 'Global',
    ],

    'actions' => [
        'export' => 'Export as Markdown',
        'import' => 'Import Markdown',
        'back_to_document' => 'Back to Document',
        'close' => 'Close',
    ],

    'import' => [
        'file_label' => 'Markdown File',
        'file_helper' => 'Upload a .md file to create a new document',
        'use_frontmatter' => 'Use YAML Frontmatter',
        'use_frontmatter_helper' => 'Extract title, type, and settings from YAML frontmatter if present',
        'success' => 'Document Imported',
        'success_body' => 'Successfully created document ":title"',
        'error' => 'Import Failed',
        'file_too_large' => 'The uploaded file exceeds the maximum allowed size.',
        'file_read_error' => 'Could not read the uploaded file.',
        'invalid_type' => 'Invalid document type in frontmatter, defaulting to Player.',
    ],

    'export' => [
        'success' => 'Document Exported',
        'success_body' => 'Document has been downloaded as Markdown',
    ],

    'relation_managers' => [
        'linked_servers' => 'Linked Servers',
        'no_servers_linked' => 'No servers linked',
        'attach_servers_description' => 'Attach servers to make this document visible on those servers.',
        'sort_order_helper' => 'Order this document appears for this server',
    ],
];
