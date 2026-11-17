<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   // ALTERADO: import necessário para Auth::attempt / Auth::logout

class LoginController extends Controller
{
    // (já vinha pronto) mostra a tela de login
    public function create()
    {
        return view('auth.login');
    }

    // ------------------------------------------------------------------------
    // R2 – Login (POST /login)
    // ------------------------------------------------------------------------
    public function attempt(Request $request)
    {
        // Valida que os dois campos vieram preenchidos. Os nomes 'email' e
        // 'password' são os "name" dos inputs em auth/login.blade.php.
        // validate() devolve só esses dois campos -> já é o array que o
        // Auth::attempt espera.
        $credenciais = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Auth::attempt procura o usuário pelo e-mail e compara a senha
        // digitada com o HASH salvo no banco. Se bater, faz o login e
        // devolve true.
        if (Auth::attempt($credenciais)) {
            // Gera um novo id de sessão após o login (segurança: evita
            // "session fixation", alguém reaproveitar uma sessão antiga).
            $request->session()->regenerate();

            // intended(): se a pessoa tentou abrir, por ex., "Comprar" sem
            // estar logada, o middleware auth mandou ela pro login; agora
            // ela volta direto para "Comprar". Se não havia página pendente,
            // vai para a listagem de produtos.
            return redirect()->intended(route('produtos.index'));
        }

        // Login falhou: volta para o formulário com uma mensagem de erro.
        // A chave é 'email' porque a view mostra o erro com @error('email').
        // onlyInput('email') mantém o e-mail preenchido, mas NÃO a senha.
        return back()
            ->withErrors(['email' => 'E-mail ou senha inválidos.'])
            ->onlyInput('email');
    }

    // ------------------------------------------------------------------------
    // R2 – Logout (POST /logout)
    // ------------------------------------------------------------------------
    public function logout(Request $request)
    {
        Auth::logout();                          // desloga o usuário
        $request->session()->invalidate();       // apaga os dados da sessão
        $request->session()->regenerateToken();  // gera novo token CSRF (o @csrf dos formulários)

        // Volta para a listagem; o topo da página mostra de novo o botão "Entrar".
        return redirect()->route('produtos.index');
    }
}
