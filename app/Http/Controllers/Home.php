<?php

namespace App\Http\Controllers;

use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\File;

class Home extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return view('home');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function features()
    {
        $html = Markdown::parse(File::get(storage_path() . '/md/FEATURES.md'));

        return view('features', compact('html'));
    }
}
