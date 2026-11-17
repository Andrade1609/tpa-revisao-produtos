<?php
// ============================================================================
// R1 – Migration da tabela produtos
// (arquivo NOVO – seria criado com: php artisan make:migration create_produtos_table)
//
// A migration é o "roteiro" que cria a tabela no banco quando rodamos
// php artisan migrate:fresh --seed.
//
// O NÚMERO no começo do nome do arquivo define a ORDEM em que as migrations
// rodam. Esta é a 000001 porque as tabelas vendas (000002) e produto_venda
// (000003) dependem dela — a pivô aponta para produtos, então produtos
// precisa existir antes.
// ============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // up(): o que fazer ao RODAR a migration -> criar a tabela.
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();                          // coluna id (chave primária, auto-incremento)

            // As colunas abaixo têm EXATAMENTE os nomes da tabela de contratos
            // do README. O ProdutoSeeder confere esses nomes; se um estiver
            // diferente, aparece o aviso "Produtos não semeados".
            $table->string('nome');                // texto (até 255 caracteres)
            $table->string('categoria');           // texto (Eletrônicos, Casa...)
            $table->integer('garantia_meses');     // inteiro
            $table->decimal('preco', 8, 2);        // decimal(8,2): até 8 dígitos, 2 após a vírgula
            $table->integer('estoque');            // inteiro

            // Chave estrangeira para a tabela users (o dono do produto, R6).
            // É OBRIGATÓRIO escrever constrained('users'): sem o parâmetro, o
            // Laravel tentaria adivinhar a tabela pelo nome da coluna
            // (vendedor_id -> "vendedors"), que não existe.
            $table->foreignId('vendedor_id')->constrained('users');

            $table->timestamps();                  // created_at e updated_at
        });
    }

    // down(): o que fazer ao DESFAZER a migration -> apagar a tabela.
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
