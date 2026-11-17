<?php
// ============================================================================
// R7 – Model Venda  (arquivo NOVO – seria criado com: php artisan make:model Venda)
//
// Uma venda é "uma compra feita por um cliente". Os itens comprados NÃO ficam
// nesta tabela: ficam na tabela pivô produto_venda (relação N:M).
// Tabela deduzida pelo Laravel: Venda -> vendas.
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    // Só cliente_id é preenchido no Venda::create(...) do CompraController.
    // id e timestamps o próprio Laravel cuida.
    protected $fillable = [
        'cliente_id',
    ];

    // Relacionamento N:M -> uma venda tem VÁRIOS produtos e um produto
    // aparece em VÁRIAS vendas. Para isso existe a tabela do meio (pivô).
    //
    // O Laravel deduz o nome da pivô juntando os dois models no singular,
    // em ordem alfabética: produto + venda = "produto_venda". Por isso não
    // precisa passar o nome da tabela.
    public function produtos()
    {
        return $this->belongsToMany(Produto::class)
            // withPivot: a pivô tem colunas EXTRAS além de venda_id/produto_id.
            // Sem esta linha, $produto->pivot->quantidade viria vazio na view
            // "Minhas compras" (que calcula subtotal e total a partir disso).
            ->withPivot('quantidade', 'preco_unitario')
            // withTimestamps: faz o attach() preencher created_at/updated_at
            // da pivô. Sem isso essas colunas ficariam NULL.
            ->withTimestamps();
    }

    // Extra (não exigido no enunciado): a venda pertence a um usuário (o cliente).
    // Permite fazer $venda->cliente->name se precisar.
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
}
