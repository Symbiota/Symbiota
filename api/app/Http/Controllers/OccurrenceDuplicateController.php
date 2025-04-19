<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Occurrence;
use App\Models\OccurrenceOcr;
use App\Helpers\OccurrenceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ExsiccataNumber;

class OccurrenceDuplicateController extends Controller{

	private $taxaFields = array('family', 'sciname', 'scientificNameAuthorship');
	private $siteFields = array('associatedCollectors', 'eventDate', 'verbatimEventDate', 'country', 'stateProvince', 'county', 'municipality', 'locality',
		'decimalLatitude', 'decimalLongitude', 'geodeticDatum', 'coordinateUncertaintyInMeters', 'verbatimCoordinates', 'minimumElevationInMeters', 'maximumElevationInMeters', 'verbatimElevation',
		'habitat', 'substrate', 'occurrenceRemarks', 'associatedTaxa', 'dynamicProperties','verbatimAttributes','reproductiveCondition', 'cultivationStatus', 'establishmentMeans');

	/**
	 * Occurrence Duplicate Controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/duplicate",
	 *	 operationId="/api/v2/occurrence/duplicate",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="recordedBy",
	 *		 in="query",
	 *		 description="Collector/observer of occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordedByLastName",
	 *		 in="query",
	 *		 description="Last name of collector/observer of occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="recordNumber",
	 *		 in="query",
	 *		 description="Personal number of the collector or observer of the occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="eventDate",
	 *		 in="query",
	 *		 description="Collection date. Multiple standards formats are allowed, but must be a valid date that can be converted to YYYY-MM-DD.",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="ometid",
	 *		 in="query",
	 *		 description="Primary key for exsiccati title ",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="exsiccatiNumber",
	 *		 in="query",
	 *		 description="Number assigned to the exsiccati series",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="subjectIdentifier",
	 *		 in="query",
	 *		 description="Identifier (occurrenceID, recordID, or occid) of subject occurrence",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of occurrences",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showDuplicateMatches(Request $request){
		$this->validate($request, [
			'recordedBy' => '',
			'recordNumber' => '',
			'eventDate' => 'date',
			'ometid' => 'integer'
		]);
		$recordedBy = $request->input('recordedBy');
		$recordedByLastName = $request->input('recordedByLastName');
		$recordNumber = $request->input('recordNumber');
		$eventDate = $request->input('eventDate');
		$ometid = $request->input('ometid');	//Exsiccati primary key
		$exsiccatiNumber = $request->input('exsiccatiNumber');
		$subjectOccid = 0;
		if($request->has('subjectIdentifier')){
			$subjectOccid = $this->getOccid($request->input('subjectIdentifier'));
		}

		if(preg_match('/^s\.{0,1}n\.{0,1}$/i', $recordNumber)){
			//Ignore: sn, s.n.
			$recordNumber = '';
		}
		if($eventDate){
			$eventDate = OccurrenceHelper::formatDate($eventDate);
		}

		$matchType = '';
		$termsMatched = '';
		$occidArr = array();
		if($ometid && $exsiccatiNumber){
			//First check for exsiccati dupliplicates
			$exsiccatiResult = ExsiccataNumber::where('ometid', $ometid)->where('exsnumber', $exsiccatiNumber)->first();
			if($exsiccatiResult){
				$occidArr = $exsiccatiResult->occurrenceLinks->pluck('occid')->toArray();
				$matchType = 'exsiccati';
				$termsMatched = 'ometid: ' . $ometid . ', exsiccatiNumber: ' . $exsiccatiNumber;
			}
		}

		if(!$occidArr){
			if(!$recordedByLastName && $recordedBy){
				$recordedByLastName = OccurrenceHelper::parseLastName($recordedBy);
			}
			if($recordNumber && $recordedByLastName){
				//Check for exact duplicates
				$sql = 'SELECT occid FROM omoccurrences WHERE (MATCH(recordedby) AGAINST(?)) AND (recordnumber = ?)';
				$termArr = array();
				$termArr[] = $recordedByLastName;
				$termArr[] = $recordNumber;

				$occidResults = DB::select($sql, $termArr);
				$occidArr = array_map(function ($recordObj) { return $recordObj->occid; }, $occidResults);
				$matchType = 'exact';
				$termsMatched = 'recordedByLastName: ' . $recordedByLastName . ', recordNumber: ' . $recordNumber;
			}

			if(!$occidArr && $recordedByLastName){
				//Check for duplicate events
				$sql = 'SELECT occid FROM omoccurrences WHERE (MATCH(recordedby) AGAINST(?)) AND (processingstatus IS NULL OR processingstatus != "unprocessed" OR locality IS NOT NULL) ';
				$termArr = array();
				$termArr[] = $recordedByLastName;

				$termsMatched = 'recordedByLastName: ' . $recordedByLastName;
				$runQry = true;
				if($recordNumber){
					if(is_numeric($recordNumber)){
						$nStart = $recordNumber - 4;
						if($nStart < 1) $nStart = 1;
						$nEnd = $recordNumber + 4;
						$sql .= 'AND (recordnumber BETWEEN ? AND ?) ';
						$termArr[] = $nStart;
						$termArr[] = $nEnd;
						$termsMatched .= ', recordNumber: ' . $nStart . '-' . $nEnd;
					}
					elseif(preg_match('/^(\d+)-{0,1}[a-zA-Z]{1,2}$/', $recordNumber, $m)){
						//ex: 123a, 123b, 123-a
						$cNum = $m[1];
						$nStart = $cNum - 4;
						if($nStart < 1) $nStart = 1;
						$nEnd = $cNum + 4;
						$sql .= 'AND (CAST(recordnumber AS SIGNED) BETWEEN ? AND ?) ';
						$termArr[] = $nStart;
						$termArr[] = $nEnd;
						$termsMatched .= ', recordNumber: ' . $nStart . '-' . $nEnd;
					}
					elseif(preg_match('/^(\D+-?)(\d+)-{0,1}[a-zA-Z]{0,2}$/', $recordNumber, $m)){
						//RM-123, RM123
						$prefix = $m[1];
						$num = $m[2];
						$nStart = $num - 5;
						if($nStart < 1) $nStart = 1;
						$repeatCnt = 0;
						$termsMatched .= ', recordNumber: ';
						$del = '';
						for($x=1; $x<11; $x++){
							$term = $prefix . ($nStart + $x);
							$termArr[] = $term;
							$repeatCnt++;
							$termsMatched .= $del . $term;
							$del = ', ';
						}
						$sql .= 'AND recordnumber IN(' . trim(str_repeat(',?', $repeatCnt), ',') . ') ';
					}
					elseif(preg_match('/^(\d{2,4}-{1})(\d+)-{0,1}[a-zA-Z]{0,2}$/', $recordNumber, $m)){
						//95-123, 1995-123
						$prefix = $m[1];
						$num = $m[2];
						$nStart = $num - 5;
						if($nStart < 1) $nStart = 1;
						$repeatCnt = 0;
						$termsMatched .= ', recordNumber: ';
						$del = '';
						for($x=1; $x<11; $x++){
							$term = $prefix . ($nStart + $x);
							$termArr[] = $term;
							$repeatCnt++;
							$termsMatched .= $del . $term;
							$del = ', ';
						}
						$sql .= 'AND recordnumber IN(' . trim(str_repeat(',?', $repeatCnt), ',') . ') ';
					}
					else{
						$runQry = false;
					}
					if($eventDate){
						$sql .= 'AND (eventdate = ?) ';
						$termArr[] = $eventDate;
						$termsMatched .= ', eventDate: ' . $eventDate;
					}
				}
				elseif($eventDate){
					$sql .= 'AND (eventdate = ?) LIMIT 10';
					$termArr[] = $eventDate;
					$termsMatched .= ', eventDate: ' . $eventDate;
				}
				else{
					$runQry = false;
				}
				if($runQry){
					//echo $sql; exit;
					$occidResults = DB::select($sql, $termArr);
					$occidArr = array_map(function ($recordObj) { return $recordObj->occid; }, $occidResults);
					$matchType = 'event';
				}
			}
		}

		if($subjectOccid){
			//Get rid of subject occurrence
			if($pos = array_search($subjectOccid, $occidArr)){
				unset($occidArr[$pos]);
			}
		}

		$cnt = 0;
		$occurResult = null;
		$analyzedResult = null;
		$consensusResult = null;
		if($occidArr){
			$cnt = count($occidArr);
			$outputFields = array( 'occid', 'collid', 'catalogNumber', 'otherCatalogNumbers', 'identificationQualifier', 'identifiedBy', 'dateIdentified', 'identificationReferences',
				'identificationRemarks', 'taxonRemarks', 'locationID', 'georeferencedBy', 'georeferenceProtocol', 'georeferenceSources', 'georeferenceVerificationStatus', 'georeferenceRemarks' );
			$outputFields = array_merge($outputFields, $this->taxaFields);
			$outputFields = array_merge($outputFields, $this->siteFields);
			$occurResult = Occurrence::with('collection:collID,collectionName,institutionCode,collectionCode')->whereIn('occid', $occidArr)->get($outputFields);
			$analyzedResult = $this->getAnalysedDataset($occurResult, $matchType);
			$this->rankAgainstOrc($analyzedResult, $subjectOccid);
			$consensusResult = $this->getConsensusDataset($analyzedResult);
		}
		else{
			$matchType = '';
		}

		$resultObj = [
			'count' => $cnt,
			'matchType' => $matchType,
			'termsMatched' => $termsMatched,
			'results' => [
				'consensus' => $consensusResult,
				'ranked' => $analyzedResult,
				'raw' => $occurResult
			]
		];
		return response()->json($resultObj);
	}

	//Helper funcitons
	protected function getOccid($id){
		if(!is_numeric($id)){
			$occid = Occurrence::where('occurrenceID', $id)->orWhere('recordID', $id)->value('occid');
			if(is_numeric($occid)) $id = $occid;
		}
		return $id;
	}

	private function getAnalysedDataset($occurrenceDataset, $matchType){
		$returnDataset = array();
		foreach($occurrenceDataset as $occurrence){
			$occurrenceArr = $occurrence->toArray();
			unset($occurrenceArr['collection']);
			foreach($occurrenceArr as $fieldName => $fieldValue){
				if(in_array($fieldName, $this->siteFields)){
					$normalizedHash = $this->hashFieldValue($fieldValue);
					if($normalizedHash){
						if(isset($returnDataset[$fieldName][$normalizedHash])){
							$returnDataset[$fieldName][$normalizedHash]['count']++;
						}
						else{
							$returnDataset[$fieldName][$normalizedHash] = array('count' => 1, 'value' => $fieldValue);
						}
					}
				}
				if($matchType != 'event'){
					if(in_array($fieldName, $this->taxaFields)){
						$normalizedHash = $this->hashFieldValue($fieldValue);
						if($normalizedHash){
							if(isset($returnDataset[$fieldName][$normalizedHash])){
								$returnDataset[$fieldName][$normalizedHash]['count']++;
							}
							else{
								$returnDataset[$fieldName][$normalizedHash] = array('count' => 1, 'value' => $fieldValue);
							}
						}
					}
				}
			}
		}
		//Add ranking
		foreach($returnDataset as $fieldName => $fieldArr){
			$cntArr = array();
			foreach($fieldArr as $hash => $unitArr){
				$cntArr[] = $unitArr['count'];
			}
			$sd = 1;
			if(count($cntArr) > 1) $sd = $this->statsStandardSeviation($cntArr);
			foreach($fieldArr as $hash => $unitArr){
				$returnDataset[$fieldName][$hash]['rank'] = $sd * $unitArr['count'];
			}
		}
		//Sort by rank
		foreach($returnDataset as $fieldName => &$fieldArr){
			if(count($fieldArr) > 1){
				uasort($fieldArr, function($a, $b) {
					return $b['rank'] <=> $a['rank'];
				});
			}
		}
		return $returnDataset;
	}

	private function hashFieldValue($inputValue){
		$retValue = strtolower($inputValue);
		$retValue = preg_replace("/[^A-Za-z0-9]/", '', $retValue);
		if(!$retValue) return false;
		$retValue = hash('sha256', $retValue);
		return $retValue;
	}

	private function statsStandardSeviation(array $a) {
		$n = count($a);
		if ($n === 0) {
			trigger_error("The array has zero elements", E_USER_WARNING);
			return false;
		}
		$mean = array_sum($a) / $n;
		$carry = 0.0;
		foreach ($a as $val) {
			$d = ((double) $val) - $mean;
			$carry += $d * $d;
		}
		return sqrt($carry / $n);
	}

	private function getConsensusDataset($inputDataset){
		$consensusDataset = array();
		foreach($inputDataset as $fieldName => $fieldArr){
			foreach($fieldArr as $hash => $unitArr){
				if($unitArr['rank'] >= 0) $consensusDataset[$fieldName] = $unitArr['value'];
				break;
			}
		}
		return $consensusDataset;
	}

	private function rankAgainstOrc(&$analyzedResult, $occid){
		/*
		 * TODO: run an alignment / distance analysis
		 * https://www.let.rug.nl/kleiweg/lev/
		 * https://en.wikipedia.org/wiki/Longest_common_subsequence
		 *
		 */
		$retRank = 0;
		if($occid){
			$ocrStr = '';
			$ocrData = OccurrenceOcr::with('media')->whereHas('media', function($query) use ($occid) {
				$query->where('occid', $occid);
			})->get('rawStr');
			foreach($ocrData as $ocrRec){
				$ocrStr .= $this->normalizeText($ocrRec->rawStr);
			}
			foreach($analyzedResult as $fieldName => $fieldArr){
				foreach($fieldArr as $hash => $unitArr){
					$value = $this->normalizeText($unitArr['value']);
					if(strpos($ocrStr, $value) !== false){
						$analyzedResult[$fieldName][$hash]['rank'] *= 2;
						$methods = '';
						if(isset($analyzedResult[$fieldName][$hash]['methods'])) $methods = $analyzedResult[$fieldName][$hash]['methods'] . '; ';
						$analyzedResult[$fieldName][$hash]['methods'] = $methods . 'OCR match';
					}
				}
			}
		}
		return $retRank;
	}

	private function normalizeText($ocrIn){
		$ocrOut = strtolower($ocrIn);
		$ocrOut = preg_replace("/[^A-Za-z0-9 ]/", '', $ocrOut);
		$ocrOut = preg_replace("/[\s]/", ' ', $ocrOut);
		$ocrOut = preg_replace('/\s\s+/', ' ', $ocrOut);
		return $ocrOut;
	}
}
