<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Media;
use App\Models\TaxonomyDescription;
use App\Models\TaxonomyDescriptionStatement;
use Illuminate\Http\Request;

class InventoryPackageController extends InventoryController{
	/**
	 * Inventory package controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/inventory/{identifier}/package",
	 *	 operationId="/api/v2/inventory/identifier/package",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="PK, GUID, or recordID associated with target inventory",
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
	 *		 description="Returns list of packages registered within system",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function oneInventoryDataPackage($id, Request $request){
		$this->validate($request, [
			'includeDescriptions' => 'integer',
			'descriptionLimit' => 'integer',
			'includeImages' => 'integer',
			'imageLimit' => 'integer'
		]);
		$includeDescriptions = $request->input('includeDescriptions', 0);
		$descriptionLimit = $request->input('descriptionLimit', 1);
		$includeImages = $request->input('includeImages', 0);
		$imageLimit = $request->input('imageLimit', 3);

		$id = $this->getClid($id);
		$inventoryObj = Inventory::find($id);

		if(!empty($inventoryObj)){
			foreach($inventoryObj->taxa as $taxaObj){
				if($includeImages) $taxaObj->media = Media::where('tid', $taxaObj->tid)->orderBy('sortSequence')->take($imageLimit)->get();
				if($includeDescriptions){
					$description = TaxonomyDescription::where('tid', $taxaObj->tid)->orderBy('displayLevel')->take($descriptionLimit)->get();
					foreach($description as $descrObj){
						$descrObj->statements = TaxonomyDescriptionStatement::where('tdbid', $descrObj->tdbid)->get();
					}
					$taxaObj->textDescription = $description;
				}
	
			}
		}
		$result = $inventoryObj;
		if(empty($result)) $result = ['status' =>false, 'error' => 'Unable to locate inventory based on identifier'];

		return response()->json($result);
	}

}