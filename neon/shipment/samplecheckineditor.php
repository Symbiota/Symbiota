<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/samplecheckineditor.php');

$action = array_key_exists("action",$_POST)?$_POST["action"]:"";
$samplePK = array_key_exists("samplePK",$_REQUEST)?$_REQUEST["samplePK"]:"";

$shipManager = new ShipmentManager();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

$status = "";
if($isEditor){
	if($action == 'save'){
		if($shipManager->editSampleCheckin($_POST)) $status = 'close';
	}
	elseif($action == 'nullCheckin'){
		if($shipManager->resetSampleCheckin($samplePK)) $status = 'close';
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Sample Check-in Editor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
			if($status == 'close') echo 'closeWindow();';
			?>

			$("input").each(function() {
				$(this).change(function(){ $("#submitButton").prop("disabled",false); });
			});
			$("select").each(function() {
				$(this).change(function(){ $("#submitButton").prop("disabled",false); });
			});
		});

		function verifySampleEditForm(f){
			if(f.sampleReceived.value == "0"){
				if(f.acceptedForAnalysis.value != "" || f.sampleCondition.value != ""){
					alert("If sample is not received, Accepted for Analysis and Sample Condition must be NULL");
					return false;
				}
			}
			else if(f.sampleReceived.value == "1"){
				if(f.acceptedForAnalysis.value == ""){
					alert("Please select if accepted for analysis");
					return false;
				}
			}
			if(f.acceptedForAnalysis.value === "0"){
				if(f.sampleCondition.value == "ok"){
					alert("Sample Condition cannot be OK if sample is Not Accepted for Analysis");
					return false;
				}
				else if(f.sampleCondition.value == ""){
					alert("Sample Condition required when sample is tagged as Not Accepted for Analysis");
					return false;
				}
			}
			return true;
		}

		function sampleReceivedChanged(f){
			$('input:radio[name=acceptedForAnalysis]').prop("checked", false );
			$('[name=sampleCondition]').val( '' );
		}

		function closeWindow(){
			window.opener.refreshForm.submit();
			window.close();
		}
	</script>
	<style type="text/css">
		fieldset{ padding:15px }
		.fieldGroupDiv{ clear:both; margin-top:2px; height: 25px; }
		.fieldDiv{ float:left; margin-left: 10px}
	</style>
</head>
<body>
<div id="popup-innertext">
	<?php
	if($isEditor && $samplePK){
		$sampleArr = $shipManager->getSampleArr($samplePK);
		$id = (isset($sampleArr['sampleCode'])?$sampleArr['sampleCode']:'');
		if(!$id && isset($sampleArr['sampleID'])) $id = $sampleArr['sampleID'];
		?>
		<fieldset style="width:800px;margin-left:auto;margin-right:auto;">
			<legend><b><?php echo $id.' (#'.$samplePK.')'; ?></b></legend>
			<form name="checkinForm" method="post" action="samplecheckineditor.php" onsubmit="return verifySampleEditForm(this)">
				<div class="fieldGroupDiv">
					<b>Sample Received:</b>
					<?php
					$sampleReceived = (isset($sampleArr['sampleReceived'])?$sampleArr['sampleReceived']:'');
					?>
					<input name="sampleReceived" type="radio" value="1" <?php echo ($sampleReceived==1?'checked':''); ?> /> Yes
					<input name="sampleReceived" type="radio" value="0" onchange="sampleReceivedChanged(this.form)" <?php echo ($sampleReceived==='0'?'checked':''); ?> /> No
				</div>
				<div class="fieldGroupDiv">
					<b>Accepted for Analysis:</b>
					<?php
					$acceptedForAnalysis = (isset($sampleArr['acceptedForAnalysis'])?$sampleArr['acceptedForAnalysis']:'');
					?>
					<input name="acceptedForAnalysis" type="radio" value="1" <?php echo ($acceptedForAnalysis==1?'checked':''); ?> /> Yes
					<input name="acceptedForAnalysis" type="radio" value="0" <?php echo ($acceptedForAnalysis==='0'?'checked':''); ?> /> No
				</div>
				<div class="fieldGroupDiv">
					<b>Sample condition:</b>
					<select name="sampleCondition">
						<option value="">Not Set</option>
						<option value="">--------------------------------</option>
						<?php
						$sampleCondition = (isset($sampleArr['sampleCondition'])?$sampleArr['sampleCondition']:'');
						$condArr = $shipManager->getConditionArr();
						foreach($condArr as $condKey => $condValue){
							echo '<option value="'.$condKey.'" '.($condKey==$sampleCondition?'selected':'').'>'.$condValue.'</option>';
						}
						?>
					</select>
				</div>
				<div class="fieldGroupDiv">
					<b>Check-in Remarks:</b> <input name="checkinRemarks" type="text" value="<?php echo isset($sampleArr['checkinRemarks'])?$sampleArr['checkinRemarks']:''; ?>" style="width:500px" />
				</div>
				<div style="clear:both;margin:15px">
					<input name="samplePK" type="hidden" value="<?php echo $samplePK; ?>" />
					<div><button id="submitButton" type="submit" name="action" value="save" disabled>Save Changes</button></div>
				</div>
			</form>
			<?php
			if(isset($sampleArr['checkinTimestamp']) && $sampleArr['checkinTimestamp']){
				?>
				<div style="clear:both;margin:15px">
					<form name="clearCheckinForm" action="samplecheckineditor.php" method="post" onsubmit="return confirm('Are you sure you want to totally reset check-in status?')">
						<input name="samplePK" type="hidden" value="<?php echo $samplePK; ?>" />
						<button type="submit" name="action" value="nullCheckin">Clear Check-in Details</button>
					</form>
				</div>
				<?php
			}
			?>
		</fieldset>
		<?php
	}
	else{
		?>
		<div style='font-weight:bold;margin:30px;'>
			Sample identifier not set or you do not have permissions to view manifests
		</div>
		<?php
	}
	?>
</div>
</body>
</html>