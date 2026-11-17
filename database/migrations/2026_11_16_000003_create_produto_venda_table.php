<?php
// ============================================================================
// R7 – Migration da tabela pivô produto_venda
// (arquivo NOVO – seria criado com: php artisan make:migration create_produto_venda_table)
//
// Tabela PIVÔ = tabela "do meio" de uma relação N:M. Cada linha aqui é
// UM ITEM de UMA venda: "na venda X, o produto Y, Z unidades, a R$ W cada".
//
// Roda por ÚLTIMO (000003) porque aponta para vendas E produtos — as duas
// precisam já existir, senão a chave estrangeira dá erro.
// ============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produto_venda', function (Blueprint $table) {
            $table->id();

            // A qual venda este item pertence.
            // cascadeOnDelete: se a venda for apagada, os itens dela somem junto
            // (não ficam itens "órfãos" apontando para uma venda que não existe).
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();

            // Qual produto foi comprado.
            $table->foreignId('produto_id')->constrained('produtos');

            // Colunas EXTRAS da pivô (por isso o withPivot(...) na model Venda).
            $table->integer('quantidade');

            // Guarda o preço NO MOMENTO da compra. Se o vendedor mudar o preço
            // do produto depois, a compra antiga continua mostrando o valor
            // que realmente foi pago.
            $table->decimal('preco_unitario', 8, 2);

            $table->timestamps();   // preenchidos graças ao withTimestamps() na model
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produto_venda');
    }
};
