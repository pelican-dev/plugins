<?php

namespace Boy132\Subdomains\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareService
{
    public function getZoneId(string $domainName): ?string
    {
        if (empty($domainName)) {
            Log::error('Cloudflare getZoneId called with empty domain name', ['domain' => $domainName]);

            return null;
        }

        try {
            $response = Http::cloudflare()->get('zones', [
                'name' => $domainName,
            ]);
        } catch (\Throwable $e) {
            Log::error('Cloudflare getZoneId request failed: ' . $e->getMessage(), ['domain' => $domainName]);

            return ['success' => false, 'errors' => ['exception' => $e->getMessage()], 'status' => $e->getCode(), 'body' => $e->getTraceAsString()];
        }

        $body = $response->json();

        if ($response->successful() && !empty($body['result']) && count($body['result']) > 0) {
            return $body['result'][0]['id'] ?? null;
        }

        if (!empty($body['errors'])) {
            Log::warning('Cloudflare getZoneId returned errors', ['domain' => $domainName, 'status' => $response->status(), 'errors' => $body['errors']]);
        }

        return ['success' => false, 'errors' => [], 'status' => $response->status(), 'body' => $body];
    }

    public function upsertDnsRecord(string $zoneId, string $name, string $recordType, string $target, ?string $recordId = null, ?int $port = null): array
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
                'name' => $name,
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
            $payload = [
                'name' => $name,
                'ttl' => $ttl,
                'type' => $recordType,
                'comment' => $comment,
                'content' => $target,
                'proxied' => $proxied,
            ];
        }

        try {
            if ($recordId) {
                $response = Http::cloudflare()->put("zones/{$zoneId}/dns_records/{$recordId}", $payload);
                $parsed = $this->parseCloudflareHttpResponse($response);

                if ($parsed['success']) {
                    return $parsed;
                }

                Log::error('Cloudflare update failed', ['zone' => $zoneId, 'recordId' => $recordId, 'response' => $parsed]);

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
            Log::error('Cloudflare upsert exception: ' . $e->getMessage(), ['zone' => $zoneId, 'payload' => $payload, 'status' => $e->getCode()]);

            return ['success' => false, 'errors' => ['exception' => $e->getMessage()], 'status' => $e->getCode(), 'body' => $e->getTraceAsString()];
        }
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
            Log::error('Cloudflare delete exception: ' . $e->getMessage(), ['zone' => $zoneId, 'id' => $recordId, 'payload' => $payload, 'status' => $e->getCode()]);

            return ['success' => false, 'errors' => ['exception' => $e->getMessage()], 'status' => $e->getCode(), 'body' => $e->getTraceAsString()];
        }
    }

    protected function parseCloudflareHttpResponse(Response $response): array
    {
        $status = $response->status();
        $body = $response->json();

        $success = $response->successful() && ($body['success'] === true || (is_array($body['result']) && count($body['result']) > 0));

        return [
            'success' => $success,
            'id' => $body['result']['id'] ?? null,
            'errors' => $body['errors'] ?? [],
            'status' => $status,
            'body' => $body,
        ];
    }
}
