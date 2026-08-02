<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidCpfRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /**
         * Formata op cpf para tirar os pontos e traços,
         * valida a partir do value que é o campo original
         */
        $cpf = preg_replace('/[^0-9]/', '', $value);

        /**
         * Verifica se tem 11 caracteres no total,
         * se não tiver retorna o $fail que é o erro da rule
         * e encerra no if
         */
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            $fail("The cpf field doesn't a valid CPF");
            return;
        }

        // Cálculo matemático para validar se é realmente válido
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $fail('The cpf fiels is invalid');
                return;
            }
        }
    }
}

/**
 * $attribute: É o nome do campo (ex: "cpf", "email").
 * $value: É o dado real que está sendo validado.
 * $fail: É uma função (callback). Se a validação falhar, você a chama passando a mensagem de erro.
 * Tradução: O :attribute dentro da string do $fail é trocado automaticamente pelo nome do campo.
 */
