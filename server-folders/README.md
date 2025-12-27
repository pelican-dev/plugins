# Server Folders

A plugin for [Pelican Panel](https://pelican.dev) that allows users to organize their servers into custom folders with role-based sharing.

## Features

- **Create Custom Folders** - Organize your servers into named folders with custom colors
- **Role-Based Sharing** - Share folders with specific user roles so team members can view them
- **Sidebar Navigation** - Quick access to folders directly from the sidebar with server count badges
- **Live Server Stats** - View real-time CPU, Memory, and Disk usage for all servers in a folder
- **Color Coding** - Assign colors to folders for easy visual identification
- **Multi-Language Support** - Includes English and German translations

## Screenshots

### Folder List
View all your folders with server counts in the sidebar navigation. Shared folders show a different icon.

### Folder View
See all servers in a folder with live resource monitoring (CPU, Memory, Disk) - identical to the main server list.

## Installation

1. Download the latest release ZIP file from the [Releases](https://github.com/FlexKleks/PelicanPlugins/releases) page
2. Go to your Pelican Panel admin area
3. Navigate to **Admin → Plugins**
4. Click **"Import file"**
5. Select the downloaded ZIP file
6. Click **"Import"**

### Database Migration

After installing the plugin, run the database migration:

```bash
cd /var/www/pelican
php artisan migrate
```

This creates the necessary database tables:
- `server_folders` - Stores folder information (name, color, user, sharing settings)
- `server_folder_server` - Links servers to folders (many-to-many relationship)
- `server_folder_role` - Links folders to roles for sharing (many-to-many relationship)

### Clear Cache

After installation, clear the application cache:

```bash
php artisan optimize:clear
php artisan view:clear
```

## Usage

### Creating a Folder

1. Navigate to **Folders** in the sidebar
2. Click **"Create Folder"**
3. Enter a folder name and optionally select a color
4. Click **"Create"**

### Adding Servers to a Folder

1. Open a folder by clicking on it
2. Click **"Add Server"** in the top right
3. Select a server from the dropdown
4. The server will be added to the folder

### Removing Servers from a Folder

1. Open the folder containing the server
2. Hover over the server row
3. Click the folder-minus icon that appears on the right
4. Confirm the removal

### Editing a Folder

1. Open the folder
2. Click **"Edit"** in the top right
3. Modify the name or color
4. Click **"Save"**

### Sharing a Folder with Roles

1. Open the folder you want to share
2. Click **"Edit"** in the top right
3. Enable **"Share Folder"**
4. Select the roles that should have access
5. Click **"Save"**

Users with the selected roles will now see this folder in their sidebar and can view the servers inside. Only the folder owner can edit or delete the folder.

### Deleting a Folder

1. Open the folder
2. Click **"Delete"** in the top right
3. Confirm the deletion

> **Note:** Deleting a folder does NOT delete the servers inside it. Servers are only unlinked from the folder.

## Server Display

The folder view displays servers in a table format identical to the main server list:

| Column | Description |
|--------|-------------|
| Icon | Server/Egg icon |
| Status | Running, Offline, Starting, etc. with colored badge |
| Name | Server name and description |
| Address | Server IP:Port |
| CPU | CPU usage with progress bar |
| Memory | Memory usage with progress bar |
| Disk | Disk usage with progress bar |

- **Green** progress bar: Usage below 70%
- **Yellow** progress bar: Usage between 70-90%
- **Red** progress bar: Usage above 90%

Stats update automatically every 15 seconds.

## Permissions

- Users can create and manage their own folders
- Users can only add servers they have access to
- Folder owners can share folders with specific roles
- Users with matching roles can view shared folders (read-only)
- Only the folder owner can edit, delete, or add/remove servers

### Shared Folder Icons

- 📁 **Filled folder** - Your own folder
- 📂 **Shared folder** - Folder shared with you by another user

## File Structure

```
server-folders/
├── config/
│   └── server-folders.php
├── database/
│   └── migrations/
│       ├── 2024_12_27_000001_create_server_folders_table.php
│       └── 2024_12_27_000002_add_shared_roles_to_server_folders.php
├── lang/
│   ├── en/
│   │   └── messages.php
│   └── de/
│       └── messages.php
├── resources/
│   └── views/
│       └── view-folder.blade.php
├── src/
│   ├── Filament/
│   │   └── App/
│   │       └── Resources/
│   │           └── ServerFolders/
│   │               ├── Pages/
│   │               │   ├── ManageServerFolders.php
│   │               │   └── ViewServerFolder.php
│   │               └── ServerFolderResource.php
│   ├── Models/
│   │   └── ServerFolder.php
│   └── ServerFoldersPlugin.php
├── plugin.json
└── README.md
```

## Requirements

- Pelican Panel 1.0+
- PHP 8.2+
- MySQL/MariaDB

## Troubleshooting

### Folders not showing in sidebar
Run `php artisan optimize:clear` and refresh the page.

### Database errors
Make sure you ran `php artisan migrate` after installing the plugin.

### Servers not displaying correctly
Run `php artisan view:clear` to clear cached views.

### Shared folders not visible
Make sure the user has a role that was selected when sharing the folder.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This plugin is open-sourced software licensed under the [MIT license](LICENSE).

## Author

Created by [FlexKleks](https://github.com/FlexKleks)

## Support

- [GitHub Issues](https://github.com/FlexKleks/PelicanPlugins/issues)
- [Pelican Discord](https://discord.gg/pelican-panel)
