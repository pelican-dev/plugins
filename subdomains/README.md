# Subdomains (by Boy132 & HarlequinSin)

Allows users to create and manage custom subdomains (A/AAAA or SRV) for their game servers using Cloudflare DNS.

## Setup

[Create a Cloudflare API token](https://developers.cloudflare.com/fundamentals/api/get-started/create-token/) and enter it via the plugin settings.  
The token needs to have read permissions for `Zone.Zone` and write for `Zone.Dns`. For better security you can also set the `Zone Resources` to exclude certain domains and add the panel ip to the `Client IP Address Filtering`.

By default every server has a subdomain limit of 0. You can change this limit by editing the server in the admin area.

Note: You can't create subdomains for servers with `0.0.0.0` or `::` as allocation!

## SRV Records

In order to create SRV records instead of A/AAAA you need to do the following:

1. Set a `SRV target` for the node
2. Add a [SRV service type](https://github.com/pelican-dev/plugins/blob/main/subdomains/src/Enums/SRVServiceType.php#L10-L15) to the features of the egg. The format is `srv-` and then the service name, e.g. `srv-minecraft` or `srv-rust`.
