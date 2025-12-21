# Robo Avatars

Adds RoboHash as an avatar provider, generating unique robot avatars for users based on their email address.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `robo-avatars` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Automatic robot avatar generation for all users
- Unique avatars based on email hash
- No user action required - avatars are generated automatically
- Fun and distinctive visual identity for users

## Technical Details

### Avatar Schema

The plugin implements the `AvatarSchemaInterface` to provide RoboHash avatars:

```php
public function get(User $user): string
{
    return 'https://robohash.org/' . md5($user->email);
}
```

Each user gets a unique robot avatar generated from the MD5 hash of their email address.

### Components

- `RoboAvatarsSchema` - Implements the avatar provider interface

### Plugin Structure

```
robo-avatars/
├── plugin.json
└── src/
    ├── RoboAvatarsPlugin.php
    ├── RoboAvatarsSchema.php
    └── Providers/
        └── RoboAvatarsPluginProvider.php
```

### External Service

This plugin uses the [RoboHash](https://robohash.org/) service to generate avatars. The avatars are:
- Deterministic - same email always produces the same robot
- Unique - different emails produce different robots
- Privacy-friendly - uses MD5 hash, not the actual email

## Dependencies

None
