<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;

use SunAsterisk\Chatwork\Chatwork;
use SunAsterisk\Chatwork\Exceptions\APIException;
use App\Repositories\Interfaces\ChatworkRepositoryInterface as ChatworkRepository;

class RoomController extends Controller
{
    private $chatworkRepository;

    public function __construct(ChatworkRepository $chatworkRepository)
    {
        $this->chatworkRepository = $chatworkRepository;
    }

    /**
     * Return a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $bot = Bot::findOrFail($request->bot_id);
            $this->authorize('getRoom', $bot);
            $chatwork = Chatwork::withAPIToken($bot->bot_key, $bot->getProxyUri());
            $rooms = $this->chatworkRepository->getRooms($chatwork, $request->type);

            return $rooms;
        } catch (APIException $e) {
            return [];
        }
    }
}
