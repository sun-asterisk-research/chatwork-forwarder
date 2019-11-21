<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{
    public function index()
    {
        $bots = Auth::user()->bots;
        return view('bots.index', compact('bots'));
    }
}
