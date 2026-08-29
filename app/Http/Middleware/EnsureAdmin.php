<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isAuthenticated($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => '인증에 실패했습니다. 유효한 API 키 또는 비밀번호를 입력해주세요.',
            ], 401);
        }

        return redirect()->guest(route('admin.login'));
    }

    private function isAuthenticated(Request $request): bool
    {
        if ($request->hasSession() && $request->session()->get('is_admin') === true) {
            return true;
        }

        $validKeys = array_values(array_filter([
            config('admin.api_key'),
            config('admin.password'),
        ], fn($k) => ! blank($k)));

        $provided = $request->bearerToken()
            ?: $request->header('X-API-KEY')
            ?: $request->input('api_key')
            ?: $request->input('password');

        if (! empty($validKeys) && ! blank($provided)) {
            foreach ($validKeys as $key) {
                if (hash_equals((string) $key, (string) $provided)) {
                    return true;
                }
            }
        }

        return false;
    }
}
