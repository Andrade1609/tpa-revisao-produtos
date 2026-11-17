<?php
// ============================================================================
// R4 – Middleware "vendedor"
// (arquivo NOVO – seria criado com: php artisan make:middleware VendedorMiddleware)
//
// Middleware = um "porteiro" que roda ANTES do controller. Ele decide se a
// requisição pode continuar ou se deve ser barrada.
//
// A pasta app/Http/Middleware precisou ser criada à mão: desde o Laravel 11
// ela não vem mais no projeto. O caminho da pasta precisa bater com o
// namespace abaixo, senão o Laravel não encontra a classe.
//
// Este middleware só funciona porque foi registrado com o apelido 'vendedor'
// em bootstrap/app.php e aplicado nas rotas em routes/web.php.
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendedorMiddleware
{
    // $request = a requisição que chegou.
    // $next    = "o próximo passo" (outro middleware ou o controller).
    public function handle(Request $request, Closure $next): Response
    {
        // $request->user() devolve o usuário logado (ou null se ninguém logou).
        // O operador ?-> evita erro quando é null: em vez de quebrar, o
        // resultado vira null, que é diferente de 'vendedor' -> cai no 403.
        //
        // A coluna "tipo" vem da tabela users ('vendedor' ou 'cliente').
        if ($request->user()?->tipo !== 'vendedor') {
            // Interrompe a requisição e mostra resources/views/errors/403.blade.php
            // ("403 – Acesso negado"), que já vinha pronta no projeto.
            abort(403);
        }

        // É vendedor: deixa a requisição seguir até o controller.
        return $next($request);
    }
}
