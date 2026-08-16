<?php

namespace App\Http\Resources\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait InteractsWithPagination
{
    /**
     * Metadados de paginação da collection.
     *
     * Nem toda collection recebe um paginator — services que devolvem um
     * array ou uma Collection comum (o store de grupo, por exemplo) passariam
     * por aqui e estourariam num currentPage() inexistente. Nesses casos o
     * retorno é null, mantendo a mesma forma de resposta em todos os casos.
     *
     * @return array{current_page: int, last_page: int, per_page: int, total: int}|null
     */
    protected function paginationMeta(): ?array
    {
        if (! $this->resource instanceof LengthAwarePaginator) {
            return null;
        }

        return [
            'current_page' => (int) $this->resource->currentPage(),
            'last_page' => (int) $this->resource->lastPage(),
            'per_page' => (int) $this->resource->perPage(),
            'total' => (int) $this->resource->total(),
        ];
    }
}
