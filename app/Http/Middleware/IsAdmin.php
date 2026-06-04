<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước!');
        }

        if (auth()->user()->role === 'admin') {
            return $next($request);
        }

        return redirect()->route('slots.index')->with('error', 'Bạn không có quyền truy cập vào khu vực của Admin!');
    }
}
