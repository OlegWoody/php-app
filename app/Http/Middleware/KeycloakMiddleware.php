<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class KeycloakMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Получаем токен из заголовков
        $token = $request->bearerToken();

        // Проверка на наличие токена
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            // Получаем публичный ключ из .env
            $publicKey = env('KEYCLOAK_PUBLIC_KEY');
            if (!$publicKey) {
                return response()->json(['error' => 'Public key not found'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            Log::info('Loaded public key for validation');

            // Декодируем токен с использованием публичного ключа и алгоритма RS256
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            // Получаем роли из декодированного токена
            $userRoles = array_merge(
                $decoded->realm_access->roles ?? [],
                $decoded->resource_access->{"nest-app"}->roles ?? []
            );

            // Логируем информацию о ролях
            Log::info('Decoded roles: ' . implode(', ', $userRoles));

            // Проверка наличия одной из указанных ролей
            if (!empty(array_intersect($roles, $userRoles))) {
                return $next($request); // Переходим к следующему обработчику
            }

            // Если нет разрешения по ролям, возвращаем ошибку
            return response()->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            // Логируем подробности ошибки
            Log::error('Token validation error: ' . $e->getMessage());

            // Возвращаем ошибку с деталями
            return response()->json(['error' => 'Invalid token', 'details' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }
}
