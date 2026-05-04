<?php

namespace App\Http\Controllers;

class AkademikController extends Controller
{
    /**
     * Show academic dashboard
     */
    public function dashboard()
    {
        return redirect()->route('dashboard');
    }

    /**
     * Redirect to appropriate section based on user role
     */
    public function index()
    {
        return redirect()->route('dashboard');
    }
}
