<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ClientPermissionEnum;
use App\Http\Requests\Api\V1\Client\ClientRequest;
use App\Http\Resources\V1\Client\ClientResource;
use App\Http\Services\ClientService;
use App\Models\Client;


class ClientController extends Controller
{

    public function __construct(protected ClientService $clientService) {}


    public function index()
    {
        $this->authorize(ClientPermissionEnum::INDEX->value);

        $clients = Client::with('address')->paginate(5);
        return $this->successResponseCollection(
            ClientResource::collection($clients),
            $clients,
            "Clientes listados com sucesso!",
            200
        );
    }

    public function show(Client $client)
    {
        $this->authorize(ClientPermissionEnum::SHOW->value);

        return $this->successResponse(
            new ClientResource($client->load('address')),
            'Cliente detalhado com sucesso!',
            200
        );
    }

  public function store(ClientRequest $request)
    {
        $this->authorize(ClientPermissionEnum::STORE->value);

        $client = $this->clientService->storeClient($request->validated());

        return $this->successResponse(
            new ClientResource($client->load('address')),
            'Cliente criado com sucesso!',
            201
        );
    }

    public function update(ClientRequest $request, Client $client)
    {
        $this->authorize(ClientPermissionEnum::UPDATE->value);

        $updatedClient = $this->clientService->updateClient($client, $request->validated());

        return $this->successResponse(
            new ClientResource($updatedClient),
            'Cliente atualizado com sucesso!',
            200
        );
    }
}
