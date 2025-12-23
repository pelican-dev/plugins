<?php

namespace Boy132\UserCreatableServers\Policies;

use App\Policies\DefaultAdminPolicies;

class UserResourceLimitsPolicy
{
    use DefaultAdminPolicies;

    protected string $modelName = 'userResourceLimits';
}
