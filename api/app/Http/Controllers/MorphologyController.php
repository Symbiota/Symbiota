<?php
namespace App\Http\Controllers;

use App\Models\MorphologyCharacter;
use App\Models\PortalIndex;
use Illuminate\Http\Request;

class MorphologyController extends Controller{
	/**
	 * Taxon Morphology controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	public function showAllCharacters(Request $request){
		$this->validate($request, [
			'limit' => 'integer',
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$fullCnt = PortalIndex::count();
		$result = PortalIndex::skip($offset)->take($limit)->get();

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

	public function showOneCharacter($id, Request $request){
		$portalObj = null;
		if(is_numeric($id)) $portalObj = PortalIndex::find($id);
		else $portalObj = PortalIndex::where('guid',$id)->first();
		if(!$portalObj->count()) $portalObj = ["status"=>false,"error"=>"Unable to locate installation based on identifier"];
		return response()->json($portalObj);
	}

}