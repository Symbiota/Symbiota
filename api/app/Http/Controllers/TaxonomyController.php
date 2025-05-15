<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use App\Models\TaxonomyStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxonomyController extends Controller {

	/**
	 * Taxonomy controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/taxonomy",
	 *	 operationId="/api/v2/taxonomy",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="taxon",
	 *		 in="query",
	 *		 description="Taxon search term",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="type",
	 *		 in="query",
	 *		 description="Type of search",
	 *		 required=false,
	 *		 @OA\Schema(
	 *			type="string",
	 *			default="EXACT",
	 *			enum={"EXACT", "START", "WHOLEWORD", "WILD"}
	 *		)
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
	 *		 description="Returns list of inventories registered within system",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllTaxaSearch(Request $request) {
		$this->validate($request, [
			'limit' => 'integer',
			'offset' => 'integer'
		]);
		$limit = $request->input('limit', 100);
		$offset = $request->input('offset', 0);

		$type = $request->input('type', 'EXACT');

		if($request->taxon){
			$taxaModel = Taxonomy::query();
			if ($type == 'START') {
				$taxaModel->where('sciname', 'LIKE', $request->taxon . '%');
			} elseif ($type == 'WILD') {
				$taxaModel->where('sciname', 'LIKE', '%' . $request->taxon . '%');
			} elseif ($type == 'WHOLEWORD') {
				$taxaModel->where('unitname1', $request->taxon)
					->orWhere('unitname2', $request->taxon)
					->orWhere('unitname3', $request->taxon);
			} else {
				//Exact match
				$taxaModel->where('sciname', $request->taxon);
			}
	
			$fullCnt = $taxaModel->count();
			$result = $taxaModel->skip($offset)->take($limit)->get();
		}else{
			$fullCnt = Taxonomy::count();
			$result = Taxonomy::skip($offset)->take($limit)->get();
		}

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
	 *	 path="/api/v2/taxonomy/{identifier}",
	 *	 operationId="/api/v2/taxonomy/identifier",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Identifier (PK = tid) associated with taxonomic target",
	 *		 required=true,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns taxonomic record of matching ID",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Taxonomy identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneTaxon($id, Request $request) {
		$taxonObj = Taxonomy::find($id);

		//Set status and parent (can't use Eloquent model due to table containing complex PKs)
		$taxStatus = DB::table('taxstatus as s')
			->select('s.parentTid', 's.taxonomicSource', 's.unacceptabilityReason', 's.notes', 'a.tid', 'a.sciname', 'a.author')
			->join('taxa as a', 's.tidAccepted', '=', 'a.tid')
			->where('s.tid', $id)->where('s.taxauthid', 1);
		$taxStatusResult = $taxStatus->get();
		$taxonObj->parentTid = $taxStatusResult[0]->parentTid;

		//Set Status
		if ($id == $taxStatusResult[0]->tid) {
			$taxonObj->status = 'accepted';
		} else {
			$taxonObj->status = 'synonym';
			$accepted = [];
			$accepted['tid'] = $taxStatusResult[0]->tid;
			$accepted['scientificName'] = $taxStatusResult[0]->sciname;
			$accepted['scientificNameAuthorship'] = $taxStatusResult[0]->author;
			$accepted['taxonomicSource'] = $taxStatusResult[0]->taxonomicSource;
			$accepted['unacceptabilityReason'] = $taxStatusResult[0]->unacceptabilityReason;
			$accepted['taxonRemarks'] = $taxStatusResult[0]->notes;
			$taxonObj->accepted = $accepted;
		}

		//Set parent
		$parStatus = DB::table('taxaenumtree as e')
			->select('p.tid', 'p.sciname as scientificName', 'p.author', 'p.rankid')
			->join('taxa as p', 'e.parentTid', '=', 'p.tid')
			->where('e.tid', $id)->where('e.taxauthid', 1);
		$parStatusResult = $parStatus->get();
		$taxonObj->classification = $parStatusResult;

		if (!$taxonObj->count()) $taxonObj = ['status' => false, 'error' => 'Unable to locate inventory based on identifier'];
		return response()->json($taxonObj);
	}


	//Support functions
	public static function getSynonyms(Int $tid) {
		$synonymResult = DB::table('taxstatus as ts')
			->join('taxstatus as s', 'ts.tidaccepted', '=', 's.tidaccepted')
			->where('ts.tid', $tid)->where('ts.taxauthid', 1)->where('s.taxauthid', 1)->pluck('s.tid');
		return $synonymResult->toArray();
	}

	public static function getChildren(Int $tid) {
		//Direct accepted children only
		$childrenResult = DB::table('taxstatus as c')
			->join('taxstatus as a', 'c.parenttid', '=', 'a.tidaccepted')
			->where('a.tid', $tid)->where('c.taxauthid', 1)->where('a.taxauthid', 1)->whereColumn('c.tid', 'c.tidaccepted')->pluck('c.tid');
		/*
		SELECT c.tid
		FROM taxstatus c INNER JOIN taxstatus a ON c.parenttid = a.tidaccepted
		WHERE a.tid = 61943 AND c.taxauthid = 1 AND a.taxauthid = 1 AND c.tid = c.tidaccepted;
		*/
		return $childrenResult->toArray();
	}

	/**
	 * @OA\Post(
	 * 	path="/api/v2/taxonomy",
	 * 	operationId="createTaxon",
	 * 	description="Create a new taxon",
	 * 	tags={"Taxonomy"},
	 * 	@OA\Parameter(
	 *		name="apiToken",
	 *		in="query",
	 *		description="API security token to authenticate post action",
	 *		required=true,
	 *		@OA\Schema(type="string")
	 *	 ),
	 * 	@OA\RequestBody(
	 * 		required=true,
	 * 		description="Taxon data to be inserted",
	 * 		@OA\MediaType(
	 * 			mediaType="application/json",
	 * 			@OA\Schema(
	 * 				required={"kingdomName", "parenttid", "rankID", "sciName", "unitName1", "author", "securityStatus"},
	 * 				@OA\Property(
	 * 					property="kingdomName",
	 * 					type="string",
	 * 					enum={"Plantae", "Fungi", "Animalia", "Chromista", "Protista", "Bacteria"},
	 * 					description="The name of the kingdom",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="parenttid",
	 * 					type="integer",
	 * 					description="The tid of the parent taxon. This is visible in the URL of the target taxon profile page, and it's also displayed in the taxonomy editor page.",
	 * 					maxLength=10
	 * 				),
	 *  				@OA\Property(
	 * 					property="sciName",
	 * 					type="string",
	 * 					description="The name of the taxon excluding authorship. E.g., 'Abutilon guineense var. forrestii'",
	 * 					maxLength=250
	 * 				),
	 *  				@OA\Property(
	 * 					property="author",
	 * 					type="string",
	 * 					description="The authorship associated with the taxon",
	 * 					maxLength=150
	 * 				),
	 *  				@OA\Property(
	 * 					property="rankID",
	 * 					enum={0, 1, 10, 15, 20, 25, 27, 30, 40, 60, 70, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 220, 230, 240, 250, 260, 300},
	 * 					type="integer",
	 * 					description="Rank id associated with the rank of the target taxon: class= 60, cultivar= 300, division= 30, family= 140, form= 260, genus= 180, kingdom= 10, non-ranked node= 0, order= 100, organism= 1, section= 200, species= 220, subclass= 70, subdivision= 40, subfamily= 150, subform= 270, subgenus= 190, subkingdom= 20, suborder= 110, subsection= 210, subspecies= 230, subtribe= 170, subvariety= 250, superclass= 50, tribe= 160, variety= 240",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="unitInd1",
	 * 					type="string",
	 * 					description="An optional character to indicate hybrid (×) or extinct (†) status",
	 * 					maxLength=1
	 * 				),
	 *  				@OA\Property(
	 * 					property="unitName1",
	 * 					type="string",
	 * 					description="First name of the new taxon. If there is only one name (e.g., if the taxon is question is a genus), enter that name. If the taxon is binomial or more (e.g., 'Acer rubrum'), just enter the first taxonomic unit (e.g., 'Acer' in the previous example)",
	 * 					maxLength=50
	 * 				),
	 *  				@OA\Property(
	 * 					property="unitInd2",
	 * 					type="string",
	 * 					description="An optional character to indicate hybrid (×) status",
	 * 					maxLength=1
	 * 				),
	 *  				@OA\Property(
	 * 					property="unitName2",
	 * 					type="string",
	 * 					description="Optional second name of the new taxon. If there is only one name (e.g., if the taxon is question is a genus), leave this empty. If the taxon is binomial or more (e.g., 'Acer rubrum'), just enter the second taxonomic unit (e.g., 'rubrum' in the previous example)",
	 * 					maxLength=50
	 * 				),
	 *  				@OA\Property(
	 * 					property="unitInd3",
	 * 					type="string",
	 * 					description="An optional string to indicate the nature of the optional third name of the taxon (e.g., 'f.', 'var.', 'subvar.', 'spp.')",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="unitName3",
	 * 					type="string",
	 * 					description="Optional third name of the new taxon. If there are only two names (e.g., if the taxon is question is a species), leave this empty. If the taxon is trinomial or more (e.g., 'Trichomanes rigidum var. elongatum'), enter the third taxonomic unit (e.g., 'elongatum' in the previous example)",
	 * 					maxLength=35
	 * 				),
	 *  				@OA\Property(
	 * 					property="cultivarEpithet",
	 * 					type="string",
	 * 					description="Optional cultivar epithet if the taxon is a cultivar. Although single quotations will be appended to the full sciName of this taxon, single quotations should NOT be used here",
	 * 					maxLength=50
	 * 				),
	 *  				@OA\Property(
	 * 					property="tradeName",
	 * 					type="string",
	 * 					description="Optional trade name if the taxon has a trade name. By convention, trade names should be entered completely capitalized (e.g., 'EMPRESS')",
	 * 					maxLength=50
	 * 				),
	 *  				@OA\Property(
	 * 					property="source",
	 * 					type="string",
	 * 					description="Optional. Add a link or citation for the source of the scientific name you are adding, for example, a Plants of the World Online entry, a literature citation with DOI, etc.",
	 * 					maxLength=250
	 * 				),
	 *  				@OA\Property(
	 * 					property="notes",
	 * 					type="string",
	 * 					description="Optional. Any notes about the taxon that might be important to communicate",
	 * 					maxLength=250
	 * 				),
	 *  				@OA\Property(
	 * 					property="securityStatus",
	 * 					type="integer",
	 * 					enum={0, 1, 5},
	 * 					description="Enter 0 if no security filter is required. Enter 1 if TODO. Enter 5 if TODO.",
	 * 					maxLength=10
	 * 				),
	 * *  				@OA\Property(
	 * 					property="UnacceptabilityReason",
	 * 					type="string",
	 * 					description="Reasons why the taxon may not be widely accepted",
	 * 					maxLength=250
	 * 				),
	 * 			)
	 * 		)
	 * 	),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns full JSON object of the of collection that was created"
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	public function create(Request $request){
		$authenticatedRoles = $this->authenticate($request)['roles'] ?? [];
		$extractedRoles = array_map(function($elem){
			return $elem['role'];
		}, $authenticatedRoles);
		$rolesPermittedToCreateCollections = array('CollAdmin', 'SuperAdmin');
		$qualifyingRoles = array_intersect($extractedRoles, $rolesPermittedToCreateCollections);

		if(count($qualifyingRoles)>0){
			try {
				$taxon = Taxonomy::create($request->all());
				return response()->json($taxon, 200);
				$family = $this->getFamily($taxon, $request->parenttid);

				// @TODO add to taxstatus
				$taxstatus = TaxonomyStatus::create([
					'tid' => $taxon->tid,
					'tidaccepted' => $taxon->tid,
					'taxauthid' => 1, // @TODO is this sufficient?
					'family' => $family,
					'parenttid' => $taxon->parenttid,
					'UnacceptabilityReason' => $taxon->UnacceptabilityReason,
					// 'modifiedUid' => '@TODO'
				]);
			} catch (\Exception $e) {
				return response()->json(['error' => 'Failed to create new taxon' . $e->getMessage()], 500);
			}

			return response()->json($taxon, 200);
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}
	
	public function getFamily($taxon, $parenttid){
		$family = '';
		if($taxon->rankID > 140){
			$family = DB::table('taxa as t')
				->select('t.sciname')
				->join('taxaenumtree as e', 't.tid', '=', 'e.parenttid')
				->where('t.tid', $parenttid)
				->orWhere('e.tid', $parenttid)
				->first();
		}
		if($taxon->rankID == 140){
			$family = $taxon->sciName;
		}
		return $family;

		// $family = '';
		// if($dataArr['rankid'] > 140){
		// 	$sqlFam = 'SELECT t.sciname '.
		// 		'FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.parenttid '.
		// 		'WHERE (t.tid = '.$parTid.' OR e.tid = '.$parTid.') AND t.rankid = 140 ';
		// 	//echo $sqlFam; exit;
		// 	$rsFam = $this->conn->query($sqlFam);
		// 	if($r = $rsFam->fetch_object()){
		// 		$family = $r->sciname;
		// 	}
		// 	$rsFam->free();
		// }
	}
}
