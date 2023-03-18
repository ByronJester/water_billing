<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class GameController extends Controller
{
    public function HomeView(Request $request)
    {
        return Inertia::render('Home', [
            'options' => [
                'aa' => 'aa'
            ]
        ]);
    }

}
