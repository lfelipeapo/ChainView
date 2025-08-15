<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Detecta automaticamente a URL do frontend baseado no ambiente
    if (app()->environment('production')) {
        // Em produção, usa a mesma URL do backend mas porta 3000
        $backendUrl = request()->getSchemeAndHttpHost();
        $frontendUrl = str_replace([':80', ':443'], ':3000', $backendUrl);
    } else {
        // Em desenvolvimento local
        $frontendUrl = 'http://localhost:3000';
    }
    return redirect($frontendUrl);
});
