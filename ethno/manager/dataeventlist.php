<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$tabIndex = array_key_exists("tabindex",$_REQUEST)?$_REQUEST["tabindex"]:0;
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

$ethnoDataManager = new EthnoDataManager();

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($collId)) $collId = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;
$statusStr = '';

$isAdmin = false;
if($SYMB_UID){
	if($IS_ADMIN){
		$isAdmin = true;
	}
	elseif($collId && ((array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS["CollAdmin"])) || (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS["CollEditor"])))){
		$isAdmin = true;
	}
}

if($action === 'Delete Data Event Record'){
	$ethnoDataManager->deleteDataEvent($_POST);
}

$ethnoDataManager->setCollid($collId);
$ethnoPersonnelArr = $ethnoDataManager->getPersonnelArr();
$ethnoCommunityArr = $ethnoDataManager->getCommunityArr();
$ethnoReferenceArr = $ethnoDataManager->getReferenceArr();
$dataEventArr = $ethnoDataManager->getDataEventArr();
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Manage Data Collection Events</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=131106" type="text/javascript"></script>
	<script>
		function verifyNewDataEventForm(f){
			var sourceValue = document.getElementById('newEthnoDataEventSourceSelector').value;
			var colsultantVerified = false;
			var referenceVerified = false;
			for(var h=0;h<f.length;h++){
				if(f.elements[h].name === "consultant[]" && f.elements[h].checked){
					colsultantVerified = true;
				}
				if(f.elements[h].name === "refid" && f.elements[h].value){
					referenceVerified = true;
				}
			}
			if(sourceValue === 'elicitation' && !colsultantVerified){
				alert("Please select at least one consultant.");
				return false;
			}
			else if(sourceValue === 'reference' && !referenceVerified){
				alert("Please select a reference.");
				return false;
			}
			else{
				return true;
			}
		}

		function toggleEthnoDiv(targetName){
			var plusDivId = 'plusButton'+targetName;
			var minusDivId = 'minusButton'+targetName;
			var contentDivId = 'content'+targetName;
			var display = document.getElementById(contentDivId).style.display;
			if(display === 'none'){
				document.getElementById(contentDivId).style.display = 'block';
				document.getElementById(plusDivId).style.display = 'none';
				document.getElementById(minusDivId).style.display = 'flex';
			}
			if(display === 'block'){
				document.getElementById(contentDivId).style.display = 'none';
				document.getElementById(plusDivId).style.display = 'flex';
				document.getElementById(minusDivId).style.display = 'none';
			}
		}

		function processEthnoDataEventSourceSelectorChange(){
			var selValue = document.getElementById('newEthnoDataEventSourceSelector').value;
			if(selValue === 'elicitation'){
				document.getElementById('newEthnoDataEventReference').style.display = 'none';
				document.getElementById('newEthnoDataEventCommunity').style.display = 'block';
				document.getElementById('newEthnoDataEventPersonnel').style.display = 'block';
				document.getElementById('newEthnoDataEventDate').style.display = 'flex';
				document.getElementById('newEthnoDataEventLocation').style.display = 'flex';
			}
			else if(selValue === 'reference'){
				document.getElementById('newEthnoDataEventReference').style.display = 'flex';
				document.getElementById('newEthnoDataEventCommunity').style.display = 'none';
				document.getElementById('newEthnoDataEventPersonnel').style.display = 'none';
				document.getElementById('newEthnoDataEventDate').style.display = 'none';
				document.getElementById('newEthnoDataEventLocation').style.display = 'none';
			}
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');

echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
if($isAdmin && $collId) echo '<a href="../../collections/misc/collprofiles.php?collid='.$collId.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
if($isAdmin) echo '<b>Manage Data Collection Events</b>';
else echo '<b>View Data Collection Event</b>';
echo '</div>';
?>
<!-- This is inner text! -->
<div id="innertext" style="padding-top:0;">
	<?php
	if($statusStr){
		?>
		<hr/>
		<div style="margin:15px;color:red;">
			<?php echo $statusStr; ?>
		</div>
		<?php
	}
	?>
	<div id="dataeventlistdiv" style="min-height:200px;">
		<?php
		if($isAdmin && $collId){
			?>
			<div style="float:right;">
				<a href="#" onclick="toggle('newdataeventdiv');">
					<img src="../../images/add.png" alt="Create New Data Collection Event" />
				</a>
			</div>
			<?php
		}
		?>
		<div id="newdataeventdiv" style="display:none;">
			<form name="dataaddform" action="dataeventeditor.php" method="post" onsubmit="return verifyNewDataEventForm(this);">
				<fieldset style="padding:15px;">
					<legend><b>Create Data Collection Event</b></legend>
					<div id="newEthnoDataEventSource" style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Data source:</b></span>
						<select id="newEthnoDataEventSourceSelector" name="datasource" onchange="processEthnoDataEventSourceSelectorChange();">
							<option value="elicitation">Elicitation</option>
							<option value="reference">Reference</option>
						</select>
					</div>
					<div id="newEthnoDataEventReference" style="display:none;clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Reference:</b></span>
						<?php
						if($ethnoReferenceArr){
							?>
							<select id="ethnoNewDataEventReference" name="refid" style="width:500px;">
								<option value="">----Select reference----</option>
								<?php
								foreach($ethnoReferenceArr as $k => $v){
									echo '<option value="'.$v['refid'].'">'.$v['title'].'</option>';
								}
								?>
							</select>
							<?php
						}
						else{
							echo '<div>';
							echo 'There are no references associated with this project.<br />';
							echo 'Please go to the <a href="../../ethno/manager/index.php?collid='.$collId.'&tabindex=3" target="_blank">Reference Management tab</a> to add references.';
							echo '</div>';
						}
						?>
					</div>
					<div id="newEthnoDataEventCommunity" style="clear:both;">
						<?php
						if($ethnoCommunityArr){
							?>
							<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('EthnoCommunity');">
								<div id='plusButtonEthnoCommunity' style="display:none;align-items:center;">
									Community: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
								</div>
								<div id='minusButtonEthnoCommunity' style="display:flex;align-items:center;">
									Community: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
								</div>
							</div>
							<div id="contentEthnoCommunity" style="display:block;padding-left:15px;clear:both;">
								<?php
								foreach($ethnoCommunityArr as $id => $pArr){
									echo '<input type="radio" name="ethComID" value="'.$id.'"> '.$pArr['communityname'].'<br />';
								}
								?>
							</div>
							<?php
						}
						else{
							echo '<div style="clear:both;">';
							echo 'There are no communities associated with this project.<br />';
							echo 'Please go to the <a href="../../ethno/manager/editcommunity.php?collid='.$collId.'" target="_blank">Community Management page</a> to add communities.';
							echo '</div>';
						}
						?>
					</div>
					<div id="newEthnoDataEventPersonnel" style="clear:both;margin-top:10px;">
						<?php
						if($ethnoPersonnelArr){
							?>
							<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('EthnoConsultants');">
								<div id='plusButtonEthnoConsultants' style="display:none;align-items:center;">
									Consultants: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
								</div>
								<div id='minusButtonEthnoConsultants' style="display:flex;align-items:center;">
									Consultants: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
								</div>
							</div>
							<div id="contentEthnoConsultants" style="display:block;padding-left:15px;clear:both;">
								<?php
								foreach($ethnoPersonnelArr as $id => $pArr){
									$name = $pArr['title'].' '.$pArr['firstname'].' '.$pArr['lastname'];
									echo '<input name="consultant[]" value="'.$id.'" type="checkbox" /> '.$name.'<br />';
								}
								?>
							</div>
							<?php
						}
						else{
							echo '<div style="clear:both;">';
							echo 'There are no consultants associated with this project.<br />';
							echo 'Please go to the <a href="../../ethno/manager/editpersonnel.php?collid='.$collId.'" target="_blank">Personnel Management page</a> to add consultants.';
							echo '</div>';
						}
						?>
					</div>
					<div id="newEthnoDataEventDate" style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Date:</b></span>
						<input name="eventdate" type="text" style="width:500px;" value="" />
					</div>
					<div id="newEthnoDataEventLocation" style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Location:</b></span>
						<textarea name="eventlocation" style="width:500px;height:50px;resize:vertical;"></textarea>
					</div>
					<div style="clear:both;float:right;margin-top:10px;">
						<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
						<input id="newEthnoDataCollectionButton" type="submit" name="submitaction" value="Create New Data Collection Event" />
					</div>
				</fieldset>
			</form>
		</div>

		<?php
		if($dataEventArr) {
			?>
			<div style="min-height:200px;clear:both">
				<table class="styledtable"
					   style="width:770px;font-family:Arial,serif;font-size:12px;margin-left:auto;margin-right:auto;">
					<tr>
						<th style="width:500px;">Description</th>
						<?php
						if ($isAdmin) {
							?>
							<th style="width:15px;"></th>
							<?php
						}
						?>
					</tr>
					<?php
					foreach ($dataEventArr as $devId => $dArr) {
						echo '<tr>';
						echo '<td style="width:500px;">'.$dArr['label'].'</td>'."\n";
						if ($isAdmin) {
							echo '<td style="width:15px;cursor:pointer;"><a href="dataeventeditor.php?eventid=' . $devId . '&collid=' . $collId . '"><img style="border:0px;" src="../../images/edit.png" /></a></td>' . "\n";
						}
						echo '</tr>';
					}
					?>
				</table>
			</div>
			<?php
		}
		else{
			echo '<div style="margin-top:10px;font-weight:bold;font-size:120%;">There are currently no Data Collection Event records in the database for this project.</div>';
		}
		?>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
