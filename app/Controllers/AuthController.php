<?php

namespace App\Controllers;

use App\Helpers\TokenGenerator;

class AuthController extends Controller
{
    public function index()
    {
        $token = TokenGenerator::generateTemporaryToken();

        return ['message' => $token];
    }
}
