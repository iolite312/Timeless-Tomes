<?php

namespace App\Controllers;

use App\Application\Response;
use App\Helpers\TokenGenerator;

class AuthController extends Controller
{
    public function index()
    {
        $token = TokenGenerator::generateTemporaryToken();

        return Response::json(['message' => $token]);
    }
}
