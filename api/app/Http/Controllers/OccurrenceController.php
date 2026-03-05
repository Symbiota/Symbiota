<?php

namespace App\Http\Controllers;

use App\Models\Occurrence;
use App\Models\PortalIndex;
use App\Models\PortalOccurrence;
use App\Helpers\OccurrenceHelper;
use App\Helpers\GeoThesaurusHelper;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class OccurrenceController extends Controller {

	/**
	 * Occurrence controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence",
	 *	 operationId="/api/v2/occurrence",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="collid",
	 *		 in="query",
	 *		 description="collid - collection identifier (primary key)",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="catalogNumber",
	 *		 in="query",
	 *		 description="catalogNumber",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="occurrenceID",
	 *		 in="query",
	 *		 description="occurrenceID global unique identifier (GUID)",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="additionalIdentifier",
	 *		 in="query",
	 *		 description="Additional identifier assigned to occurrence, previousy referenced as dwc:otherCatalogNumbers",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="family",
	 *		 in="query",
	 *		 description="family",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="scientificName",
	 *		 in="query",
	 *		 description="Scientific Name - binomen only without authorship",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="tid",
	 *		 in="query",
	 *		 description="Primary identifier of scientific name (aka tid)",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="parentTid",
	 *		 in="query",
	 *		 description="Primary identifier of parent taxon (aka tid)",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordedBy",
	 *		 in="query",
	 *		 description="Collector/observer of occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordedByLastName",
	 *		 in="query",
	 *		 description="Last name of collector/observer of occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordNumber",
	 *		 in="query",
	 *		 description="Personal number of the collector or observer of the occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="eventDateMin",
	 *		 in="query",
	 *		 description="Date (YYYY-MM-DD) when occurrence was collected or observed, or earliest date if eventDateMax is supplied to produce a range",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="eventDateMax",
	 *		 in="query",
	 *		 description="Last date (YYYY-MM-DD) range that the occurrence was collected or observed.",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="country",
	 *		 in="query",
	 *		 description="country",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="stateProvince",
	 *		 in="query",
	 *		 description="State / Province, or second level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="county",
	 *		 in="query",
	 *		 description="County / parish, or third level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="decimalLatitudeMin",
	 *		 in="query",
	 *		 description="Minimum latitude as a decimal (e.g lower limit of bounding box)",
	 *		 required=false,
	 *		 @OA\Schema(type="number")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="decimalLatitudeMax",
	 *		 in="query",
	 *		 description="Maximum latitude as a decimal (e.g upper limit of bounding box)",
	 *		 required=false,
	 *		 @OA\Schema(type="number")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="decimalLongitudeMin",
	 *		 in="query",
	 *		 description="Minimum longitude as a decimal (e.g lower limit of bounding box)",
	 *		 required=false,
	 *		 @OA\Schema(type="number")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="decimalLongitudeMax",
	 *		 in="query",
	 *		 description="Maximum longitude as a decimal (e.g upper limit of bounding box)",
	 *		 required=false,
	 *		 @OA\Schema(type="number")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="elevationMin",
	 *		 in="query",
	 *		 description="Minimum elevation in meters to nearest integer",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="elevationMax",
	 *		 in="query",
	 *		 description="Maximum elevation in meters to nearest integer",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="depthMin",
	 *		 in="query",
	 *		 description="Minimum depth in meters to nearest integer",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="depthMax",
	 *		 in="query",
	 *		 description="Maximum depth in meters to nearest integer",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="datasetID",
	 *		 in="query",
	 *		 description="Dataset identifier associated with groups of occurrences. See [https://docs.symbiota.org/User_Guide/Datasets/](https://docs.symbiota.org/User_Guide/Datasets/)",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="dateLastModifiedMin",
	 *		 in="query",
	 *		 description="Single date that the occurrence was last modified, or minimum date when dateLastModifiedMax supplied to obtain a range (YYYY-MM-DD)",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="dateLastModifiedMax",
	 *		 in="query",
	 *		 description="Maximum date range that the occurrence was last modified (YYYY-MM-DD) ",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Controls the number of results per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=100)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="offset",
	 *		 in="query",
	 *		 description="Determines the starting point for the search results. A limit of 100 and offset of 200, will display 100 records starting the 200th record.",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=0)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of occurrences",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllOccurrences(Request $request) {
		$this->validate($request, [
			'collid' => 'integer',
			'tid' => 'integer',
			'parentTid' => 'integer',
			'eventDateMin' => 'date_format:Y-m-d',
			'eventDateMax' => 'date_format:Y-m-d',
			'decimalLatitudeMin' => ['numeric', 'between:-90,90'],
			'decimalLatitudeMax' => ['numeric', 'between:-90,90'],
			'decimalLongitudeMin' => ['numeric', 'between:-180,180'],
			'decimalLongitudeMax' => ['numeric', 'between:-180,180'],
			'elevationMin' => ['integer', 'between:0,8000'],
			'elevationMax' => ['integer', 'between:0,8000'],
			'depthMin' => ['integer', 'between:0,12000'],
			'depthMax' => ['integer', 'between:0,12000'],
			'dateLastModifiedMin' => 'date_format:Y-m-d',
			'dateLastModifiedMax' => 'date_format:Y-m-d',
			'limit' => ['integer', 'max:300'],
			'offset' => 'integer'
		]);
		//custom validation
		if($request->has('eventDateMin') && $request->has('eventDateMax') && $request->eventDateMin > $request->eventDateMax){
			return response()->json(['status' => false, 'error' => 'eventDateMin greater than eventDateMax'], 422);
		}
		if($request->has('decimalLatitudeMin') && $request->has('decimalLatitudeMax') && $request->decimalLatitudeMin > $request->decimalLatitudeMax){
			return response()->json(['status' => false, 'error' => 'decimalLatitudeMin greater than decimalLatitudeMax'], 422);
		}
		if($request->has('decimalLongitudeMin') && $request->has('decimalLongitudeMax') && $request->decimalLongitudeMin > $request->decimalLongitudeMax){
			return response()->json(['status' => false, 'error' => 'decimalLongitudeMin greater than decimalLongitudeMax'], 422);
		}
		if($request->has('elevationMin') && $request->has('elevationMax') && $request->elevationMin > $request->elevationMax){
			return response()->json(['status' => false, 'error' => 'elevationMin greater than elevationMax'], 422);
		}
		if($request->has('depthMin') && $request->has('depthMax') && $request->depthMin > $request->depthMax){
			return response()->json(['status' => false, 'error' => 'depthMin greater than depthMax'], 422);
		}
		if($request->has('dateLastModifiedMin') && $request->has('dateLastModifiedMax') && $request->dateLastModifiedMin > $request->dateLastModifiedMax){
			return response()->json(['status' => false, 'error' => 'dateLastModifiedMin greater than dateLastModifiedMax'], 422);
		}

		$limit = $request->input('limit', 100);
		$offset = $request->input('offset', 0);

		$occurrenceModel = Occurrence::query();
		$occurrenceModel->with('identifier');
		$occurrenceModel->where('recordSecurity', 0);

		if($request->has('collid')){
			$occurrenceModel->where('collid', $request->collid);
		}
		if ($request->has('catalogNumber')) {
			$occurrenceModel->where('catalogNumber', $request->catalogNumber);
		}
		if ($request->has('occurrenceID')) {
			$occurrenceID = $request->occurrenceID;
			$occurrenceModel->where(function ($query) use ($occurrenceID) {
				$query->where('occurrenceID', $occurrenceID)
					->orWhere('recordID', $occurrenceID);
			});
		}
		if ($request->has('additionalIdentifier')) {
			$identifier = $request->additionalIdentifier;
			$occurrenceModel->whereHas('identifier', function ($query) use ($identifier) {
				$query->where('identifierValue', $identifier);
			});
		}

		//Taxonomy
		if ($request->has('family')) {
			$occurrenceModel->where('family', $request->family);
		}
		if ($request->has('scientificName')) {
			if(strpos($request->scientificName, '%')){
				$occurrenceModel->where('sciname', 'LIKE', $request->scientificName);
			}
			else{
				$occurrenceModel->where('sciname', $request->scientificName);
			}
		}
		if ($request->has('tid')) {
			$occurrenceModel->where('tidInterpreted', $request->tid);
		}
		if ($request->has('parentTid')) {
			$occurrenceModel->join('taxaenumtree as e', 'tidinterpreted', '=', 'e.tid');
			$occurrenceModel->where('e.taxAuthID', 1)->where('e.parentTid', 127299);
		}
		//Collector units
		if ($request->has('recordedBy')) {
			// $occurrenceModel->where('recordedBy', $request->recordedBy);
			// $occurrenceModel->whereRaw("MATCH(recordedBy) AGAINST (? IN BOOLEAN MODE)", [Helper::readyPhraseForBooleanModeFulltextSearch($request->recordedBy)]);
			$occurrenceModel->whereRaw("MATCH(recordedBy) AGAINST (? IN NATURAL LANGUAGE MODE)", [$request->recordedBy]);
		}
		if ($request->has('recordedByLastName')) {
			// $occurrenceModel->where('o.recordedBy', 'LIKE', '%' . $request->recordedByLastName . '%');
			// $occurrenceModel->whereRaw("MATCH(recordedBy) AGAINST (? IN BOOLEAN MODE)", [Helper::readyPhraseForBooleanModeFulltextSearch($request->recordedByLastName)]);
			$occurrenceModel->whereRaw("MATCH(recordedBy) AGAINST (? IN NATURAL LANGUAGE MODE)", [$request->recordedByLastName]);
		}
		if ($request->has('recordNumber')) {
			$occurrenceModel->where('recordNumber', $request->recordNumber);
		}
		if ($request->has('eventDateMin')) {
			if ($request->has('eventDateMax')){
				$occurrenceModel->where('eventDate', '>=', $request->eventDateMin);
			}
			else{
				$occurrenceModel->where('eventDate', $request->eventDateMin);
			}
		}
		if ($request->has('eventDateMax')) {
			$occurrenceModel->where('eventDate', '<=', $request->eventDateMax);
		}
		//Locality place names
		if($request->has('country')){
			$geoCountries = GeoThesaurusHelper::getGeoterms($request->country);
			if (!empty($geoCountries['country']) || !empty($geoCountries['countryCode'])) {
				$occurrenceModel->where(function ($query) use ($geoCountries) {
					if (!empty($geoCountries['countryCode'])) {
						$query->whereIn('countryCode', $geoCountries['countryCode']);
					}
					if (!empty($geoCountries['country'])) {
						$query->whereIn('country', $geoCountries['country'], "or");
					}
				});
			}
		}
		if($request->has('stateProvince')){
			$occurrenceModel->where('stateProvince', $request->stateProvince);
		}
		if($request->has('county')){
			$occurrenceModel->where('county', 'LIKE', $request->county . '%');
		}
		if ($request->has('decimalLatitudeMin')) {
			$occurrenceModel->where('decimalLatitude', '>=', $request->decimalLatitudeMin);
		}
		if ($request->has('decimalLatitudeMax')) {
			$occurrenceModel->where('decimalLatitude', '<=', $request->decimalLatitudeMax);
		}
		if ($request->has('decimalLongitudeMin')) {
			$occurrenceModel->where('decimalLongitude', '>=', $request->decimalLongitudeMin);
		}
		if ($request->has('decimalLongitudeMax')) {
			$occurrenceModel->where('decimalLongitude', '<=', $request->decimalLongitudeMax);
		}
		if ($request->has('elevationMin')) {
			if($request->has('elevationMax')){
				$occurrenceModel->where('minimumElevationInMeters', '>=', $request->elevationMin);
			}
			else{
				$occurrenceModel->where('minimumElevationInMeters', $request->elevationMin);
			}
		}
		if ($request->has('elevationMax')) {
			$occurrenceModel->whereRaw('IFNULL(maximumElevationInMeters,minimumElevationInMeters) <= ?', $request->elevationMax);
		}
		if ($request->has('depthMin')) {
			if($request->has('depthMax')){
				$occurrenceModel->where('minimumDepthInMeters', '>=', $request->depthMin);
			}
			else{
				$occurrenceModel->where('minimumDepthInMeters', $request->depthMin);
			}
		}
		if ($request->has('depthMax')) {
			$occurrenceModel->whereRaw('IFNULL(maximumDepthInMeters,minimumDepthInMeters) <= ?', $request->depthMax);
		}
		if ($request->has('datasetID')) {
			$datasetID = $request->datasetID;
			$occurrenceModel->whereHas('dataset', function ($query) use ($datasetID) {
				$query->where('omoccurdatasetlink.datasetID', $datasetID);
			});
		}
		if ($request->has('dateLastModifiedMin')) {
			if ($request->has('dateLastModifiedMax')){
				$occurrenceModel->where('dateLastModified', '>', $request->dateLastModifiedMin);
			}
			else{
				$occurrenceModel->whereRaw('dateLastModified > ? AND dateLastModified < ? + INTERVAL 1 DAY', $request->dateLastModifiedMin, $request->dateLastModifiedMin);
			}
		}
		if ($request->has('dateLastModifiedMax')) {
			$occurrenceModel->whereRaw('dateLastModified < ? + INTERVAL 1 DAY', $request->dateLastModifiedMax);
		}

		$fullCnt = $occurrenceModel->count();
		$result = $occurrenceModel->skip($offset)->take($limit)->get();

		$eor = false;
		$retObj = [
			'offset' => (int)$offset,
			'limit' => (int)$limit,
			'endOfRecords' => $eor,
			'count' => $fullCnt,
			'results' => $result
		];
		return response()->json($retObj);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}",
	 *	 operationId="/api/v2/occurrence/identifier",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) or recordID associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="includeMedia",
	 *		 in="query",
	 *		 description="Whether (1) or not (0) to include media within output",
	 *		 required=false,
	 *		 @OA\Schema(
	 *			type="integer",
	 *			default="0",
	 *			enum={0, 1}
	 *		)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="includeIdentifications",
	 *		 in="query",
	 *		 description="Whether (1) or not (0) to include full Identification (Determination) History within output",
	 *		 required=false,
	 *		 @OA\Schema(
	 *			type="integer",
	 *			default="0",
	 *			enum={0, 1}
	 *		)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="includeDatasets",
	 *		 in="query",
	 *		 description="Whether (1) or not (0) to include datasets within output",
	 *		 required=false,
	 *		 @OA\Schema(
	 *			type="integer",
	 *			default="0",
	 *			enum={0, 1}
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns single occurrence record",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneOccurrence($id, Request $request) {
		$this->validate($request, [
			'includeMedia' => 'integer',
			'includeIdentifications' => 'integer',
			'includeDatasets' => 'integer'
		]);
		$occurrenceModel = null;
		if(is_numeric($id)){
			$occurrenceModel = Occurrence::where('occid', $id);
		}
		else{
			$occurrenceModel = Occurrence::where(function ($query) use ($id) {
				$query->where('occurrenceID', $id)->orWhere('recordID', $id);
			});
		}
		$occurrenceModel->with('identifier');
		if ($request->input('includeMedia')) $occurrenceModel->with('media');
		if ($request->input('includeIdentifications')) $occurrenceModel->with('identification');
		if ($request->input('includeDatasets')) $occurrenceModel->with('dataset');
		$occurrence = $occurrenceModel->get();
		if ($occurrence->isEmpty()) {
			return response()->json(['error' => 'Occurrence not found'], 404);
		}
		return response()->json($occurrence);
	}


	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}/identification",
	 *	 operationId="/api/v2/occurrence/identifier/identification",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) or recordID associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns identification records associated with a given occurrence record",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneOccurrenceIdentifications($id, Request $request) {
		$occid = $this->getOccidFromOtherIds($id)->occid ?? null;
		if (!$occid) return response()->json(['error' => 'Occurrence not found with that ID'], 404);
		$occurrence = Occurrence::find($occid);
		if ($occurrence->isEmpty()) {
			return response()->json(['error' => 'Occurrence not found'], 404);
		}
		$identification = null;
		$identification = $occurrence->identification;
		if ($identification->isEmpty()) {
			return response()->json(['error' => 'Occurrence found, but no identification found'], 404);
		}
		return response()->json($identification);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}/media",
	 *	 operationId="/api/v2/occurrence/identifier/media",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) or recordID associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns media records associated with a given occurrence record",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneOccurrenceMedia($id, Request $request) {
		$occid = $this->getOccidFromOtherIds($id)->occid ?? null;
		if (!$occid) return response()->json(['error' => 'Occurrence not found with that ID'], 404);
		$occurrence = Occurrence::find($occid);
		if ($occurrence->isEmpty()) {
			return response()->json(['error' => 'Occurrence not found'], 404);
		}
		$media = null;
		if ($occurrence) {
			$media=$occurrence->media;
		}
		if ($media->isEmpty()) {
			return response()->json(['error' => 'Occurrence found, but no media found'], 404);
		}
		return response()->json($media);
	}

	/**
	 * @OA\Post(
	 *	 path="/api/v2/occurrence",
	 *	 operationId="/api/v2/occurrence",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="apiToken",
	 *		 in="query",
	 *		 description="API security token to authenticate POST action",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="collid",
	 *		in="query",
	 *		description="primary key of target collection dataset",
	 *		required=true,
	 *		@OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\RequestBody(
	 *		required=true,
	 *		description="Occurrence object to be inserted",
	 *		@OA\MediaType(
	 *			mediaType="application/json",
	 *			@OA\Schema(
	 *				@OA\Property(
	 *					property="basisOfRecord",
	 *					type="string",
	 *					description="The specific nature of the data record (PreservedSpecimen, fossilSpecimen, HumanObservation, MachineObservation, etc)",
	 *					maxLength=32
	 *				),
	 *				@OA\Property(
	 *					property="catalogNumber",
	 *					type="string",
	 *					description="Primary catalog number",
	 *					maxLength=32
	 *				),
	 *				@OA\Property(
	 *					property="sciname",
	 *					type="string",
	 *					description="Scientific name, without the author",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="scientificNameAuthorship",
	 *					type="string",
	 *					description="The authorship information of scientific name",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="family",
	 *					type="string",
	 *					description="Taxonomic family of the scientific name",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="recordedBy",
	 *					type="string",
	 *					description="Primary collector or observer",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="recordNumber",
	 *					type="string",
	 *					description="Identifier given at the time occurrence was recorded; typically the personal identifier of the primary collector or observer",
	 *					maxLength=45
	 *				),
	 *				@OA\Property(
	 *					property="associatedCollectors",
	 *					type="string",
	 *					description="Secondary collectors/observers",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="eventDate",
	 *					type="string",
	 *					description="Date the occurrence was collected or observed, or earliest date if a range was provided"
	 *				),
	 *				@OA\Property(
	 *					property="eventDate2",
	 *					type="string",
	 *					description="Last date the occurrence was collected or observed. Used when a date range is provided"
	 *				),
	 *				@OA\Property(
	 *					property="verbatimEventDate",
	 *					type="string",
	 *					description="Verbatim Event Date"
	 *				),
	 *				@OA\Property(
	 *					property="habitat",
	 *					type="string",
	 *					description="Habitat"
	 *				),
	 *				@OA\Property(
	 *					property="substrate",
	 *					type="string",
	 *					description="Substrate"
	 *				),
	 *				@OA\Property(
	 *					property="eventID",
	 *					type="string",
	 *					description="Event ID"
	 *				),
	 *				@OA\Property(
	 *					property="locationID",
	 *					type="string",
	 *					description="Location ID"
	 *				),
	 *				@OA\Property(
	 *					property="country",
	 *					type="string",
	 *					description="The name of the country or major administrative unit",
	 *					maxLength=64
	 *				),
	 *				@OA\Property(
	 *					property="stateProvince",
	 *					type="string",
	 *					description="The name of the next smaller administrative region than country (state, province, canton, department, region, etc.)",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="county",
	 *					type="string",
	 *					description="The full, unabbreviated name of the next smaller administrative region than stateProvince (county, shire, department, etc.",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="processingStatus",
	 *					type="string",
	 *					description="Processing status of the specimen record",
	 *					maxLength=45
	 *				),
	 *			),
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="201",
	 *		 description="Success: Returns JSON object of the of occurrence record that was created"
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	public function insert(Request $request) {
		if (!Helper::isValidJson($request->getContent())) {
			return response()->json(['error' => 'Invalid JSON format in request body'], 400);
		}
		if ($this->authenticate($request)) {
			$this->validate($request, [
				'collid' => 'required|integer'
			]);
			$collid = $request->input('collid');
			//Check to see if user has the necessary permission edit/add occurrences for target collection
			if (!$this->isAuthorizedSub($collid)) {
				return response()->json(['error' => 'Unauthorized to add new records to target collection (collid = ' . $collid . ')'], 401);
			}
			$inputArr = $request->all();
			$inputArr['recordID'] = (string) Str::uuid();
			$inputArr['dateEntered'] = date('Y-m-d H:i:s');

			try {
				$occurrence = Occurrence::create($inputArr);
			} catch (QueryException $e) {
				//TODO: need to improve error catching and reporting (e.g. due to bad collid, or other foreign key)
				return response()->json([
					'error' => 'Failed to insert record due to SQL error',
					//'details' => $e->getMessage()
				], 500);
			}
			return response()->json($occurrence, 201);
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	private $ignoredPatch = <<<TXT
	/**
	 * @OA\Patch(
	 *	 path="/api/v2/occurrence/{identifier}",
	 *	 operationId="/api/v2/occurrence/identifier",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="apiToken",
	 *		 in="query",
	 *		 description="API security token to authenticate PATCH action",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Primary key (occid), occurrenceID GUID, or record GUID (UUID) associated with target occurrence record",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\RequestBody(
	 *		required=true,
	 *		description="Occurrence object to be updated",
	 *		@OA\MediaType(
	 *			mediaType="application/json",
	 *			@OA\Schema(
	 *				@OA\Property(
	 *					property="basisOfRecord",
	 *					type="string",
	 *					description="The specific nature of the data record (PreservedSpecimen, fossilSpecimen, HumanObservation, MachineObservation, etc)",
	 *					maxLength=32
	 *				),
	 *				@OA\Property(
	 *					property="catalogNumber",
	 *					type="string",
	 *					description="Primary catalog number",
	 *					maxLength=32
	 *				),
	 *				@OA\Property(
	 *					property="sciname",
	 *					type="string",
	 *					description="Scientific name, without the author",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="scientificNameAuthorship",
	 *					type="string",
	 *					description="The authorship information of scientific name",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="family",
	 *					type="string",
	 *					description="Taxonomic family of the scientific name",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="recordedBy",
	 *					type="string",
	 *					description="Primary collector or observer",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="recordNumber",
	 *					type="string",
	 *					description="Identifier given at the time occurrence was recorded; typically the personal identifier of the primary collector or observer",
	 *					maxLength=45
	 *				),
	 *				@OA\Property(
	 *					property="associatedCollectors",
	 *					type="string",
	 *					description="Secondary collectors/observers",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="eventDate",
	 *					type="string",
	 *					description="Date the occurrence was collected or observed, or earliest date if a range was provided"
	 *				),
	 *				@OA\Property(
	 *					property="eventDate2",
	 *					type="string",
	 *					description="Last date the occurrence was collected or observed. Used when a date range is provided"
	 *				),
	 *				@OA\Property(
	 *					property="verbatimEventDate",
	 *					type="string",
	 *					description="Verbatim Event Date"
	 *				),
	 *				@OA\Property(
	 *					property="habitat",
	 *					type="string",
	 *					description="Habitat"
	 *				),
	 *				@OA\Property(
	 *					property="substrate",
	 *					type="string",
	 *					description="Substrate"
	 *				),
	 *				@OA\Property(
	 *					property="eventID",
	 *					type="string",
	 *					description="Event ID"
	 *				),
	 *				@OA\Property(
	 *					property="locationID",
	 *					type="string",
	 *					description="Location ID"
	 *				),
	 *				@OA\Property(
	 *					property="country",
	 *					type="string",
	 *					description="The name of the country or major administrative unit",
	 *					maxLength=64
	 *				),
	 *				@OA\Property(
	 *					property="stateProvince",
	 *					type="string",
	 *					description="The name of the next smaller administrative region than country (state, province, canton, department, region, etc.)",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="county",
	 *					type="string",
	 *					description="The full, unabbreviated name of the next smaller administrative region than stateProvince (county, shire, department, etc.",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="processingStatus",
	 *					type="string",
	 *					description="Processing status of the specimen record",
	 *					maxLength=45
	 *				),
	 *			),
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Success: Returns full JSON object of the of occurrence record that was edited"
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	TXT;
	public function update($id, Request $request) {
		if ($this->authenticate($request)) {
			$occurrence = Occurrence::find($id);
			if (!$occurrence) {
				return response()->json(['status' => 'failure', 'error' => 'Occurrence resource not found'], 400);
			}
			if($this->isAuthorizedSub($occurrence['collid'])) {
				//$occurrence->update($request->all());
				//return response()->json($occurrence, 200);
			}
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	private $ignoredDelete = <<<TXT
	/**
	 * @OA\Delete(
	 *	 path="/api/v2/occurrence/{identifier}",
	 *	 operationId="/api/v2/occurrence/identifier",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="apiToken",
	 *		 in="query",
	 *		 description="API security token to authenticate DELETE action",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Primary key (occid), occurrenceID GUID, or record GUID (UUID) associated with target occurrence record",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="204",
	 *		 description="Success: Record deleted successfully"
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	TXT;
	public function delete($id, Request $request) {
		if ($this->authenticate($request)) {
			$occurrence = Occurrence::find($id);
			if (!$occurrence) {
				return response()->json(['status' => 'failure', 'error' => 'Occurrence resource not found'], 400);
			}
			if ($this->isAuthorizedSub($occurrence['collid'])) {
				//$occurrence->delete(); // @TODO why is this disabled?
				//return response('Occurrence Deleted Successfully', 200);
			}
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	/**
	 * @OA\Post(
	 *	 path="/api/v2/occurrence/skeletal",
	 *	 operationId="/api/v2/occurrence/skeletal",
	 *	 description="If an existing record can be located within target collection based on matching the input identifier, empty (null) target fields will be updated with Skeletal Data.
	 *		If the target field contains data, it will remain unaltered.
	 *		If multiple records are returned matching the input identifier, data will be added only to the first record.
	 *		If an identifier is not provided or a matching record can not be found, a new Skeletal record will be created and primed with input data.
	 *		Note that catalogNumber or otherCatalogNumber must be provided to create a new skeletal record. If processingStatus is not defined, new skeletal records will be set as 'unprocessed'",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		name="apiToken",
	 *		in="query",
	 *		description="API security token to authenticate post action",
	 *		required=true,
	 *		@OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="collid",
	 *		in="query",
	 *		description="primary key of target collection dataset",
	 *		required=true,
	 *		@OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="identifier",
	 *		in="query",
	 *		description="catalog number, other identifiers, occurrenceID, or recordID GUID (UUID) used to locate target occurrence occurrence",
	 *		required=false,
	 *		@OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="identifierTarget",
	 *		in="query",
	 *		description="Target field for matching identifier: catalog number, other identifiers, GUID (occurrenceID or recordID), occid (primary key for occurrence). If identifier field is null, a new skeletal record will be created, given that a catalog number is provided.",
	 *		required=false,
	 *		@OA\Schema(
	 *			type="string",
	 *			default="CATALOGNUMBER",
	 *			enum={"CATALOGNUMBER", "IDENTIFIERS", "GUID", "OCCID", "NONE"}
	 *		)
	 *	 ),
	 *	 @OA\RequestBody(
	 *		required=true,
	 *		description="Occurrence data to be inserted",
	 *		@OA\MediaType(
	 *			mediaType="application/json",
	 *			@OA\Schema(
	 *				@OA\Property(
	 *					property="basisOfRecord",
	 *					type="string",
	 *					description="The specific nature of the data record (PreservedSpecimen, fossilSpecimen, HumanObservation, MachineObservation, etc)",
	 *					maxLength=32
	 *				),
	 *				@OA\Property(
	 *					property="catalogNumber",
	 *					type="string",
	 *					description="Primary catalog number",
	 *					maxLength=32
	 *				),
	 *				@OA\Property(
	 *					property="sciname",
	 *					type="string",
	 *					description="Scientific name, without the author",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="scientificNameAuthorship",
	 *					type="string",
	 *					description="The authorship information of scientific name",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="family",
	 *					type="string",
	 *					description="Taxonomic family of the scientific name",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="recordedBy",
	 *					type="string",
	 *					description="Primary collector or observer",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="recordNumber",
	 *					type="string",
	 *					description="Identifier given at the time occurrence was recorded; typically the personal identifier of the primary collector or observer",
	 *					maxLength=45
	 *				),
	 *				@OA\Property(
	 *					property="eventDate",
	 *					type="string",
	 *					description="Date the occurrence was collected or observed, or earliest date if a range was provided"
	 *				),
	 *				@OA\Property(
	 *					property="eventDate2",
	 *					type="string",
	 *					description="Last date the occurrence was collected or observed. Used when a date range is provided"
	 *				),
	 *				@OA\Property(
	 *					property="country",
	 *					type="string",
	 *					description="The name of the country or major administrative unit",
	 *					maxLength=64
	 *				),
	 *				@OA\Property(
	 *					property="stateProvince",
	 *					type="string",
	 *					description="The name of the next smaller administrative region than country (state, province, canton, department, region, etc.)",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="county",
	 *					type="string",
	 *					description="The full, unabbreviated name of the next smaller administrative region than stateProvince (county, shire, department, etc.",
	 *					maxLength=255
	 *				),
	 *				@OA\Property(
	 *					property="processingStatus",
	 *					type="string",
	 *					description="Processing status of the specimen record",
	 *					maxLength=45
	 *				),
	 *			),
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns full JSON object of the of media record that was edited"
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	public function skeletalImport(Request $request) {
		if (!Helper::isValidJson($request->getContent())) {
			return response()->json(['error' => 'Invalid JSON format in request body'], 400);
		}
		$this->validate($request, [
			'collid' => 'required|integer',
			'eventDate' => 'date',
			'eventDate2' => 'date',
			'identifierTarget' => 'in:CATALOGNUMBER,IDENTIFIERS,GUID,OCCID,NONE',
		]);
		if ($this->authenticate($request)) {
			$collid = $request->input('collid');
			$identifier = $request->input('identifier');
			$identifierTarget = $request->input('identifierTarget', 'CATALOGNUMBER');

			//Check to see if user has the necessary permission edit/add occurrences for target collection
			if ($this->isAuthorizedSub($collid)) {
				//Remove fields with empty values and non-approved target fields
				$updateArr = $request->all();
				$skeletalFieldsAllowed = array('catalogNumber', 'sciname', 'scientificNameAuthorship', 'family', 'recordedBy', 'recordNumber', 'eventDate', 'eventDate2', 'country', 'stateProvince', 'county', 'processingStatus');
				foreach ($updateArr as $fieldName => $fieldValue) {
					if (!$fieldValue) unset($updateArr[$fieldName]);
					elseif (!in_array($fieldName, $skeletalFieldsAllowed)) unset($updateArr[$fieldName]);
				}
				if (!$updateArr) {
					return response()->json(['error' => 'Bad request: input data empty or does not contains allowed fields'], 400);
				}

				//Get target record, if exists
				$targetOccurrence = null;
				if ($identifier) {
					$occurrenceModel = null;
					if ($identifierTarget == 'OCCID') {
						$occurrenceModel = Occurrence::where('occid', $identifier);
					} elseif ($identifierTarget == 'GUID') {
						$occurrenceModel = Occurrence::where('occurrenceID', $identifier)->orWhere('recordID', $identifier);
					} elseif ($identifierTarget == 'CATALOGNUMBER') {
						$occurrenceModel = Occurrence::where('catalogNumber', $identifier);
					} elseif ($identifierTarget == 'IDENTIFIERS') {
						$occurrenceModel = Occurrence::whereHas('identifier', function ($query) use ($identifier) {
							$query->where('identifierValue', $identifier);
						});
					}
					if ($occurrenceModel) {
						$targetOccurrence = $occurrenceModel->where('collid', $collid)->first();
					}
				}
				if ($targetOccurrence) {
					foreach ($updateArr as $fieldName => $fieldValue) {
						//Remove input if target field already contains data
						if ($targetOccurrence[$fieldName]) {
							unset($updateArr[$fieldName]);
						}
					}
					if (!empty($updateArr['eventDate'])) {
						$updateArr['eventDate'] = OccurrenceHelper::formatDate($updateArr['eventDate']);
					}
					if (!empty($updateArr['eventDate2'])) {
						$updateArr['eventDate2'] = OccurrenceHelper::formatDate($updateArr['eventDate2']);
					}
					$responseObj = ['number of fields affected' => count($updateArr), 'fields affected' => $updateArr];
					if ($updateArr) {
						$targetOccurrence->update($updateArr);
					}
					return response()->json($responseObj, 200);
				} else {
					//Record doesn't exist, thus create a new skeletal records, given that a catalog number exists
					$updateArr['collid'] = $collid;
					if (empty($updateArr['catalogNumber']) && empty($updateArr['otherCatalogNumbers'])) {
						return response()->json(['error' => 'Bad request: catalogNumber or otherCatalogNumbers required when creating a new record'], 400);
					}
					if (empty($updateArr['processingStatus'])) $updateArr['processingStatus'] = 'unprocessed';
					$updateArr['recordID'] = (string) Str::uuid();
					$updateArr['dateEntered'] = date('Y-m-d H:i:s');
					$newOccurrence = Occurrence::create($updateArr);
					return response()->json($newOccurrence, 201);
				}
			}
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}/reharvest",
	 *	 operationId="/api/v2/occurrence/identifier/reharvest",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Triggers a reharvest event of a snapshot record. If record is Live managed, request is ignored",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request: Occurrence identifier is required, API can only be triggered locally (at this time).",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="500",
	 *		 description="Error: unable to locate record",
	 *	 ),
	 * )
	 */
	public function oneOccurrenceReharvest($id, Request $request) {
		$responseArr = array();
		$host = '';
		if (!empty($GLOBALS['SERVER_HOST'])) $host = $GLOBALS['SERVER_HOST'];
		else $host = $_SERVER['SERVER_NAME'];
		if ($host && $request->getHttpHost() != $host) {
			$responseArr['status'] = 400;
			$responseArr['error'] = 'At this time, API call can only be triggered locally';
			return response()->json($responseArr);
		}
		$occid = $this->getOccidFromOtherIds($id)->occid ?? null;
		if (!$occid) return response()->json(['error' => 'Occurrence not found with that ID'], 404);
		$occurrence = Occurrence::find($occid);
		if (!$occurrence) {
			$responseArr['status'] = 500;
			$responseArr['error'] = 'Unable to locate occurrence record (occid = '.$occid.')';
			return response()->json($responseArr);
		}
		if ($occurrence->collection->managementType == 'Live Data') {
			$responseArr['status'] = 400;
			$responseArr['error'] = 'Updating a Live Managed record is not allowed ';
			return response()->json($responseArr);
		}
		$publications = $occurrence->portalPublications;
		foreach ($publications as $pub) {
			if ($pub->direction == 'import') {
				$sourcePortalID = $pub->portalID;
				$remoteOccid = $pub->pivot->remoteOccid;
				if ($sourcePortalID && $remoteOccid) {
					//Get remote occurrence data
					$urlRoot = PortalIndex::where('portalID', $sourcePortalID)->value('urlRoot');
					$url = $urlRoot.'/api/v2/occurrence/'.$remoteOccid;
					if($remoteOccurrence = Helper::getAPIResponse($url)){
						$remoteOccurrence['occid'] = $occid;
						$remoteCollid = $remoteOccurrence['collid'];
						$sourceDateLastModified = $remoteOccurrence['dateLastModified'];
						$clearFieldArr = array(
							'collid', 'dbpk', 'tidInterpreted', 'dynamicProperties', 'processingStatus', 'recordID',
							'modified', 'dateEntered' ,'dateLastModified', 'genus', 'specificEpithet', 'institutionCode', 'collectionCode',
							'scientificNameAuthorship', 'identifiedBy', 'dateIdentified', 'verbatimEventDate', 'countryCode', 'localitySecurity'
						);
						foreach($clearFieldArr as $field){
							unset($remoteOccurrence[$field]);
						}
						//Update local occurrence record with remote data
						if($occurrence->update($remoteOccurrence)){
							//print_r($occurrence); exit;
							$ts = date('Y-m-d H:i:s');
							$changeArr = $occurrence->getChanges();
							$responseArr['status'] = 200;
							$responseArr['numberFieldChanged'] = count($changeArr);
							if($changeArr) $responseArr['fieldsModified'] = $changeArr;
							$responseArr['sourceDateLastModified'] = $sourceDateLastModified;
							$responseArr['dateLastModified'] = $ts;
							$responseArr['sourceCollectionUrl'] = $urlRoot . '/collections/misc/collprofiles.php?collid=' . $remoteCollid;
							$responseArr['sourceRecordUrl'] = $urlRoot . '/collections/individual/index.php?occid=' . $remoteOccid;
							//Reset Portal Occurrence refreshDate
							$portalOccur = PortalOccurrence::where('occid', $occid)->where('pubid', $pub->pubid)->first();
							$portalOccur->refreshTimestamp = $ts;
							$portalOccur->save();
						}
						else{
							return response()->json(['error' => 'Unspecified Error'], 501);
						}
					}
					else {
						$responseArr['status'] = 400;
						$responseArr['error'] = 'Unable to locate remote/source occurrence (sourceID = '.$occid.')';
						$responseArr['sourceUrl'] = $url;
					}
				}
			}
		}
		return response()->json($responseArr);
	}

	//Helper functions
	private function isAuthorizedSub(int $collid): bool {
		if ($this->isAuthorized('SuperAdmin')) return true;
		elseif($collid){
			if($this->isAuthorized('CollAdmin', $collid)) return true;
			elseif($this->isAuthorized('CollEditor', $collid)) return true;
		}
		return false;
	}

	protected function getOccidFromOtherIds($id) {
		$decodedId = urldecode($id);
		$occid = null;
		if (is_numeric($decodedId)) {
			$occid = DB::table('omoccurrences as o')
				->select('o.occid')
				->where('occid', $decodedId)
				->first();
		} else {
			$occid = DB::table('omoccurrences as o')->select('o.occid')
				->where('recordID', (string)$decodedId)
				->orWhere('occurrenceID', (string)$decodedId)
				->first();
		}
		return $occid;
	}
}
