<?php

namespace App\Http\Middleware;

use App\Services\MenuAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        abort_unless(
            app(MenuAccessService::class)->isEnabled($request->user(), $menuKey),
            403,
            'Menu ini sedang dinonaktifkan untuk akun Anda.'
        );

        return $next($request);
    }
}
