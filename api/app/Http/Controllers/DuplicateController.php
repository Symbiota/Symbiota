<?php
namespace App\Http\Controllers;
include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuplicateController extends Controller{
	/**
	 * Duplicate controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	public function showDuplicateSpecimens(Request $request){
		$this->validate($request, [
			'limit' => ['integer', 'max:300'],
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$conditions = array();
		if($request->has('collector')) $conditions['collector'] = $request->collector;
		if($request->has('number')) $conditions['number'] = $request->number;
		if($request->has('eventDate')) $conditions['eventDate'] = $request->eventDate;
		if($request->has('exsiccatiIdentifier')) $conditions['exsiccatiIdentifier'] = $request->exsiccatiIdentifier;
		if($request->has('exsiccatiNumber')) $conditions['exsiccatiNumber'] = $request->exsiccatiNumber;



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


}