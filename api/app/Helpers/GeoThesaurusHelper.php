<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class GeoThesaurusHelper
{
    public static function getGeoterms($geoTerms)
    {
        $entries = DB::table('geographicthesaurus')
        ->whereIn('geoterm', $geoTerms)
        ->get();

    $numcodes = $entries->pluck('numcode')->unique()->filter()->values();
    if ($numcodes->isEmpty()) {
        return $geoTerms;
    }

    $allGeoterms = DB::table('geographicthesaurus')
        ->whereIn('numcode', $numcodes)
        ->pluck('geoterm')
        ->unique()
        ->toArray();

    return $allGeoterms;
    }
}