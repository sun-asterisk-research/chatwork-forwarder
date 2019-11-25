<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;

use SunAsterisk\Chatwork\Chatwork;

class RoomController extends Controller
{
    /**
     * Return a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bot = Bot::findOrFail($request->bot_id);
        $chatwork = Chatwork::withAPIToken($bot->bot_key);
        $rooms = $chatwork->rooms()->list();
        return $rooms;
    }
}
