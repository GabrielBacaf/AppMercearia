<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class LocationService
{
    public function extractFromLink(string $link): array
    {
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
                $response = Http::withOptions(['allow_redirects' => false])->get($link);
                $redirectUrl = $response->header('Location');

                if ($redirectUrl && preg_match('/(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/', $redirectUrl, $matches)) {
                    $lat = $matches[1];
                    $lng = $matches[2];
                }

                if (!$lat) {
                    $responseFull = Http::get($link);
                    if (preg_match('/(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/', $responseFull->body(), $matches)) {
                        $lat = $matches[1];
                        $lng = $matches[2];
                    }
                }
            } catch (Exception $e) {
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
                $geoResponse = Http::withHeaders([
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
            } catch (Exception $e) {
                // Return just coordinates if geocoding fails
            }

            return $addressData;
        }

        throw new Exception('Não foi possível extrair as coordenadas do link fornecido.');
    }
}
