<?php

namespace Boy132\Subdomains\Services;

use Boy132\Subdomains\Models\Subdomain;
use Exception;

class SubdomainService
{
    /**
     * @param  array<mixed>  $data
     *
     * @throws Exception
     */
    public function handle(array $data, ?Subdomain $subdomain = null): Subdomain
    {
        $NewSubdomain = false;

        if (is_null($subdomain)) {
            $subdomain = Subdomain::create($data);
            $NewSubdomain = true;
        } else {
            $subdomain->update($data);
        }

        $subdomain->refresh();

        try {
            $subdomain->upsertOnCloudflare();
        } catch (Exception $exception) {
            if ($NewSubdomain) {
                $subdomain->delete();
            }
            throw $exception;
        }

        return $subdomain;
    }
}
