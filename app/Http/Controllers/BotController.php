<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Bot;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function index()
    {
        $bots = Auth::user()->bots()
                            ->orderBy('bots.created_at', 'desc')
                            ->get();
        return view('bots.index', compact('bots'));
    }
}
