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
		$inventoryObj->taxa;
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
		$result = $inventoryObj;
		if(!$result->count()) $result = ['status' =>false, 'error' => 'Unable to locate inventory based on identifier'];

		return response()->json($result);
	}

}