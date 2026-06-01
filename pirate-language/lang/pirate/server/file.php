<?php

return [
    'title' => 'Ship\'s Files',
    'name' => 'Name',
    'size' => 'Size',
    'modified_at' => 'Last Modified',
    'actions' => [
        'open' => 'Open',
        'download' => 'Download',
        'copy' => [
            'title' => 'Copy',
            'notification' => 'File Copied, Matey',
        ],
        'upload' => [
            'title' => 'Upload',
            'from_files' => 'Upload Files',
            'from_url' => 'Upload from URL',
            'url' => 'URL',
            'drop_files' => 'Drop yer files here ter upload',
            'success' => 'Files uploaded successfully',
            'failed' => 'Failed ter upload files',
            'header' => 'Uploadin\' Files',
            'error' => 'An error occurred while uploadin\'',
        ],
        'rename' => [
            'title' => 'Rename',
            'file_name' => 'File Name',
            'notification' => 'File Renamed',
        ],
        'move' => [
            'title' => 'Move',
            'directory' => 'Directory',
            'directory_hint' => 'Enter the new directory relative ter yer current location.',
            'new_location' => 'New Location',
            'new_location_hint' => 'Enter the destination fer this file or folder relative ter yer current location.',
            'notification' => 'File Moved',
            'bulk_notification' => ':count files were moved ter :directory',
        ],
        'permissions' => [
            'title' => 'Permissions',
            'read' => 'Read',
            'write' => 'Write',
            'execute' => 'Execute',
            'owner' => 'Captain',
            'group' => 'Crew',
            'public' => 'All Hands',
            'notification' => 'Permissions changed ter :mode',
        ],
        'archive' => [
            'title' => 'Archive',
            'archive_name' => 'Archive Chest Name',
            'notification' => 'Archive Chest Created',
            'extension' => 'Extension',
        ],
        'unarchive' => [
            'title' => 'Unarchive',
            'notification' => 'Unpacking Complete',
        ],
        'new_file' => [
            'title' => 'New Scroll',
            'file_name' => 'New scroll name',
            'syntax' => 'Syntax Highlighting',
            'create' => 'Create',
        ],
        'new_folder' => [
            'title' => 'New Cargo Hold',
            'folder_name' => 'New cargo hold name',
        ],
        'nested_search' => [
            'title' => 'Deep Search',
            'search_term' => 'Search Term',
            'search_term_placeholder' => 'Enter a search term, fer example *.txt',
            'search' => 'Search',
            'search_for_term' => 'Search fer :term',
        ],
        'delete' => [
            'notification' => 'File Scuttled',
            'bulk_notification' => ':count files were scuttled',
        ],
        'edit' => [
            'title' => 'Editin\': :file',
            'save_close' => 'Save & Close',
            'save' => 'Save',
            'cancel' => 'Abandon Changes',
            'notification' => 'File Saved',
        ],
    ],
    'alerts' => [
        'file_too_large' => [
            'title' => '<code>:name</code> be too large fer the hold!',
            'body' => 'Maximum size be :max',
        ],
        'file_not_found' => [
            'title' => '<code>:name</code> be nowhere on the charts!',
        ],
        'file_not_editable' => [
            'title' => '<code>:name</code> be a cargo hold, not a file',
        ],
        'file_already_exists' => [
            'title' => '<code>:name</code> already exists aboard!',
        ],
        'files_node_error' => [
            'title' => 'Could not load the ship\'s files!',
        ],
        'pelicanignore' => [
            'title' => 'Ye be editin\' a <code>.pelicanignore</code> file!',
            'body' => 'Any files or cargo holds listed here will be excluded from backup chests. Wildcards may be used with an asterisk (<code>*</code>).<br>Ye may negate a previous rule by placin\' an exclamation mark (<code>!</code>) at the start.',
        ],
    ],
];
