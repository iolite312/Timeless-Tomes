<?php

namespace App\Controllers;

use App\Application\Response;

class HomeController extends Controller
{
    public function index()
    {
        return ['message' => 'Hello World'];
    }
}
