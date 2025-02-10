<?php

namespace App\Http\Controllers;

use App\Models\TaxonomyDescription;
use Illuminate\Http\Request;

class TaxonomyDescriptionController extends Controller
{
	/**
	 * Taxonomy Description controller instance.
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * @OA\Get(
	 *   path="/api/v2/taxonomy/{identifier}/description",
	 *   operationId="getTaxonomyDescriptions",
	 *   tags={"Taxonomy"},
	 *   @OA\Parameter(
	 *     name="identifier",
	 *     in="path",
	 *     description="PK, GUID, or recordID associated with target taxonomic unit",
	 *     required=true,
	 *     @OA\Schema(type="string")
	 *   ),
	 *   @OA\Parameter(
	 *     name="limit",
	 *     in="query",
	 *     description="Controls the number of results in the page.",
	 *     required=false,
	 *     @OA\Schema(type="integer", default=100)
	 *   ),
	 *   @OA\Parameter(
	 *     name="offset",
	 *     in="query",
	 *     description="Determines the starting point for the search results.",
	 *     required=false,
	 *     @OA\Schema(type="integer", default=0)
	 *   ),
	 *   @OA\Response(
	 *     response="200",
	 *     description="Returns list of taxonomic descriptions for a given taxon",
	 *     @OA\JsonContent()
	 *   ),
	 *   @OA\Response(
	 *     response="400",
	 *     description="Error: Bad request."
	 *   )
	 * )
	 */
	public function showAllDescriptions($id, Request $request)
	{
		$this->validate($request, [
			'limit' => 'integer',
			'offset' => 'integer'
		]);

		$limit = $request->input('limit', 100);
		$offset = $request->input('offset', 0);

		$inventoryQuery = TaxonomyDescription::where('taxonomy_id', $id);

		$fullCnt = $inventoryQuery->count();
		$result = $inventoryQuery->skip($offset)->take($limit)->get();

		$eor = ($offset + $limit) >= $fullCnt;

		return response()->json([
			'offset' => (int)$offset,
			'limit' => (int)$limit,
			'endOfRecords' => $eor,
			'count' => $fullCnt,
			'results' => $result
		]);
	}

	/**
	 * @OA\Get(
	 *   path="/api/v2/taxonomy/{identifier}/description/{description_id}",
	 *   operationId="getSingleTaxonomyDescription",
	 *   tags={"Taxonomy"},
	 *   @OA\Parameter(
	 *     name="identifier",
	 *     in="path",
	 *     description="PK, GUID, or recordID associated with target taxonomic unit",
	 *     required=true,
	 *     @OA\Schema(type="string")
	 *   ),
	 *   @OA\Parameter(
	 *     name="description_id",
	 *     in="path",
	 *     description="Identifier (PK, tdbid) associated with taxonomic description",
	 *     required=true,
	 *     @OA\Schema(type="integer")
	 *   ),
	 *   @OA\Response(
	 *     response="200",
	 *     description="Returns taxonomic description",
	 *     @OA\JsonContent()
	 *   ),
	 *   @OA\Response(
	 *     response="400",
	 *     description="Error: Bad request. Inventory identifier is required."
	 *   )
	 * )
	 */
	public function showOneDescriptions($id, $description_id)
	{
		$inventoryObj = TaxonomyDescription::where('taxonomy_id', $id)
			->where('id', $description_id)
			->first();

		if (!$inventoryObj) {
			return response()->json(['status' => false, 'error' => 'Unable to locate inventory based on identifier'], 404);
		}

		return response()->json($inventoryObj);
	}
}
