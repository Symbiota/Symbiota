<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class GeoThesaurusHelper
{
    public static function getGeoterms($geoTerms)
    {
        if (is_string($geoTerms))
            $geoTerms = collect(explode(',', $geoTerms))->map(fn($item) => trim($item));
        else
            $geoTerms = collect($geoTerms);

        $entries = DB::table('geographicthesaurus')
            ->whereIn('geoterm', $geoTerms)
            ->get();

        $withIso2 = $entries->whereNotNull('iso2');
        $withoutIso2 = $entries->whereNull('iso2');

        $countryCodes = $withIso2->pluck('iso2')->unique()->values();
        $matchedGeo = $entries->pluck('geoterm');
        $unmatchedGeo = $geoTerms->diff($matchedGeo)->values();

        return [
            'countryCode' => $countryCodes->toArray(),
            'country' => $withoutIso2->pluck('geoterm')->merge($unmatchedGeo)->unique()->values()->toArray(),
        ];
    }
}