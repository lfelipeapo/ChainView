<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Detecta automaticamente a URL do frontend
    if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
        return redirect('http://localhost:3000');
    } else {
        // Em produção, redireciona para a mesma URL
        return redirect(request()->getSchemeAndHttpHost());
    }
});
