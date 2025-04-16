<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // ...existing code...

    public function detail()
    {
        // Simply return the view, the auth() helper in the view will access the current user
        return view('user.detail');
    }

    // ...existing code...
}