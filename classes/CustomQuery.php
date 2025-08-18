<?php
include_once('utilities/Language.php');

class CustomQuery {
	const MAX_CUSTOM_INPUTS = 8;

	const OPERATOR_OPTIONS = [
		'EQUALS' => '=',
		'NOT_EQUALS' => '!=',
		'STARTS_WITH' => 'LIKE',
		'LIKE' => 'LIKE',
		'NOT_LIKE' => 'NOT LIKE',
		'GREATER_THAN' => '<',
		'LESS_THAN' => '>',
		'IS_NULL' => 'IS NULL',
		'NOT_NULL' => 'IS NOT NULL'
	];

	private static function parse_request(array $request, array $fieldFilter = []): array {
		if(!count($request)) {
			$request = $_REQUEST;
		}

		$customQueryRequest = [];

		$map = [
			'q_customandor' => [
				'field' => 'andor',
				'predicate' => fn($v) => ($v == 'AND' || $v== 'OR')
			],
			'q_customopenparen' => [
				'field' => 'openparen',
				'predicate' => fn($v) => preg_match('/^\({1,3}$/', $v)
			],
			'q_customfield' => [
				'field' => 'field',
				'predicate' => fn($v) => array_key_exists($v, $fieldFilter)
			],
			'q_customtype' => [
				'field' => 'term',
				'predicate' => fn($v) => array_key_exists($v, self::OPERATOR_OPTIONS)
			],
			'q_customvalue' => [
				'field' => 'value',
			],
			'q_customcloseparen' => [
				'field' => 'closeparen',
				'predicate' => fn($v) => preg_match('/^\){1,3}$/', $v)
			],
		];

		for($i = 1; $i <= self::MAX_CUSTOM_INPUTS; $i++) {
			$customValue = [];

			foreach($map as $key => $mapping) {
				if(($v = $request[$key . $i] ?? null) && (!isset($mapping['predicate']) || $mapping['predicate']($v))) {
					$customValue[$mapping['field']] = $v;
				}
			}

			$field = $customValue['field'] ?? null;
			$term = $customValue['term'] ?? null;
			$value = $customValue['value'] ?? null;

			if($field && $term && ($value || in_array($term, ['IS_NULL', 'NOT_NULL']))) {
				$customQueryRequest[$i] = $customValue;
			}
		}

		return $customQueryRequest;
	}

	static function buildCustomWhere(array $request, $tablePrefix ='', array $fieldFilter = []): array {
		if(!count($fieldFilter)) {
			$fieldFilter = self::getOccurrenceFields();
		}

		$customQueryRequest = self::parse_request($request, $fieldFilter);

		$sql = '';
		$binds = [];
		foreach($customQueryRequest as $customValue) {
			$field = $customValue['field'] ?? null;
			$andOr = $customValue['andor'] ?? null;
			$compareOperator = self::OPERATOR_OPTIONS[$customValue['term']] ?? null;
			$openParen = $customValue['openparen'] ?? '';
			$closeParen = $customValue['closeparen'] ?? '';

			if($field && $compareOperator) {
				if($sql) {
					if($andOr === 'AND') {
						$sql .= 'AND ';
					} else if($andOr) {
						$sql .= 'OR ';
					}
				}

				$bindValue = true;

				if($customValue['term'] === 'STARTS_WITH') {
					$binds[] = $customValue['value'] . '%';
				} else if($customValue['term'] === 'NOT_LIKE' || $customValue['term'] === 'LIKE') {
					$binds[] = '%' . $customValue['value'] . '%';
				} else if(!in_array($customValue['term'], ['IS_NULL', 'NOT_NULL'])) {
					$binds[] = $customValue['value'];
				} else {
					$bindValue = false;
				}

				$sql .= $openParen .
					($tablePrefix? $tablePrefix . '.': '') . $field . ' ' . $compareOperator . ($bindValue? ' ?':'') .
				$closeParen . ' ';
			}
		}

		return [
			'sql' => $sql,
			'bindings' => $binds
		];
	}

	static function getOccurrenceFields(): array {
		global $LANG;
		Language::load('collections/editor/includes/queryform');

		return array(
			'absoluteAge'=> $LANG['ABS_AGE'],
			'associatedCollectors'=> $LANG['ASSOC_COLLECTORS'],
			'associatedOccurrences'=> $LANG['ASSOC_OCCS'],
			'associatedTaxa'=> $LANG['ASSOC_TAXA'],
			'attributes' => $LANG['ATTRIBUTES'],
			'scientificNameAuthorship' => $LANG['AUTHOR'],
			'basisOfRecord'=>$LANG['BASIS_OF_RECORD'],
			'bed'=>$LANG['BED'],
			'behavior'=>$LANG['BEHAVIOR'],
			'biostratigraphy'=>$LANG['BIOSTRAT'],
			'biota' => $LANG['BIOTA'],
			'catalogNumber'=>$LANG['CAT_NUM'],
			'collectionCode'=>$LANG['COL_CODE'],
			'recordNumber'=>$LANG['COL_NUMBER'],
			'recordedBy'=>$LANG['COL_OBS'],
			'continent'=>$LANG['CONTINENT'],
			'coordinateUncertaintyInMeters'=>$LANG['COORD_UNCERT_M'],
			'country'=>$LANG['COUNTRY'],
			'county'=>$LANG['COUNTY'],
			'cultivationStatus'=>$LANG['CULT_STATUS'],
			'dataGeneralizations'=>$LANG['DATA_GEN'],
			'eventDate'=>$LANG['DATE'],
			'eventDate2'=> $LANG['DATE2'],
			'dateEntered'=>$LANG['DATE_ENTERED'],
			'dateLastModified'=>$LANG['DATE_LAST_MODIFIED'],'dbpk'=>$LANG['DBPK'],'decimalLatitude'=>$LANG['DEC_LAT'],
			'decimalLongitude'=>$LANG['DEC_LONG'],
			'maximumDepthInMeters'=>$LANG['DEPTH_MAX'],'minimumDepthInMeters'=>$LANG['DEPTH_MIN'],
			'verbatimAttributes'=>$LANG['DESCRIPTION'],
			'disposition'=>$LANG['DISPOSITION'],
			'dynamicProperties'=>$LANG['DYNAMIC_PROPS'],
			'earlyInterval'=>$LANG['EARLY_INT'],
			'element'=>$LANG['ELEMENT'],
			'maximumElevationInMeters'=>$LANG['ELEV_MAX_M'],
			'minimumElevationInMeters'=>$LANG['ELEV_MIN_M'],
			'establishmentMeans'=>$LANG['ESTAB_MEANS'],
			'family'=>$LANG['FAMILY'],
			'fieldNotes'=>$LANG['FIELD_NOTES'],
			'fieldnumber'=>$LANG['FIELD_NUMBER'],
			'formation'=>$LANG['FORMATION'],
			'geodeticDatum'=>$LANG['GEO_DATUM'],
			'georeferenceProtocol'=>$LANG['GEO_PROTOCOL'],
			'geologicalContextID'=>$LANG['GEO_CONTEXT_ID'],
			'georeferenceRemarks'=>$LANG['GEO_REMARKS'],
			'georeferenceSources'=>$LANG['GEO_SOURCES'],
			'georeferenceVerificationStatus'=>$LANG['GEO_VERIF_STATUS'],
			'georeferencedBy'=>$LANG['GEO_BY'],
			'lithogroup'=>$LANG['GROUP'],
			'habitat'=>$LANG['HABITAT'],
			'identificationQualifier'=>$LANG['ID_QUALIFIER'],
			'identificationReferences'=>$LANG['ID_REFERENCES'],
			'identificationRemarks'=>$LANG['ID_REMARKS'],
			'identifiedBy'=>$LANG['IDED_BY'],
			'individualCount'=>$LANG['IND_COUNT'],
			'identifierName' => $LANG['IDENTIFIER_TAG_NAME'],
			'identifierValue' => $LANG['IDENTIFIER_TAG_VALUE'],
			'informationWithheld'=>$LANG['INFO_WITHHELD'],
			'institutionCode'=>$LANG['INST_CODE'],
			'island'=>$LANG['ISLAND'],
			'islandgroup'=>$LANG['ISLAND_GROUP'],
			'labelProject'=>$LANG['LAB_PROJECT'],
			'language'=>$LANG['LANGUAGE'],
			'lateInterval'=>$LANG['LATE_INT'],
			'lifeStage'=>$LANG['LIFE_STAGE'],
			'lithology'=>$LANG['LITHOLOGY'],
			'locationid'=>$LANG['LOCATION_ID'],
			'locality'=>$LANG['LOCALITY'],
			'recordSecurity'=>$LANG['SECURITY'],
			'securityReason'=>$LANG['SECURITY_REASON'],
			'localStage'=>$LANG['LOCAL_STAGE'],
			'locationRemarks'=>$LANG['LOC_REMARKS'],
			'member'=>$LANG['MEMBER'],
			'username'=>$LANG['MODIFIED_BY'],
			'municipality'=>$LANG['MUNICIPALITY'],
			'occurrenceRemarks'=>$LANG['NOTES_REMARKS'],
			'ocrFragment'=>$LANG['OCR_FRAGMENT'],
			'otherCatalogNumbers'=>$LANG['OTHER_CAT_NUMS'],
			'ownerInstitutionCode'=>$LANG['OWNER_CODE'],
			'preparations'=>$LANG['PREPARATIONS'],
			'reproductiveCondition'=>$LANG['REP_COND'],
			'samplingEffort'=>$LANG['SAMP_EFFORT'],
			'samplingProtocol'=>$LANG['SAMP_PROTOCOL'],
			'sciname'=>$LANG['SCI_NAME'],
			'sex'=>$LANG['SEX'],
			'slideProperties'=>$LANG['SLIDE_PROP'],
			'stage'=>$LANG['STAGE'],
			'stateProvince'=>$LANG['STATE_PROVINCE'],
			'stratRemarks'=>$LANG['STRAT_REMARKS'],
			'substrate'=>$LANG['SUBSTRATE'],
			'taxonEnvironment'=>$LANG['TAXON_ENVIRONMENT'],
			'taxonRemarks'=>$LANG['TAXON_REMARKS'],
			'typeStatus'=>$LANG['TYPE_STATUS'],
			'verbatimCoordinates'=>$LANG['VERBAT_COORDS'],
			'verbatimEventDate'=>$LANG['VERBATIM_DATE'],
			'verbatimDepth'=>$LANG['VERBATIM_DEPTH'],
			'verbatimElevation'=>$LANG['VERBATIM_ELE'],
			'waterbody'=> $LANG['WATER_BODY']
		);
	}

	static function renderCustomInputs(array $customFields = []): void {
		global $SERVER_ROOT;

		if(!count($customFields)) {
			$CUSTOM_FIELDS = self::getOccurrenceFields();
		} else {
			$CUSTOM_FIELDS = $customFields;
		}

		$MAX_CUSTOM_INPUTS = self::MAX_CUSTOM_INPUTS;
		$CUSTOM_TERMS = array_keys(self::OPERATOR_OPTIONS);
		$CUSTOM_VALUES = self::parse_request(
			$_REQUEST,
			$CUSTOM_FIELDS,
		);

		include($SERVER_ROOT . '/collections/editor/includes/customInput.php');
	}
}
