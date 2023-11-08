<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/sampleeditor.php');

$action = array_key_exists("action",$_POST)?$_POST["action"]:"";
$shipmentPK = array_key_exists("shipmentPK",$_REQUEST)?$_REQUEST["shipmentPK"]:"";
$samplePK = array_key_exists("samplePK",$_REQUEST)?$_REQUEST["samplePK"]:"";

$shipManager = new ShipmentManager();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

$status = "";
$errStr = '';
if($isEditor){
	if($action == 'save'){
		if($shipManager->editSample($_POST)) $status = 'close';
	}
	elseif($action == 'savenew'){
		$shipManager->setShipmentPK($shipmentPK);
		if($shipManager->addSample($_POST)) $status = 'close';
		else $errStr = $shipManager->getErrorStr();
	}
	elseif($action == 'deleteSample'){
		if($shipManager->deleteSample($samplePK)) $status = 'close';
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Sample Editor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#editForm :input").change(function() {
				$("#editForm").data("changed",true);
				$("#submitButton").prop("disabled",false).css('opacity',1);
			});

			$(window).on('beforeunload', function() {
				if($("#editForm").data("changed")) {
					return "Data changes have not been saved. Are you sure you want to leave?";
				}
		        return;
		    });

			<?php
			if($status == 'close') echo 'closeWindow();';
			?>
		});

		function validateSampleForm(f){
			if(f.sampleID.value.trim() == "" && f.sampleCode.value.trim() == ""){
				alert("Sample ID OR Sample Code must have a value ");
				return false;
			}
			if(f.individualCount.value.trim() != "" && !isNumeric(f.individualCount.value)){
				alert("Individual Count field must be a numeric value");
				return false;
			}
			if(f.filterVolume.value.trim() != "" && !isNumeric(f.filterVolume.value)){
				alert("Filter Volume field must be a numeric value");
				return false;
			}
			$("#editForm").data("changed",false);
			return true;
		}

		function isNumeric(inStr){
		   	var validChars = "0123456789-.";
		   	var isNumber = true;
		   	var charVar;

		   	for(var i = 0; i < inStr.length && isNumber == true; i++){
		   		charVar = inStr.charAt(i);
				if(validChars.indexOf(charVar) == -1){
					isNumber = false;
					break;
		      	}
		   	}
			return isNumber;
		}

		function checkCollectionTransfer(f){
			if(f.samplePK && f.samplePK.value){
				$.ajax({
					type: "POST",
					url: "rpc/confirmCollectionTransfer.php",
					dataType: 'json',
					data: { samplepk: f.samplePK.value ,classnew: f.sampleClass.value }
				}).done(function( resJson ) {
					if(resJson.code == 1){
						$('<div></div>').appendTo('body')
						.html('<div>This sampleClass change requieres occurrence<br/>record to be transferred to '+resJson.collCode+'.<br/>Do you want it to be done automatically?</div>')
						.dialog({
							modal: true,
							title: 'Delete message',
							zIndex: 10000,
							autoOpen: true,
							width: 'auto',
							resizable: false,
							buttons: {
								Yes: function() {
									$.ajax({
										type: "POST",
										url: "rpc/transferOccurrence.php",
										dataType: 'text',
										data: { occid: resJson.occid, target: resJson.targetCollid }
									}).done(function( resultCode ) {
										if(resultCode == 0) alert("FAILED transferring occurrence record");
									});
									$(this).dialog("close");
								},
								No: function() {
									$(this).dialog("close");
								}
							},
							close: function(event, ui) {
								$(this).remove();
							}
						});
					}
					else if(resJson.code == 2){
						alert("Is this sampleClass valid? Unable to locate within collection's table");
					}
				});
			}
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
		button { cursor:pointer; }
	</style>
</head>
<body>
<div id="popup-innertext">
	<?php
	if($isEditor){
		if($errStr) echo '<div style="color:red;margin:15px;">'.$errStr.'</div>';
		$sampleArr = array();
		if($samplePK) $sampleArr = $shipManager->getSampleArr($samplePK);
		?>
		<fieldset style="width:800px; margin-left:auto;margin-right:auto;">
			<legend><b><?php echo ($samplePK?(isset($sampleArr['sampleCode'])?$sampleArr['sampleCode']:$sampleArr['sampleID']).' (#'.$samplePK.')':'New Record'); ?></b></legend>
			<form id="editForm" method="post" action="sampleeditor.php" onsubmit="return validateSampleForm(this)">
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Sample ID:</b> <input name="sampleID" type="text" value="<?php echo isset($sampleArr['sampleID'])?$sampleArr['sampleID']:''; ?>" style="width:500px" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Sample Code:</b> <input name="sampleCode" type="text" value="<?php echo isset($sampleArr['sampleCode'])?$sampleArr['sampleCode']:''; ?>" style="width:250px" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Alt. Sample ID(s):</b> <input name="alternativeSampleID" type="text" value="<?php echo isset($sampleArr['alternativeSampleID'])?$sampleArr['alternativeSampleID']:''; ?>" style="width:500px" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Sample Class:</b> <input name="sampleClass" type="text" value="<?php echo isset($sampleArr['sampleClass'])?$sampleArr['sampleClass']:''; ?>" onchange="checkCollectionTransfer(this.form)" style="width:600px" required />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Named Location:</b> <input name="namedLocation" type="text" value="<?php echo isset($sampleArr['namedLocation'])?$sampleArr['namedLocation']:''; ?>" style="" />
					</div>
					<div class="fieldDiv">
						<b>Quarantine Status:</b>
						<select name="quarantineStatus">
							<?php
							$quarValue = 'N';
							if(isset($sampleArr['quarantineStatus'])) $quarValue = strtoupper($sampleArr['quarantineStatus']);
							?>
							<option value="">-----</option>
							<option value="Y" <?php if($quarValue=='Y') echo 'SELECTED'; ?>>Y</option>
							<option value="N" <?php if($quarValue=='N') echo 'SELECTED'; ?>>N</option>
							<?php
							if($quarValue && $quarValue != 'Y' && $quarValue != 'N'){
								echo '<option value="'.$quarValue.'" SELECTED>'.$quarValue.'</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Collect Date:</b> <input name="collectDate" type="date" value="<?php echo isset($sampleArr['collectDate'])?$sampleArr['collectDate']:''; ?>" style="" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Taxon ID:</b> <input name="taxonID" type="text" value="<?php echo isset($sampleArr['taxonID'])?$sampleArr['taxonID']:''; ?>" style="width:100px" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Individual Count:</b> <input name="individualCount" type="text" value="<?php echo isset($sampleArr['individualCount'])?$sampleArr['individualCount']:''; ?>" style="" />
					</div>
					<div class="fieldDiv">
						<b>Filter Volume:</b> <input name="filterVolume" type="text" value="<?php echo isset($sampleArr['filterVolume'])?$sampleArr['filterVolume']:''; ?>" style="" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Domain Remarks:</b> <input name="domainRemarks" type="text" value="<?php echo isset($sampleArr['domainRemarks'])?$sampleArr['domainRemarks']:''; ?>" style="width: 500px" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Notes:</b> <input name="sampleNotes" type="text" value="<?php echo isset($sampleArr['sampleNotes'])?$sampleArr['sampleNotes']:''; ?>" style="width:500px" />
					</div>
				</div>
				<div style="clear:both;margin:15px">
					<?php
					if($samplePK){
						?>
						<input name="samplePK" type="hidden" value="<?php echo $samplePK; ?>" />
						<div><button id="submitButton" type="submit" name="action" value="save" style="opacity: 50%">Save Changes</button></div>
						<?php
					}
					else{
						?>
						<input name="checkinSample" type="checkbox" value="1" checked /> check-in sample<br/>
						<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
						<div><button id="submitButton" type="submit" name="action" value="savenew" style="opacity: 50%">Save Record</button></div>
						<?php
					}
					?>
				</div>
			</form>
		</fieldset>
		<?php
		if($samplePK){
			$occid = (isset($sampleArr['occid']) && $sampleArr['occid']?$sampleArr['occid']:'');
			?>
			<fieldset style="width:800px;margin-left:auto;margin-right:auto;">
				<legend><b>Delete <?php echo (isset($sampleArr['sampleCode'])?$sampleArr['sampleCode']:$sampleArr['sampleID']).' (#'.$samplePK.')'; ?></b></legend>
				<?php
				if($occid){
					echo '<div style="color:red;margin:20px 0px">';
					echo 'Sample can\'t be deleted until linked occurrence (<a href="../../collections/editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">#'.$occid.'</a>) is deleted or unlinked. ';
					echo 'If an IGSN has been assigned, do NOT delete the occurrence record. Instead, contact an data administrator to evaluate the situtation.';
					echo '</div>';
				}
				else{
					?>
					<form method="post" action="sampleeditor.php" onsubmit="return confirm('Are you sure you want to permanently delete this sample?')">
						<div style="clear:both;margin:15px">
							<input name="samplePK" type="hidden" value="<?php echo $samplePK; ?>" />
							<button type="submit" name="action" value="deleteSample" <?php echo ($occid?'disabled':''); ?>>Delete Sample</button>
						</div>
					</form>
					<?php
				}
				?>
			</fieldset>
			<?php
		}
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