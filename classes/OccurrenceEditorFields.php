<?php

class OccurrenceEditorFields {

	private $fieldMap = array();




	public function getFieldDiv($fieldName){
		$fieldLower = strtolower($fieldName);
		$value = '';
		if(isset($this->fieldMap[$fieldName]['value'])) $value = $this->fieldMap[$fieldName]['value'];
		$docTag = $fieldName;
		$length = 0;
		if(isset($this->fieldMap[$fieldName]['length'])) $fieldLength = $this->fieldMap[$fieldName]['length'];
		$onChange = '';
		if(isset($this->fieldMap[$fieldName]['js'])) $fieldLength = $this->fieldMap[$fieldName]['js'];

		$retStr = '
			<div id="' . $fieldName . '-div" class="field-div">
				<div class="flabel">
					<label for="' . $fieldLower . '">' . $LANG[strtoupper($fieldName)] . '</label>
					<a href="#" onclick="return dwcDoc(\'' . $docTag . '\')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a>
				</div>
				<input type="text" id="' . $fieldLower . '" name="' . $fieldLower . '" maxlength="' . $length . '" value="' . $value . '" onchange="' . $onChange . '" >
			</div>
		';
		return $retStr;
	}

	private function setFeidlMap(){

		$this->fieldMap['verbatimEventDate']['doc'] = 'verbatim-date';
		$this->fieldMap['verbatimEventDate']['change'] = 'verbatimEventDateChanged(this)';
	}

}
?>