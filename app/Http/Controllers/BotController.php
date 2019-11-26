<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Bot;
use Illuminate\Http\Request;
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
}
