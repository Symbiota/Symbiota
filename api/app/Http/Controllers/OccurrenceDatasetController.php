<?php

namespace App\Http\Controllers;

use App\Models\OccurrenceDataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class OccurrenceDatasetController extends Controller{
	/**
	 * Occurrence Dataset controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/dataset",
	 *	 operationId="/api/v2/occurrence/dataset",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="nameSearchTerm",
	 *		 in="query",
	 *		 description="Wildcard search term for dataset name field",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Controls the number of results per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=1000)
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
	 *		 description="Returns list of occurrence datasets",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllDatasets(Request $request){
		$this->validate($request, [
			'limit' => ['integer', 'max:1000'],
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',1000);
		$offset = $request->input('offset',0);

		$datasetQuery = OccurrenceDataset::query();
		//$datasetQuery->join('omoccurdatasetlink', 'omoccurdatasets.datasetID', '=', 'omoccurdatasetlink.datasetID')->distinct();
		if($request->has('nameSearchTerm')){
			$datasetQuery->where('name', 'LIKE', '%' . $request->nameSearchTerm . '%');
		}

		$fullCnt = (clone $datasetQuery)->count();
		$result = $datasetQuery->offset($offset)->limit($limit)->get();

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
	 *	 path="/api/v2/occurrence/dataset/{identifier}",
	 *	 operationId="/api/v2/occurrence/dataset/identifier",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Dataset ID or GUID (datasetIdentifier) associated with target collection",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns occurrence dataset",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Dataset identifier is required.",
	 *	 ),
	 * )
	 */

	public function showOneDataset($id){
		$query = null;
		if(is_numeric($id)) {
			$query = OccurrenceDataset::findOrFail($id);
		}
		else{
			$query = OccurrenceDataset::where('datasetIdentifier', $id)->get();
		}

		if(!$query || !$query->count()){
			$query = ['status' => false, 'error' => 'Unable to locate collection based on identifier', 404];
		}

		$count = DB::table('omoccurdatasetlink')->where('datasetID', $query->datasetID)->count();
		$query->occurrenceCount = $count;

		return response()->json($query);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/dataset/{identifier}/occurrence",
	 *	 operationId="/api/v2/occurrence/dataset/identifier/occurrence",
	 *	 tags={"Occurrence"},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Dataset ID or GUID (datasetIdentifier) associated with target collection",
	 *		 required=true,
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
	 *		 description="Returns occurrence dataset",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Dataset identifier is required.",
	 *	 ),
	 * )
	 */
	public function showDatasetOccurrences($id, Request $request){
		$this->validate($request, [
			'limit' => ['integer', 'max:1000'],
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$dataset = null;
		if(is_numeric($id)) {
			$dataset = OccurrenceDataset::findOrFail($id);
		}
		else{
			$dataset = OccurrenceDataset::where('datasetIdentifier', $id)->get();
		}
		$occurrenceQuery = $dataset->occurrence();

		$fullCnt = (clone $occurrenceQuery)->count();
		$result = $occurrenceQuery->offset($offset)->limit($limit)->get();

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
}