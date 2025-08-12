<?php

class CustomQuery {
	const MAX_CUSTOM_INPUTS = 8;

	const TERMS_OPTIONS = [
		'EQUALS',
		'NOT_EQUALS',
		'STARTS_WITH',
		'LIKE',
		'NOT_LIKE',
		'GREATER_THAN',
		'LESS_THAN',
		'IS_NULL',
		'NOT_NULL'
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
				'predicate' => fn($v) => in_array($v, self::TERMS_OPTIONS)
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
			foreach($map as $key => $mapping) {
				if(($v = $qryArr[$key . $i] ?? null) && (!isset($mapping['predicate']) || $mapping['predicate']($v))) {
					$customValues[$i][$mapping['field']] = $v;
				}
			}
		}

		return $customValues;
	}


	static function renderCustomInputs(): void {
		global $SERVER_ROOT;

		$MAX_CUSTOM_INPUTS = self::MAX_CUSTOM_INPUTS;
		$CUSTOM_TERMS = self::TERMS_OPTIONS;
		$CUSTOM_VALUES = self::getCustomValues(
			$_REQUEST,
			[]
			/* Added in stacked pull request self::getOccurrenceFields() */
		);

		include($SERVER_ROOT . '/collections/editor/includes/customInput.php');
	}
}
