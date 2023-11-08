<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/shipmenteditor.php');

$action = array_key_exists("action",$_POST)?$_POST["action"]:"";
$shipmentPK = array_key_exists("shipmentPK",$_REQUEST)?$_REQUEST["shipmentPK"]:"";

$shipManager = new ShipmentManager();
if($shipmentPK) $shipManager->setShipmentPK($shipmentPK);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;

$status = "";
if($isEditor){
	if($action == 'save'){
		if($shipManager->editShipment($_POST)) $status = 'close';
	}
	elseif($action == 'nullCheckin'){
		if($shipManager->resetShipmentCheckin()) $status = 'close';
	}
	elseif($action == 'deleteshipment'){
		if($shipManager->deleteShipment($shipmentPK)) $status = 'close';
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Shipment Editor</title>
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
		});

		function verifyShipmentEditForm(f){

			return true;
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
	if($isEditor && $shipmentPK){
		$shipArr = $shipManager->getShipmentArr();
		?>
		<fieldset style="width:800px;margin-left:auto;margin-right:auto;">
			<legend><b><?php echo $shipArr['shipmentID'].' (#'.$shipmentPK.')'; ?></b></legend>
			<form method="post" action="shipmenteditor.php">
				<div class="fieldGroupDiv">
					<div class="fieldDiv"><b>Domain:</b> <input name="domainID" type="text" value="<?php echo $shipArr['domainID']; ?>" style="width:80px;" required /></div>
					<div class="fieldDiv"><b>Date Shipped:</b> <input name="dateShipped" type="text" value="<?php echo $shipArr['dateShipped']; ?>" /></div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv"><b>Shipped From:</b> <input name="shippedFrom" type="text" value="<?php echo $shipArr['shippedFrom']; ?>" /></div>
					<div class="fieldDiv"><b>Sender ID:</b> <input name="senderID" type="text" value="<?php echo $shipArr['senderID']; ?>" style="width:200px" /></div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv"><b>Destination Facility:</b> <input name="destinationFacility" type="text" value="<?php echo $shipArr['destinationFacility']; ?>" /></div>
					<div class="fieldDiv"><b>Sent To ID:</b> <input name="sentToID" type="text" value="<?php echo $shipArr['sentToID']; ?>" /></div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv"><b>Shipment Service:</b> <input name="shipmentService" type="text" value="<?php echo $shipArr['shipmentService']; ?>" style="width:100px;" /></div>
					<div class="fieldDiv"><b>Shipment Method:</b> <input name="shipmentMethod" type="text" value="<?php echo $shipArr['shipmentMethod']; ?>" style="width:100px;" /></div>
					<div class="fieldDiv"><b>Tracking Number:</b> <input name="trackingNumber" type="text" value="<?php echo $shipArr['trackingNumber']; ?>" /></div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv"><b>General Notes:</b> <input name="shipmentNotes" type="text" value="<?php echo $shipArr['shipmentNotes']; ?>" style="width:500px" /></div>
				</div>
				<div style="clear:both;margin:15px">
					<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
					<div><button id="submitButton" type="submit" name="action" value="save" disabled>Save Changes</button></div>
					<?php
					if(isset($shipArr['checkinTimestamp']) && $shipArr['checkinTimestamp']){
						?>
						<div style="margin-top:15px">
							<button type="submit" name="action" value="nullCheckin" onclick="return confirm('Are you sure you want to totally reset check-in status?')">Clear Check-in Details</button>
						</div>
						<?php
					}
					?>
				</div>
			</form>
		</fieldset>
		<fieldset style="width:800px;margin-left:auto;margin-right:auto;">
			<?php
			$shipmentIsDeletable = $shipManager->shipmentISDeletable();
			?>
			<legend><b>Delete Shipment <?php echo $shipArr['shipmentID'].' (#'.$shipmentPK.')'; ?></b></legend>
			<form method="post" action="shipmenteditor.php" onsubmit="return confirm('WARNING: Are you sure you want to permanently delete this shipment?')">
				<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
				<button name="action" type="submit" value="deleteshipment" <?php echo ($shipmentIsDeletable?'':'DISABLED'); ?>>Delete Shipment</button>
				<?php
				if(!$shipmentIsDeletable) echo '<div style="color:red">Shipment can not be deleted! Note that all specimen links need to be removed or disassociated before a shipment manifest can be removed from the system</div>';
				?>
			</form>
		</fieldset>
		<?php
	}
	else{
		?>
		<div style='font-weight:bold;margin:30px;'>
			Shipment identifier not set or you do not have permissions to view manifests
		</div>
		<?php
	}
	?>
</div>
</body>
</html>