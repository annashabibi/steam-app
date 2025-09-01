<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken
{
    /**
     * URL yang tidak dicek CSRF-nya.
     */
    protected $except = [
        'api/payment/webhook',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah request termasuk dalam pengecualian
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return $next($request);
            }
        }

        // Kalau bukan pengecualian, cek token
        if (
            $request->method() !== 'GET' &&
            $request->method() !== 'HEAD' &&
            !$this->tokensMatch($request)
        ) {
            throw new TokenMismatchException('CSRF token mismatch.');
        }

        return $next($request);
    }

    protected function tokensMatch(Request $request): bool
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        return $request->session()->token() === $token;
    }
}
