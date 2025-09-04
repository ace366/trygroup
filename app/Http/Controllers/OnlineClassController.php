<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnlineClassController extends Controller
{
    public function index()
    {
        return view('online_classes.index');
    }
}
