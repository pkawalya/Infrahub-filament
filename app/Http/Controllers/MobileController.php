<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MobileController extends Controller
{
    /**
     * Gate: redirect to login if no token cookie/header.
     * The actual auth is handled client-side via localStorage tokens,
     * so these are just thin Blade views.
     */

    public function home()
    {
        return view('mobile.home', ['active' => 'home']);
    }

    public function login()
    {
        return view('mobile.login');
    }

    public function projects()
    {
        return view('mobile.projects.index', ['active' => 'projects']);
    }

    public function projectShow(int $id)
    {
        return view('mobile.projects.show', ['active' => 'projects', 'id' => $id]);
    }

    public function tasks()
    {
        return view('mobile.tasks', ['active' => 'tasks']);
    }

    public function forms()
    {
        return view('mobile.forms', ['active' => 'forms']);
    }

    public function profile()
    {
        return view('mobile.profile', ['active' => 'profile']);
    }

    public function notifications()
    {
        return view('mobile.profile', ['active' => 'profile']); // placeholder
    }
}
