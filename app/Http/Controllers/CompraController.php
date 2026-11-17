<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Venda;                   // ALTERADO: model criada no R7
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    // ALTERADO: para pegar o id do usuário logado
use Illuminate\Support\Facades\DB;      // ALTERADO: para usar DB::transaction (bônus)

class CompraController extends Controller
{
    // ------------------------------------------------------------------------
    // R8 – Minhas compras (GET /compras)
    // ------------------------------------------------------------------------
    public function index()
    {
        // where('cliente_id', Auth::id()) -> SÓ as vendas do usuário logado.
        // Sem esse filtro o Diego veria as compras da Carla (o teste do R8
        // verifica exatamente isso).
        $vendas = Venda::where('cliente_id', Auth::id())
            // Carrega os produtos de cada venda numa consulta só (com os
            // dados da pivô: quantidade e preco_unitario). A view usa isso
            // para calcular subtotal e total.
            ->with('produtos')
            // latest() = ordena por created_at decrescente (mais recente primeiro).
            ->latest()
            ->get();

        // A view compras/index.blade.php espera a variável $vendas.
        return view('compras.index', compact('vendas'));
    }

    // (já vinha pronto) mostra o formulário de compra
    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();

        return view('compras.create', compact('produtos'));
    }

    // ------------------------------------------------------------------------
    // R7 + Bônus – Finalizar compra (POST /compras)
    // ------------------------------------------------------------------------
    public function store(Request $request)
    {
        // O formulário envia um array no formato itens[id_do_produto] = quantidade.
        // Ex.: itens[1] = 2, itens[2] = 0, itens[3] = 1
        // 'itens.*' aplica a regra a CADA valor do array (cada quantidade).
        $request->validate([
            'itens' => 'required|array',
            'itens.*' => 'integer|min:0',
        ]);

        // Passo 1 do R7: ignorar os itens com quantidade 0.
        // array_filter mantém só os que passam no teste (quantidade > 0).
        // As CHAVES do array (ids dos produtos) são preservadas.
        // Resultado do exemplo acima: [1 => 2, 3 => 1]
        $itens = array_filter($request->input('itens', []), fn ($quantidade) => (int) $quantidade > 0);

        // Extra: se tudo estava 0, não faz sentido criar uma venda vazia.
        // withInput() devolve os valores digitados para o formulário (old()).
        if (empty($itens)) {
            return back()->withErrors(['itens' => 'Escolha pelo menos um produto.'])->withInput();
        }

        // Busca no banco só os produtos escolhidos.
        // array_keys($itens) = os ids -> [1, 3]
        $produtos = Produto::whereIn('id', array_keys($itens))->get();

        // BÔNUS – confere o estoque de TODOS os itens ANTES de criar qualquer coisa.
        // Se a checagem ficasse dentro do loop do attach, a venda já teria sido
        // criada quando o erro aparecesse — e o teste exige que "nenhuma compra
        // pode ser criada" nesse caso.
        foreach ($produtos as $produto) {
            if ($itens[$produto->id] > $produto->estoque) {
                return back()
                    ->withErrors(['itens' => "Estoque insuficiente para {$produto->nome}."])
                    ->withInput();
            }
        }

        // Transação: tudo aqui dentro é "tudo ou nada". Se qualquer linha der
        // erro no meio, o banco desfaz o que já tinha feito (não fica venda
        // pela metade nem estoque descontado sem venda).
        DB::transaction(function () use ($produtos, $itens) {
            // Passo 2 do R7: cria a venda com o cliente = usuário logado.
            $venda = Venda::create(['cliente_id' => Auth::id()]);

            foreach ($produtos as $produto) {
                // Passo 3 do R7: attach() insere uma linha na pivô produto_venda
                // (venda_id e produto_id automáticos + as colunas extras abaixo).
                $venda->produtos()->attach($produto->id, [
                    'quantidade' => $itens[$produto->id],
                    // preço vem do BANCO (preço atual do produto), nunca do
                    // formulário — senão o cliente poderia alterar o preço.
                    'preco_unitario' => $produto->preco,
                ]);

                // BÔNUS: decrement subtrai a quantidade comprada do estoque.
                // Equivale a: UPDATE produtos SET estoque = estoque - N WHERE id = ...
                $produto->decrement('estoque', $itens[$produto->id]);
            }
        });

        // Passo 4 do R7: redireciona para "Minhas compras".
        // A chave TEM que ser 'sucesso': é a que o layout lê com session('sucesso').
        // E o texto precisa ser "Compra realizada!", que é o que o teste confere.
        return redirect()->route('compras.index')->with('sucesso', 'Compra realizada!');
    }
}
