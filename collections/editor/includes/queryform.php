<?php
if(!$displayQuery && array_key_exists('displayquery',$_REQUEST)) $displayQuery = $_REQUEST['displayquery'];

$qryArr = $occManager->getQueryVariables();
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
$qCustomField1 = (array_key_exists('cf1',$qryArr)?$qryArr['cf1']:'');
$qCustomType1 = (array_key_exists('ct1',$qryArr)?$qryArr['ct1']:'');
$qCustomValue1 = (array_key_exists('cv1',$qryArr)?htmlentities($qryArr['cv1'], ENT_COMPAT, $CHARSET):'');
$qCustomField2 = (array_key_exists('cf2',$qryArr)?$qryArr['cf2']:'');
$qCustomType2 = (array_key_exists('ct2',$qryArr)?$qryArr['ct2']:'');
$qCustomValue2 = (array_key_exists('cv2',$qryArr)?htmlentities($qryArr['cv2'], ENT_COMPAT, $CHARSET):'');
$qCustomField3 = (array_key_exists('cf3',$qryArr)?$qryArr['cf3']:'');
$qCustomType3 = (array_key_exists('ct3',$qryArr)?$qryArr['ct3']:'');
$qCustomValue3 = (array_key_exists('cv3',$qryArr)?htmlentities($qryArr['cv3'], ENT_COMPAT, $CHARSET):'');
$qOcrFrag = (array_key_exists('ocr',$qryArr)?htmlentities($qryArr['ocr'], ENT_COMPAT, $CHARSET):'');
$qOrderBy = (array_key_exists('orderby',$qryArr)?$qryArr['orderby']:'');
$qOrderByDir = (array_key_exists('orderbydir',$qryArr)?$qryArr['orderbydir']:'');

//Set processing status
$processingStatusArr = array();
if(defined('PROCESSINGSTATUS') && PROCESSINGSTATUS){
	$processingStatusArr = PROCESSINGSTATUS;
}
else{
	$processingStatusArr = array('unprocessed','unprocessed/NLP','stage 1','stage 2','stage 3','pending duplicate','pending review-nfn','pending review','expert required','reviewed','closed');
}
//if(!isset($_REQUEST['q_catalognumber'])) $displayQuery = true;
?>
<div id="querydiv" style="clear:both;width:850px;display:<?php echo ($displayQuery?'block':'none'); ?>;">
	<form name="queryform" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return verifyQueryForm(this)">
		<fieldset style="padding:5px;">
			<legend>Record Search Form</legend>
			<?php
			if(!$crowdSourceMode){
				?>
				<div class="fieldGroupDiv">
					<div class="fieldDiv" title="Full name of collector as entered in database. To search just on last name, place the wildcard character (%) before name (%Gentry).">
						<?php echo (defined('RECORDEDBYLABEL')?RECORDEDBYLABEL:'Collector'); ?>:
						<input type="text" name="q_recordedby" value="<?php echo $qRecordedBy; ?>" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="Separate multiple terms by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
						<?php echo (defined('RECORDNUMBERLABEL')?RECORDNUMBERLABEL:'Number'); ?>:
						<input type="text" name="q_recordnumber" value="<?php echo $qRecordNumber; ?>" style="width:120px;" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="Enter ranges separated by ' - ' (space before and after dash required), e.g.: 2002-01-01 - 2003-01-01">
						<?php echo (defined('EVENTDATELABEL')?EVENTDATELABEL:'Date'); ?>:
						<input type="text" name="q_eventdate" value="<?php echo $qEventDate; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<?php
			}
			?>
			<div class="fieldGroupDiv">
				<div class="fieldDiv" title="Separate multiples by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
					<?php echo (defined('CATALOGNUMBERLABEL')?CATALOGNUMBERLABEL:'Catalog Number'); ?>:
					<input type="text" name="q_catalognumber" value="<?php echo $qCatalogNumber; ?>" onchange="setOrderBy(this)" />
				</div>
				<?php
				if($crowdSourceMode){
					?>
					<div class="fieldDiv" title="Search for term embedded within OCR block of text">
						OCR Fragment:
						<input type="text" name="q_ocrfrag" value="<?php echo $qOcrFrag; ?>" style="width:200px;" />
					</div>
					<?php
				}
				else{
					?>
					<div class="fieldDiv" title="Separate multiples by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
						<?php echo (defined('OTHERCATALOGNUMBERSLABEL')?OTHERCATALOGNUMBERSLABEL:'Other Catalog Numbers'); ?>:
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
						Entered by:
						<input type="text" name="q_recordenteredby" value="<?php echo $qRecordEnteredBy; ?>" style="width:70px;" onchange="setOrderBy(this)" />
						<button type="button" onclick="enteredByCurrentUser()" style="font-size:70%" title="Limit to recent records entered by current user">CU</button>
					</div>
					<div class="fieldDiv" title="Enter ranges separated by ' - ' (space before and after dash required), e.g.: 2002-01-01 - 2003-01-01">
						Date entered:
						<input type="text" name="q_dateentered" value="<?php echo $qDateEntered; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="Enter ranges separated by ' - ' (space before and after dash required), e.g.: 2002-01-01 - 2003-01-01">
						Date modified:
						<input type="text" name="q_datelastmodified" value="<?php echo $qDateLastModified; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<?php echo (defined('PROCESSINGSTATUSLABEL')?PROCESSINGSTATUSLABEL:'Processing Status'); ?>:
						<select name="q_processingstatus" onchange="setOrderBy(this)">
							<option value=''>All Records</option>
							<option>-------------------</option>
							<?php
							foreach($processingStatusArr as $v){
								//Don't display these options is editor is crowd sourced
								$keyOut = strtolower($v);
								echo '<option value="'.$keyOut.'" '.($qProcessingStatus==$keyOut?'SELECTED':'').'>'.ucwords($v).'</option>';
							}
							echo '<option value="isnull" '.($qProcessingStatus=='isnull'?'SELECTED':'').'>No Set Status</option>';
							if($qProcessingStatus && $qProcessingStatus != 'isnull' && !in_array($qProcessingStatus,$processingStatusArr)){
								echo '<option value="'.$qProcessingStatus.'" SELECTED>'.$qProcessingStatus.'</option>';
							}
							?>
						</select>
					</div>
					<div class="fieldDiv">
						<input name="q_imgonly" type="checkbox" value="1" <?php echo ($qImgOnly==1?'checked':''); ?> onchange="this.form.q_withoutimg.checked = false;" />
						with images
					</div>
					<div class="fieldDiv">
						<input name="q_withoutimg" type="checkbox" value="1" <?php echo ($qWithoutImg==1?'checked':''); ?> onchange="this.form.q_imgonly.checked = false;" />
						without images
					</div>
				</div>
				<?php
				if($ACTIVATE_EXSICCATI){
					if($exsList = $occManager->getExsiccatiList()){
						?>
						<div class="fieldGroupDiv" title="Enter Exsiccati Title">
							<div class="fieldDiv">
								Exsiccati Title:
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
				$advFieldArr = array('family'=>'Family','sciname'=>'Scientific Name','othercatalognumbers'=>'Other Catalog Numbers',
					'country'=>'Country','stateProvince'=>'State/Province','county'=>'County','municipality'=>'Municipality',
					'recordedby'=>'Collector','recordnumber'=>'Collector Number','eventdate'=>'Collection Date');
			}
			else{
				$advFieldArr = array('associatedCollectors'=>'Associated Collectors','associatedOccurrences'=>'Associated Occurrences',
					'associatedTaxa'=>'Associated Taxa','attributes'=>'Attributes','scientificNameAuthorship'=>'Author',
					'basisOfRecord'=>'Basis Of Record','behavior'=>'Behavior','catalogNumber'=>'Catalog Number','collectionCode'=>'Collection Code (override)','recordNumber'=>'Collection Number',
					'recordedBy'=>'Collector/Observer','coordinateUncertaintyInMeters'=>'Coordinate Uncertainty (m)','country'=>'Country',
					'county'=>'County','cultivationStatus'=>'Cultivation Status','dataGeneralizations'=>'Data Generalizations','eventDate'=>'Date',
					'dateEntered'=>'Date Entered','dateLastModified'=>'Date Last Modified','dbpk'=>'dbpk','decimalLatitude'=>'Decimal Latitude',
					'decimalLongitude'=>'Decimal Longitude','maximumDepthInMeters'=>'Depth Maximum (m)','minimumDepthInMeters'=>'Depth Minimum (m)',
					'verbatimAttributes'=>'Description','disposition'=>'Disposition','dynamicProperties'=>'Dynamic Properties',
					'maximumElevationInMeters'=>'Elevation Maximum (m)','minimumElevationInMeters'=>'Elevation Minimum (m)',
					'establishmentMeans'=>'Establishment Means','family'=>'Family','fieldNotes'=>'Field Notes','fieldnumber'=>'Field Number',
					'geodeticDatum'=>'Geodetic Datum','georeferenceProtocol'=>'Georeference Protocol',
					'georeferenceRemarks'=>'Georeference Remarks','georeferenceSources'=>'Georeference Sources',
					'georeferenceVerificationStatus'=>'Georeference Verification Status','georeferencedBy'=>'Georeferenced By','habitat'=>'Habitat',
					'identificationQualifier'=>'Identification Qualifier','identificationReferences'=>'Identification References',
					'identificationRemarks'=>'Identification Remarks','identifiedBy'=>'Identified By','individualCount'=>'Individual Count',
					'informationWithheld'=>'Information Withheld','institutionCode'=>'Institution Code (override)','labelProject'=>'Project',
					'language'=>'Language','lifeStage'=>'Life Stage','locationid'=>'Location ID','locality'=>'Locality',
					'localitySecurity'=>'Locality Security','localitySecurityReason'=>'Locality Security Reason','locationRemarks'=>'Location Remarks',
					'username'=>'Modified By','municipality'=>'Municipality','occurrenceRemarks'=>'Notes (Occurrence Remarks)','ocrFragment'=>'OCR Fragment',
					'otherCatalogNumbers'=>'Other Catalog Numbers','ownerInstitutionCode'=>'Owner Code','preparations'=>'Preparations',
					'reproductiveCondition'=>'Reproductive Condition','samplingEffort'=>'Sampling Effort','samplingProtocol'=>'Sampling Protocol',
					'sciname'=>'Scientific Name','sex'=>'Sex','stateProvince'=>'State/Province',
					'substrate'=>'Substrate','taxonRemarks'=>'Taxon Remarks','typeStatus'=>'Type Status','verbatimCoordinates'=>'Verbatim Coordinates',
					'verbatimEventDate'=>'Verbatim Date','verbatimDepth'=>'Verbatim Depth','verbatimElevation'=>'Verbatim Elevation');
			}
			//sort($advFieldArr);
			?>
			<div class="fieldGroupDiv">
				Custom Field 1:
				<select name="q_customfield1" onchange="customSelectChanged(1)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField1?'SELECTED':'').'>';
						// Use a label defined in the collection templating system for each field, if set
						echo defined(strtoupper($k).'LABEL')?constant(strtoupper($k).'LABEL'):$v;
						echo '</option>';
					}
					?>
				</select>
				<select name="q_customtype1">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType1=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType1=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType1=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType1=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType1=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType1=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType1=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue1" type="text" value="<?php echo $qCustomValue1; ?>" style="width:200px;" />
				<a href="#" onclick="toggleCustomDiv2();return false;">
					<img src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv2" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue2||$qCustomType2=='NULL'||$qCustomType2=='NOTNULL'?'block':'none');?>;">
				Custom Field 2:
				<select name="q_customfield2" onchange="customSelectChanged(2)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField2?'SELECTED':'').'>';
						// Use a label defined in the collection templating system for each field, if set
						echo defined(strtoupper($k).'LABEL')?constant(strtoupper($k).'LABEL'):$v;
						echo '</option>';
					}
					?>
				</select>
				<select name="q_customtype2">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType2=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType2=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType2=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType2=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType2=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType2=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType2=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue2" type="text" value="<?php echo $qCustomValue2; ?>" style="width:200px;" />
				<a href="#" onclick="toggleCustomDiv3();return false;">
					<img src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv3" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue3||$qCustomType3=='NULL'||$qCustomType3=='NOTNULL'?'block':'none');?>;">
				Custom Field 3:
				<select name="q_customfield3" onchange="customSelectChanged(3)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField3?'SELECTED':'').'>';
						// Use a label defined in the collection templating system for each field, if set
						echo defined(strtoupper($k).'LABEL')?constant(strtoupper($k).'LABEL'):$v;
						echo '</option>';
					}
					?>
				</select>
				<select name="q_customtype3">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType3=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType3=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType3=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType3=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType3=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType3=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType3=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue3" type="text" value="<?php echo $qCustomValue3; ?>" style="width:200px;" />
			</div>
			<div class="fieldGroupDiv">
				<?php
				if($isGenObs && ($IS_ADMIN || ($collId && array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS["CollAdmin"])))){
					?>
					<div class="fieldDiv">
						<input type="checkbox" name="q_returnall" value="1" <?php echo ($qReturnAll?'CHECKED':''); ?> /> Show records for all users (admin control)
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
						<div style="float:right;margin-top:10px;" title="Go to Label Printing Module">
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
				<input type="button" name="submitaction" value="Display Editor" onclick="submitQueryEditor(this.form)" />
				<input type="button" name="submitaction" value="Display Table" onclick="submitQueryTable(this.form)" />
				<span style="margin-left:10px;">
					<input type="button" name="reset" value="Reset Form" onclick="resetQueryForm(this.form)" />
				</span>
				<span style="margin-left:10px;">
					Sort by:
					<select name="orderby">
						<option value=""></option>
						<option value="recordedby" <?php echo ($qOrderBy=='recordedby'?'SELECTED':''); ?>><?php echo (defined('RECORDEDBYLABEL')?RECORDEDBYLABEL:'Collector'); ?></option>
						<option value="recordnumber" <?php echo ($qOrderBy=='recordnumber'?'SELECTED':''); ?>><?php echo (defined('RECORDNUMBERLABEL')?RECORDNUMBERLABEL:'Number'); ?></option>
						<option value="eventdate" <?php echo ($qOrderBy=='eventdate'?'SELECTED':''); ?>><?php echo (defined('EVENTDATELABEL')?EVENTDATELABEL:'Date'); ?></option>
						<option value="catalognumber" <?php echo ($qOrderBy=='catalognumber'?'SELECTED':''); ?>><?php echo (defined('CATALOGNUMBERLABEL')?CATALOGNUMBERLABEL:'Catalog Number'); ?></option>
						<option value="recordenteredby" <?php echo ($qOrderBy=='recordenteredby'?'SELECTED':''); ?>>Entered By</option>
						<option value="dateentered" <?php echo ($qOrderBy=='dateentered'?'SELECTED':''); ?>>Date Entered</option>
						<option value="datelastmodified" <?php echo ($qOrderBy=='datelastmodified'?'SELECTED':''); ?>>Date Last modified</option>
						<option value="processingstatus" <?php echo ($qOrderBy=='processingstatus'?'SELECTED':''); ?>><?php echo (defined('PROCESSINGSTATUSLABEL')?PROCESSINGSTATUSLABEL:'Processing Status'); ?></option>
						<option value="sciname" <?php echo ($qOrderBy=='sciname'?'SELECTED':''); ?>><?php echo (defined('SCIENTIFICNAMELABEL')?SCIENTIFICNAMELABEL:'Scientific Name'); ?></option>
						<option value="family" <?php echo ($qOrderBy=='family'?'SELECTED':''); ?>><?php echo (defined('FAMILYLABEL')?FAMILYLABEL:'Family'); ?></option>
						<option value="country" <?php echo ($qOrderBy=='country'?'SELECTED':''); ?>><?php echo (defined('COUNTRYLABEL')?COUNTRYLABEL:'Country'); ?></option>
						<option value="stateprovince" <?php echo ($qOrderBy=='stateprovince'?'SELECTED':''); ?>><?php echo (defined('STATEPROVINCELABEL')?STATEPROVINCELABEL:'State/Province'); ?></option>
						<option value="county" <?php echo ($qOrderBy=='county'?'SELECTED':''); ?>><?php echo (defined('COUNTYLABEL')?COUNTYLABEL:'County'); ?></option>
						<option value="municipality" <?php echo ($qOrderBy=='municipality'?'SELECTED':''); ?>><?php echo (defined('MUNICIPALITYLABEL')?MUNICIPALITYLABEL:'Municipality'); ?></option>
						<option value="locationid" <?php echo ($qOrderBy=='locationid'?'SELECTED':''); ?>><?php echo (defined('LOCATIONIDLABEL')?LOCATIONIDLABEL:'Location ID'); ?></option>
						<option value="locality" <?php echo ($qOrderBy=='locality'?'SELECTED':''); ?>><?php echo (defined('LOCALITYLABEL')?LOCALITYLABEL:'Locality'); ?></option>
						<option value="decimallatitude" <?php echo ($qOrderBy=='decimallatitude'?'SELECTED':''); ?>><?php echo (defined('DECIMALLATITUDELABEL')?DECIMALLATITUDELABEL:'Latitude'); ?></option>
						<option value="decimallongitude" <?php echo ($qOrderBy=='decimallongitude'?'SELECTED':''); ?>><?php echo (defined('DECIMALLONGITUDELABEL')?DECIMALLONGITUDELABEL:'Longitude'); ?></option>
						<option value="minimumelevationinmeters" <?php echo ($qOrderBy=='minimumelevationinmeters'?'SELECTED':''); ?>>Elevation Minimum</option>
						<option value="maximumelevationinmeters" <?php echo ($qOrderBy=='maximumelevationinmeters'?'SELECTED':''); ?>>Elevation Maximum</option>
					</select>
				</span>
				<span>
					<select name="orderbydir">
						<option value="ASC">ascending</option>
						<option value="DESC" <?php echo ($qOrderByDir=='DESC'?'SELECTED':''); ?>>descending</option>
					</select>
				</span>
			</div>
		</fieldset>
	</form>
</div>
<script>
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
		f.q_customfield1.options[0].selected = true;
		f.q_customtype1.options[0].selected = true;
		f.q_customvalue1.value = "";
		f.q_customfield2.options[0].selected = true;
		f.q_customtype2.options[0].selected = true;
		f.q_customvalue2.value = "";
		f.q_customfield3.options[0].selected = true;
		f.q_customtype3.options[0].selected = true;
		f.q_customvalue3.value = "";
		f.q_imgonly.checked = false;
		f.q_withoutimg.checked = false;
		f.orderby.value = "";
		f.orderbydir.value = "ASC";
	}
</script>