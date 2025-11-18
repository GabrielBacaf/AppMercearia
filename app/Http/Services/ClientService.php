<?php

namespace App\Http\Services;

use App\Models\Address;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ClientService
{
    public function storeClient(array $data): Client
    {
        return DB::transaction(function () use ($data) {

            $addressData = Arr::get($data, 'address');
            $clientData = Arr::except($data, ['address']);

            $client = new Client($clientData);

            if ($addressData) {
                $address = Address::create($addressData);
                $client->address()->associate($address);
            }

            $client->save();

            return $client;
        });
    }

    public function updateClient(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $addressData = Arr::get($data, 'address');
            $clientData = Arr::except($data, ['address']);

            $client->fill($clientData);

            if ($addressData) {
                if ($client->address) {

                    $client->address->update($addressData);
                } else {

                    $address = Address::create($addressData);
                    $client->address()->associate($address);
                }
            }

            $client->save();

            return $client;
        });
    }
}
