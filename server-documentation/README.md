# Server Documentation Plugin for Pelican Panel

A documentation management plugin for [Pelican Panel](https://pelican.dev) that allows administrators to create, organize, and distribute documentation to server users with granular permission-based visibility.

## Features

- **Rich Text Editor** - Full WYSIWYG editing with formatting, lists, code blocks, tables, and more
- **4-Tier Permission System** - Control who sees what documentation based on their role
- **Global & Server-Specific Docs** - Create documentation that appears on all servers or only specific ones
- **Server Assignment During Creation** - Assign documents to servers with egg-based filtering when creating
- **Version History** - Track changes with automatic versioning, rate-limited to prevent spam
- **Markdown Import/Export** - Import `.md` files or export documents for backup/migration
- **Server Panel Integration** - Documents appear in the player's server sidebar with search
- **Admin Panel Integration** - Full CRUD management with filtering, search, and bulk actions
- **Drag-and-Drop Reordering** - Easily reorder documents in relation managers
- **Audit Logging** - All document operations are logged for accountability

## Screenshots

### Admin Panel - Document List
![Admin Documents List](docs/images/admin-documents-list.png)
*Full document management with Import Markdown action, type badges, and global indicators*

### Admin Panel - Create Document
![Admin Create Document](docs/images/admin-create-document.png)
*Permission type selector with all 4 tiers visible in dropdown*

### Admin Panel - Edit Document with Linked Servers
![Admin Edit Document](docs/images/admin-edit-document.png)
*Rich text editor with Servers relation manager showing linked servers*

### Server Panel - Server Admin View
![Server Admin View](docs/images/server-admin-view.png)
*Server admins see "Server Admin", "Server Mod", and "Player" documents (4 docs)*

### Server Panel - Server Mod View
![Server Mod View](docs/images/server-mod-view.png)
*Server mods see "Server Mod" and "Player" documents, including the Moderator Handbook (3 docs)*

### Server Panel - Player View
![Player View](docs/images/player-view.png)
*Players only see documents marked as "Player" type (2 docs)*

### Version History
![Version History](docs/images/version-history.png)
*Version table with change summaries showing character diff (e.g., "+2 chars")*

### Version Preview
![Version Preview](docs/images/version-history-preview.png)
*Preview modal showing full content of a previous version*

### Version Restore
![Version Restore](docs/images/version-history-restore.png)
*Confirmation dialog before restoring a previous version*

### After Restore
![After Restore](docs/images/version-history-restored.png)
*New version created with "Restored from version X" summary*

## The 4-Tier Permission System

### Why Custom Tiers?

Pelican Panel has two built-in permission contexts:
1. **Admin Panel** - Root admins and users with admin roles
2. **Server Panel** - Server owners and subusers with granular permissions

However, for documentation, we needed more nuance. A game server host typically has:
- **Infrastructure documentation** - Only for the hosting team (network configs, security policies)
- **Server administration guides** - For server owners renting/managing servers
- **Moderator handbooks** - For trusted users helping manage game servers
- **Player-facing docs** - Rules, guides, and welcome messages for everyone

Pelican's native permissions don't map cleanly to these roles, so we created a **virtual permission tier system** that infers user roles from their existing Pelican permissions.

### Permission Tiers

| Tier | Badge | Who Can See | How It's Determined |
|------|-------|-------------|---------------------|
| **Host Admin** | 🔴 Red | Root Admins only | `user.isRootAdmin()` |
| **Server Admin** | 🟠 Orange | Server owners + admins with Server Update/Create | Server ownership OR admin panel server permissions |
| **Server Mod** | 🔵 Blue | Subusers with control permissions | Has `control.*` subuser permissions (start/stop/restart/console) |
| **Player** | 🟢 Green | Anyone with server access | Default - anyone who can view the server |

### Hierarchy

Higher tiers see all documents at their level **and below**:
- **Host Admin** sees: Host Admin, Server Admin, Server Mod, Player
- **Server Admin** sees: Server Admin, Server Mod, Player
- **Server Mod** sees: Server Mod, Player
- **Player** sees: Player only

### Example Use Cases

| Document | Type | Global | Use Case |
|----------|------|--------|----------|
| Infrastructure Security Policy | Host Admin | Yes | Internal security guidelines for your hosting team |
| Server Administration Guide | Server Admin | Yes | SOPs for anyone managing a server |
| Moderator Handbook | Server Mod | Yes | Guidelines for trusted helpers with console access |
| Welcome to Our Servers! | Player | Yes | Community rules visible to all players |
| Minecraft Server Info | Player | No | Server-specific information for one server only |

## Installation

### Requirements
- Pelican Panel v1.0+
- PHP 8.2+

### Install via Admin Panel

1. Download the plugin zip or clone to your plugins directory
2. Navigate to **Admin Panel → Plugins**
3. Click **Install** next to "Server Documentation"
4. Run migrations when prompted

### Manual Installation

```bash
# Copy plugin to plugins directory
cp -r server-documentation /var/www/html/plugins/

# Run migrations
php /var/www/html/artisan migrate
```

> **Note**: This plugin has no external composer dependencies - it uses Pelican's bundled packages only.

## Usage

### Creating Documents

1. Go to **Admin Panel → Documents**
2. Click **New Document**
3. Fill in:
   - **Title** - Display name for the document
   - **Slug** - URL-friendly identifier (auto-generated from title)
   - **Type** - Permission tier (Host Admin, Server Admin, Server Mod, Player)
   - **All Servers** - Toggle to show on every server
   - **Published** - Toggle to hide from non-admins while drafting
   - **Sort Order** - Lower numbers appear first in lists
4. **Server Assignment** (if "All Servers" is disabled):
   - Optionally filter servers by **Egg** (game type)
   - Select servers using checkboxes
   - Use "Select All" / "Deselect All" for bulk selection
5. Write your content using the rich text editor
6. Click **Save**

### Attaching to Servers (After Creation)

You can also attach documents to servers after creation:

1. Edit the document
2. Scroll to the **Servers** relation manager
3. Click **Attach** and select servers
4. Use drag-and-drop to reorder documents

Or from the server side:
1. Go to **Admin Panel → Servers → [Server] → Documents tab**
2. Click **Attach** and select documents
3. Use drag-and-drop to reorder

### Importing Markdown

1. Go to **Admin Panel → Documents**
2. Click **Import Markdown**
3. Upload a `.md` file
4. Optionally enable "Use YAML Frontmatter" to extract metadata:

```yaml
---
title: My Document
slug: my-document
type: player
is_global: true
is_published: true
sort_order: 10
---

# Document Content

Your markdown content here...
```

### Exporting Documents

1. Edit any document
2. Click the **Download** icon in the header
3. Document downloads as `.md` with YAML frontmatter

### Version History

1. Edit any document
2. Click the **History** icon (shows badge with version count)
3. View previous versions with timestamps and editors
4. Click **Preview** to see old content
5. Click **Restore** to revert to a previous version

## Configuration

### Environment Variables

All settings can be configured via environment variables or by publishing the config file:

```bash
# Cache Settings
SERVER_DOCS_CACHE_TTL=300              # Cache TTL for document queries (seconds, 0 to disable)
SERVER_DOCS_BADGE_CACHE_TTL=60         # Cache TTL for navigation badge count (seconds)

# Version History
SERVER_DOCS_VERSIONS_TO_KEEP=50        # Max versions per document (0 = unlimited)
SERVER_DOCS_AUTO_PRUNE=false           # Auto-prune old versions on save

# Import Settings
SERVER_DOCS_MAX_IMPORT_SIZE=512        # Max markdown import file size (KB)
SERVER_DOCS_ALLOW_HTML_IMPORT=false    # Allow raw HTML in imports (security risk)

# Permissions
SERVER_DOCS_EXPLICIT_PERMISSIONS=false # Require explicit document permissions

# Audit Logging
SERVER_DOCS_AUDIT_LOG_CHANNEL=single   # Log channel for audit events
```

### Admin Permissions

By default, users with server management permissions (`update server` or `create server`) can manage documents. Set `SERVER_DOCS_EXPLICIT_PERMISSIONS=true` to require explicit document permissions instead.

The plugin registers these Gates:

- `viewList document`
- `view document`
- `create document`
- `update document`
- `delete document`

To extend access to other admin roles, modify the `registerDocumentPermissions()` method in the ServiceProvider.

### Customization

The plugin uses Pelican's standard extensibility patterns:

```php
// In another plugin or service provider
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource;

// Modify the form
DocumentResource::modifyForm(function (Form $form) {
    return $form->schema([
        // Add custom fields
    ]);
});

// Modify the table
DocumentResource::modifyTable(function (Table $table) {
    return $table->columns([
        // Add custom columns
    ]);
});
```

## File Structure

```text
server-documentation/
├── composer.json              # PSR-4 autoloading (no external deps)
├── config/server-documentation.php  # Configuration options
├── plugin.json                # Plugin metadata
├── database/
│   ├── factories/             # Model factories for testing
│   └── migrations/            # Database schema
├── lang/en/strings.php        # Translations (i18n ready)
├── resources/
│   ├── css/                   # Document content styling
│   └── views/filament/        # Blade templates
├── tests/
│   └── Unit/                  # Unit tests (Pest)
│       ├── Enums/
│       ├── Models/
│       ├── Policies/
│       └── Services/
└── src/
    ├── Enums/                 # DocumentType enum
    ├── Models/                # Document, DocumentVersion
    ├── Policies/              # Permission logic
    ├── Providers/             # Service provider
    ├── Services/              # DocumentService, MarkdownConverter
    └── Filament/
        ├── Admin/             # Admin panel resources
        ├── Concerns/          # Shared traits (HasDocumentTableColumns)
        └── Server/            # Server panel pages
```

## Database Schema

```text
documents
├── id, uuid
├── title, slug (unique)
├── content (HTML from rich editor)
├── type (host_admin, server_admin, server_mod, player)
├── is_global, is_published
├── sort_order
├── author_id, last_edited_by
├── timestamps, soft_deletes

document_versions
├── id, document_id
├── title, content (snapshot)
├── version_number
├── edited_by, change_summary
├── created_at

document_server (pivot)
├── document_id, server_id
├── sort_order
├── timestamps
```

## Testing

The plugin includes comprehensive unit tests using Pest PHP:

```bash
# Run all tests
cd /path/to/pelican-panel
php artisan test --filter=ServerDocumentation

# Run specific test file
php artisan test plugins/server-documentation/tests/Unit/Services/DocumentServiceTest.php

# Run with coverage
php artisan test --filter=ServerDocumentation --coverage
```

### Test Coverage

- **DocumentService** - Version creation, caching, permission checks
- **MarkdownConverter** - HTML↔Markdown conversion, sanitization, frontmatter
- **DocumentType Enum** - Hierarchy levels, visibility, options
- **DocumentPolicy** - Authorization for admin and server panel
- **Document Model** - Events, scopes, relationships

## Contributing

This plugin was developed for [Pelican Panel](https://pelican.dev). Contributions welcome!

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests: `php artisan test --filter=ServerDocumentation`
5. Submit a pull request

## License

MIT License - see [LICENSE](LICENSE) for details.

## Credits

- Built for [Pelican Panel](https://pelican.dev)
- Uses Pelican's bundled [League CommonMark](https://commonmark.thephpleague.com/) for Markdown→HTML parsing
- Built-in HTML→Markdown converter for exports (no external dependencies)

