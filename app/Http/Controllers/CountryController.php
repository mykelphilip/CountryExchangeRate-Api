<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function refresh()
    {
$result = $this->countryService->refreshCountries();

if (!$result) {
    Log::error('Country refresh failed: No result returned from service');
    return response()->json(['error' => 'Country Not Found'], 404);
}
// Log the result for visibility
Log::info('Country refresh triggered', ['result' => $result]);

if (isset($result['error'])) {
    // Log which type of error occurred
    Log::warning('Country refresh failed', [
        'type' => $result['error'],
        'details' => $result['details'] ?? null,
    ]);

    if ($result['error'] === 'Validation failed') {
        return response()->json($result, 400);
    } elseif ($result['error'] === 'External data source unavailable') {
        return response()->json($result, 503);
    } else {
        Log::error('Unexpected refresh error', ['result' => $result]);
        return response()->json($result, 500);
    }
}

// Log a success message
Log::info('Country refresh completed successfully', [
    'last_refreshed_at' => $result['last_refreshed_at'] ?? now(),
]);

return response()->json([
    'message' => 'Countries refreshed successfully',
    'last_refreshed_at' => $result['last_refreshed_at'],
]);

}

    public function index(Request $request)
    {
        $query = Country::query();
        if (!$query) {
            return response()->json(['error' => 'Database query failed'], 400);
        }

        if ($request->has('region')) {
            $query->where('region', $request->region);
        }
        if ($request->has('currency')) {
            $query->where('currency_code', $request->currency);
        }
        if ($request->has('sort')) {
            $sortField = 'estimated_gdp';
            $sortDirection = 'desc';
            if (in_array($request->sort, ['gdp_desc', 'name_desc', 'name_asc', 'population_desc', 'population_asc'])) {
                $parts = explode('_', $request->sort);
                $sortField = str_replace(['gdp', 'name', 'population'], ['estimated_gdp', 'name', 'population'], $parts[0]);
                $sortDirection = $parts[1];
            }
            $query->orderBy($sortField, $sortDirection);
        }

        $countries = $query->get();
        if ($countries->isEmpty()) {
            return response()->json(['error' => 'Country List Not Found'], 404);
        }

        return response()->json($countries);
    }

    public function show(string $name)
    {
        $country = Country::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        if (!$country) {
            return response()->json(['error' => 'Could not fetch Country Data'], 404); 
        }
        return response()->json($country);
    }

    public function destroy(string $name)
    {
        $country = Country::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if (!$country) {
            return response()->json(['error' => 'Could not fetch Countries List to Delete'], 404);
        } else {
            Log::info('Deleting country', ['name' => $name]);
            $country->delete();
        return response()->json(['message' => 'Country deleted successfully']);
        }
        
    }

    public function status()
    {
        $totalCountries = Country::count();
        $lastRefreshedAt = Country::max('last_refreshed_at');
        if ($totalCountries === 0 || $lastRefreshedAt === null) {
            return response()->json(['error' => 'No country in the database'], 404);
        } else {
            return response()->json([
            'total_countries' => $totalCountries,
            'last_refreshed_at' => $lastRefreshedAt,
        ]);
        }
    }

    public function image()
    {
        $path = storage_path('app/cache/summary.png');
        if (!file_exists($path)) {
            return response()->json(['error' => 'Summary image not found'], 404);
        }
        return response()->file($path);
    }
}