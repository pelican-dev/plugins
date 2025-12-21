# Tickets

A simple ticket system for users to submit support requests and for administrators to manage them.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `tickets` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |
| **Panels** | admin, server |

## Features

- User ticket submission from server panel
- Admin ticket management and responses
- Ticket categories and priorities
- Ticket assignment to administrators
- Multi-language support (English and German)

## Technical Details

### Database Migrations

| Migration | Description |
|-----------|-------------|
| `001_create_tickets_table.php` | Creates the `tickets` table |

### Enums

- `TicketCategory` - Defines ticket categories (e.g., General, Technical, Billing)
- `TicketPriority` - Defines priority levels (e.g., Low, Medium, High)

### Models

- `Ticket` - Eloquent model for ticket management

### Policies

- `TicketPolicy` - Authorization rules for ticket operations

### Filament Resources

| Panel | Resource |
|-------|----------|
| Admin | `TicketResource` with `ManageTickets` page |
| Server | `TicketResource` with `ManageTickets` page |

### Filament Actions

- `AnswerAction` - Quick action for administrators to respond to tickets
- `AssignToMeAction` - Quick action for administrators to assign tickets to themselves

### Translations

- English (`lang/en/tickets.php`)
- German (`lang/de/tickets.php`)

### Plugin Structure

```
tickets/
├── plugin.json
├── database/
│   └── migrations/
│       └── 001_create_tickets_table.php
├── lang/
│   ├── de/
│   │   └── tickets.php
│   └── en/
│       └── tickets.php
└── src/
    ├── TicketsPlugin.php
    ├── Models/
    │   └── Ticket.php
    ├── Enums/
    │   ├── TicketCategory.php
    │   └── TicketPriority.php
    ├── Policies/
    │   └── TicketPolicy.php
    ├── Providers/
    │   └── TicketsPluginProvider.php
    └── Filament/
        ├── Components/
        │   └── Actions/
        │       ├── AnswerAction.php
        │       └── AssignToMeAction.php
        ├── Admin/
        │   └── Resources/
        │       └── Tickets/
        └── Server/
            └── Resources/
                └── Tickets/
```

## Dependencies

None
