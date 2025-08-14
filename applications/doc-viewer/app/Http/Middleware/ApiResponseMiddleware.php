<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Se for uma resposta JSON, padronizar o formato
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            
            // Se não tiver estrutura padronizada, adicionar
            if (!isset($data['success']) && !isset($data['message'])) {
                $statusCode = $response->getStatusCode();
                $isSuccess = $statusCode >= 200 && $statusCode < 300;
                
                $response->setData([
                    'success' => $isSuccess,
                    'data' => $data,
                    'timestamp' => now()->toISOString()
                ]);
            }
        }

        // Garantir charset UTF-8
        if ($response->headers->get('Content-Type') === 'application/json') {
            $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        }

        return $response;
    }
}
