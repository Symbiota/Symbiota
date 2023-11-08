<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/samplecheckin.php?'.$_SERVER['QUERY_STRING']);

$shipManager = new ShipmentManager();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Sample Check-in</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			function checkinSample(f){
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
				if(f.acceptedForAnalysis.value === 0){
					if(f.sampleCondition.value == "ok"){
						alert("Sample Condition cannot be OK when sample is tagged as Not Accepted for Analysis");
						return false;
					}
					else if(f.sampleCondition.value == ""){
						alert("Sample Condition required when sample is tagged as Not Accepted for Analysis");
						return false;
					}
				}
				var sampleIdentifier = f.identifier.value.trim();
				if(sampleIdentifier != ""){
					//alert("rpc/checkinsample.php?identifier="+sampleIdentifier+"&received="+f.sampleReceived.value+"&accepted="+f.acceptedForAnalysis.value+"&condition="+f.sampleCondition.value+"&altSampleID="+f.alternativeSampleID.value+"&notes="+f.checkinRemarks.value);
					$.ajax({
						type: "POST",
						url: "rpc/checkinsample.php",
						dataType: 'json',
						data: { identifier: sampleIdentifier, received: f.sampleReceived.value, accepted: f.acceptedForAnalysis.value, condition: f.sampleCondition.value, altSampleID: f.alternativeSampleID.value, notes: f.checkinRemarks.value }
					}).done(function( retJson ) {
						$("#checkinText").show();
						if(retJson.status == 0){
							$("#checkinText").css('color', 'red');
							$("#checkinText").text('check-in failed!');
						}
						else if(retJson.status == 1){
							$("#checkinText").css('color', 'green');
							$("#checkinText").text('success!!!');
							$("#scSpan-"+retJson.samplePK).html("checked in");
							f.identifier.value = "";
							f.alternativeSampleID.value = "";
							if(f.formReset.checked == true){
								f.sampleReceived.value = 1;
								f.acceptedForAnalysis.value = 1;
								f.sampleCondition.value = "ok";
								f.checkinRemarks.value = "";
							}
							addToSuccessList(sampleIdentifier);
						}
						else if(retJson.status == 2){
							$("#checkinText").css('color', 'orange');
							$("#checkinText").text('sample already checked in!');
						}
						else if(retJson.status == 3){
							$("#checkinText").css('color', 'red');
							$("#checkinText").text('sample not found!');
						}
						else if(retJson.status == 4){
							$("#checkinText").css('color', 'red');
							$("#checkinText").text('shipment must be checked in first!');
						}
						else{
							$("#checkinText").css('color', 'red');
							$("#checkinText").text('Failed: unknown error!');
						}
						$("#checkinText").animate({fontSize: "125%"}, "slow");
						$("#checkinText").animate({fontSize: "100%"}, "slow");
						$("#checkinText").animate({fontSize: "125%"}, "slow");
						$("#checkinText").animate({fontSize: "100%"}, "slow").delay(6000).fadeOut();
						f.identifier.focus();
					});
				}
			}
//			$this->errorStr = 'Sample already exists with sampleID: <a href="manifestviewer.php?quicksearch='.$recArr['sampleid'].
//			'" target="_blank" onclick="window.close()">'.$recArr['sampleid'].'</a>';

			function sampleReceivedChanged(f){
				$(f.acceptedForAnalysis).prop("checked", false );
				$('[name=sampleCondition]').val( '' );
			}

			function addToSuccessList(identifierStr){
				var newAnchor = document.createElement('a');
				newAnchor.setAttribute("href", "manifestviewer.php?quicksearch="+identifierStr);
				newAnchor.setAttribute("target", "_blank");
				var newText = document.createTextNode(identifierStr);
				newAnchor.appendChild(newText);

				var newDiv = document.createElement('div');
				newDiv.setAttribute("id", identifierStr+"Div");
				newDiv.appendChild(newAnchor);

				var listElem = document.getElementById("samplelistdiv");
				listElem.insertBefore(newDiv,listElem.childNodes[0]);
			}
		</script>
		<style type="text/css">
			fieldset{ padding:15px;width:600px }
			.displayFieldDiv{ margin-bottom: 3px }
		</style>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<a href="../index.php">NEON Biorepository Tools</a> &gt;&gt;
			<b>Sample Check-in</b>
		</div>
		<div id="innertext">
			<?php
			if($isEditor){
				?>
				<div id="sampleCheckinDiv" style="width:900">
					<fieldset style="width:100%">
						<legend><b>Sample Check-in</b></legend>
						<form name="submitform" method="post" onsubmit="checkinSample(this); return false;">
							<div class="displayFieldDiv">
								<b>Identifier:</b> <input name="identifier" type="text" style="width:275px" required />
								<div id="checkinText" style="display:inline"></div>
							</div>
							<div class="displayFieldDiv">
								<b>Sample Received:</b>
								<input name="sampleReceived" type="radio" value="1" checked /> Yes
								<input name="sampleReceived" type="radio" value="0" onchange="sampleReceivedChanged(this.form)" /> No
							</div>
							<div class="displayFieldDiv">
								<b>Accepted for Analysis:</b>
								<input name="acceptedForAnalysis" type="radio" value="1" checked /> Yes
								<input name="acceptedForAnalysis" type="radio" value="0" onchange="this.form.sampleCondition.value = ''" /> No
							</div>
							<div class="displayFieldDiv">
								<b>Sample Condition:</b>
								<select name="sampleCondition">
									<option value="">Not Set</option>
									<option value="">--------------------------------</option>
									<?php
									$condArr = $shipManager->getConditionArr();
									foreach($condArr as $condKey => $condValue){
										echo '<option value="'.$condKey.'" '.($condKey=='ok'?'SELECTED':'').'>'.$condValue.'</option>';
									}
									?>
								</select>
							</div>
							<div class="displayFieldDiv">
								<b>Alternative ID:</b> <input name="alternativeSampleID" type="text" style="width:225px" />
							</div>
							<div class="displayFieldDiv">
								<b>Remarks:</b> <input name="checkinRemarks" type="text" style="width:300px" />
							</div>
							<div class="displayFieldDiv">
								<input name="formReset" type="checkbox" checked /> reset form after each submission
							</div>
							<div class="displayFieldDiv">
								<button type="submit">Submit</button>
							</div>
						</form>
					</fieldset>
				</div>
				<fieldset>
					<legend><b>Samples Checked In</b></legend>
					<div id="samplelistdiv"></div>
				</fieldset>
				<?php
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>