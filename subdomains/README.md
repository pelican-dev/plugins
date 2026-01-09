# Subdomains (by Boy132)

Allows users to create and manage custom subdomains for their game servers using Cloudflare DNS.

## Features

- Create custom subdomains for game servers
- Cloudflare DNS integration for automatic record management (A/AAAA and SRV)
- Admin management of Cloudflare domains
- Per-server subdomain limits

### Supported games & required tags

| Game | Required Egg tag |
|------|------------------|
| Factorio | `factorio` |
| Minecraft | `minecraft` |
| Mumble | `mumble` |
| Rust | `rust` |
| SCP: Secret Laboratory | `scpsl` |
| TeamSpeak 3 | `teamspeak` |

### SRV requirements

SRV records require a few things to be configured before they can be created:

1. **Node SRV target** — the node must have an `SRV Target` defined (this points SRV records to the correct host).
2. **Server allocation port** — the server must have an allocation with a port (SRV records include the port number).
3. **Egg tag** — the server's Egg must include a supported tag (e.g., `minecraft`) so the plugin knows the service and protocol to use (for example `_minecraft._tcp`).

### Troubleshooting

- **Cloudflare zone fetch failed / missing zone ID:** ensure the API token has access to the zone, or paste the Zone ID manually in **Admin > Domains**.
- **Missing IP for A/AAAA:** ensure the server has an allocation with a valid IP address.
- **Missing SRV port:** ensure the server allocation includes a port.
- **Missing SRV target:** configure the node's SRV target.
- **Unsupported SRV service:** add the appropriate tag to the Egg (e.g., `minecraft`).

### Examples

#### A/AAAA Record

1. Admin > Domains > Create > `example.com`
2. Server > Subdomains > Create > Name: `play`, Select: `A` > Save

#### SRV Record

1. Admin > Domains > Create > `example.com`
2. Node > Set SRV Target > `play.example.com` - This is the public hostname/ip that SRV records will point to.
3. Ensure the server egg has the been tagged correctly. Refer to the Supported games & required tags table above.
4. Server > Subdomains > Create > Name: `play`, Select: `SRV` > Save