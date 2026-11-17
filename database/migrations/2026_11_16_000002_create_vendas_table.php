<?php
// ============================================================================
// R7 – Migration da tabela vendas
// (arquivo NOVO – seria criado com: php artisan make:migration create_vendas_table)
//
// Roda em 2º lugar (000002): precisa existir ANTES da pivô produto_venda,
// que aponta para ela.
// ============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();

            // Quem comprou. Aponta para users -> por isso constrained('users')
            // (sem o parâmetro o Laravel procuraria a tabela "clientes").
            $table->foreignId('cliente_id')->constrained('users');

            // created_at = data da compra (mostrada em "Minhas compras").
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
