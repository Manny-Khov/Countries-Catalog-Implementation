<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $response = Http::get('https://restcountries.com/v3.1/all');
        $countries = $response->json();

        $perPage = 25;

        $currentPage = $request->input('page', 1);

        $offset = ($currentPage - 1) * $perPage;

        $paginatedItems = array_slice($countries, $offset, $perPage);

        $data = array_map(function ($country) {
            return [
                'flag' => $country['flags']['png'] ?? null,
                'officialName' => $country['name']['official'] ?? null,
                'cca2' => $country['cca2'] ?? null,
                'cca3' => $country['cca3'] ?? null,
                'nativeName' => $country['name']['nativeName'][array_key_first($country['name']['nativeName'] ?? [])]['official'] ?? null,
                'altSpellings' => $country['altSpellings'] ?? [],
                'callingCodes' => (isset($country['idd']['root']) ? $country['idd']['root'] : '') .
                                  (isset($country['idd']['suffixes']) ? implode(', ', $country['idd']['suffixes']) : '')
            ];
        }, $countries);
    }
}