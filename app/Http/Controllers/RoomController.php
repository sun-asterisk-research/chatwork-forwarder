<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;

use JoliCode\Slack\Api\Client;
use JoliCode\Slack\ClientFactory;
use SunAsterisk\Chatwork\Chatwork;
use SunAsterisk\Chatwork\Exceptions\APIException;
use App\Repositories\Interfaces\ChatworkRepositoryInterface as ChatworkRepository;
use App\Repositories\Interfaces\SlackRepositoryInterface as SlackRepository;

class RoomController extends Controller
{
    private $chatworkRepository;
    private $slackRepository;

    public function __construct(ChatworkRepository $chatworkRepository, SlackRepository $slackRepository)
    {
        $this->chatworkRepository = $chatworkRepository;
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
            $chatwork = Chatwork::withAPIToken($bot->bot_key, $bot->getAPIOptions());
            if ($bot->type === Bot::TYPE_SLACK) {
                $rooms = $this->slackRepository->getRooms($slack, $request->type);

                return $rooms;
            } else {
                $rooms = $this->chatworkRepository->getRooms($chatwork, $request->type);

                return $rooms;
            }
        } catch (APIException $e) {
            return [];
        }
    }
}
