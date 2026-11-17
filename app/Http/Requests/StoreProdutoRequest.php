<?php
// ============================================================================
// R5 – FormRequest de cadastro de produto
// (arquivo NOVO – seria criado com: php artisan make:request StoreProdutoRequest)
//
// FormRequest = uma classe só para VALIDAR um formulário. Quando o controller
// recebe StoreProdutoRequest como parâmetro, o Laravel valida ANTES de entrar
// no método. Se algo falhar, ele volta sozinho para o formulário com os erros
// (que a view mostra com @error('campo')) e com os valores digitados (old()).
//
// A pasta app/Http/Requests também precisou ser criada à mão (Laravel 11+).
// ============================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
{
    // authorize(): "este usuário PODE enviar este formulário?"
    // ATENÇÃO: o make:request gera este método retornando FALSE, e aí TODA
    // requisição recebe 403. Tem que ser TRUE. Quem controla o acesso aqui
    // são os middlewares auth + vendedor (nas rotas), não este método.
    public function authorize(): bool
    {
        return true;
    }

    // rules(): as regras de cada campo, copiadas da tabela de contratos do README.
    public function rules(): array
    {
        return [
            // obrigatório, texto, no máximo 255 caracteres
            'nome' => 'required|string|max:255',

            // in: só aceita EXATAMENTE um destes valores (com acento!).
            // Impede alguém de mandar uma categoria inventada.
            'categoria' => 'required|in:Eletrônicos,Vestuário,Casa,Alimentos',

            // between:0,36 -> aceita de 0 a 36 (inclusive).
            // O teste do R5 manda 48, que precisa dar erro.
            'garantia_meses' => 'required|integer|between:0,36',

            // gt:0 = "greater than" 0 -> MAIOR que zero (0 dá erro).
            // Cuidado: min:0 aceitaria o 0, e o teste manda preço 0 esperando erro.
            'preco' => 'required|numeric|gt:0',

            // min:0 -> zero ou mais (estoque 0 é permitido).
            'estoque' => 'required|integer|min:0',

            // vendedor_id NÃO aparece aqui de propósito: ele nunca vem do
            // formulário, é definido no controller com Auth::id() (R6).
        ];
    }
}
