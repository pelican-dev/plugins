<?php

namespace Avalon\MikroTikNATSync\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncMikrotikCommand extends Command
{
    protected $signature = 'mikrotik:sync';
    protected $description = 'Synchronize NAT rules with MikroTik checking for forbidden ports';

    public function handle()
    {
        $this->info('Starting MikroTik Sync...');

        $mk_ip = str_replace(['http://', 'https://'], '', env('MIKROTIK_NAT_SYNC_IP'));
        $mk_port = env('MIKROTIK_NAT_SYNC_PORT', '9080');
        $mk_user = env('MIKROTIK_NAT_SYNC_USER');
        $mk_pass = env('MIKROTIK_NAT_SYNC_PASSWORD');
        $mk_interface = env('MIKROTIK_NAT_SYNC_INTERFACE', 'ether1');

        // Get forbidden ports list and convert to array
        $forbidden_string = env('MIKROTIK_NAT_SYNC_FORBIDDEN_PORTS', '');
        $forbidden_ports = array_map('trim', explode(',', $forbidden_string));

        if (!$mk_ip || !$mk_user || !$mk_pass) {
            $this->error('MikroTik settings are not configured!');
            return;
        }

        if (str_contains($mk_ip, ':')) {
            $url = "http://" . $mk_ip . "/rest/ip/firewall/nat";
        } else {
            $url = "http://" . $mk_ip . ":" . $mk_port . "/rest/ip/firewall/nat";
        }

        $active_servers = DB::table('servers')
            ->join('allocations', 'allocations.server_id', '=', 'servers.id')
            ->select('servers.uuid', 'servers.name', 'allocations.ip', 'allocations.port')
            ->get();

        $whitelist = [];
        foreach ($active_servers as $srv) {
            // CHECK: if port is in forbidden list
            if (in_array((string)$srv->port, $forbidden_ports)) {
                $this->warn("Port {$srv->port} for server {$srv->name} is FORBIDDEN. Skipping.");
                continue;
            }

            $target_ip = ($srv->ip == '0.0.0.0') ? '192.168.70.231' : $srv->ip;
            $whitelist[$srv->port . '-tcp'] = ['ip' => $target_ip, 'name' => $srv->name, 'uuid' => $srv->uuid];
            $whitelist[$srv->port . '-udp'] = ['ip' => $target_ip, 'name' => $srv->name, 'uuid' => $srv->uuid];
        }

        try {
            $response = Http::withBasicAuth($mk_user, $mk_pass)->timeout(10)->get($url);
            if (!$response->successful()) {
                $this->error('API Error: ' . $response->body());
                return;
            }

            $existing_rules = [];
            $rules_data = $response->json();
            
            // Safety check if response is array
            if (!is_array($rules_data)) {
                 $this->error('Invalid response format from MikroTik.');
                 return;
            }

            foreach ($rules_data as $rule) {
                if (isset($rule['comment']) && str_contains($rule['comment'], 'Pelican:')) {
                    $dst_port = $rule['dst-port'] ?? '';
                    $protocol = $rule['protocol'] ?? 'tcp';
                    $key = $dst_port . '-' . $protocol;
                    
                    // We need rule ID to delete it later if needed
                    if (isset($rule['.id'])) {
                        $existing_rules[$key] = $rule['.id'];
                    }
                }
            }

            // Remove old rules that are not in whitelist
            foreach ($existing_rules as $key => $id) {
                if (!isset($whitelist[$key])) {
                    $this->warn("Deleting rule: $key");
                    Http::withBasicAuth($mk_user, $mk_pass)->delete("$url/$id");
                }
            }

            // Add new rules
            foreach ($whitelist as $key => $info) {
                if (!isset($existing_rules[$key])) {
                    [$port, $proto] = explode('-', $key);
                    $this->info("Adding rule: $key for {$info['name']}");
                    
                    $payload = [
                        'chain' => 'dstnat',
                        'action' => 'dst-nat',
                        'to-addresses' => $info['ip'],
                        'to-ports' => (string)$port,
                        'protocol' => $proto,
                        'dst-port' => (string)$port,
                        'in-interface' => $mk_interface,
                        'comment' => "Pelican: {$info['name']} ({$info['uuid']})"
                    ];
                    
                    Http::withBasicAuth($mk_user, $mk_pass)->put($url, $payload);
                }
            }
            $this->info('Sync Complete.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
