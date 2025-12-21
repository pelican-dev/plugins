<?php


namespace Boy132\ServerTags\Policies;

use App\Policies\DefaultAdminPolicies;

class ServerTagPolicy
{
    use DefaultAdminPolicies;

    protected string $modelName = 'server_tag';
}