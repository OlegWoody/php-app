<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;  // Если вы используете JWT для токенов

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Получаем токен из заголовка Authorization
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            // Декодируем токен (предполагается использование JWT)
            $decodedToken = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            $roles = $decodedToken->realm_access->roles;

            // Проверка на наличие роли
            if (!in_array($role, $roles)) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
