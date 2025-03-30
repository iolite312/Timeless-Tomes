<?php

namespace App\Middleware;

use App\Enums\RoleEnum;
use App\Application\Request;
use App\Helpers\TokenHelper;

class EnsureValidRoleAccess implements MiddlewareInterface
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(): bool
    {
        $role = RoleEnum::from(TokenHelper::decode(Request::getAuthToken())->claims()->get('user')['role']);
        // var_dump($role);
        // die;
        if (empty($this->allowedRoles)) {
            return true;
        }

        return !in_array($role, $this->allowedRoles, true);
    }
}
