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

	static function getCustomValues(array $qryArr, array $customFieldArr = []): array {
		$customValues = [];

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
				'predicate' => fn($v) => array_key_exists($v, $customFieldArr)
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
				if(($v = $qryArr[$key . $i] ?? null) && (!isset($mapping['predicate']) || $mapping['predicate']($v))) {
					$customValue[$mapping['field']] = $v;
				}
			}

			$field = $customValue['field'] ?? null;
			$term = $customValue['term'] ?? null;
			$value = $customValue['value'] ?? null;

			if($field && $term && ($value || in_array($term, ['IS_NULL', 'NOT_NULL']))) {
				$customValues[$i] = $customValue;
			}
		}

		return $customValues;
	}

	static function buildCustomWhere(array $customValues, array $customFieldArr): array {
		$sql = '';
		$binds = [];
		foreach($customValues as $customValue) {
			$field = $customFieldArr[$customValue['field']] ?? null;
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

				$sql .= $openParen .
					$field . ' ' . $compareOperator . ' ?' .
				$closeParen . ' ';

				if($customValue['term'] === 'STARTS_WITH') {
					$binds[] = $customValue['value'] . '%';
				} else if($customValue['term'] === 'NOT LIKE' || $customValue['term'] === 'LIKE') {
					$binds[] = '%' . $customValue['value'] . '%';
				}
			}
		}

		return [
			'sql' => $sql,
			'bindings' => $binds
		];
	}


	static function renderCustomInputs(): void {
		global $SERVER_ROOT;

		$MAX_CUSTOM_INPUTS = self::MAX_CUSTOM_INPUTS;
		$CUSTOM_TERMS = array_keys(self::OPERATOR_OPTIONS);
		$CUSTOM_VALUES = self::getCustomValues(
			$_REQUEST,
			[]
			/* Added in stacked pull request self::getOccurrenceFields() */
		);

		include($SERVER_ROOT . '/collections/editor/includes/customInput.php');
	}
}
