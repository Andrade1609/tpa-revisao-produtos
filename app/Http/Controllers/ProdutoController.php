<?php

namespace App\Http\Controllers;

// ALTERADO: saiu "use Illuminate\Http\Request" e entraram estes três imports.
use App\Http\Requests\StoreProdutoRequest;  // nosso FormRequest de validação (R5)
use App\Models\Produto;                     // a model criada no R1
use Illuminate\Support\Facades\Auth;        // para pegar o usuário logado (R6)

class ProdutoController extends Controller
{
    // ------------------------------------------------------------------------
    // R1 – Listagem pública de produtos (GET /produtos)
    // ------------------------------------------------------------------------
    public function index()
    {
        // with('vendedor') = "eager loading": busca todos os vendedores numa
        // consulta só. Sem ele, cada linha da tabela que mostra
        // $produto->vendedor->name faria uma consulta nova ao banco
        // (problema N+1). O enunciado pede explicitamente o with().
        //
        // orderBy('nome') é opcional, só para a lista ficar em ordem alfabética.
        $produtos = Produto::with('vendedor')->orderBy('nome')->get();

        // compact('produtos') é o mesmo que ['produtos' => $produtos].
        // A view produtos/index.blade.php espera EXATAMENTE a variável $produtos.
        return view('produtos.index', compact('produtos'));
    }

    // (já vinha pronto) mostra o formulário de cadastro
    public function create()
    {
        return view('produtos.create');
    }

    // ------------------------------------------------------------------------
    // R5 + R6 – Cadastro validado e dono do produto (POST /produtos)
    // ------------------------------------------------------------------------
    // ALTERADO: o parâmetro era "Request $request" e virou "StoreProdutoRequest".
    // Só de trocar o tipo, o Laravel roda as regras de validação ANTES de
    // entrar aqui. Se falhar, nem chega nesta linha: volta pro formulário.
    public function store(StoreProdutoRequest $request)
    {
        // $request->validated() devolve SÓ os campos que passaram na validação
        // (nome, categoria, garantia_meses, preco, estoque).
        //
        // + ['vendedor_id' => Auth::id()] junta o id do usuário logado (R6).
        // O dono vem do LOGIN, nunca do formulário — assim ninguém consegue
        // cadastrar um produto em nome de outro vendedor.
        Produto::create($request->validated() + ['vendedor_id' => Auth::id()]);

        // Volta para a listagem. A chave 'sucesso' é a que o layout
        // (layouts/app.blade.php) mostra na caixinha verde.
        return redirect()->route('produtos.index')->with('sucesso', 'Produto cadastrado!');
    }
}
