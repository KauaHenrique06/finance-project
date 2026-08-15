<?php 

namespace App\Services\Address;

use App\Exceptions\ApiException;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AddressService
{
    public function store(array $data)
    {
        $url = config('services.brasil_api.url');

        try {

            $response = Http::retry(3, 10)->get("$url/cep/v2/{$data['cep']}");
            $response = $response->json();

        } catch (\Exception $e) {
            throw new ApiException('Unexpected error on BrasilAPI: ' . $e->getMessage());
        }   

        return DB::transaction(function () use ($data, $response) {

            $addressData  = Address::create([
                'cep' => $data['cep'],
                'state' => $response['state'],
                'number' => $data['number'],
                'neighborhood' => $response['neighborhood'],
                'street' => $response['street'],
                'city' => $response['city'],
            ]);

            return $addressData;
        });
    }
}