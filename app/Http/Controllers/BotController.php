<?php

namespace App\Http\Controllers;

use Exception;
use Auth;
use App\Models\Bot;
use App\Repositories\Interfaces\BotRepositoryInterface as BotRepository;
use App\Http\Requests\BotCreateRequest;
use App\Http\Requests\BotUpdateRequest;
use Illuminate\Http\Request;

class BotController extends Controller
{
    private $botRepository;

    public function __construct(BotRepository $botRepository)
    {
        $this->botRepository = $botRepository;
        $this->authorizeResource(Bot::class);
    }

    protected function resourceAbilityMap()
    {
        return array_merge(parent::resourceAbilityMap(), ['index' => 'viewAny']);
    }

    public function index()
    {
        $perPage = config('paginate.perPage');
        $bots = $this->botRepository->getAllByUser($perPage);
        if ($bots->count() == 0 && $bots->previousPageUrl()) {
            return redirect($bots->previousPageUrl());
        } else {
            return view('bots.index', compact('bots'));
        }
    }

    public function destroy(Request $request, Bot $bot)
    {
        $page = $request->page ? ['page' => $request->page] : null;
        $this->authorize('delete', $bot);
        if ($bot->webhooks->count() > 0) {
            return redirect()->back()
                ->with('messageFail', [
                    'status' => 'Delete failed',
                    'message' => 'This bot has been added to some webhooks, please remove it first',
                ]);
        }

        try {
            $this->botRepository->delete($bot->id);

            return redirect()->route('bots.index', $page)->with('messageSuccess', [
                'status' => 'Delete success',
                'message' => __('message.bot.notification.delete.success'),
            ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => __('message.bot.notification.delete.fail'),
            ]);
        }
    }

    public function create()
    {
        return view('bots.create');
    }

    public function store(BotCreateRequest $request)
    {
        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        try {
            $bot = $this->botRepository->create($data);
            return redirect()->route('bots.edit', $bot)
                ->with('messageSuccess', [
                    'status' => 'Create success',
                    'message' => 'This bot successfully created',
                ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Create failed',
                'message' => 'Create failed. Something went wrong',
            ])->withInput();
        }
    }

    public function edit(Bot $bot)
    {
        unset($bot->bot_key);

        return view('bots.edit', compact('bot'));
    }

    public function update(BotUpdateRequest $request, Bot $bot)
    {
        $data = $request->except('_token');
        if ($data['bot_key'] == '') {
            $data['bot_key'] = $bot->bot_key;
        }

        try {
            $bot = $this->botRepository->update($bot->id, $data);
            return redirect()->route('bots.edit', $bot)
                ->with('messageSuccess', [
                    'status' => 'Update success',
                    'message' => 'This bot successfully updated',
                ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Update failed',
                'message' => 'Update failed. Something went wrong',
            ])->withInput();
        }
    }
}
