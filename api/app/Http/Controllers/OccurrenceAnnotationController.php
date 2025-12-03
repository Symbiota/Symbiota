<?php

namespace App\Http\Controllers;

use App\Models\Occurrence;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OccurrenceAnnotationController extends OccurrenceController{
	/**
	 * Occurrence Annotation controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/annotation/search",
	 *	 operationId="/api/v2/occurrence/annotation/search",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="collid",
	 *		 in="query",
	 *		 description="Internal identifier (PK) for collection",
	 *		 required=true,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="type",
	 *		 in="query",
	 *		 description="Annotation type (internal, external) ",
	 *		 required=true,
	 *		 @OA\Schema(type="string", default="internal", enum = {"internal", "external"})
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="source",
	 *		 in="query",
	 *		 description="External source of Annotation (e.g. geolocate) ",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="fieldName",
	 *		 in="query",
	 *		 description="Name of occurrence field that was annotated (e.g. recordedBy, eventDate) ",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="fromDate",
	 *		 in="query",
	 *		 description="The start date of a date range the annotation was created (e.g. 2022-02-05) ",
	 *		 required=false,
	 *		 @OA\Schema(type="date")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="toDate",
	 *		 in="query",
	 *		 description="The end date of a date range the annotation was created (e.g. 2022-02-05) ",
	 *		 required=false,
	 *		 @OA\Schema(type="date")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Controls the number of results per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=500)
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
	 *		 description="Returns list of occurrence edits",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllAnnotations(Request $request){
		$this->validate($request, [
			'collid' => ['required', 'integer'],
			'type' => [Rule::in(['internal', 'external'])],
			'source' => 'alpha',
			'fieldName' => 'alpha',
			'fromDate' => 'date',
			'toDate' => 'date',
			'limit' => ['integer', 'max:500'],
			'offset' => 'integer'
		]);
		$collid = $request->input('collid');
		$type = $request->input('type', 'internal');
		$source = $request->input('source');
		$fieldName = $request->input('fieldName');
		$fromDate = $request->input('fromDate');
		$toDate = $request->input('toDate');
		$limit = $request->input('limit', 100);
		$offset = $request->input('offset', 0);

		$annotation = null;
		$fullCnt = 0;
		$result = null;
		if($type == 'internal'){
			$annotation = DB::table('omoccuredits as e')->select('e.*', 'o.occurrenceID', 'o.recordID')
				->join('omoccurrences as o', 'e.occid', '=', 'o.occid')
				->where('o.collid', $collid)->where('o.recordSecurity', '=', 0);
			if($fieldName){
				$annotation = $annotation->where('e.fieldname', $fieldName);
			}
			if($fromDate){
				$annotation = $annotation->where('e.initialTimestamp', '>', $fromDate);
			}
			if($toDate){
				$annotation = $annotation->where('e.initialTimestamp', '<', $toDate);
			}
			$fullCnt = $annotation->count();
			$result = $annotation->skip($offset)->take($limit)->get();
			$result = $this->formatInternalResults($result);
		}
		elseif($type == 'external'){
			$annotation = DB::table('omoccurrevisions as r')->select('r.*', 'o.occurrenceID', 'o.recordID')
				->join('omoccurrences as o', 'o.occid', '=', 'r.occid')
				->where('o.collid', $collid)->where('o.recordSecurity', '=', 0);
			if($source){
				$annotation = $annotation->where('r.externalSource', $source);
			}
			if($fieldName){
				$annotation = $annotation->where('r.oldvalues', 'like', '%'.$fieldName.'%');
			}
			if($fromDate){
				$annotation = $annotation->where('r.initialTimestamp', '>', $fromDate);
			}
			if($toDate){
				$annotation = $annotation->where('r.initialTimestamp', '<', $toDate);
			}
			$fullCnt = $annotation->count();
			$result = $annotation->skip($offset)->take($limit)->get();
			$result = $this->formatExternalResults($result, $fieldName);
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

	public function showOccurrenceAnnotations($id, Request $request){
		$this->validate($request, [
			'type' => [Rule::in(['internal', 'external'])]
		]);
		$type = $request->input('type', 'internal');

		$id = $this->getOccid($id);
		$annotation = null;
		if($type == 'internal'){
			$annotation = Occurrence::find($id)->annotationInternal;
		}
		elseif($type == 'external'){
			$annotation = Occurrence::find($id)->annotationExternal;
		}

		return response()->json($annotation);
	}

	/**
	 * @OA\Post(
	 *	 path="/api/v2/occurrence/annotation/insert",
	 *	 operationId="/api/v2/occurrence/annotation/insert",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		name="apiToken",
	 *		in="query",
	 *		description="API security token to authenticate post action",
	 *		required=true,
	 *		@OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="occid",
	 *		in="query",
	 *		description="Internal occurrence identifier",
	 *		required=true,
	 *		@OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="externalSource",
	 *		in="query",
	 *		description="External source of annotation (e.g. vexternalSourceouchervision, geolocate)",
	 *		required=false,
	 *		@OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="externalEditor",
	 *		in="query",
	 *		description="External editor (a human) responsible for annotation",
	 *		required=false,
	 *		@OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="externalTimestamp",
	 *		in="query",
	 *		description="Timestamp when external annotation was created",
	 *		required=false,
	 *		@OA\Schema(type="string", format="date-time")
	 *	 ),
	 *	 @OA\Parameter(
	 *		name="appliedStatus",
	 *		in="query",
	 *		description="Whether to apply changes to main occurrence record (0=not applied, 1=applied)",
	 *		required=false,
	 *		@OA\Schema(type="integer", default=0)
	 *	 ),
	 *	 @OA\RequestBody(
	 *		required=true,
	 *		description="Annotation data containing field name/value pairs for the occurrence",
	 *		@OA\MediaType(
	 *			mediaType="application/json",
	 *			@OA\Schema(
	 *				@OA\Property(
	 *					property="catalogNumber",
	 *					type="string",
	 *					description="Catalog number"
	 *				),
	 *				@OA\Property(
	 *					property="scientificName",
	 *					type="string",
	 *					description="Scientific name"
	 *				),
	 *				@OA\Property(
	 *					property="family",
	 *					type="string",
	 *					description="Taxonomic family"
	 *				),
	 *				@OA\Property(
	 *					property="stateProvince",
	 *					type="string",
	 *					description="State or province"
	 *				),
	 *				@OA\Property(
	 *					property="locality",
	 *					type="string",
	 *					description="Locality description"
	 *				)
	 *			),
	 *		)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns success message with annotation details",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request.",
	 *	 ),
	 *	 @OA\Response(
	 *		 response="401",
	 *		 description="Unauthorized",
	 *	 ),
	 * )
	 */
	public function insertAnnotation(Request $request){
		$this->validate($request, [
			'occid' => 'required|integer',
			'externalSource' => 'string|max:45',
			'externalEditor' => 'string|max:100',
			'externalTimestamp' => 'date',
			'appliedStatus' => 'integer|in:0,1'
		]);

		if($user = $this->authenticate($request)){
			$occid = $request->input('occid');
			$externalSource = $request->input('externalSource');
			$externalEditor = $request->input('externalEditor');
			$externalTimestamp = $request->input('externalTimestamp');
			$appliedStatus = $request->input('appliedStatus', 0); // Default to not applied

			// Get the request body content - expect field data directly
			$newValues = $request->json()->all();
			
			// Validate that we have field data
			if(empty($newValues)){
				return response()->json(['error' => 'No field values provided'], 400);
			}

			// Get the existing occurrence record to get current values
			$occurrence = Occurrence::find($occid);
			if(!$occurrence){
				return response()->json(['error' => 'Occurrence not found'], 404);
			}

			// Check permissions - using same logic from parent OccurrenceController
			$authorized = false;
			foreach($user['roles'] as $role){
				if($role['role'] == 'SuperAdmin' || 
				   ($role['role'] == 'CollAdmin' && $role['tablePK'] == $occurrence->collid) ||
				   ($role['role'] == 'CollEditor' && $role['tablePK'] == $occurrence->collid)){
					$authorized = true;
					break;
				}
			}
			
			if(!$authorized){
				return response()->json(['error' => 'Unauthorized to edit target collection'], 401);
			}

			// Build old values array for fields that will be changed
			$oldValues = [];
			$vettedNewValues = [];

			// Get list of fillable fields from the Occurrence model
			$fillableFields = $occurrence->getFillable();

			foreach($newValues as $fieldName => $fieldValue){
				// Convert field name to match database column naming convention
				$dbFieldName = $this->convertFieldNameToDbColumn($fieldName);
				
				// Only process fields that exist in the fillable array
				if(in_array($dbFieldName, $fillableFields)){
					$currentValue = $occurrence->getAttribute($dbFieldName);
					
					// Only include fields that are actually changing
					if($currentValue != $fieldValue){
						$oldValues[$dbFieldName] = $currentValue;
						$vettedNewValues[$dbFieldName] = $fieldValue;
					}
				}
			}

			if(empty($vettedNewValues)){
				return response()->json(['error' => 'No valid field changes found'], 400);
			}

			// Create the revision record using Eloquent
			try{
				$revision = DB::table('omoccurrevisions')->insert([
					'occid' => $occid,
					'oldValues' => json_encode($oldValues),
					'newValues' => json_encode($vettedNewValues),
					'externalSource' => $externalSource,
					'externalEditor' => $externalEditor,
					'reviewStatus' => 0, // Default to not reviewed
					'appliedStatus' => $appliedStatus,
					'externalTimestamp' => $externalTimestamp,
					'initialTimestamp' => date('Y-m-d H:i:s')
				]);

				if(!$revision){
					return response()->json(['error' => 'Failed to create revision record'], 500);
				}

				$responseData = [
					'status' => 'SUCCESS',
					'message' => 'Annotation successfully submitted',
					'occid' => $occid,
					'fieldsChanged' => count($vettedNewValues),
					'appliedStatus' => $appliedStatus
				];

				// If appliedStatus is 1, update the main occurrence record
				if($appliedStatus){
					try{
						$updated = $occurrence->update($vettedNewValues);
						if($updated){
							$responseData['message'] .= ' and applied to occurrence record';
						}
						else{
							$responseData['warning'] = 'Annotation submitted but failed to apply to occurrence record';
						}
					}
					catch(\Exception $e){
						$responseData['warning'] = 'Annotation submitted but error applying to occurrence record: ' . $e->getMessage();
					}
				}
				else{
					$responseData['message'] .= ' but NOT applied to occurrence record';
				}

				return response()->json($responseData, 201);
			}
			catch(\Exception $e){
				return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
			}
		}
		
		return response()->json(['error' => 'Unauthorized'], 401);
	}

	/**
	 * Helper method to convert field names from input to database column names
	 * Maps common field variations to their database equivalents
	 */
	private function convertFieldNameToDbColumn($fieldName){
		$fieldMappings = [
			'collector' => 'recordedBy',
			'collectionDate' => 'eventDate',
			'verbatimCollectionDate' => 'verbatimEventDate',
			'specimenDescription' => 'occurrenceRemarks',
			'additionalText' => 'fieldNotes',
			'cultivated' => 'cultivationStatus',
			'datum' => 'geodeticDatum'
		];

		// Return mapped field name if mapping exists, otherwise return original
		return $fieldMappings[$fieldName] ?? $fieldName;
	}

	//Helper funcitons
	private function formatExternalResults($resultObj, $fieldLimit){
		$retArr = array();
		foreach($resultObj as $unitKey => $unitObj){
			$unitArr = (array)$unitObj;
			$unitArr = array_change_key_case($unitArr);
			if(isset($unitArr['oldvalues'])){
				$newArr1 = array();
				$newArr2 = array();
				$newArr1['annotationID'] = $unitArr['orid'];
				$newArr1['occid'] = $unitArr['occid'];
				if($unitArr['occurrenceid']) $newArr1['occurrenceID'] = $unitArr['occurrenceid'];
				else $newArr1['occurrenceID'] = $unitArr['recordid'];
				$newArr2['externalSource'] = $unitArr['externalsource'];
				$newArr2['externalEditor'] = $unitArr['externaleditor'];
				$newArr2['reviewStatus'] = $unitArr['reviewstatus'];
				$newArr2['appliedStatus'] = $unitArr['appliedstatus'];
				$newArr2['recordID'] = $unitArr['guid'];
				$newArr2['externalError'] = $unitArr['errormessage'];
				$newArr2['externalTimestamp'] = $unitArr['externaltimestamp'];
				$newArr2['recordTimestamp'] = $unitArr['initialtimestamp'];
				$oldValueArr = json_decode($unitArr['oldvalues'], true);
				$newValueArr = json_decode($unitArr['newvalues'], true);
				foreach($oldValueArr as $fieldName => $oldValue){
					if(!$fieldLimit || $fieldLimit == $fieldName){
						if(array_key_exists($fieldName, $newValueArr)){
							$retArr[] = array_merge($newArr1, array('fieldName' => $fieldName, 'oldValue' => $oldValue, 'newValue' => $newValueArr[$fieldName]), $newArr2);
						}
					}
				}
			}
		}
		return $retArr;
	}

	private function formatInternalResults($resultObj){
		$retArr = array();
		foreach($resultObj as $unitKey => $unitObj){
			$unitArr = (array)$unitObj;
			$unitArr = array_change_key_case($unitArr);
			$retArr[$unitKey]['annotationID'] = $unitArr['ocedid'];
			$retArr[$unitKey]['occid'] = $unitArr['occid'];
			if($unitArr['occurrenceid']) $retArr[$unitKey]['occurrenceID'] = $unitArr['occurrenceid'];
			else $retArr[$unitKey]['occurrenceID'] = $unitArr['recordid'];
			$retArr[$unitKey]['fieldName'] = $unitArr['fieldname'];
			$retArr[$unitKey]['newValue'] = $unitArr['fieldvaluenew'];
			$retArr[$unitKey]['oldValue'] = $unitArr['fieldvalueold'];
			$retArr[$unitKey]['reviewStatus'] = $unitArr['reviewstatus'];
			$retArr[$unitKey]['appliedStatus'] = $unitArr['appliedstatus'];
			$retArr[$unitKey]['recordID'] = $unitArr['guid'];
			$retArr[$unitKey]['recordTimestamp'] = $unitArr['initialtimestamp'];
		}
		return $retArr;
	}
}
