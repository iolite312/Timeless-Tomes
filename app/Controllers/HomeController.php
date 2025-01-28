<?php

namespace App\Controllers;

use App\Application\Response;

class HomeController extends Controller
{
    public function index()
    {
        return Response::json(['message' => 'Hello World']);
    }
}
