<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('RUCKUS_API', env('RUCKUS_API'));
    }

}
