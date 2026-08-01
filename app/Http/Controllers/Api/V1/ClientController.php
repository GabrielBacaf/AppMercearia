<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ClientPermissionEnum;
use App\Http\Requests\Api\V1\Client\ClientRequest;
use App\Http\Resources\V1\Client\ClientResource;
use App\Http\Services\ClientService;
use App\Models\Client;
use App\Http\Services\LocationService;
use Exception;


class ClientController extends Controller
{

    public function __construct(protected ClientService $clientService) {}


    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize(ClientPermissionEnum::INDEX->value);

        $clients = Client::with('address')->latest()->paginate(5);
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

    public function extractLocation(\Illuminate\Http\Request $request, LocationService $locationService)
    {
        $link = $request->input('link');

        if (!$link) {
            return response()->json(['error' => 'Link is required'], 400);
        }

        try {
            $addressData = $locationService->extractFromLink($link);
            return response()->json($addressData);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
