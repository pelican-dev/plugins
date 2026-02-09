## McLogCleaner

McLogCleaner automatically deletes all `.log.gz` files from the server’s `logs` folder.

> **Note:** `latest.log` will always remain intact and is never deleted.

### Usage
To use this plugin, add `mclogcleaner` as a feature to the egg you want to run it with.

### Log Deletion Options
When you click **Delete logs**, a dropdown menu appears where you can choose the **minimum age (in days)** of log files to delete:
- Logs older than 7 days
- Logs older than 30 days
- All logs (regardless of age)
- A custom age in days
