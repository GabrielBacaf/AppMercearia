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


    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize(ClientPermissionEnum::INDEX->value);

        $query = Client::with('address');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(5);
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

    public function extractLocation(\Illuminate\Http\Request $request)
    {
        $link = $request->input('link');

        if (!$link) {
            return response()->json(['error' => 'Link is required'], 400);
        }

        $lat = null;
        $lng = null;

        // 1. Regex directly on the input
        if (preg_match('/(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/', $link, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        }

        // 2. Try fetching URL if coords not found yet
        if (!$lat && filter_var($link, FILTER_VALIDATE_URL)) {
            try {
                $response = \Illuminate\Support\Facades\Http::withOptions(['allow_redirects' => false])->get($link);
                $redirectUrl = $response->header('Location');

                if ($redirectUrl && preg_match('/(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/', $redirectUrl, $matches)) {
                    $lat = $matches[1];
                    $lng = $matches[2];
                }

                if (!$lat) {
                    $responseFull = \Illuminate\Support\Facades\Http::get($link);
                    if (preg_match('/(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/', $responseFull->body(), $matches)) {
                        $lat = $matches[1];
                        $lng = $matches[2];
                    }
                }
            } catch (\Exception $e) {
                // Ignore and proceed to fail
            }
        }

        if ($lat && $lng) {
            $addressData = [
                'latitude' => $lat,
                'longitude' => $lng,
                'street' => '',
                'number' => '',
                'city' => '',
                'state' => '',
                'postal_code' => '',
                'country' => ''
            ];

            // Reverse Geocoding with OpenStreetMap Nominatim
            try {
                $geocodeUrl = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";
                $geoResponse = \Illuminate\Support\Facades\Http::withHeaders([
                    'User-Agent' => 'AppMercearia/1.0'
                ])->get($geocodeUrl);

                if ($geoResponse->successful() && $geoResponse->json('address')) {
                    $addr = $geoResponse->json('address');
                    
                    $addressData['street'] = $addr['road'] ?? $addr['street'] ?? $addr['pedestrian'] ?? '';
                    $addressData['number'] = $addr['house_number'] ?? '';
                    $addressData['city'] = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? '';
                    $addressData['state'] = $addr['state'] ?? '';
                    $addressData['postal_code'] = $addr['postcode'] ?? '';
                    $addressData['country'] = $addr['country'] ?? '';
                }
            } catch (\Exception $e) {
                // Return just coordinates if geocoding fails
            }

            return response()->json($addressData);
        }

        return response()->json(['error' => 'Não foi possível extrair as coordenadas'], 400);
    }
}
