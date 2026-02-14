<?php

namespace Avalon\MikroTikNATSync\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncMikrotikCommand extends Command
{
    protected $signature = 'mikrotik:sync';
    protected $description = 'Синхронізація правил NAT з MikroTik з перевіркою заборонених портів';

    public function handle()
    {
        $this->info('Starting MikroTik Sync...');

        $mk_ip = str_replace(['http://', 'https://'], '', env('MIKROTIK_IP'));
        $mk_port = env('MIKROTIK_PORT', '9080');
        $mk_user = env('MIKROTIK_USER');
        $mk_pass = env('MIKROTIK_PASS');
        $mk_interface = env('MIKROTIK_INTERFACE', 'ether1');
        
        // Отримуємо список заборонених портів та перетворюємо в масив
        $forbidden_string = env('MIKROTIK_FORBIDDEN_PORTS', '');
        $forbidden_ports = array_map('trim', explode(',', $forbidden_string));

        if (!$mk_ip || !$mk_user || !$mk_pass) {
            $this->error('Налаштування MikroTik не заповнені!');
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
            // ПЕРЕВІРКА: чи порт не в списку заборонених
            if (in_array((string)$srv->port, $forbidden_ports)) {
                $this->warn("Порт {$srv->port} для сервера {$srv->name} ЗАБОРОНЕНИЙ. Пропускаємо.");
                continue;
            }

            $target_ip = ($srv->ip == '0.0.0.0') ? '192.168.70.231' : $srv->ip;
            $whitelist[$srv->port . '-tcp'] = ['ip' => $target_ip, 'name' => $srv->name, 'uuid' => $srv->uuid];
            $whitelist[$srv->port . '-udp'] = ['ip' => $target_ip, 'name' => $srv->name, 'uuid' => $srv->uuid];
        }

        try {
            $response = Http::withBasicAuth($mk_user, $mk_pass)->timeout(10)->get($url);
            if (!$response->successful()) {
                $this->error('Помилка API: ' . $response->body());
                return;
            }

            $existing_rules = [];
            foreach ($response->json() as $rule) {
                if (isset($rule['comment']) && str_contains($rule['comment'], 'Pelican:')) {
                    $key = ($rule['dst-port'] ?? '') . '-' . ($rule['protocol'] ?? 'tcp');
                    $existing_rules[$key] = $rule['.id'];
                }
            }

            foreach ($existing_rules as $key => $id) {
                if (!isset($whitelist[$key])) {
                    $this->warn("Deleting rule: $key");
                    Http::withBasicAuth($mk_user, $mk_pass)->delete("$url/$id");
                }
            }

            foreach ($whitelist as $key => $info) {
                if (!isset($existing_rules[$key])) {
                    [$port, $proto] = explode('-', $key);
                    $this->info("Adding rule: $key for {$info['name']}");
                    Http::withBasicAuth($mk_user, $mk_pass)->put($url, [
                        'chain' => 'dstnat',
                        'action' => 'dst-nat',
                        'to-addresses' => $info['ip'],
                        'to-ports' => (string)$port,
                        'protocol' => $proto,
                        'dst-port' => (string)$port,
                        'in-interface' => $mk_interface,
                        'comment' => "Pelican: {$info['name']} ({$info['uuid']})"
                    ]);
                }
            }
            $this->info('Sync Complete.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
