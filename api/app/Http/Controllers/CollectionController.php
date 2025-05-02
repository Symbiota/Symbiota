<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller{
	/**
	 * Collection controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/collection",
	 *	 operationId="/api/v2/collection",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="managementType",
	 *		 in="query",
	 *		 description="live, snapshot, aggregate",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="collectionType",
	 *		 in="query",
	 *		 description="preservedSpecimens, observations, researchObservation",
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
	 *		 description="Returns list of collections",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllCollections(Request $request){
		$this->validate($request, [
			'limit' => ['integer', 'max:1000'],
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',1000);
		$offset = $request->input('offset',0);

		$conditions = [];
		if($request->has('managementType')){
			if($request->managementType == 'live') $conditions[] = ['managementType','Live Data'];
			elseif($request->managementType == 'snapshot') $conditions[] = ['managementType','Snapshot'];
			elseif($request->managementType == 'aggregate') $conditions[] = ['managementType','Aggregate'];
		}
		if($request->has('collectionType')){
			if($request->collectionType == 'specimens') $conditions[] = ['collType','Preserved Specimens'];
			elseif($request->collectionType == 'observations') $conditions[] = ['collType','Observations'];
			elseif($request->collectionType == 'researchObservations') $conditions[] = ['collType','General Observations'];
		}

		$fullCnt = Collection::where($conditions)->count();
		$result = Collection::where($conditions)->skip($offset)->take($limit)->get();

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
	 *	 path="/api/v2/collection/{identifier}",
	 *	 operationId="/api/v2/collection/identifier",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Installation ID or GUID associated with target collection",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns collection data",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Collection identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneCollection($id, Request $request){
		$collectionObj = null;
		if(is_numeric($id)) $collectionObj = Collection::find($id);
		else $collectionObj = Collection::where('collectionGuid',$id)->first();
		if(!$collectionObj->count()) $collectionObj = ["status"=>false,"error"=>"Unable to locate collection based on identifier"];
		return response()->json($collectionObj);
	}

	/**
	 * @OA\Post(
	 * 	path="/api/v2/collection",
	 * 	operationId="createCollection",
	 * 	description="Create a new biocollection entity",
	 * 	tags={"Collections"},
	 * 	@OA\RequestBody(
	 * 		required=true,
	 * 		description="Collection data to be inserted",
	 * 		@OA\MediaType(
	 * 			mediaType="application/json",
	 * 			@OA\Schema(
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
	 * 					description="TODO",
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
	 * 					description="'Preserved Specimens', 'General Observations', or 'Observations'. Preserved Specimens signify a collection type that contains physical samples that are available for inspection by researchers and taxonomic experts. Use Observations when the record is not based on a physical specimen. Personal Observation Management is a dataset where registered users can independently manage their own subset of records. Records entered into this dataset are explicitly linked to the user’s profile and can only be edited by them. This type of collection is typically used by field researchers to manage their collection data and print labels prior to depositing the physical material within a collection. Even though personal collections are represented by a physical sample, they are classified as “observations” until the physical material is publicly available within a collection",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="managementType",
	 * 					type="string",
	 * 					description="Use 'Snapshot' when there is a separate in-house database maintained in the collection and the dataset within the Symbiota portal is only a periodically updated snapshot of the central database. A 'Live Data' dataset is when the data is managed directly within the portal and the central database is the portal data",
	 * 					maxLength=45
	 * 				),
	 *  				@OA\Property(
	 * 					property="publicEdits",
	 * 					type="integer",
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
		// if($user = $this->authenticate($request)){
			// @TODO make colleciton GUID?
			try {
				$collection = Collection::create($request->all());
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
		// }
		// return response()->json(['error' => 'Unauthorized'], 401);
	}	

	public function update($id, Request $request){
		//$collection = Collection::findOrFail($id);
		//$collection->update($request->all());
		//return response()->json($collection, 200);
	}

	public function delete($id){
		//Collection::findOrFail($id)->delete();
		//return response('Collection Deleted Successfully', 200);
	}
}