<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $countries = Cache::remember('countries', 60 * 60, function () {
            $response = Http::get('https://restcountries.com/v3.1/all');
            return collect($response->json());
        });

        $searchQuery = $request->input('search', '');
        $sortOrder = $request->input('sort', 'asc');

        if (!empty($searchQuery)) {
            $countries = $countries->filter(function ($country) use ($searchQuery) {
                return Str::contains(strtolower($country['name']['official']), strtolower($searchQuery));
            });
        }
        
        $countries = $countries->sortBy([
            fn ($a, $b) => $sortOrder === 'asc' ? 
                           strcmp($a['name']['official'], $b['name']['official']) : 
                           strcmp($b['name']['official'], $a['name']['official'])
        ]);

        $transformedCountries = $countries->map(function ($country) {
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
        });

        $perPage = 25;
        $currentPage = $request->input('page', 1);
        $currentResults = $transformedCountries->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedResults = new LengthAwarePaginator($currentResults, $transformedCountries->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath()
        ]);

        return response()->json($paginatedResults);
    }
}
