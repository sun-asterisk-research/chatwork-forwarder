<?php

namespace App\Http\Controllers;

use Exception;
use App\Repositories\Interfaces\BotRepositoryInterface as BotRepository;

class BotController extends Controller
{
    private $botRepository;

    public function __construct(BotRepository $botRepository)
    {
        $this->botRepository = $botRepository;
    }

    public function index()
    {
        $bots = $this->botRepository->getAllByUser();

        return view('bots.index', compact('bots'));
    }

    public function destroy($id)
    {
        try {
            $this->botRepository->delete($id);

            return redirect('/bots')->with('messageSuccess', __('message.bot.notification.delete.success'));
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', __('message.bot.notification.delete.fail'));
        }
    }
}
