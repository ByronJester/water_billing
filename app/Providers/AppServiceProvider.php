<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    { 
        Inertia::share([
            'message' => function () {
                return session('message');
            },
        ]);

        Inertia::share([
            'auth' => function () {
                return Auth::user();
            },
        ]);

        // Inertia::share([
        //     'errors' => function () {
        //         return Session::get('errors')
        //             ? Session::get('errors')->getBag('default')->getMessages()
        //             : (object) [];
        //     },
        // ]);

        Inertia::share('errors', function () {
            if (Session::get('errors')) {
                $bags = [];

                foreach (Session::get('errors')->getBags() as $bag => $error) {
                   $bags[$bag] = $error->getMessages();
                }

                return $bags;
            }

            return (object)[];
        });
    }
}
