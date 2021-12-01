<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;

use JoliCode\Slack\ClientFactory;
use SunAsterisk\Chatwork\Exceptions\APIException;
use App\Repositories\Interfaces\SlackRepositoryInterface as SlackRepository;

class RoomController extends Controller
{
    private $slackRepository;

    public function __construct(SlackRepository $slackRepository)
    {
        $this->slackRepository = $slackRepository;
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
            $slack = ClientFactory::create($bot->bot_key);
            $rooms = $this->slackRepository->getRooms($slack, $request->type);

            return $rooms;
        } catch (APIException $e) {
            return [];
        }
    }
}
