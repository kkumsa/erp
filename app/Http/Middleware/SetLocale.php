<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * 로케일 설정: 인증 사용자는 DB, 비인증(로그인 페이지 등)은 세션/쿠키 사용.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if ($user = $request->user()) {
            $locale = $user->locale ?? config('app.locale', 'ko');
        } else {
            $locale = $request->session()->get('locale') ?? $request->cookie('locale', config('app.locale', 'ko'));
        }

        if ($locale && in_array($locale, ['ko', 'en'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
