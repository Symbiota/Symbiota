<?php
global $LANG, $LANG_TAG, $SERVER_ROOT;

if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.en.php');

// Pass in through function scope;
$MAX_CUSTOM_INPUTS = $MAX_CUSTOM_INPUTS ?? 8;
$CUSTOM_TERMS = $CUSTOM_TERMS ?? [];
$CUSTOM_VALUES = $CUSTOM_VALUES ?? [];
$CUSTOM_FIELDS = $CUSTOM_FIELDS ?? array(
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

function selected(bool $v) {
	return $v ? 'SELECTED': '';
}

function onChange($index) {
	return 'customSelectChanged(' .  $index .')';
}
?> 

<div style="display:flex; flex-direction: column; gap:1rem;">
	<?php for($index = 1; $index <= $MAX_CUSTOM_INPUTS; $index++): ?>

	<?php
	$cAndOr = $CUSTOM_VALUES[$index]['andor'] ?? 'AND';
	$cOpenParen = $CUSTOM_VALUES[$index]['openparen'] ?? null;
	$cField = $CUSTOM_VALUES[$index]['field'] ?? null;
	$cTerm = $CUSTOM_VALUES[$index]['term'] ?? null;
	$cValue = $CUSTOM_VALUES[$index]['value'] ?? null;
	$cCloseParen = $CUSTOM_VALUES[$index]['closeparen'] ?? null;
	$divDisplay = 'none';

	if($index == 1 || $cValue != '' || $cTerm == 'IS_NULL' || $cTerm == 'NOT_NULL') {
		$divDisplay = 'flex';
	}
	?>

	<div id="customdiv<?= $index ?>" style="align-items:center; gap:0.5rem; display: <?= $divDisplay ?>" >
		<?= $LANG['CUSTOM_FIELD'] . ' ' . $index; ?>:
		<?php if($index > 1): ?> 
		<select 
			name="q_customandor<?= $index ?>" 
			onchange="<?= onChange($index) ?>"
		>
			<option value="AND">
				<?= $LANG['AND'] ?>
			</option>
			<option <?= selected($cAndOr == 'OR') ?> value="OR">
				<?php echo $LANG['OR']; ?>
			</option>
		</select>
		<?php endif ?> 

		<select name="q_customopenparen<?= $index ?>" 
			onchange="<?= onChange($index) ?>"
			aria-label="<?= $LANG['OPEN_PAREN_FIELD']; ?>">
			<option value="">---</option>
			<option value="(" <?= selected($cOpenParen == '(') ?>>(</option>
			
			<?php if($index < ($MAX_CUSTOM_INPUTS - 1)): ?>
			<option value="((" <?= selected($cOpenParen == '((') ?>>((</option>
			<?php endif ?>

			<?php if($index < $MAX_CUSTOM_INPUTS): ?>
			<option value="(((" <?= selected($cOpenParen == '(((') ?>>(((</option>
			<?php endif ?>
		</select>

		<select name="q_customfield<?= $index; ?>" 
			onchange="<?= onChange($index) ?>"
			aria-label="<?= $LANG['CRITERIA']; ?>">
			<option value=""><?= $LANG['SELECT_FIELD_NAME']; ?></option>
			<option value="">---------------------------------</option>
			<?php foreach($CUSTOM_FIELDS as $k => $v): ?>
			<option value="<?= $k ?>" <?= selected($k == $cField) ?>>
				<?= $v ?>
			</option>
			<?php endforeach?>
		</select>

		<select name="q_customtype<?= $index; ?>" aria-label="<?= $LANG['CONDITION']; ?>">
			<?php foreach($CUSTOM_TERMS as $term): ?>
			<option <?= selected($cTerm == $term)?> value="<?= $term ?>">
				<?= $LANG[$term] ?>
			</option>
			<?php endforeach ?>
		</select>

		<input name="q_customvalue<?= $index; ?>" type="text" value="<?= $cValue; ?>" style="width:200px; margin:0; padding-top: 0; padding-bottom: 0;" aria-label="<?= $LANG['CRITERIA']; ?>"/>

		<select name="q_customcloseparen<?= $index ?>" 
			onchange="<?= onChange($index) ?>"
			aria-label="<?= $LANG['CLOSE_PAREN_FIELD']; ?>">
			<option value="">---</option>
			<option value=")" <?= selected($cCloseParen == ')') ?>>)</option>
			
			<?php if($index < ($MAX_CUSTOM_INPUTS - 1)): ?>
			<option value="))" <?= selected($cCloseParen == '))') ?>>))</option>
			<?php endif ?>

			<?php if($index < $MAX_CUSTOM_INPUTS): ?>
			<option value=")))" <?= selected($cCloseParen == ')))') ?>>)))</option>
			<?php endif ?>
		</select>

		<?php if($index < $MAX_CUSTOM_INPUTS): ?>
		<a href="#" style="height:1.2em;" onclick="if(document.getElementById('customdiv<?= $index +1 ?>')) {document.getElementById('customdiv<?= $index +1 ?>').style.display='flex'};return false;"><img class="editimg" src="../../images/plus.png" style="display:inline-block;width:1.2em;height:1.2em;" alt="<?php echo htmlspecialchars($LANG['ADD_CUSTOM_FIELD'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" /></a>

		<?php endif ?>

		<?php if($index > 1): ?>
		<a href="#" style="height:1.2em;" onclick="if(document.getElementById('customdiv<?= $index ?>')) {document.getElementById('customdiv<?= $index ?>').style.display='none'};return false;">
		<img class="editimg" src="../../images/minus.png" style="display:inline-block;width:1.2em;" alt="<?php echo htmlspecialchars($LANG['ADD_CUSTOM_FIELD'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" />
		</a>
		<?php endif ?>
	</div>
	<?php endfor ?>
</div>
