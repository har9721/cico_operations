<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
    public function __construct()
    {
        info('this is a log');
    }
}
