<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedResourceCollection extends ResourceCollection
{
     /**
     * A mensagem de sucesso a ser incluída na resposta.
     * @var string
     */
    protected string $message;

    /**
     * Cria uma nova instância da collection.
     *
     * @param mixed $resource O recurso paginado (ex: o resultado de Model::paginate()).
     * @param string $message A mensagem de sucesso customizada.
     */
    public function __construct($resource, string $message = 'Recursos listados com sucesso!')
    {
        parent::__construct($resource);
        $this->message = $message;
    }

    /**
     * Transforma a collection em um array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => $this->message,
            'data'    => $this->collection,
            'meta'    => [
                'total'        => $this->resource->total(),
                'per_page'     => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page'    => $this->resource->lastPage(),
            ],
        ];
    }
}
