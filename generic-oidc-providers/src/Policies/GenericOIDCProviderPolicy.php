<?php

namespace Boy132\GenericOIDCProviders\Policies;

use App\Policies\DefaultAdminPolicies;

class GenericOIDCProviderPolicy
{
    use DefaultAdminPolicies;

    protected string $modelName = 'genericOidcProvider';
}
