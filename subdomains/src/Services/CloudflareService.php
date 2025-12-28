<?php

namespace Boy132\Subdomains\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class CloudflareService
{
    public function findDnsRecordId(string $zoneId, string $name, string $type): ?string
    {
        if (empty($zoneId)) {
            return null;
        }

        try {
            $response = Http::cloudflare()->get("zones/{$zoneId}/dns_records", [
                'name' => $name,
                'type' => $type,
                'per_page' => 1,
            ]);
        } catch (\Throwable $e) {
            Log::error('Cloudflare findDnsRecordId request failed: ' . $e->getMessage(), ['zone' => $zoneId, 'name' => $name, 'type' => $type]);
            return null;
        }

        $status = $response->status();
        $body = $response->json() ?? [];

        if ($response->successful() && !empty($body['result']) && count($body['result']) > 0) {
            return $body['result'][0]['id'] ?? null;
        }

        if (!empty($body['errors'])) {
            Log::warning('Cloudflare findDnsRecordId returned errors', ['zone' => $zoneId, 'name' => $name, 'type' => $type, 'status' => $status, 'errors' => $body['errors']]);
        }

        return null;
    }

    public function upsertDnsRecord(
        string $zoneId,
        string $name,
        string $recordType,
        string $target,
        ?int $port = null,
    ): array
    {
        if (empty($zoneId) || empty($name) || empty($recordType)) {
            Log::error('Cloudflare upsertDnsRecord missing required parameters', ['zone' => $zoneId, 'name' => $name, 'type' => $recordType]);
            return ['success' => false, 'id' => null, 'errors' => ['missing_parameters' => true], 'status' => 0, 'body' => null];
        }

        // Hardcoded/derived defaults
        $priority = 0;
        $weight = 0;
        $ttl = 1;
        $comment = 'Created by Pelican Subdomains plugin';
        $proxied = false;

        // Build payload based on type
        if ($recordType === 'SRV') {
            if (empty($port) || empty($target)) {
                Log::error('Cloudflare upsert missing SRV target or port', ['zone' => $zoneId, 'name' => $name, 'type' => $recordType]);
                return ['success' => false, 'id' => null, 'errors' => ['missing_srv_target_or_port' => true], 'status' => 0, 'body' => null];
            }

            $payload = [
                'name' => sprintf('_minecraft._tcp.%s', $name),
                'ttl' => $ttl,
                'type' => 'SRV',
                'comment' => $comment,
                'content' => sprintf('%d %d %d %s', $priority, $weight, $port, $target),
                'proxied' => $proxied,
                'data' => [
                    'priority' => $priority,
                    'weight' => $weight,
                    'port' => (int) $port,
                    'target' => $target,
                ],
            ];
        } else {
            $ip = $content ?? $target;

            if (empty($ip)) {
                Log::error('Cloudflare upsert missing IP/content for record type ' . $recordType, ['zone' => $zoneId, 'name' => $name, 'type' => $recordType]);
                return ['success' => false, 'id' => null, 'errors' => ['missing_ip' => true], 'status' => 0, 'body' => null];
            }

            $payload = [
                'name' => $name,
                'ttl' => $ttl,
                'type' => $recordType,
                'comment' => $comment,
                'content' => $target,
                'proxied' => $proxied,
            ];
        }

        $existingId = $this->findDnsRecordId($zoneId, $payload['name'], $payload['type']);

        try {
            if ($existingId) {
                $response = Http::cloudflare()->put("zones/{$zoneId}/dns_records/{$existingId}", $payload);
                $parsed = $this->parseCloudflareHttpResponse($response);

                if ($parsed['success']) {
                    return $parsed;
                }

                Log::error('Cloudflare update failed', ['zone' => $zoneId, 'id' => $existingId, 'response' => $parsed]);
                return $parsed;
            }

            $response = Http::cloudflare()->post("zones/{$zoneId}/dns_records", $payload);
            $parsed = $this->parseCloudflareHttpResponse($response);

            if ($parsed['success'] && !empty($parsed['id'])) {
                return $parsed;
            }

            Log::error('Cloudflare create failed', ['zone' => $zoneId, 'payload' => $payload, 'response' => $parsed]);
            return $parsed;
        } catch (\Throwable $e) {
            Log::error('Cloudflare upsert exception: ' . $e->getMessage(), ['zone' => $zoneId, 'payload' => $payload]);
            return ['success' => false, 'id' => null, 'errors' => ['exception' => $e->getMessage()], 'status' => 0, 'body' => null];
        }
    }


    protected function parseCloudflareResponse(array $response): array
    {
        return [
            'success' => !empty($response['success']),
            'id' => $response['result']['id'] ?? null,
            'result' => $response['result'] ?? null,
            'errors' => $response['errors'] ?? [],
        ];
    }

    protected function parseCloudflareHttpResponse(Response $response): array
    {
        $status = $response->status();
        $body = $response->json() ?? [];

        $success = $response->successful() && (!empty($body['success']) || !empty($body['result']));

        return [
            'success' => $success,
            'id' => $body['result']['id'] ?? null,
            'errors' => $body['errors'] ?? [],
            'status' => $status,
            'body' => $body,
        ];
    }


    public function deleteDnsRecord(string $zoneId, string $recordId): array
    {
        if (empty($zoneId) || empty($recordId)) {
            return ['success' => false, 'errors' => ['missing_parameters' => true], 'status' => 0, 'body' => null];
        }

        try {
            $response = Http::cloudflare()->delete("zones/{$zoneId}/dns_records/{$recordId}");

            $parsed = $this->parseCloudflareHttpResponse($response);

            if ($parsed['success']) {
                return $parsed;
            }

            Log::error('Cloudflare delete failed', ['zone' => $zoneId, 'id' => $recordId, 'response' => $parsed]);
            return $parsed;
        } catch (\Throwable $e) {
            Log::error('Cloudflare delete exception: ' . $e->getMessage(), ['zone' => $zoneId, 'id' => $recordId]);
            return ['success' => false, 'errors' => ['exception' => $e->getMessage()], 'status' => 0, 'body' => null];
        }
    }
}
