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

        $foundGeoterms = $entries->pluck('geoterm');
        $iso2Codes = $entries->pluck('iso2')->filter()->unique();

        $isoGeoterms = collect();
        if ($iso2Codes->isNotEmpty()) {
            $isoGeoterms = DB::table('geographicthesaurus')
                ->whereIn('iso2', $iso2Codes)
                ->pluck('geoterm');
        }

        $notFoundGeoterms = $geoTerms->diff($foundGeoterms);

        return $isoGeoterms
            ->merge($entries->whereNull('iso2')->pluck('geoterm'))
            ->merge($notFoundGeoterms)
            ->unique()
            ->values()
            ->toArray();
    }
}