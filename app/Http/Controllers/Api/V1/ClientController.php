<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Client\ClientRequest;
use App\Http\Resources\V1\Client\ClientResource;
use App\Http\Services\ClientService;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    public function __construct(protected ClientService $clientService) {}


    public function index()
    {
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
        return $this->successResponse(
            new ClientResource($client->load('address')),
            'Cliente detalhado com sucesso!',
            200
        );
    }

  public function store(ClientRequest $request)
    {
        $data = $request->validated();
        $client = $this->clientService->storeClient($data);

        return $this->successResponse(
            new ClientResource($client->load('address')),
            'Cliente criado com sucesso!',
            201
        );
    }

    public function update(ClientRequest $request, Client $client)
    {
        $data = $request->validated();
        $updatedClient = $this->clientService->updateClient($client, $data);

        return $this->successResponse(
            new ClientResource($updatedClient),
            'Cliente atualizado com sucesso!',
            200
        );
    }
}
