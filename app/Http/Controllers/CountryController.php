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
    const COUNTRIES_URL = 'https://restcountries.com/v3.1/all';
    const PER_PAGE = 25;

    public function index(Request $request)
    {
        $countries = $this->getCachedCountries();

        $searchQuery = $request->input('search', '');
        $sortOrder = $request->input('sort', 'asc');

        $filteredCountries = $this->filterAndSortCountries($countries, $searchQuery, $sortOrder);
        $transformedCountries = $this->transformCountriesData($filteredCountries);

        return response()->json($this->paginate($transformedCountries, $request->input('page', 1)));
    }

    private function getCachedCountries()
    {
        return Cache::remember('countries', 60 * 60, function () {
            try {
                $response = Http::get(self::COUNTRIES_URL);
                return collect($response->json());
            } catch (\Exception $e) {
                return collect([]);
            }
        });
    }

    private function filterAndSortCountries($countries, $searchQuery, $sortOrder)
    {
        return $countries->when($searchQuery, function ($collection) use ($searchQuery) {
            return $collection->filter(function ($country) use ($searchQuery) {
                return Str::contains(strtolower($country['name']['official']), strtolower($searchQuery));
            });
        })->sortBy([
            fn ($a, $b) => $sortOrder === 'asc' ? 
                           strcmp($a['name']['official'], $b['name']['official']) : 
                           strcmp($b['name']['official'], $a['name']['official'])
        ]);
    }

    private function transformCountriesData($countries)
    {
        return $countries->map(function ($country) {
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
    }

    private function paginate($items, $currentPage)
    {
        $currentResults = $items->slice(($currentPage - 1) * self::PER_PAGE, self::PER_PAGE)->values();
        return new LengthAwarePaginator($currentResults, $items->count(), self::PER_PAGE, $currentPage, [
            'path' => Paginator::resolveCurrentPath()
        ]);
    }
}
