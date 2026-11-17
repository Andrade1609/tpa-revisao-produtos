<?php
// ============================================================================
// R1 – Model Produto  (arquivo NOVO – seria criado com: php artisan make:model Produto)
//
// A model é a "ponte" entre o PHP e a tabela do banco. O Laravel deduz o nome
// da tabela a partir do nome da classe: Produto -> produtos (plural em inglês,
// que aqui funciona porque basta acrescentar "s").
// ============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    // $fillable = lista de colunas que podem ser preenchidas "em massa",
    // ou seja, com Produto::create([...]). Qualquer coluna fora desta lista
    // é IGNORADA no create (proteção contra o usuário mandar campos extras).
    //
    // vendedor_id está aqui porque o ProdutoController faz
    // Produto::create([... 'vendedor_id' => Auth::id()]). Sem ele aqui,
    // o produto seria salvo sem dono e daria erro (a coluna é obrigatória).
    protected $fillable = [
        'nome',
        'categoria',
        'garantia_meses',
        'preco',
        'estoque',
        'vendedor_id',
    ];

    // Relacionamento N:1 -> "um produto PERTENCE A um usuário (o vendedor)".
    // belongsTo é usado do lado que TEM a chave estrangeira (produtos.vendedor_id).
    //
    // O 2º parâmetro ('vendedor_id') diz qual coluna liga as tabelas.
    // Com isso a view consegue fazer $produto->vendedor->name
    // ("Ana Vendedora" / "Bruno Vendedor" na coluna Vendedor da listagem).
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
}
