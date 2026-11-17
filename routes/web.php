<?php

use App\Http\Controllers\CompraController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

// Páginas de ilustração (prontas)
Route::view('/', 'home')->name('home');
Route::view('/sobre', 'sobre')->name('sobre');

// Autenticação (públicas: quem não está logado precisa conseguir abrir o login)
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'attempt'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// R1 – Listagem de produtos: PÚBLICA, por isso fica FORA dos grupos com middleware.
Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');

// ALTERADO – R3 + R4: cadastro de produtos.
// Route::middleware([...])->group(...) aplica os middlewares a TODAS as rotas
// de dentro do grupo, sem repetir em cada uma.
//
// A ORDEM IMPORTA: primeiro 'auth' (está logado? se não, vai para a rota
// chamada 'login'), depois 'vendedor' (é vendedor? se não, 403).
// Invertido, um visitante sem login levaria 403 em vez de ir para o login,
// e o teste do R3 falharia.
//
// 'auth' já vem pronto no Laravel. 'vendedor' é o apelido que registramos
// em bootstrap/app.php apontando para app/Http/Middleware/VendedorMiddleware.php.
Route::middleware(['auth', 'vendedor'])->group(function () {
    Route::get('/produtos/create', [ProdutoController::class, 'create'])->name('produtos.create');
    Route::post('/produtos', [ProdutoController::class, 'store'])->name('produtos.store');
});

// ALTERADO – R3: compras. Basta estar logado (cliente OU vendedor), então só 'auth'.
Route::middleware('auth')->group(function () {
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
});
