<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    private $loggedUser;

    public function __constructor()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }
}
