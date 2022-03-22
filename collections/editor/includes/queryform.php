<?php
if(!$displayQuery && array_key_exists('displayquery',$_REQUEST)) $displayQuery = $_REQUEST['displayquery'];
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.en.php');

$qryArr = $occManager->getQueryVariables();
// Construct a link containing the queryform search parameters
$queryLink = '?displayquery=1&collid='.$collId.'&'.http_build_query($qryArr, '', '&amp;');

$qCatalogNumber = (array_key_exists('cn',$qryArr)?$qryArr['cn']:'');
$qOtherCatalogNumbers = (array_key_exists('ocn',$qryArr)?$qryArr['ocn']:'');
$qRecordedBy = (array_key_exists('rb',$qryArr)?$qryArr['rb']:'');
$qRecordNumber = (array_key_exists('rn',$qryArr)?$qryArr['rn']:'');
$qEventDate = (array_key_exists('ed',$qryArr)?$qryArr['ed']:'');
$qRecordEnteredBy = (array_key_exists('eb',$qryArr)?$qryArr['eb']:'');
$qReturnAll = (array_key_exists('returnall',$qryArr)?$qryArr['returnall']:0);
$qProcessingStatus = (array_key_exists('ps',$qryArr)?$qryArr['ps']:'');
$qDateEntered = (array_key_exists('de',$qryArr)?$qryArr['de']:'');
$qDateLastModified = (array_key_exists('dm',$qryArr)?$qryArr['dm']:'');
$qExsiccatiId = (array_key_exists('exsid',$qryArr)?$qryArr['exsid']:'');
$qImgOnly = (array_key_exists('io',$qryArr)?$qryArr['io']:0);
$qWithoutImg = (array_key_exists('woi',$qryArr)?$qryArr['woi']:0);
$qCustomOpenParen1 = (array_key_exists('cop1',$qryArr)?$qryArr['cop1']:'');
$qCustomField1 = (array_key_exists('cf1',$qryArr)?$qryArr['cf1']:'');
$qCustomType1 = (array_key_exists('ct1',$qryArr)?$qryArr['ct1']:'');
$qCustomValue1 = (array_key_exists('cv1',$qryArr)?htmlentities($qryArr['cv1'], ENT_COMPAT, $CHARSET):'');
$qCustomCloseParen1 = (array_key_exists('ccp1',$qryArr)?$qryArr['ccp1']:'');
$qCustomAndOr2 = (array_key_exists('cao2',$qryArr)?$qryArr['cao2']:'');
$qCustomOpenParen2 = (array_key_exists('cop2',$qryArr)?$qryArr['cop2']:'');
$qCustomField2 = (array_key_exists('cf2',$qryArr)?$qryArr['cf2']:'');
$qCustomType2 = (array_key_exists('ct2',$qryArr)?$qryArr['ct2']:'');
$qCustomValue2 = (array_key_exists('cv2',$qryArr)?htmlentities($qryArr['cv2'], ENT_COMPAT, $CHARSET):'');
$qCustomCloseParen2 = (array_key_exists('ccp2',$qryArr)?$qryArr['ccp2']:'');
$qCustomAndOr3 = (array_key_exists('cao3',$qryArr)?$qryArr['cao3']:'');
$qCustomOpenParen3 = (array_key_exists('cop3',$qryArr)?$qryArr['cop3']:'');
$qCustomField3 = (array_key_exists('cf3',$qryArr)?$qryArr['cf3']:'');
$qCustomType3 = (array_key_exists('ct3',$qryArr)?$qryArr['ct3']:'');
$qCustomValue3 = (array_key_exists('cv3',$qryArr)?htmlentities($qryArr['cv3'], ENT_COMPAT, $CHARSET):'');
$qCustomCloseParen3 = (array_key_exists('ccp3',$qryArr)?$qryArr['ccp3']:'');
$qCustomAndOr4 = (array_key_exists('cao4',$qryArr)?$qryArr['cao4']:'');
$qCustomOpenParen4 = (array_key_exists('cop4',$qryArr)?$qryArr['cop4']:'');
$qCustomField4 = (array_key_exists('cf4',$qryArr)?$qryArr['cf4']:'');
$qCustomType4 = (array_key_exists('ct4',$qryArr)?$qryArr['ct4']:'');
$qCustomValue4 = (array_key_exists('cv4',$qryArr)?htmlentities($qryArr['cv4']):'');
$qCustomCloseParen4 = (array_key_exists('ccp4',$qryArr)?$qryArr['ccp4']:'');
$qCustomAndOr5 = (array_key_exists('cao5',$qryArr)?$qryArr['cao5']:'');
$qCustomOpenParen5 = (array_key_exists('cop5',$qryArr)?$qryArr['cop5']:'');
$qCustomField5 = (array_key_exists('cf5',$qryArr)?$qryArr['cf5']:'');
$qCustomType5 = (array_key_exists('ct5',$qryArr)?$qryArr['ct5']:'');
$qCustomValue5 = (array_key_exists('cv5',$qryArr)?htmlentities($qryArr['cv5']):'');
$qCustomCloseParen5 = (array_key_exists('ccp5',$qryArr)?$qryArr['ccp5']:'');
$qCustomAndOr6 = (array_key_exists('cao6',$qryArr)?$qryArr['cao6']:'');
$qCustomOpenParen6 = (array_key_exists('cop6',$qryArr)?$qryArr['cop6']:'');
$qCustomField6 = (array_key_exists('cf6',$qryArr)?$qryArr['cf6']:'');
$qCustomType6 = (array_key_exists('ct6',$qryArr)?$qryArr['ct6']:'');
$qCustomValue6 = (array_key_exists('cv6',$qryArr)?htmlentities($qryArr['cv6']):'');
$qCustomCloseParen6 = (array_key_exists('ccp6',$qryArr)?$qryArr['ccp6']:'');
$qCustomAndOr7 = (array_key_exists('cao7',$qryArr)?$qryArr['cao7']:'');
$qCustomOpenParen7 = (array_key_exists('cop7',$qryArr)?$qryArr['cop7']:'');
$qCustomField7 = (array_key_exists('cf7',$qryArr)?$qryArr['cf7']:'');
$qCustomType7 = (array_key_exists('ct7',$qryArr)?$qryArr['ct7']:'');
$qCustomValue7 = (array_key_exists('cv7',$qryArr)?htmlentities($qryArr['cv7']):'');
$qCustomCloseParen7 = (array_key_exists('ccp7',$qryArr)?$qryArr['ccp7']:'');
$qCustomAndOr8 = (array_key_exists('cao8',$qryArr)?$qryArr['cao8']:'');
$qCustomOpenParen8 = (array_key_exists('cop8',$qryArr)?$qryArr['cop8']:'');
$qCustomField8 = (array_key_exists('cf8',$qryArr)?$qryArr['cf8']:'');
$qCustomType8 = (array_key_exists('ct8',$qryArr)?$qryArr['ct8']:'');
$qCustomValue8 = (array_key_exists('cv8',$qryArr)?htmlentities($qryArr['cv8']):'');
$qCustomCloseParen8 = (array_key_exists('ccp8',$qryArr)?$qryArr['ccp8']:'');
$qOcrFrag = (array_key_exists('ocr',$qryArr)?htmlentities($qryArr['ocr'], ENT_COMPAT, $CHARSET):'');
$qOrderBy = (array_key_exists('orderby',$qryArr)?$qryArr['orderby']:'');
$qOrderByDir = (array_key_exists('orderbydir',$qryArr)?$qryArr['orderbydir']:'');

//Set processing status
$processingStatusArr = array();
if(isset($PROCESSINGSTATUS) && $PROCESSINGSTATUS){
	$processingStatusArr = $PROCESSINGSTATUS;
}
else{
	$processingStatusArr = array('unprocessed','unprocessed/NLP','stage 1','stage 2','stage 3','pending review-nfn','pending review','expert required','reviewed','closed');
}
//if(!isset($_REQUEST['q_catalognumber'])) $displayQuery = true;
?>
<div id="querydiv" style="clear:both;width:920px;display:<?php echo ($displayQuery?'block':'none'); ?>;">
	<form name="queryform" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return verifyQueryForm(this)">
		<fieldset style="padding:5px;">
			<legend><?php echo $LANG['RECORD_SEARCH_FORM']; ?></legend>
			<?php
			if(!$crowdSourceMode){
				?>
				<div class="fieldGroupDiv">
					<div class="fieldDiv" title="<?php echo $LANG['WILD_EXPLAIN']; ?>">
						<?php echo $LANG['COLLECTOR']; ?>:
						<input type="text" name="q_recordedby" value="<?php echo $qRecordedBy; ?>" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['SEPARATE_RANGES']; ?>">
						<?php echo $LANG['NUMBER']; ?>:
						<input type="text" name="q_recordnumber" value="<?php echo $qRecordNumber; ?>" style="width:120px;" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['ENTER_RANGES']; ?>">
						<?php echo $LANG['DATE']; ?>:
						<input type="text" name="q_eventdate" value="<?php echo $qEventDate; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<?php
			}
			?>
			<div class="fieldGroupDiv">
				<div class="fieldDiv" title="<?php echo $LANG['SEPARATE_RANGES']; ?>">
					<?php echo $LANG['CAT_NUM']; ?>:
					<input type="text" name="q_catalognumber" value="<?php echo $qCatalogNumber; ?>" onchange="setOrderBy(this)" />
				</div>
				<?php
				if($crowdSourceMode){
					?>
					<div class="fieldDiv" title="Search for term embedded within OCR block of text">
						<?php echo $LANG['OCR_FRAGMENT']; ?>:
						<input type="text" name="q_ocrfrag" value="<?php echo $qOcrFrag; ?>" style="width:200px;" />
					</div>
					<?php
				}
				else{
					?>
					<div class="fieldDiv" title="<?php echo $LANG['SEPARATE_RANGES']; ?>">
						<?php echo $LANG['OTHER_CAT_NUMS']; ?>:
						<input type="text" name="q_othercatalognumbers" value="<?php echo $qOtherCatalogNumbers; ?>" />
					</div>
					<?php
				}
				?>
			</div>
			<?php
			if(!$crowdSourceMode){
				?>
				<div class="fieldGroupDiv">
					<div class="fieldDiv" style="<?php echo ($isGenObs?'display:none':''); ?>">
						<?php echo $LANG['ENTERED_BY']; ?>:
						<input type="text" name="q_recordenteredby" value="<?php echo $qRecordEnteredBy; ?>" style="width:70px;" onchange="setOrderBy(this)" />
						<button type="button" onclick="enteredByCurrentUser()" style="font-size:70%" title="<?php echo $LANG['LIMIT_TO_CURRENT']; ?>"><?php echo $LANG['CU']; ?></button>
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['ENTER_RANGES']; ?>">
						<?php echo $LANG['DATE_ENTERED']; ?>:
						<input type="text" name="q_dateentered" value="<?php echo $qDateEntered; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['ENTER_RANGES']; ?>">
						<?php echo $LANG['DATE_MODIFIED']; ?>:
						<input type="text" name="q_datelastmodified" value="<?php echo $qDateLastModified; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<?php echo $LANG['PROC_STATUS']; ?>:
						<select name="q_processingstatus" onchange="setOrderBy(this)">
							<option value=''><?php echo $LANG['ALL_RECORDS']; ?></option>
							<option>-------------------</option>
							<?php
							foreach($processingStatusArr as $v){
								//Don't display these options is editor is crowd sourced
								$keyOut = strtolower($v);
								echo '<option value="'.$keyOut.'" '.($qProcessingStatus==$keyOut?'SELECTED':'').'>'.ucwords($v).'</option>';
							}
							echo '<option value="isnull" '.($qProcessingStatus=='isnull'?'SELECTED':'').'>'.$LANG['NO_SET_STATUS'].'</option>';
							if($qProcessingStatus && $qProcessingStatus != 'isnull' && !in_array($qProcessingStatus,$processingStatusArr)){
								echo '<option value="'.$qProcessingStatus.'" SELECTED>'.$qProcessingStatus.'</option>';
							}
							?>
						</select>
					</div>
					<div class="fieldDiv">
						<input name="q_imgonly" type="checkbox" value="1" <?php echo ($qImgOnly==1?'checked':''); ?> onchange="this.form.q_withoutimg.checked = false;" />
						<?php echo $LANG['WITH_IMAGES']; ?>
					</div>
					<div class="fieldDiv">
						<input name="q_withoutimg" type="checkbox" value="1" <?php echo ($qWithoutImg==1?'checked':''); ?> onchange="this.form.q_imgonly.checked = false;" />
						<?php echo $LANG['WITHOUT_IMAGES']; ?>
					</div>
				</div>
				<?php
				if($ACTIVATE_EXSICCATI){
					if($exsList = $occManager->getExsiccatiList()){
						?>
						<div class="fieldGroupDiv" title="<?php echo $LANG['ENTER_EXS_TITLE']; ?>">
							<div class="fieldDiv">
								<?php echo $LANG['EXS_TITLE']; ?>:
								<select name="q_exsiccatiid" style="max-width:650px">
									<option value=""></option>
									<?php
									foreach($exsList as $exsID => $exsTitle){
										echo '<option value="'.$exsID.'" '.($qExsiccatiId==$exsID?'SELECTED':'').'>'.$exsTitle.'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<?php
					}
				}
			}
			$advFieldArr = array();
			if($crowdSourceMode){
				$advFieldArr = array('family'=>$LANG['FAMILY'],'sciname'=>$LANG['SCI_NAME'],'othercatalognumbers'=>$LANG['OTHER_CAT_NUMS'],
					'country'=>$LANG['COUNTRY'],'stateProvince'=>$LANG['STATE_PROVINCE'],'county'=>$LANG['COUNTY'],'municipality'=>$LANG['MUNICIPALITY'],
					'recordedby'=>$LANG['COLLECTOR'],'recordnumber'=>$LANG['COL_NUMBER'],'eventdate'=>$LANG['COL_DATE']);
			}
			else{
				$advFieldArr = array('associatedCollectors'=>$LANG['ASSOC_COLLECTORS'],'associatedOccurrences'=>$LANG['ASSOC_OCCS'],
					'associatedTaxa'=>$LANG['ASSOC_TAXA'],'attributes'=>$LANG['ATTRIBUTES'],'scientificNameAuthorship'=>$LANG['AUTHOR'],
					'basisOfRecord'=>$LANG['BASIS_OF_RECORD'],'behavior'=>$LANG['BEHAVIOR'],'catalogNumber'=>$LANG['CAT_NUM'],'collectionCode'=>$LANG['COL_CODE'],'recordNumber'=>$LANG['COL_NUMBER'],
					'recordedBy'=>$LANG['COL_OBS'],'coordinateUncertaintyInMeters'=>$LANG['COORD_UNCERT_M'],'country'=>$LANG['COUNTRY'],
					'county'=>$LANG['COUNTY'],'cultivationStatus'=>$LANG['CULT_STATUS'],'dataGeneralizations'=>$LANG['DATA_GEN'],'eventDate'=>$LANG['DATE'],
					'dateEntered'=>$LANG['DATE_ENTERED'],'dateLastModified'=>$LANG['DATE_LAST_MODIFIED'],'dbpk'=>$LANG['DBPK'],'decimalLatitude'=>$LANG['DEC_LAT'],
					'decimalLongitude'=>$LANG['DEC_LONG'],'maximumDepthInMeters'=>$LANG['DEPTH_MAX'],'minimumDepthInMeters'=>$LANG['DEPTH_MIN'],
					'verbatimAttributes'=>$LANG['DESCRIPTION'],'disposition'=>$LANG['DISPOSITION'],'dynamicProperties'=>$LANG['DYNAMIC_PROPS'],
					'maximumElevationInMeters'=>$LANG['ELEV_MAX_M'],'minimumElevationInMeters'=>$LANG['ELEV_MIN_M'],
					'establishmentMeans'=>$LANG['ESTAB_MEANS'],'family'=>$LANG['FAMILY'],'fieldNotes'=>$LANG['FIELD_NOTES'],'fieldnumber'=>$LANG['FIELD_NUMBER'],
					'geodeticDatum'=>$LANG['GEO_DATUM'],'georeferenceProtocol'=>$LANG['GEO_PROTOCOL'],
					'georeferenceRemarks'=>$LANG['GEO_REMARKS'],'georeferenceSources'=>$LANG['GEO_SOURCES'],
					'georeferenceVerificationStatus'=>$LANG['GEO_VERIF_STATUS'],'georeferencedBy'=>$LANG['GEO_BY'],'habitat'=>$LANG['HABITAT'],
					'identificationQualifier'=>$LANG['ID_QUALIFIER'],'identificationReferences'=>$LANG['ID_REFERENCES'],
					'identificationRemarks'=>$LANG['ID_REMARKS'],'identifiedBy'=>$LANG['IDED_BY'],'individualCount'=>$LANG['IND_COUNT'],
					'informationWithheld'=>$LANG['INFO_WITHHELD'],'institutionCode'=>$LANG['INST_CODE'],'labelProject'=>$LANG['LAB_PROJECT'],
					'language'=>$LANG['LANGUAGE'],'lifeStage'=>$LANG['LIFE_STAGE'],'locationid'=>$LANG['LOCATION_ID'],'locality'=>$LANG['LOCALITY'],
					'localitySecurity'=>$LANG['LOC_SEC'],'localitySecurityReason'=>$LANG['LOC_SEC_REASON'],'locationRemarks'=>$LANG['LOC_REMARKS'],
					'username'=>$LANG['MODIFIED_BY'],'municipality'=>$LANG['MUNICIPALITY'],'occurrenceRemarks'=>$LANG['NOTES_REMARKS'],'ocrFragment'=>$LANG['OCR_FRAGMENT'],
					'otherCatalogNumbers'=>$LANG['OTHER_CAT_NUMS'],'ownerInstitutionCode'=>$LANG['OWNER_CODE'],'preparations'=>$LANG['PREPARATIONS'],
					'reproductiveCondition'=>$LANG['REP_COND'],'samplingEffort'=>$LANG['SAMP_EFFORT'],'samplingProtocol'=>$LANG['SAMP_PROTOCOL'],
					'sciname'=>$LANG['SCI_NAME'],'sex'=>$LANG['SEX'],'stateProvince'=>$LANG['STATE_PROVINCE'],
					'substrate'=>$LANG['SUBSTRATE'],'taxonRemarks'=>$LANG['TAXON_REMARKS'],'typeStatus'=>$LANG['TYPE_STATUS'],'verbatimCoordinates'=>$LANG['VERBAT_COORDS'],
					'verbatimEventDate'=>$LANG['VERBATIM_DATE'],'verbatimDepth'=>$LANG['VERBATIM_DEPTH'],'verbatimElevation'=>$LANG['VERBATIM_ELE']);
			}
			// sort($advFieldArr);
			?>
			<div class="fieldGroupDiv">
				<?php echo $LANG['CUSTOM_FIELD_1']; ?>:
				<select name="q_customopenparen1" onchange="customSelectChanged(1)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen1=='('?'SELECTED':''); ?> value="(">(</option>
					<option <?php echo ($qCustomOpenParen1=='(('?'SELECTED':''); ?> value="((">((</option>
					<option <?php echo ($qCustomOpenParen1=='((('?'SELECTED':''); ?> value="(((">(((</option>
				</select>
				<select name="q_customfield1" onchange="customSelectChanged(1)">
					<option value=""><?php echo $LANG['SELECT_FIELD_NAME']; ?></option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField1?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype1">
					<option><?php echo $LANG['EQUALS']; ?></option>
					<option <?php echo ($qCustomType1=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS"><?php echo $LANG['NOT_EQUALS']; ?></option>
					<option <?php echo ($qCustomType1=='STARTS'?'SELECTED':''); ?> value="STARTS"><?php echo $LANG['STARTS_WITH']; ?></option>
					<option <?php echo ($qCustomType1=='LIKE'?'SELECTED':''); ?> value="LIKE"><?php echo $LANG['CONTAINS']; ?></option>
					<option <?php echo ($qCustomType1=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE"><?php echo $LANG['DOESNT_CONTAIN']; ?></option>
					<option <?php echo ($qCustomType1=='GREATER'?'SELECTED':''); ?> value="GREATER"><?php echo $LANG['GREATER_THAN']; ?></option>
					<option <?php echo ($qCustomType1=='LESS'?'SELECTED':''); ?> value="LESS"><?php echo $LANG['LESS_THAN']; ?></option>
					<option <?php echo ($qCustomType1=='NULL'?'SELECTED':''); ?> value="NULL"><?php echo $LANG['IS_NULL']; ?></option>
					<option <?php echo ($qCustomType1=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL"><?php echo $LANG['IS_NOT_NULL']; ?></option>
				</select>
				<input name="q_customvalue1" type="text" value="<?php echo $qCustomValue1; ?>" style="width:200px;" />
				<select name="q_customcloseparen1" onchange="customSelectChanged(1)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen1==')'?'SELECTED':''); ?> value=")">)</option>
				</select>
				<a href="#" onclick="toggleCustomDiv2();return false;">
					<img class="editimg" src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv2" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue2||$qCustomType2=='NULL'||$qCustomType2=='NOTNULL'?'block':'none');?>;">
				<?php echo $LANG['CUSTOM_FIELD_2']; ?>:
				<select name="q_customandor2" onchange="customSelectChanged(2)">
					<option>AND</option>
					<option <?php echo ($qCustomAndOr2=='OR'?'SELECTED':''); ?> value="OR">OR</option>
				</select>
				<select name="q_customopenparen2" onchange="customSelectChanged(2)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen2=='('?'SELECTED':''); ?> value="(">(</option>
					<option <?php echo ($qCustomOpenParen2=='(('?'SELECTED':''); ?> value="((">((</option>
					<option <?php echo ($qCustomOpenParen2=='((('?'SELECTED':''); ?> value="(((">(((</option>
				</select>
				<select name="q_customfield2" onchange="customSelectChanged(2)">
					<option value=""><?php echo $LANG['SELECT_FIELD_NAME']; ?></option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField2?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype2">
					<option><?php echo $LANG['EQUALS']; ?></option>
					<option <?php echo ($qCustomType2=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS"><?php echo $LANG['NOT_EQUALS']; ?></option>
					<option <?php echo ($qCustomType2=='STARTS'?'SELECTED':''); ?> value="STARTS"><?php echo $LANG['STARTS_WITH']; ?></option>
					<option <?php echo ($qCustomType2=='LIKE'?'SELECTED':''); ?> value="LIKE"><?php echo $LANG['CONTAINS']; ?></option>
					<option <?php echo ($qCustomType2=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE"><?php echo $LANG['DOESNT_CONTAIN']; ?></option>
					<option <?php echo ($qCustomType2=='GREATER'?'SELECTED':''); ?> value="GREATER"><?php echo $LANG['GREATER_THAN']; ?></option>
					<option <?php echo ($qCustomType2=='LESS'?'SELECTED':''); ?> value="LESS"><?php echo $LANG['LESS_THAN']; ?></option>
					<option <?php echo ($qCustomType2=='NULL'?'SELECTED':''); ?> value="NULL"><?php echo $LANG['IS_NULL']; ?></option>
					<option <?php echo ($qCustomType2=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL"><?php echo $LANG['IS_NOT_NULL']; ?></option>
				</select>
				<input name="q_customvalue2" type="text" value="<?php echo $qCustomValue2; ?>" style="width:200px;" />
				<select name="q_customcloseparen2" onchange="customSelectChanged(2)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen2==')'?'SELECTED':''); ?> value=")">)</option>
					<option <?php echo ($qCustomCloseParen2=='))'?'SELECTED':''); ?> value="))">))</option>
				</select>
				<a href="#" onclick="toggleCustomDiv3();return false;">
					<img class="editimg" src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv3" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue3||$qCustomType3=='NULL'||$qCustomType3=='NOTNULL'?'block':'none');?>;">
				<?php echo $LANG['CUSTOM_FIELD_3']; ?>:
				<select name="q_customandor3" onchange="customSelectChanged(3)">
					<option>AND</option>
					<option <?php echo ($qCustomAndOr3=='OR'?'SELECTED':''); ?> value="OR">OR</option>
				</select>
				<select name="q_customopenparen3" onchange="customSelectChanged(3)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen3=='('?'SELECTED':''); ?> value="(">(</option>
					<option <?php echo ($qCustomOpenParen3=='(('?'SELECTED':''); ?> value="((">((</option>
					<option <?php echo ($qCustomOpenParen3=='((('?'SELECTED':''); ?> value="(((">(((</option>
				</select>
				<select name="q_customfield3" onchange="customSelectChanged(3)">
					<option value=""><?php echo $LANG['SELECT_FIELD_NAME']; ?></option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField3?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype3">
					<option><?php echo $LANG['EQUALS']; ?></option>
					<option <?php echo ($qCustomType3=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS"><?php echo $LANG['NOT_EQUALS']; ?></option>
					<option <?php echo ($qCustomType3=='STARTS'?'SELECTED':''); ?> value="STARTS"><?php echo $LANG['STARTS_WITH']; ?></option>
					<option <?php echo ($qCustomType3=='LIKE'?'SELECTED':''); ?> value="LIKE"><?php echo $LANG['CONTAINS']; ?></option>
					<option <?php echo ($qCustomType3=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE"><?php echo $LANG['DOESNT_CONTAIN']; ?></option>
					<option <?php echo ($qCustomType3=='GREATER'?'SELECTED':''); ?> value="GREATER"><?php echo $LANG['GREATER_THAN']; ?></option>
					<option <?php echo ($qCustomType3=='LESS'?'SELECTED':''); ?> value="LESS"><?php echo $LANG['LESS_THAN']; ?></option>
					<option <?php echo ($qCustomType3=='NULL'?'SELECTED':''); ?> value="NULL"><?php echo $LANG['IS_NULL']; ?></option>
					<option <?php echo ($qCustomType3=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL"><?php echo $LANG['IS_NOT_NULL']; ?></option>
				</select>
				<input name="q_customvalue3" type="text" value="<?php echo $qCustomValue3; ?>" style="width:200px;" />
				<select name="q_customcloseparen3" onchange="customSelectChanged(3)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen3==')'?'SELECTED':''); ?> value=")">)</option>
					<option <?php echo ($qCustomCloseParen3=='))'?'SELECTED':''); ?> value="))">))</option>
					<option <?php echo ($qCustomCloseParen3==')))'?'SELECTED':''); ?> value=")))">)))</option>
				</select>
				<a href="#" onclick="toggleCustomDiv4();return false;">
					<img class="editimg" src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv4" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue4||$qCustomType4=='NULL'||$qCustomType4=='NOTNULL'?'block':'none');?>;">
				Custom Field 4:
				<select name="q_customandor4" onchange="customSelectChanged(4)">
					<option>AND</option>
					<option <?php echo ($qCustomAndOr4=='OR'?'SELECTED':''); ?> value="OR">OR</option>
				</select>
				<select name="q_customopenparen4" onchange="customSelectChanged(4)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen4=='('?'SELECTED':''); ?> value="(">(</option>
					<option <?php echo ($qCustomOpenParen4=='(('?'SELECTED':''); ?> value="((">((</option>
					<option <?php echo ($qCustomOpenParen4=='((('?'SELECTED':''); ?> value="(((">(((</option>
				</select>
				<select name="q_customfield4" onchange="customSelectChanged(4)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField4?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype4">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType4=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType4=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType4=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType4=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOES NOT CONTAIN</option>
					<option <?php echo ($qCustomType4=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType4=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType4=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType4=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue4" type="text" value="<?php echo $qCustomValue4; ?>" style="width:200px;" />
				<select name="q_customcloseparen4" onchange="customSelectChanged(4)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen4==')'?'SELECTED':''); ?> value=")">)</option>
					<option <?php echo ($qCustomCloseParen4=='))'?'SELECTED':''); ?> value="))">))</option>
					<option <?php echo ($qCustomCloseParen4==')))'?'SELECTED':''); ?> value=")))">)))</option>
				</select>
				<a href="#" onclick="toggleCustomDiv5();return false;">
					<img class="editimg" src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv5" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue5||$qCustomType5=='NULL'||$qCustomType5=='NOTNULL'?'block':'none');?>;">
				Custom Field 5:
				<select name="q_customandor5" onchange="customSelectChanged(5)">
					<option>AND</option>
					<option <?php echo ($qCustomAndOr5=='OR'?'SELECTED':''); ?> value="OR">OR</option>
				</select>
				<select name="q_customopenparen5" onchange="customSelectChanged(5)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen5=='('?'SELECTED':''); ?> value="(">(</option>
					<option <?php echo ($qCustomOpenParen5=='(('?'SELECTED':''); ?> value="((">((</option>
					<option <?php echo ($qCustomOpenParen5=='((('?'SELECTED':''); ?> value="(((">(((</option>
				</select>
				<select name="q_customfield5" onchange="customSelectChanged(5)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField5?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype5">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType5=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType5=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType5=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType5=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOES NOT CONTAIN</option>
					<option <?php echo ($qCustomType5=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType5=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType5=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType5=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue5" type="text" value="<?php echo $qCustomValue5; ?>" style="width:200px;" />
				<select name="q_customcloseparen5" onchange="customSelectChanged(5)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen5==')'?'SELECTED':''); ?> value=")">)</option>
					<option <?php echo ($qCustomCloseParen5=='))'?'SELECTED':''); ?> value="))">))</option>
					<option <?php echo ($qCustomCloseParen5==')))'?'SELECTED':''); ?> value=")))">)))</option>
				</select>
				<a href="#" onclick="toggleCustomDiv6();return false;">
					<img class="editimg" src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv6" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue6||$qCustomType6=='NULL'||$qCustomType6=='NOTNULL'?'block':'none');?>;">
				Custom Field 6:
				<select name="q_customandor6" onchange="customSelectChanged(6)">
					<option>AND</option>
					<option <?php echo ($qCustomAndOr6=='OR'?'SELECTED':''); ?> value="OR">OR</option>
				</select>
				<select name="q_customopenparen6" onchange="customSelectChanged(6)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen6=='('?'SELECTED':''); ?> value="(">(</option>
					<option <?php echo ($qCustomOpenParen6=='(('?'SELECTED':''); ?> value="((">((</option>
					<option <?php echo ($qCustomOpenParen6=='((('?'SELECTED':''); ?> value="(((">(((</option>
				</select>
				<select name="q_customfield6" onchange="customSelectChanged(6)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField6?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype6">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType6=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType6=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType6=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType6=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOES NOT CONTAIN</option>
					<option <?php echo ($qCustomType6=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType6=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType6=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType6=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue6" type="text" value="<?php echo $qCustomValue6; ?>" style="width:200px;" />
				<select name="q_customcloseparen6" onchange="customSelectChanged(6)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen6==')'?'SELECTED':''); ?> value=")">)</option>
					<option <?php echo ($qCustomCloseParen6=='))'?'SELECTED':''); ?> value="))">))</option>
					<option <?php echo ($qCustomCloseParen6==')))'?'SELECTED':''); ?> value=")))">)))</option>
				</select>
				<a href="#" onclick="toggleCustomDiv7();return false;">
					<img class="editimg" src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv7" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue7||$qCustomType7=='NULL'||$qCustomType7=='NOTNULL'?'block':'none');?>;">
				Custom Field 7:
				<select name="q_customandor7" onchange="customSelectChanged(7)">
					<option>AND</option>
					<option <?php echo ($qCustomAndOr7=='OR'?'SELECTED':''); ?> value="OR">OR</option>
				</select>
				<select name="q_customopenparen7" onchange="customSelectChanged(7)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen7=='('?'SELECTED':''); ?> value="(">(</option>
					<option <?php echo ($qCustomOpenParen7=='(('?'SELECTED':''); ?> value="((">((</option>
				</select>
				<select name="q_customfield7" onchange="customSelectChanged(7)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField7?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype7">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType7=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType7=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType7=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType7=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOES NOT CONTAIN</option>
					<option <?php echo ($qCustomType7=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType7=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType7=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType7=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue7" type="text" value="<?php echo $qCustomValue7; ?>" style="width:200px;" />
				<select name="q_customcloseparen7" onchange="customSelectChanged(7)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen7==')'?'SELECTED':''); ?> value=")">)</option>
					<option <?php echo ($qCustomCloseParen7=='))'?'SELECTED':''); ?> value="))">))</option>
					<option <?php echo ($qCustomCloseParen7==')))'?'SELECTED':''); ?> value=")))">)))</option>
				</select>
				<a href="#" onclick="toggleCustomDiv8();return false;">
					<img class="editimg" src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv8" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue8||$qCustomType8=='NULL'||$qCustomType8=='NOTNULL'?'block':'none');?>;">
				Custom Field 8:
				<select name="q_customandor8" onchange="customSelectChanged(8)">
					<option>AND</option>
					<option <?php echo ($qCustomAndOr8=='OR'?'SELECTED':''); ?> value="OR">OR</option>
				</select>
				<select name="q_customopenparen8" onchange="customSelectChanged(8)">
					<option value="">---</option>
					<option <?php echo ($qCustomOpenParen8=='('?'SELECTED':''); ?> value="(">(</option>
				</select>
				<select name="q_customfield8" onchange="customSelectChanged(8)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField8?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype8">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType8=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType8=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType8=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType8=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOES NOT CONTAIN</option>
					<option <?php echo ($qCustomType8=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType8=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType8=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType8=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue8" type="text" value="<?php echo $qCustomValue8; ?>" style="width:200px;" />
				<select name="q_customcloseparen8" onchange="customSelectChanged(8)">
					<option value="">---</option>
					<option <?php echo ($qCustomCloseParen8==')'?'SELECTED':''); ?> value=")">)</option>
					<option <?php echo ($qCustomCloseParen8=='))'?'SELECTED':''); ?> value="))">))</option>
					<option <?php echo ($qCustomCloseParen8==')))'?'SELECTED':''); ?> value=")))">)))</option>
				</select>
			</div>
			<div class="fieldGroupDiv">
				<?php
				if($isGenObs && ($IS_ADMIN || ($collId && array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS["CollAdmin"])))){
					?>
					<div class="fieldDiv">
						<input type="checkbox" name="q_returnall" value="1" <?php echo ($qReturnAll?'CHECKED':''); ?> /> <?php echo $LANG['SHOW_RECS_ALL']; ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="fieldGroupDiv">
				<?php
				if(!$crowdSourceMode){
					$qryStr = '';
					if($qRecordedBy) $qryStr .= '&recordedby='.$qRecordedBy;
					if($qRecordNumber) $qryStr .= '&recordnumber='.$qRecordNumber;
					if($qEventDate) $qryStr .= '&eventdate='.$qEventDate;
					if($qCatalogNumber) $qryStr .= '&catalognumber='.$qCatalogNumber;
					if($qOtherCatalogNumbers) $qryStr .= '&othercatalognumbers='.$qOtherCatalogNumbers;
					if($qRecordEnteredBy) $qryStr .= '&recordenteredby='.$qRecordEnteredBy;
					if($qDateEntered) $qryStr .= '&dateentered='.$qDateEntered;
					if($qDateLastModified) $qryStr .= '&datelastmodified='.$qDateLastModified;
					if($qryStr){
						?>
						<div style="float:right;margin-top:10px;" title="<?php echo $LANG['GO_LABEL_PRINT']; ?>">
							<a href="../reports/labelmanager.php?collid=<?php echo $collId.$qryStr; ?>">
								<img src="../../images/list.png" style="width:15px;" />
							</a>
						</div>
						<?php
					}
				}
				?>
				<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
				<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
				<input type="hidden" name="occid" value="<?php echo $occManager->getOccId(); ?>" />
				<input type="hidden" name="occindex" value="<?php echo $occManager->getOccIndex(); ?>" />
				<input type="hidden" name="occidlist" value="<?php echo $occManager->getOccidIndexStr(); ?>" />
				<input type="hidden" name="direction" value="" />
				<button name="submitaction" value="Display Editor" onclick="submitQueryEditor(this.form)" ><?php echo $LANG['DISPLAY_EDITOR']; ?></button>
				<button name="submitaction" value="Display Table" onclick="submitQueryTable(this.form)" ><?php echo $LANG['DISPLAY_TABLE']; ?></button>
				<span style="margin-left:10px;">
					<input type="button" name="reset" value="Reset Form" onclick="resetQueryForm(this.form)" />
				</span>
				<span style="margin-left:10px;">
					<?php echo $LANG['SORT_BY']; ?>:
					<select name="orderby">
						<option value=""></option>
						<option value="recordedby" <?php echo ($qOrderBy=='recordedby'?'SELECTED':''); ?>><?php echo $LANG['COLLECTOR']; ?></option>
						<option value="recordnumber" <?php echo ($qOrderBy=='recordnumber'?'SELECTED':''); ?>><?php echo $LANG['NUMBER']; ?></option>
						<option value="eventdate" <?php echo ($qOrderBy=='eventdate'?'SELECTED':''); ?>><?php echo $LANG['DATE']; ?></option>
						<option value="catalognumber" <?php echo ($qOrderBy=='catalognumber'?'SELECTED':''); ?>><?php echo $LANG['CAT_NUM']; ?></option>
						<option value="recordenteredby" <?php echo ($qOrderBy=='recordenteredby'?'SELECTED':''); ?>><?php echo $LANG['ENTERED_BY']; ?></option>
						<option value="dateentered" <?php echo ($qOrderBy=='dateentered'?'SELECTED':''); ?>><?php echo $LANG['DATE_ENTERED']; ?></option>
						<option value="datelastmodified" <?php echo ($qOrderBy=='datelastmodified'?'SELECTED':''); ?>><?php echo $LANG['DATE_LAST_MODIFIED']; ?></option>
						<option value="processingstatus" <?php echo ($qOrderBy=='processingstatus'?'SELECTED':''); ?>><?php echo $LANG['PROC_STATUS']; ?></option>
						<option value="sciname" <?php echo ($qOrderBy=='sciname'?'SELECTED':''); ?>><?php echo $LANG['SCI_NAME']; ?></option>
						<option value="family" <?php echo ($qOrderBy=='family'?'SELECTED':''); ?>><?php echo $LANG['FAMILY']; ?></option>
						<option value="country" <?php echo ($qOrderBy=='country'?'SELECTED':''); ?>><?php echo $LANG['COUNTRY']; ?></option>
						<option value="stateprovince" <?php echo ($qOrderBy=='stateprovince'?'SELECTED':''); ?>><?php echo $LANG['STATE_PROVINCE']; ?></option>
						<option value="county" <?php echo ($qOrderBy=='county'?'SELECTED':''); ?>><?php echo $LANG['COUNTY']; ?></option>
						<option value="municipality" <?php echo ($qOrderBy=='municipality'?'SELECTED':''); ?>><?php echo $LANG['MUNICIPALITY']; ?></option>
						<option value="locationid" <?php echo ($qOrderBy=='locationid'?'SELECTED':''); ?>><?php echo $LANG['LOCATION_ID']; ?></option>
						<option value="locality" <?php echo ($qOrderBy=='locality'?'SELECTED':''); ?>><?php echo $LANG['LOCALITY']; ?></option>
						<option value="decimallatitude" <?php echo ($qOrderBy=='decimallatitude'?'SELECTED':''); ?>><?php echo $LANG['DEC_LAT']; ?></option>
						<option value="decimallongitude" <?php echo ($qOrderBy=='decimallongitude'?'SELECTED':''); ?>><?php echo $LANG['DEC_LONG']; ?></option>
						<option value="minimumelevationinmeters" <?php echo ($qOrderBy=='minimumelevationinmeters'?'SELECTED':''); ?>><?php echo $LANG['ELEV_MIN']; ?></option>
						<option value="maximumelevationinmeters" <?php echo ($qOrderBy=='maximumelevationinmeters'?'SELECTED':''); ?>><?php echo $LANG['ELEV_MAX']; ?></option>
					</select>
				</span>
				<span>
					<select name="orderbydir">
						<option value="ASC"><?php echo $LANG['ASCENDING']; ?></option>
						<option value="DESC" <?php echo ($qOrderByDir=='DESC'?'SELECTED':''); ?>><?php echo $LANG['DESCENDING']; ?></option>
					</select>
				</span>
				<input type="button" name="copylink" value="Copy Search As Link" onclick="copyQueryLink(event)">
			</div>
		</fieldset>
	</form>
</div>
<script>

	// Function to copy the query link to the clipboard
	function copyQueryLink(evt){

		// Prevent the button from triggering and reloading the page
		evt.preventDefault();

		// Get the queryform parameters, only the ones that are set
		var params = $('form[name="queryform"] :input').filter(function () { return $(this).val() != ""; }).serialize();

		// Check if the catalogNumber field is set, and if not, add it to the query
		var catalogNumber = $('input[name="q_catalognumber"]').val() == "" ? '&q_catalognumber=' : '';

		// Construct the full link to the query form search parameters. Add displayquery to show the query form
		var link = location.protocol + '//' + location.host + location.pathname + '?' + params + catalogNumber + '&displayquery=1';

		// Copy to clipboard
		navigator.clipboard.writeText(link).then(() => {
		  /* clipboard succcessfully set */
		  //console.log("Clipboard copy successful");
		}, () => {
		  /* clipboard write failed */
		  //console.log("Clipboard copy failed");
		});
	}

	function enteredByCurrentUser(){
		var f = document.queryform;
		resetQueryForm(f);
		f.q_recordenteredby.value = "<?php echo $GLOBALS['USERNAME']?>";
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0');
		f.q_dateentered.value = today.getFullYear()+'-'+mm+'-'+dd;
	}

	function resetQueryForm(f){
		f.occid.value = "";
		f.occidlist.value = "";
		f.direction.value = "";
		f.occindex.value = "0";
		f.q_catalognumber.value = "";
		f.q_othercatalognumbers.value = "";
		f.q_recordedby.value = "";
		f.q_recordnumber.value = "";
		f.q_eventdate.value = "";
		f.q_recordenteredby.value = "";
		f.q_dateentered.value = "";
		f.q_datelastmodified.value = "";
		f.q_processingstatus.value = "";
		if(f.q_exsiccatiid) f.q_exsiccatiid.value = "";

		// Reset all of the custom fields
		f.q_customopenparen1.options[0].selected = true;
		f.q_customfield1.options[0].selected = true;
		f.q_customtype1.options[0].selected = true;
		f.q_customvalue1.value = "";
		f.q_customcloseparen1.options[0].selected = true;

		f.q_customandor2.options[0].selected = true;
		f.q_customopenparen2.options[0].selected = true;
		f.q_customfield2.options[0].selected = true;
		f.q_customtype2.options[0].selected = true;
		f.q_customvalue2.value = "";
		f.q_customcloseparen2.options[0].selected = true;

		f.q_customandor3.options[0].selected = true;
		f.q_customopenparen3.options[0].selected = true;
		f.q_customfield3.options[0].selected = true;
		f.q_customtype3.options[0].selected = true;
		f.q_customvalue3.value = "";
		f.q_customcloseparen3.options[0].selected = true;

		f.q_customandor4.options[0].selected = true;
		f.q_customopenparen4.options[0].selected = true;
		f.q_customfield4.options[0].selected = true;
		f.q_customtype4.options[0].selected = true;
		f.q_customvalue4.value = "";
		f.q_customcloseparen4.options[0].selected = true;

		f.q_customandor5.options[0].selected = true;
		f.q_customopenparen5.options[0].selected = true;
		f.q_customfield5.options[0].selected = true;
		f.q_customtype5.options[0].selected = true;
		f.q_customvalue5.value = "";
		f.q_customcloseparen5.options[0].selected = true;

		f.q_customandor6.options[0].selected = true;
		f.q_customopenparen6.options[0].selected = true;
		f.q_customfield6.options[0].selected = true;
		f.q_customtype6.options[0].selected = true;
		f.q_customvalue6.value = "";
		f.q_customcloseparen6.options[0].selected = true;

		f.q_customandor7.options[0].selected = true;
		f.q_customopenparen7.options[0].selected = true;
		f.q_customfield7.options[0].selected = true;
		f.q_customtype7.options[0].selected = true;
		f.q_customvalue7.value = "";
		f.q_customcloseparen7.options[0].selected = true;

		f.q_customandor8.options[0].selected = true;
		f.q_customopenparen8.options[0].selected = true;
		f.q_customfield8.options[0].selected = true;
		f.q_customtype8.options[0].selected = true;
		f.q_customvalue8.value = "";
		f.q_customcloseparen8.options[0].selected = true;

		f.q_imgonly.checked = false;
		f.q_withoutimg.checked = false;
		f.orderby.value = "";
		f.orderbydir.value = "ASC";

		// Hide all the custom field divs except the first on reset
		document.getElementById('customdiv2').style.display = "none";
		document.getElementById('customdiv3').style.display = "none";
		document.getElementById('customdiv4').style.display = "none";
		document.getElementById('customdiv5').style.display = "none";
		document.getElementById('customdiv6').style.display = "none";
		document.getElementById('customdiv7').style.display = "none";
		document.getElementById('customdiv8').style.display = "none";
	}
</script>