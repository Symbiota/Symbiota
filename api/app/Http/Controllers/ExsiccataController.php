<?php

namespace App\Http\Controllers;

use App\Models\Exsiccata;
use App\Models\ExsiccataNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExsiccataController extends Controller {

	/**
	 * Exsiccata controller ins	e.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/exsiccata",
	 *	 operationId="/api/v2/exsiccata",
	 *	 tags={"Exsiccata"},
	 *	 @OA\Parameter(
	 *		 name="title",
	 *		 in="query",
	 *		 description="Term within exsiccata title",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="sourceIdentifier",
	 *		 in="query",
	 *		 description="Identifier associated with data source of the exsiccata title record",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Controls the number of results in the page.",
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
	 *		 description="Returns list of exsiccata titles registered within system",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllExsiccata(Request $request) {
		$this->validate($request, [
			'limit' => 'integer',
			'offset' => 'integer'
		]);
		$limit = $request->input('limit', 100);
		$offset = $request->input('offset', 0);

		$exsiccataQuery = Exsiccata::query();

		if ($request->has('title')) {
			$exsiccataQuery->where('title', 'LIKE', '%' . $request->title . '%');
		}
		if ($request->has('sourceIdentifier')) {
			$sourceIdentifier = $request->sourceIdentifier;
			$exsiccataQuery->where(function ($query) use ($sourceIdentifier) {
				$query->where('sourceIdentifier', $sourceIdentifier)
					->orWhere('sourceIdentifier', 'LIKE', '%=' . $sourceIdentifier);
			});
		}

		$fullCnt = $exsiccataQuery->count();
		$result = $exsiccataQuery->skip($offset)->take($limit)->get();

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
	 *	 @OA\Get(
	 *	   path="/api/v2/exsiccata/{identifier}",
	 *	   operationId="/api/v2/exsiccata/{identifier}",
	 *	   tags={"Exsiccata"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 required=true,
	 *		 description="Exsiccata title identifier (ometid) or recordID GUID",
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns exsiccata record with matching identifier",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	  @OA\Response(
	 *		response="400",
	 *		description="Error: Bad request. Valid Exsiccata identifier required",
	 *	  ),
	 *	 @OA\Response(
	 *		 response="404",
	 *		 description="Record not found"
	 *	 )
	 * )
	 */
	public function showOneExsiccata($identifier) {
		$record = null;
		if (is_numeric($identifier)){
			$record = Exsiccata::find($identifier);
		}
		else {
			$record = Exsiccata::where('recordID', $identifier)->first();
		}
		if (!$record) {
			return response()->json(['status' => false, 'error' => 'Record not found'], 404);
		}

		return response()->json($record);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/exsiccata/{identifier}/number",
	 *	 operationId="/api/v2/exsiccata/identifier/number",
	 *	 tags={"Exsiccata"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Exsiccata title identifier (ometid) or recordID GUID associated with target exsiccata title",
	 *		 required=true,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="exsiccataNumber",
	 *		 in="query",
	 *		 description="Verbatim exsiccata number",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="exsiccataNumberMax",
	 *		 in="query",
	 *		 description="The upper range of an exsiccata number. If exsiccataNumber is also supplied, output will be the range between the two numbers.",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *   @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Controls the number of results in the page.",
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
	 *		 description="Returns all exsiccata numbers associated with a single title corresponding to matching identifier",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Exsiccata identifier is required.",
	 *	 ),
	 *   @OA\Response(
	 *	  response="404",
	 *	  description="Record not found"
	 *   )
	 * )
	 */
	public function showExsiccataNumbers($identifier, Request $request) {
		$this->validate($request, [
			'limit' => 'integer',
			'offset' => 'integer'
		]);
		$limit = $request->input('limit', 100);
		$offset = $request->input('offset', 0);

		$titleRecord = null;
		if (is_numeric($identifier)){
			$titleRecord = Exsiccata::find($identifier);
		}
		else {
			$titleRecord = Exsiccata::where('recordID', $identifier)->first();
		}
		if (!$titleRecord) {
			return response()->json(["status" => false, "error" => "Unable to locate exsiccata based on identifier"], 404);
		}

		$numberQuery = ExsiccataNumber::where('ometid', $titleRecord->ometid);
		if($request->has('exsiccataNumber')){
			if($request->has('exsiccataNumberMax')){
				$numberQuery->whereRaw('exsNumber > ' . $request->exsiccataNumber);
			}
			else{
				$numberQuery->where('exsNumber', $request->exsiccataNumber);
			}
		}
		if($request->has('exsiccataNumberMax')){
			$numberQuery->whereRaw('exsNumber < ' . $request->exsiccataNumberMax);
		}

		$fullCnt = $numberQuery->count();
		$result = $numberQuery->orderByRaw('exsnumber + 0')->skip($offset)->take($limit)->get();

		$retObj = [
			'offset' => (int)$offset,
			'limit' => (int)$limit,
			'count' => $fullCnt,
			'results' => $result,
		];
		return response()->json($retObj);
	}

	 /**
	 * @OA\Get(
	 *	 path="/api/v2/exsiccata/{identifier}/number/{numberIdentifier}",
	 *	 operationId="/api/v2/exsiccata/identifier/number/numberIdentifier",
	 *	 tags={"Exsiccata"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 required=true,
	 *		 description="Exsiccata title identifier (ometid) or recordID GUID",
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="numberIdentifier",
	 *		 in="path",
	 *		 required=true,
	 *		 description="Exsiccata number identifier (omenid)",
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns single exsiccata number along with list of associated occurrences",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Valid Exsiccata identifier and Number identifier required"
	 *	 ),
	 *	 @OA\Response(
	 *		 response="404",
	 *		 description="Record not found"
	 *	 )
	 * )
	 */
	public function showOccurrencesByExsiccataNumber($identifier, $numberIdentifier){
		//Check to make sure that title exists, and report otherwise
		//Not really needed becauses omenid are unique, but adding as a second check
		$titleRecord = null;
		if (is_numeric($identifier)){
			$titleRecord = Exsiccata::find($identifier);
		}
		else {
			$titleRecord = Exsiccata::where('recordID', $identifier)->first();
		}
		if (!$titleRecord) {
			return response()->json(["status" => false, "error" => "Unable to locate exsiccata based on identifier"], 404);
		}

		//Get list of occurrence associated with exsiccata number
		$occurrenceQuery = ExsiccataNumber::where('ometid', $titleRecord->ometid)->where('omenid', $numberIdentifier);
		$occurrenceQuery->with('occurrences');
		$results = $occurrenceQuery->get();

		if ($results->isEmpty()) {
			return response()->json(['error' => 'Unable to locate occurrences based on exsiccata number'], 404);
		}

		return response()->json($results);
	}
}
