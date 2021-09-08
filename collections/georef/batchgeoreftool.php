<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceGeorefTools.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../collections/georef/batchgeoreftool.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$submitAction = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

$qCountry = array_key_exists('qcountry',$_POST)?$_POST['qcountry']:'';
$qState = array_key_exists('qstate',$_POST)?$_POST['qstate']:'';
$qCounty = array_key_exists('qcounty',$_POST)?$_POST['qcounty']:'';
$qMunicipality = array_key_exists('qmunicipality',$_POST)?$_POST['qmunicipality']:'';
$qLocality = array_key_exists('qlocality',$_POST)?$_POST['qlocality']:'';
$qDisplayAll = array_key_exists('qdisplayall',$_POST)?$_POST['qdisplayall']:0;
$qVStatus = array_key_exists('qvstatus',$_POST)?$_POST['qvstatus']:'';
$qSciname = array_key_exists('qsciname',$_POST)?$_POST['qsciname']:'';
$qProcessingStatus = array_key_exists('qprocessingstatus',$_POST)?$_POST['qprocessingstatus']:'';

//$latDeg = array_key_exists('latdeg',$_POST)?$_POST['latdeg']:'';
//$latMin = array_key_exists('latmin',$_POST)?$_POST['latmin']:'';
//$latSec = array_key_exists('latsec',$_POST)?$_POST['latsec']:'';
//$decimalLatitude = array_key_exists('decimallatitude',$_POST)?$_POST['decimallatitude']:'';
//$latNS = array_key_exists('latns',$_POST)?$_POST['latns']:'';

//$lngDeg = array_key_exists('lngdeg',$_POST)?$_POST['lngdeg']:'';
//$lngMin = array_key_exists('lngmin',$_POST)?$_POST['lngmin']:'';
//$lngSec = array_key_exists('lngsec',$_POST)?$_POST['lngsec']:'';
//$decimalLongitude = array_key_exists('decimallongitude',$_POST)?$_POST['decimallongitude']:'';
//$lngEW = array_key_exists('lngew',$_POST)?$_POST['lngew']:'';

//$coordinateUncertaintyInMeters = array_key_exists('coordinateuncertaintyinmeters',$_POST)?$_POST['coordinateuncertaintyinmeters']:'';
//$geodeticDatum = array_key_exists('geodeticdatum',$_POST)?$_POST['geodeticdatum']:'';
$georeferenceSources = array_key_exists('georeferencesources',$_POST)?$_POST['georeferencesources']:'';
$georeferenceProtocol = array_key_exists('georeferenceprotocol',$_POST)?$_POST['georeferenceprotocol']:'';
//$georeferenceRemarks = array_key_exists('georeferenceremarks',$_POST)?$_POST['georeferenceremarks']:'';
//$footprintWKT = array_key_exists('footprintwkt',$_POST)?$_POST['footprintwkt']:'';
$georeferenceVerificationStatus = array_key_exists('georeferenceverificationstatus',$_POST)?$_POST['georeferenceverificationstatus']:'';
//$minimumElevationInMeters = array_key_exists('minimumelevationinmeters',$_POST)?$_POST['minimumelevationinmeters']:'';
//$maximumElevationInMeters = array_key_exists('maximumelevationinmeters',$_POST)?$_POST['maximumelevationinmeters']:'';
//$minimumElevationInFeet = array_key_exists('minimumelevationinfeet',$_POST)?$_POST['minimumelevationinfeet']:'';
//$maximumElevationInFeet = array_key_exists('maximumelevationinfeet',$_POST)?$_POST['maximumelevationinfeet']:'';

if(is_array($collid)) $collid = implode(',',$collid);

//Sanitation
if(!preg_match('/^[,\d]+$/',$collid)) $collid = 0;
$submitAction = filter_var($submitAction, FILTER_SANITIZE_STRING);
$qCountry = filter_var($qCountry, FILTER_SANITIZE_STRING);
$qState = filter_var($qState, FILTER_SANITIZE_STRING);
$qCounty = filter_var($qCounty, FILTER_SANITIZE_STRING);
$qMunicipality = filter_var($qMunicipality, FILTER_SANITIZE_STRING);
$qLocality = filter_var($qLocality, FILTER_SANITIZE_STRING);
$qDisplayAll = filter_var($qDisplayAll, FILTER_SANITIZE_STRING);
$qVStatus = filter_var($qVStatus, FILTER_SANITIZE_STRING);
$qSciname = filter_var($qSciname, FILTER_SANITIZE_STRING);
$qProcessingStatus = filter_var($qProcessingStatus, FILTER_SANITIZE_STRING);
$georeferenceSources = filter_var($georeferenceSources, FILTER_SANITIZE_STRING);
$georeferenceProtocol = filter_var($georeferenceProtocol, FILTER_SANITIZE_STRING);
$georeferenceVerificationStatus = filter_var($georeferenceVerificationStatus, FILTER_SANITIZE_STRING);

// Add collection customization variables
if($collid && !strpos($collid, ",") && file_exists('../editor/includes/config/occurVarColl'.$collid.'.php')){
	//Specific to particular collection
	include('../editor/includes/config/occurVarColl'.$collid.'.php');
}
elseif(file_exists('../editor/includes/config/occurVarDefault.php')){
	//Specific to Default values for portal
	include('../editor/includes/config/occurVarDefault.php');
}

if(!$georeferenceSources) $georeferenceSources = 'georef batch tool '.date('Y-m-d');
if(!$georeferenceVerificationStatus) $georeferenceVerificationStatus = 'reviewed - high confidence';

$geoManager = new OccurrenceGeorefTools();
$activeCollArr = explode(',', $collid);
foreach($activeCollArr as $k => $id){
	if((!isset($USER_RIGHTS["CollAdmin"]) || !in_array($id,$USER_RIGHTS["CollAdmin"])) && (!isset($USER_RIGHTS["CollEditor"]) || !in_array($id,$USER_RIGHTS["CollEditor"]))){
		unset($activeCollArr[$k]);
	}
}
$geoManager->setCollId($IS_ADMIN?$collid:implode(',',$activeCollArr));
$collMap = $geoManager->getCollMap();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif($activeCollArr) $isEditor = true;

$statusStr = '';
if($isEditor && $submitAction){
	if($qCountry) $geoManager->setQueryVariables('qcountry',$qCountry);
	if($qState) $geoManager->setQueryVariables('qstate',$qState);
	if($qCounty) $geoManager->setQueryVariables('qcounty',$qCounty);
	if($qMunicipality) $geoManager->setQueryVariables('qmunicipality',$qMunicipality);
	if($qSciname) $geoManager->setQueryVariables('qsciname',$qSciname);
	if($qDisplayAll) $geoManager->setQueryVariables('qdisplayall',$qDisplayAll);
	if($qVStatus) $geoManager->setQueryVariables('qvstatus',$qVStatus);
	if($qLocality) $geoManager->setQueryVariables('qlocality',$qLocality);
	if($qProcessingStatus) $geoManager->setQueryVariables('qprocessingstatus',$qProcessingStatus);
	if($submitAction == 'Update Coordinates') $statusStr = $geoManager->updateCoordinates($_POST);
}
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Georeferencing Tools</title>
		<?php
		$activateJQuery = true;
		if(file_exists($SERVER_ROOT.'/includes/head.php')){
			include_once($SERVER_ROOT.'/includes/head.php');
		}
		else{
			echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
		}
		?>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/collections.georef.js?ver=1" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/collections.georef.batchgeoreftool.js?ver=201912" type="text/javascript"></script>
	</head>
	<body>
		<!-- This is inner text! -->
		<div  id='innertext'>
			<?php
			if($collid){
				?>
				<div style="float:left;">
					<div style="font-weight: bold; font-size:140%;float:left">
						<?php
						if(is_numeric($collid)) echo $collMap[$collid]['collectionname'].' ('.$collMap[$collid]['code'].')';
						else echo 'Multiple Collection Cleaning Tool (<a href="#" onclick="$(\'#collDiv\').show()" style="color:blue;text-decoration:underline">'.count($activeCollArr).' collections</a>)';
						?>
					</div>
					<?php
					if(count($collMap) > 1 && $activeCollArr){
						?>
						<div style="float:left;margin-left:5px;"><a href="#" onclick="toggle('mult_coll_div')"><img src="../../images/add.png" style="width:12px" /></a></div>
						<?php
					}
					?>
					<div class='navpath' style="margin:10px;clear:both;">
						<a href='../../index.php'>Home</a> &gt;&gt;
						<?php
						if(is_numeric($collid)){
							?>
							<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management Menu</a> &gt;&gt;
							<?php
						}
						else{
							?>
							<a href="../../profile/viewprofile.php?tabindex=1">Specimen Management</a> &gt;&gt;
							<?php
						}
						?>
						<b>Batch Georeferencing Tools</b>
					</div>
					<?php
					if($statusStr){
						?>
						<div style='margin:20px;font-weight:bold;color:red;'>
							<?php echo $statusStr; ?>
						</div>
						<?php
					}
					if(count($collMap) > 1 && $activeCollArr){
						?>
						<div id="mult_coll_div" style="clear:both;display:none;">
							<fieldset style="padding: 15px;margin:20px;">
								<legend><b>Multiple Collection Selector</b></legend>
								<form name="selectcollidform" action="batchgeoreftool.php" method="post" onsubmit="return checkSelectCollidForm(this)">
									<div><input name="selectall" type="checkbox" onclick="selectAllCollections(this);" /> Select / Unselect All</div>
									<?php
									foreach($collMap as $id => $collArr){
										if(in_array($id, $USER_RIGHTS["CollAdmin"]) || in_array($id, $USER_RIGHTS["CollEditor"])){
											echo '<div>';
											echo '<input name="collid[]" type="checkbox" value="'.$id.'" '.(in_array($id,$activeCollArr)?'CHECKED':'').' /> ';
											echo $collArr['collectionname'].' ('.$collArr['code'].')';
											echo '</div>';
										}
									}
									?>
									<div style="margin: 15px">
										<button name="submitaction" type="submit" value="EvaluateCollections">Evaluate Collections</button>
									</div>
								</form>
								<div>* Only collections with administrative access are shown</div>
							</fieldset>
						</div>
						<?php
					}
					if(count($activeCollArr) > 1){
						echo '<div id="collDiv" style="display:none;margin:0px 20px;clear:both;">';
						foreach($activeCollArr as $activeCollid){
							echo '<div>'.$collMap[$activeCollid]['collectionname'].' ('.$collMap[$activeCollid]['code'].')</div>';
						}
						echo '</div>';
					}
					?>
				</div>
				<?php
			}
			if($collid){
				if($isEditor){
					?>
					<div style="float:right;">
						<form name="queryform" method="post" action="batchgeoreftool.php" onsubmit="return verifyQueryForm(this)">
							<fieldset style="padding:5px;width:600px;background-color:lightyellow;">
								<legend><b>Query Form</b></legend>
								<div style="height:20px;">
									<div style="clear:both;">
										<div style="float:left;margin-right:10px;">
											<select name="qcountry" style="width:150px;">
												<option value=''>All Countries</option>
												<option value=''>--------------------</option>
												<?php
												$countryStr = array_key_exists('countrystr',$_POST)?strip_tags($_POST['countrystr']):'';
												$countryArr = array();
												if($countryStr) $countryArr = explode('|',$countryStr);
												else $countryArr = $geoManager->getCountryArr();
												foreach($countryArr as $c){
													echo '<option '.($qCountry==$c?'SELECTED':'').'>'.$c.'</option>';
												}
												?>
											</select>
										</div>
										<div style="float:left;margin-right:10px;">
											<select name="qstate" style="width:150px;">
												<option value=''>All States</option>
												<option value=''>--------------------</option>
												<?php
												$stateStr = array_key_exists('statestr',$_POST)?strip_tags($_POST['statestr']):'';
												$stateArr = array();
												if($stateStr) $stateArr = explode('|',$stateStr);
												else $stateArr = $geoManager->getStateArr();
												foreach($stateArr as $s){
													echo '<option '.($qState==$s?'SELECTED':'').'>'.$s.'</option>';
												}
												?>
											</select>
										</div>
										<div style="float:left;margin-right:10px;">
											<select name="qcounty" style="width:180px;">
												<option value=''>All Counties</option>
												<option value=''>--------------------</option>
												<?php
												$countyStr = array_key_exists('countystr',$_POST)?strip_tags($_POST['countystr']):'';
												$countyArr = array();
												if($countyStr) $countyArr = explode('|',$countyStr);
												else $countyArr = $geoManager->getCountyArr();
												foreach($countyArr as $c){
													echo '<option '.($qCounty==$c?'SELECTED':'').'>'.$c.'</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div style="clear:both;margin-top:5px;">
										<div style="float:left;margin-right:10px;">
											<select name="qmunicipality" style="width:180px;">
												<option value=''>All Municipalities</option>
												<option value=''>--------------------</option>
												<?php
												$municipalityStr = array_key_exists('municipalitystr',$_POST)?strip_tags($_POST['municipalitystr']):'';
												$municipalityArr = array();
												if($municipalityStr) $municipalityArr = explode('|',$municipalityStr);
												else $municipalityArr = $geoManager->getMunicipalityArr();
												foreach($municipalityArr as $m){
													echo '<option '.($qMunicipality==$m?'SELECTED':'').'>'.$m.'</option>';
												}
												?>
											</select>
										</div>
										<div style="float:left;margin-right:10px;">
											<select name="qprocessingstatus">
												<option value="">All Records</option>
												<option value="">-----------------------</option>
												<?php
												$processingStr = array_key_exists('processingstr',$_POST)?strip_tags($_POST['processingstr']):'';
												$processingArr = array();
												if($processingStr) $processingArr = explode('|',$processingStr);
												else $processingArr = $geoManager->getProcessingStatus();
												foreach($processingArr as $pStatus){
													echo '<option '.($qProcessingStatus==$pStatus?'SELECTED':'').'>'.ucwords($pStatus).'</option>';
												}
												?>
											</select>
										</div>
										<div style="float:left;">
											<img src="../../images/add.png" onclick="toggle('advfilterdiv')" title="Advanced Options" />
										</div>
									</div>
								</div>
								<div id="advfilterdiv" style="clear:both;margin-top:5px;display:<?php echo ($qSciname || $qVStatus || $qDisplayAll?'block':'none'); ?>;">
									<div style="float:left;margin-right:15px;">
										<b>Verification status:</b>
										<input id="qvstatus" name="qvstatus" type="text" value="<?php echo $qVStatus; ?>" style="width:175px;" />
									</div>
									<div style="float:left;">
										<b>Family/Genus:</b>
										<input name="qsciname" type="text" value="<?php echo $qSciname; ?>" style="width:150px;" />
									</div>
									<div style="clear:both;margin-top:5px;">
										<input name="qdisplayall" type="checkbox" value="1" <?php echo ($qDisplayAll?'checked':''); ?> />
										Including previously georeferenced records
									</div>
								</div>
								<div style="margin-top:5px;clear:both;">
									<div style="float:right;">
										<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
										<input name="submitaction" type="submit" value="Generate List" />
										<span id="qworkingspan" style="display:none;">
											<img src="../../images/workingcircle.gif" />
										</span>
									</div>
									<div style="float:left">
										<b>Locality Term:</b>
										<input name="qlocality" type="text" value="<?php echo $qLocality; ?>" style="width:250px;" />
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div style="clear:both;">
						<form name="georefform" method="post" action="batchgeoreftool.php" onsubmit="return verifyGeorefForm(this)">
							<div style="float:right;">
								<span>
									<a href="#" onclick="geoCloneTool();"><img src="../../images/list.png" title="Search for clones previously georeferenced" style="width:15px;" /></a>
								</span>
								<span style="margin-left:10px;">
									<a href="#" onclick="geoLocateLocality();"><img src="../../images/geolocate.png" title="GeoLocate locality" style="width:15px;" /></a>
								</span>
								<span style="margin-left:10px;">
									<a href="#" onclick="analyseLocalityStr();"><img src="../../images/find.png" title="Analyse Locality string for embedded Lat/Long or UTM" style="width:15px;" /></a>
								</span>
								<?php
								if(!strpos($collid,',')){
									?>
									<span style="margin-left:10px;">
										<a href="#" onclick="openFirstRecSet();"><img src="../../images/edit.png" title="Edit first set of records" style="width:15px;" /></a>
									</span>
									<?php
								}
								?>
							</div>
							<div>
								<?php
								$localArr = $geoManager->getLocalityArr();
								$localCnt = '---';
								if(isset($localArr)) $localCnt = count($localArr);
								if($localCnt == 1000) $localCnt = '1000 limit reached (not all available localities shown)';
								echo '<b>Return Count:</b> '.$localCnt;
								?>
							</div>
							<div style="clear:both;border:2px solid;width:100%;height:200px;resize: both;overflow: auto">
								<select id="locallist" name="locallist[]" multiple="multiple" style="width:100%;height:100%">
									<?php
									if(isset($localArr)){
										if($localArr){
											foreach($localArr as $k => $v){
												$locStr = '';
												if(!$qCountry && $v['country']) $locStr = $v['country'].'; ';
												if(!$qState && $v['stateprovince']) $locStr .= $v['stateprovince'].'; ';
												if(!$qCounty && $v['county']) $locStr .= $v['county'].'; ';
												if(!$qMunicipality && $v['municipality']) $locStr .= $v['municipality'].'; ';
												if($v['locality']) $locStr .= str_replace(';',',',$v['locality']);
												if($v['verbatimcoordinates']) $locStr .= ', '.$v['verbatimcoordinates'];
												if(array_key_exists('decimallatitude',$v) && $v['decimallatitude']){
													$locStr .= ' ('.$v['decimallatitude'].', '.$v['decimallongitude'].') ';
												}
												echo '<option value="'.$v['occid'].'">'.trim($locStr,' ,').' ['.$v['cnt'].']</option>'."\n";
											}
										}
										else{
											echo '<option value="">No localities returned matching search term</option>';
										}
									}
									else{
										echo '<option value="">Use query form above to build locality list</option>';
									}
									?>
								</select>
							</div>
							<div style="float:right;">
								<fieldset>
									<legend><b>Statistics</b></legend>
									<div style="">
										Records to be Georeferenced
									</div>
									<div style="margin:5px;">
										<?php
										$statArr = $geoManager->getCoordStatistics();
										echo '<div>Total: '.$statArr['total'].'</div>';
										echo '<div>Percentage: '.$statArr['percent'].'%</div>';
										?>
									</div>
								</fieldset>
							</div>
							<div style="margin:15px;">
								<table>
									<tr>
										<td></td>
										<td><b>Deg.</b></td>
										<td style="width:55px;"><b>Min.</b></td>
										<td style="width:55px;"><b>Sec.</b></td>
										<td style="width:20px;">&nbsp;</td>
										<td style="width:15px;">&nbsp;</td>
										<td><b>Decimal</b></td>
									</tr>
									<tr>
										<td style="vertical-align:middle"><b>Latitude:</b> </td>
										<td><input name="latdeg" type="text" value="" onchange="updateLatDec(this.form)" style="width:30px;" /></td>
										<td><input name="latmin" type="text" value="" onchange="updateLatDec(this.form)" style="width:50px;" /></td>
										<td><input name="latsec" type="text" value="" onchange="updateLatDec(this.form)" style="width:50px;" /></td>
										<td>
											<select name="latns" onchange="updateLatDec(this.form)">
												<option>N</option>
												<option >S</option>
											</select>
										</td>
										<td> = </td>
										<td>
											<input id="decimallatitude" name="decimallatitude" type="text" value="" style="width:80px;" />
											<span style="cursor:pointer;padding:3px;" onclick="openMappingAid();">
												<img src="../../images/world.png" style="border:0px;width:13px;" />
											</span>
										</td>
									</tr>
									<tr>
										<td style="vertical-align:middle"><b>Longitude:</b> </td>
										<td><input name="lngdeg" type="text" value="" onchange="updateLngDec(this.form)" style="width:30px;" /></td>
										<td><input name="lngmin" type="text" value="" onchange="updateLngDec(this.form)" style="width:50px;" /></td>
										<td><input name="lngsec" type="text" value="" onchange="updateLngDec(this.form)" style="width:50px;" /></td>
										<td style="width:20px;">
											<select name="lngew" onchange="updateLngDec(this.form)">
												<option>E</option>
												<option SELECTED>W</option>
											</select>
										</td>
										<td> = </td>
										<td><input id="decimallongitude" name="decimallongitude" type="text" value="" style="width:80px;" /></td>
									</tr>
									<tr>
										<td colspan="3" style="vertical-align:middle">
											<b>Error (in meters):</b>
										</td>
										<td colspan="2" style="vertical-align:middle">
											<input id="coordinateuncertaintyinmeters" name="coordinateuncertaintyinmeters" type="text" value="" style="width:50px;" onchange="verifyCoordUncertainty(this)" />
										</td>
										<td colspan="2" style="vertical-align:middle">
											<span style="margin-left:20px;font-weight:bold;">Datum:</span>
											<input id="geodeticdatum" name="geodeticdatum" type="text" value="" style="width:75px;" />
											<span style="cursor:pointer;margin-left:3px;" onclick="toggle('utmdiv');">
												<img src="../../images/editplus.png" style="border:0px;width:14px;" />
											</span>
										</td>
									</tr>
									<tr>
										<td colspan="3" style="vertical-align:middle">
											<b>Footprint WKT:</b>
										</td>
										<td colspan="4" style="vertical-align:middle">
											<input id="footprintwkt" name="footprintwkt" type="text" value="" style="width:500px;" onchange="verifyFootprintWKT(this)" />
										</td>
									</tr>
									<tr>
										<td colspan="7">
											<div id="utmdiv" style="display:none;padding:15px 10px;background-color:lightyellow;border:1px solid yellow;width:400px;height:75px;margin-bottom:10px;">
												<div>
													<div style="margin:3px;float:left;">
														East: <input name="utmeast" type="text" style="width:100px;" />
													</div>
													<div style="margin:3px;float:left;">
														North: <input name="utmnorth" type="text" style="width:100px;" />
													</div>
													<div style="margin:3px;float:left;">
														Zone: <input name="utmzone" style="width:40px;" />
													</div>
												</div>
												<div style="clear:both;margin:3px;">
													<div style="float:left;">
														Hemisphere:
														<select name="hemisphere" title="Use hemisphere designator (e.g. 12N) rather than grid zone ">
															<option value="Northern">North</option>
															<option value="Southern">South</option>
														</select>
													</div>
													<div style="margin:5px 0px 0px 15px;float:left;">
														<input type="button" value="Convert UTM values to lat/long " onclick="insertUtm(this.form)" />
													</div>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td colspan="3" style="vertical-align:middle">
											<b>Sources:</b>
										</td>
										<td colspan="4">
											<input id="georeferencesources" name="georeferencesources" type="text" value="<?php echo $georeferenceSources; ?>" style="width:500px;" />
										</td>
									</tr>
									<tr>
										<td colspan="3" style="vertical-align:middle">
											<b>Protocols:</b>
										</td>
										<td colspan="4">
											<input id="georeferenceprotocol" name="georeferenceprotocol" type="text" value="<?php echo $georeferenceProtocol; ?>" style="width:500px;" />
										</td>
									</tr>
									<tr>
										<td colspan="3" style="vertical-align:middle">
											<b>Remarks:</b>
										</td>
										<td colspan="4">
											<input name="georeferenceremarks" type="text" value="" style="width:500px;" />
										</td>
									</tr>
									<tr>
										<td colspan="3" style="vertical-align:middle">
											<b>Verification Status:</b>
										</td>
										<td colspan="4">
											<input id="georeferenceverificationstatus" name="georeferenceverificationstatus" type="text" value="<?php echo $georeferenceVerificationStatus; ?>" style="width:400px;" />
										</td>
									</tr>
									<tr>
										<td colspan="3" style="vertical-align:middle">
											<b>Elevation:</b>
										</td>
										<td colspan="4">
											<input name="minimumelevationinmeters" type="text" value="" style="width:50px;" /> to
											<input name="maximumelevationinmeters" type="text" value="" style="width:50px;" /> meters
											<span style="margin-left:80px;">
												<input type="text" value="" style="width:50px;" onchange="updateMinElev(this.value)" /> to
												<input type="text" value="" style="width:50px;" onchange="updateMaxElev(this.value)" /> feet
											</span>
										</td>
									</tr>
									<tr>
										<td colspan="3">
											<b>Processing status: </b>
										</td>
										<td colspan="4">
											<select name="processingstatus">
												<option value="">Leave as is</option>
												<option>-------------------</option>
												<?php

												// Set the list of processing statuses, from the collection editor template
												$processingStatusArr = array();
												if(defined('PROCESSINGSTATUS') && PROCESSINGSTATUS){
													$processingStatusArr = PROCESSINGSTATUS;
												}
												else{
													$processingStatusArr = array('unprocessed','unprocessed/NLP','stage 1','stage 2','stage 3','pending duplicate','pending review-nfn','pending review','expert required','reviewed','closed');
												}

												foreach($processingStatusArr as $v){

													$keyOut = strtolower($v);

													echo '<option value="'.$keyOut.'">'.ucwords($v).'</option>';
												}
												?>
											</select>
											<span style="margin-left:20px;font-size:80%">
												Georefer by:
												<input name="georeferencedby" type="text" value="<?php echo $USERNAME; ?>" style="width:75px" readonly />
											</span>
										</td>
									</tr>
									<tr>
										<td colspan="7">
											<input name="submitaction" type="submit" value="Update Coordinates" />
											<span id="workingspan" style="display:none;">
												<img src="../../images/workingcircle.gif" />
											</span>
											<input name="qcountry" type="hidden" value="<?php echo $qCountry; ?>" />
											<input name="qstate" type="hidden" value="<?php echo $qState; ?>" />
											<input name="qcounty" type="hidden" value="<?php echo $qCounty; ?>" />
											<input name="qmunicipality" type="hidden" value="<?php echo $qMunicipality; ?>" />
											<input name="qlocality" type="hidden" value="<?php echo $qLocality; ?>" />
											<input name="qsciname" type="hidden" value="<?php echo $qSciname; ?>" />
											<input name="qvstatus" type="hidden" value="<?php echo $qVStatus; ?>" />
											<input name="qprocessingstatus" type="hidden" value="<?php echo $qProcessingStatus; ?>" />
											<input name="qdisplayall" type="hidden" value="<?php echo $qDisplayAll; ?>" />
											<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
										</td>
									</tr>
								</table>
								<div style="margin-top:15px">Note: Existing georeference field data will be replaced by incoming data.
								However, elevation data will only be added when the target fields are null.
								Georeference fields that will be replaced include: decimalLatitude, decimalLongitude, coordinateUncertaintyInMeters, geodeticdatum,
								footprintwkt, georeferencedby, georeferenceRemarks, georeferenceSources, georeferenceProtocol, georeferenceVerificationStatus </div>
							</div>
							<div>
								<?php
								if(!$countryStr && $countryArr) $countryStr = implode('|',$countryArr);
								if(!$stateStr && $stateArr) $stateStr = implode('|',$stateArr);
								if(!$countyStr && $countyArr) $countyStr = implode('|',$countyArr);
								if(!$municipalityStr && $municipalityArr) $municipalityStr = implode('|',$municipalityArr);
								if(!$processingStr && $processingArr) $processingStr = implode('|',$processingArr);
								?>
								<input name="countrystr" type="hidden" value="<?php echo htmlentities($countryStr); ?>" />
								<input name="statestr" type="hidden" value="<?php echo htmlentities($stateStr); ?>" />
								<input name="countystr" type="hidden" value="<?php echo htmlentities($countyStr); ?>" />
								<input name="municipalitystr" type="hidden" value="<?php echo htmlentities($municipalityStr); ?>" />
								<input name="processingstr" type="hidden" value="<?php echo htmlentities($processingStr); ?>" />
							</div>
						</form>
					</div>
					<?php
				}
				else{
					?>
					<div style='font-weight:bold;font-size:120%;'>
						ERROR: You do not have permission to edit this collection
					</div>
					<?php
				}
			}
			elseif($collMap){
				?>
				<div style="margin:0px 0px 20px 20xp;font-weight:bold;font-size:120%;">Batch Georeferencing Tool</div>
				<fieldset style="padding: 15px;margin:20px;">
					<legend><b>Collection Selector</b></legend>
					<form name="selectcollidform" action="batchgeoreftool.php" method="post" onsubmit="return checkSelectCollidForm(this)">
						<div><input name="selectall" type="checkbox" onclick="selectAllCollections(this);" /> Select / Unselect All</div>
						<?php
						foreach($collMap as $id => $collArr){
							echo '<div>';
							echo '<input name="collid[]" type="checkbox" value="'.$id.'" /> ';
							echo $collArr['collectionname'].' ('.$collArr['code'].')';
							echo '</div>';
						}
						?>
						<div style="margin: 15px">
							<button name="submitaction" type="submit" value="EvaluateCollections">Evaluate Collections</button>
						</div>
					</form>
					<div>* Only collections with administrative access are shown</div>
				</fieldset>
				<?php
			}
			else{
				?>
				<div style='font-weight:bold;font-size:120%;'>
					ERROR: Collection identifier is null
				</div>
				<?php
			}
			?>
		</div>
	</body>
</html>
