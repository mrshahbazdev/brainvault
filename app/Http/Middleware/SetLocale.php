<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = ['en', 'de'];

        $locale = $request->cookie('locale');

        if (!$locale || !in_array($locale, $supported)) {
            $locale = session('locale');
        }

        if (!$locale || !in_array($locale, $supported)) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
