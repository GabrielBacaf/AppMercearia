<?php

namespace App\Http\Services;

use App\Models\Address;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ClientService
{
    public function storeClient(array $data): Client|null
    {

        $client = null;

        DB::transaction(function () use ($data, &$client) {

            $addressData = Arr::get($data, 'address');
            $clientData = Arr::only($data, ['name', 'email', 'phone']);

            $address = $addressData ? Address::create($addressData) : null;

            if ($address !== null) {
                $clientData = array_merge(
                    $clientData,
                    [
                        'address_id' => $address->id,
                    ]
                );
            }

            $client = Client::create($clientData);
        });

        return $client;
    }

    public function updateClient(Client $client, array $data): Client
    {

        DB::transaction(function () use ($client, $data) {

            $addressData = Arr::get($data, 'address');
            $clientData = Arr::only($data, ['name', 'email', 'phone']);

            $client->update($clientData);

            if (is_array($addressData) && !empty($addressData) && $addressData !== null) {

                if ($client->address) {
                    $client->address->update($addressData);
                } else {
                    $client->address()->create($addressData);
                }
            }
        });

        return $client->fresh();
    }
}
