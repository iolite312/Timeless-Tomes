<?php

namespace App\Middleware;

use App\Application\Request;
use App\Helpers\TokenHelper;

class EnsureValidLogin implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (!TokenHelper::isValid(Request::getAuthToken())) {
            return true;
        }

        return false;
    }
}
