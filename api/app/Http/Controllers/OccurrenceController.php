<?php

namespace App\Http\Controllers;

use App\Occurrence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OccurrenceController extends Controller{
	/**
	 * Occurrence controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence",
	 *	 operationId="/api/v2/occurrence",
	 *	 tags={""},
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
	 *		 description="occurrenceID",
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
	 *		 description="State, Province, or second level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="county",
	 *		 in="query",
	 *		 description="County, parish, or third level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Pagination parameter: maximum number of records per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=100)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="offset",
	 *		 in="query",
	 *		 description="Pagination parameter: page number",
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
	public function showAllOccurrences(Request $request){
		$this->validate($request, [
			'limit' => ['integer', 'max:300'],
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$conditions = [];
		if($request->has('catalogNumber')) $conditions[] = ['catalogNumber',$request->catalogNumber];
		if($request->has('occurrenceID')) $conditions[] = ['occurrenceID',$request->occurrenceID];
		if($request->has('country')) $conditions[] = ['country',$request->country];
		if($request->has('stateProvince')) $conditions[] = ['stateProvince',$request->stateProvince];
		if($request->has('county')) $conditions[] = ['county','LIKE',$request->county.'%'];

		$fullCnt = Occurrence::where($conditions)->count();
		$result = Occurrence::where($conditions)->skip($offset)->take($limit)->get();

		$eor = false;
		$retObj = [
			"offset" => (int)$offset,
			"limit" => (int)$limit,
			"endOfRecords" => $eor,
			"count" => $fullCnt,
			"results" => $result
		];
		return response()->json($retObj);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}",
	 *	 operationId="/api/v2/occurrence/identifier",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="includeMedia",
	 *		 in="query",
	 *		 description="Whether to include media within output",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns occurrence data",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneOccurrence($id, Request $request){
		$this->validate($request, [
			'includeMedia' => 'integer',
			'includeIdentHistory' => 'integer'
		]);
		if(!is_numeric($id)){
			$occid = Occurrence::where('occurrenceID',$id)->value('occid');
			if(!$occid) $occid = DB::table('guidoccurrences')->where('guid',$id)->value('occid');
			if(is_numeric($occid)) $id = $occid;
		}
		$occurrence = Occurrence::find($id);
		if($occurrence) $occurrence->recordID = DB::table('guidoccurrences')->where('occid',$id)->value('guid');
		if($request->input('includeMedia')) $occurrence->media = Occurrence::find($id)->media;
		if($request->input('includeIdentHistory ')) $occurrence->identification = Occurrence::find($id)->identification;
		return response()->json($occurrence);
	}

	public function create(Request $request){
		//$occurrence = Occurrence::create($request->all());
		//return response()->json($occurrence, 201);
	}

	public function update($id, Request $request){
		//$occurrence = Occurrence::findOrFail($id);
		//$occurrence->update($request->all());
		//return response()->json($occurrence, 200);
	}

	public function delete($id){
		//Occurrence::findOrFail($id)->delete();
		//return response('Occurrence Deleted Successfully', 200);
	}
}