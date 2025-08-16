<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Detecta automaticamente a URL do frontend
    if (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') {
        return redirect('http://localhost:3000');
    } else {
        // Em produção, serve o index.html do frontend
        return response()->file(public_path('index.html'));
    }
});
