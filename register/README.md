# Register (by Boy132)

A simple way for users to register themselves on the panel without administrator intervention.

## Features

- Self-service user registration
- Seamless integration with existing login flow
- Optional maximum user cap (auto-disables new signups after limit is reached)
- Automatic default `user_resource_limits` assignment for newly registered users when `user-creatable-servers` is installed

## Integration with User Creatable Servers

This plugin can automatically assign default CPU, RAM, disk, and server limit values for every newly registered user.

Configure these values in the plugin settings UI:

- `max_users` (`0` = unlimited)
- `default_cpu`
- `default_memory`
- `default_disk`
- `default_server_limit` (`0` = unlimited)
