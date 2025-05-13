<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
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
	 * 	path="/api/v2/collection",
	 * 	operationId="createCollection",
	 * 	description="Create a new biocollection entity",
	 * 	tags={"Collections"},
	 * 	@OA\Parameter(
	 *		name="apiToken",
	 *		in="query",
	 *		description="API security token to authenticate post action",
	 *		required=true,
	 *		@OA\Schema(type="string")
	 *	 ),
	 * 	@OA\RequestBody(
	 * 		required=true,
	 * 		description="Collection data to be inserted",
	 * 		@OA\MediaType(
	 * 			mediaType="application/json",
	 * 			@OA\Schema(
	 * 				required={"institutionCode", "collectionName", "collType", "managementType", "publicEdits"},
	 * 				@OA\Property(
	 * 					property="institutionCode",
	 * 					type="string",
	 * 					description="The name (or acronym) in use by the institution having custody of the occurrence records",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="collectionCode",
	 * 					type="string",
	 * 					description="The name, acronym, or code identifying the collection or data set from which the record was derived",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="collectionName",
	 * 					type="string",
	 * 					description="What you want the collection to be called",
	 * 					maxLength=150
	 * 				),
	 *  				@OA\Property(
	 * 					property="collectionID",
	 * 					type="string",
	 * 					description="Global Unique Identifier for this collection (see dwc:collectionID): If your collection already has a previously assigned GUID, that identifier should be represented here. For physical specimens, the recommended best practice is to use an identifier from a collections registry such as the Global Registry of Biodiversity Repositories",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="fullDescription",
	 * 					type="string",
	 * 					description="Description of the collection in <2000 characters",
	 * 					maxLength=2000
	 * 				),
	 *  				@OA\Property(
	 * 					property="individualUrl",
	 * 					type="string",
	 * 					description="A dynamic link back to the source record if available",
	 * 					maxLength=500
	 * 				),
	 *  				@OA\Property(
	 * 					property="latitudeDecimal",
	 * 					type="number",
	 * 					description="Latitude as a decimal",
	 * 					maxLength=15
	 * 				),
	 *  				@OA\Property(
	 * 					property="longitudeDecimal",
	 * 					type="number",
	 * 					description="Longitude as a decimal",
	 * 					maxLength=15
	 * 				),
	 *  				@OA\Property(
	 * 					property="collType",
	 * 					type="string",
	 * 					enum={"Preserved Specimens", "General Observations", "Observations"},
	 * 					description="'Preserved Specimens', 'General Observations', or 'Observations'. Preserved Specimens signify a collection type that contains physical samples that are available for inspection by researchers and taxonomic experts. Use Observations when the record is not based on a physical specimen. Personal Observation Management is a dataset where registered users can independently manage their own subset of records. Records entered into this dataset are explicitly linked to the user’s profile and can only be edited by them. This type of collection is typically used by field researchers to manage their collection data and print labels prior to depositing the physical material within a collection. Even though personal collections are represented by a physical sample, they are classified as “observations” until the physical material is publicly available within a collection",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="managementType",
	 * 					type="string",
	 * 					enum={"Snapshot", "Live Data"},
	 * 					description="Use 'Snapshot' when there is a separate in-house database maintained in the collection and the dataset within the Symbiota portal is only a periodically updated snapshot of the central database. A 'Live Data' dataset is when the data is managed directly within the portal and the central database is the portal data",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="publicEdits",
	 * 					type="integer",
	 * 					enum={0,1},
	 * 					description="The option to enable public edits (1 for yes, 0 for no)",
	 * 					maxLength=1
	 * 				),
	 *  				@OA\Property(
	 * 					property="rightsHolder",
	 * 					type="string",
	 * 					description="The organization or person managing or owning the rights of the resource. For more details, see Darwin Core definition",
	 * 					maxLength=250
	 * 				),
	 *  				@OA\Property(
	 * 					property="rights",
	 * 					type="string",
	 * 					description="Information or a URL link to page with details explaining how one can use the data. See Darwin Core definition",
	 * 					maxLength=250
	 * 				),
	 *  				@OA\Property(
	 * 					property="accessRights",
	 * 					type="string",
	 * 					description="Information or a URL link to page with details explaining how one can use the data. See Darwin Core definition",
	 * 					maxLength=1000
	 * 				),
	 *  				@OA\Property(
	 * 					property="sortSeq",
	 * 					type="string",
	 * 					description="Leave this field empty if you want the collections to sort alphabetically (default)",
	 * 					maxLength=10
	 * 				),
	 *  				@OA\Property(
	 * 					property="icon",
	 * 					type="string",
	 * 					description="URL of an image icon representing the collection. The URL path can be absolute or relative. The use of icons are optional",
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
			// @TODO make colleciton GUID?
			try {
				$collection = Taxonomy::create($request->all());
				$collectionStats = CollectionStats::create([
					'collid' => $collection->collID,
					'recordcnt' => 0,
					// 'uploadedby' => $GLOBALS['USERNAME']
					'uploadedby' => 'TODO'
				]);
			} catch (\Exception $e) {
				return response()->json(['error' => 'Failed to create collection stats' . $e->getMessage()], 500);
			}

			return response()->json($collection, 200);
		}
		return response()->json(['error' => 'Unauthorized'], 401);
	}	
}
