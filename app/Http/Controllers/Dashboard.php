<?php

namespace App\Http\Controllers;

class Dashboard extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return view('dashboard');
    }
}
