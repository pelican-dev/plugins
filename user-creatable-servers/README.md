# User Creatable Servers (by Boy132 & contributions from CER06)

Allow users to create their own servers within defined resource limits set by administrators.

## Setup

Add the deployment tag (`user_creatable_servers` by default) to the nodes that should be used for creating servers.

## Features

- Users can create servers without admin intervention
- Configurable resource limits per user (CPU, RAM, disk, etc.)
- Configurable default CPU, memory, and disk allocations automatically assigned to newly created users
- Optional aggregate CPU, memory, and disk caps across all UCS users (`0` is unlimited and deployment remains limited by node capacity)
- Admin management of user resource allocations
- Resource usage overview widget for users
- Integration with existing server management
