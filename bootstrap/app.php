<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ALTERADO – R4: registra o APELIDO 'vendedor' para o nosso middleware.
        // É isso que permite escrever Route::middleware('vendedor') em
        // routes/web.php. Sem este registro aparece o erro
        // "Target class [vendedor] does not exist".
        //
        // Atenção: este é o bootstrap/app.php. NÃO confundir com config/app.php,
        // que guarda configurações (nome, fuso, idioma) e não deve ser mexido.
        $middleware->alias([
            'vendedor' => \App\Http\Middleware\VendedorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
