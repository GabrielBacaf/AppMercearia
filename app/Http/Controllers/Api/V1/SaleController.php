<?php

namespace App\Http\Controllers\Api\V1;

class SaleController extends Controller
{
    public function index() {

         $this->authorize(ClientPermissionEnum::INDEX->value);

        $clients = Client::with('address')->paginate(5);
        return $this->successResponseCollection(
            ClientResource::collection($clients),
            $clients,
            "Clientes listados com sucesso!",
            200
        );



    }
}
