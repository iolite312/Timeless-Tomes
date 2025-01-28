<?php

namespace App\Middleware;

use App\Application\Request;
use App\Helpers\TokenValidator;

class EnsureValidLogin implements MiddlewareInterface
{
    public function handle(): bool
    {
        if (!TokenValidator::isValid(Request::getAuthToken())) {
            return true;
        }

        return false;
    }
}
