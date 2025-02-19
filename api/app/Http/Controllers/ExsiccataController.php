<?php

namespace App\Http\Controllers;

use App\Models\Exsiccata;
use App\Models\ExsiccataNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExsiccataController extends Controller {

    /**
     * Exsiccata controller instance.
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
     *	 tags={""},
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
     *		 description="Returns list of inventories registered within system",
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

        $fullCnt = Exsiccata::count();
        $result = Exsiccata::skip($offset)->take($limit)->get();

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
	 *	 path="/api/v2/exsiccata/{identifier}/number",
	 *	 operationId="/api/v2/exsiccata/identifier/number",
	 *	 tags={""},
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
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Identifier (ometid (PK) or recordID) associated with target exsiccata title",
	 *		 required=true,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns all exsiccata numbers associated with a single exsiccati title corresponding to matching identifier",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Exsiccata identifier is required.",
	 *	 ),
	 * )
	 */
    public function showOneExsiccataNumbers($identifier, Request $request){
        $this->validate($request, [
			'identifier' => 'required',
			'limit' => 'integer',
			'offset' => 'integer'
		]);
		$limit = $request->input('limit', 100);
		$offset = $request->input('offset', 0);

        $exsiccataQuery = Exsiccata::query();

		if(is_numeric($identifier)){

            // $exsiccataQuery = Exsiccata::find($identifier);
            $exsiccataQuery->where('ometid', $identifier);
        } else{
            $exsiccataQuery->where('recordID', $identifier);
            // $exsiccataQuery = Exsiccata::where('recordID',$identifier)->first();
        }

        $exsiccata = $exsiccataQuery->first();

		if(!$exsiccata){
            return response()->json(["status"=>false, "error"=>"Unable to locate exsiccata based on identifier"], 404);
            // $exsiccataQuery = ["status"=>false,"error"=>"Unable to locate exsiccata based on identifier"];
        }

        $numberQuery = ExsiccataNumber::where('omenid', $exsiccata->ometid)->select('omenid', 'exsnumber', 'notes', 'initialtimestamp')->skip($offset)->take($limit);

		$fullCnt = $numberQuery->count();
		$result = $numberQuery->get();

		$eor = ($offset + $limit) >= $fullCnt; // @TODO why
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
