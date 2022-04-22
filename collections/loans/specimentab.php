<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/outgoing.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$loanId = $_REQUEST['loanid'];

$loanManager = new OccurrenceLoans();
if($collid) $loanManager->setCollId($collid);
$specList = $loanManager->getSpecimenList($loanId);
?>
<script type="text/javascript">
	var skipFormVerification = false;

	function initLoanDetAutocomplete(f){
		$( f.sciname ).autocomplete({
			source: "../editor/rpc/getspeciessuggest.php",
			minLength: 3,
			change: function(event, ui) {
				if(f.sciname.value){
					pauseSubmit = true;
					verifyLoanDetSciName(f);
				}
				else{
					f.scientificnameauthorship.value = "";
					f.family.value = "";
					f.tidtoadd.value = "";
				}
			}
		});
	}

	function verifyLoanDetSciName(f){
		$.ajax({
			type: "POST",
			url: "../editor/rpc/verifysciname.php",
			dataType: "json",
			data: { term: f.sciname.value }
		}).done(function( data ) {
			if(data){
				f.scientificnameauthorship.value = data.author;
				f.family.value = data.family;
				f.tidtoadd.value = data.tid;
			}
			else{
	            alert("WARNING: Taxon not found. It may be misspelled or needs to be added to taxonomic thesaurus by a taxonomic editor.");
				f.scientificnameauthorship.value = "";
				f.family.value = "";
				f.tidtoadd.value = "";
			}
		});
	}

	function verifyLoanDet(){
		if(document.getElementById('dafsciname').value == ""){
			alert("Scientific Name field must have a value");
			return false;
		}
		if(document.getElementById('identifiedby').value == ""){
			alert("Determiner field must have a value (enter 'unknown' if not defined)");
			return false;
		}
		if(document.getElementById('dateidentified').value == ""){
			alert("Determination Date field must have a value (enter 's.d.' if not defined)");
			return false;
		}
		return true;
	}

	function verifySpecEditForm(f){
		var cbChecked = false;
		var dbElements = document.getElementsByName("occid[]");
		for(i = 0; i < dbElements.length; i++){
			var dbElement = dbElements[i];
			if(dbElement.checked){
				cbChecked = true;
				break;
			}
		}
		if(!cbChecked){
			alert("Please select specimens to which you wish to apply the action");
			return false;
		}

		return true;
	}

	function processSpecimen(f,splist){
		if(!f.catalognumber.value){
			alert("Please enter a catalog number!");
			return false;
		}
		else{
			let mode = f.processmode.value;
			//alert("rpc/processLoanSpecimens.php?loanid="+f.loanid.value+"&catalognumber="+f.catalognumber.value+"&target="+f.targetidentifier.value+"&collid="+f.collid.value+"&processmode="+f.processmode.value);
			$.ajax({
				method: "POST",
				data: { loanid: f.loanid.value, catalognumber: f.catalognumber.value, target: f.targetidentifier.value, collid: f.collid.value, processmode: mode },
				dataType: "text",
				url: "rpc/processLoanSpecimens.php"
			})
			.done(function(retStr) {
				if(retStr == "0"){
					$("#message-span").html("ERROR: No specimens found with that catalog number");
					$("#message-span").css("color","red");
				}
				else if(retStr == "1"){
					f.catalognumber.value = '';
					let msgStr = "SUCCESS: specimen record ";
					if(mode == "link") msgStr = msgStr + "linked";
					else msgStr = msgStr + "checked-in";
					$("#message-span").html(msgStr);
					$("#message-span").css("color","green");

					if(splist == 0){
						$("#speclist-div").show();
						$("#nospecdiv").hide();
					}
				}
				else if(retStr == "2"){
					if(mode == "link"){
						$("#message-span").html("ERROR: more than one specimens located with same catalog number");
						$("#message-span").css("color","red");
					}
					else{
						$("#message-span").html("SUCCESS: but more than one specimens were checked-in");
						$("#message-span").css("color","orange");
					}
				}
				else if(retStr == "3"){
					if(mode == "link"){
						$("#message-span").html("Warning: already linked to loan");
						$("#message-span").css("color","orange");
					}
					else{
						$("#message-span").html("Warning: already checked-in or not linked to loan");
						$("#message-span").css("color","orange");
					}
				}
				else{
					f.catalognumber.value = "";
					document.refreshspeclist.submit();
				}
				setTimeout(function () {
					$("#message-span").html("");
				}, 4000);
			})
			.fail(function() {
				alert("Technical error: processing specimen failed ");
			});
		}
		return false;
	}

	function selectAll(cb){
		boxesChecked = true;
		if(!cb.checked){
			boxesChecked = false;
		}
		var dbElements = document.getElementsByName("occid[]");
		for(i = 0; i < dbElements.length; i++){
			var dbElement = dbElements[i];
			dbElement.checked = boxesChecked;
		}
	}

	function openCheckinPopup(loanId, occid, collid){
		urlStr = "specnoteseditor.php?loanid="+loanId+"&occid="+occid+"&collid="+collid;
		newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width=800,height=300,left=60,top=250');
		window.name = "parentWin";
		if(newWindow.opener == null) newWindow.opener = self;
		return false;
	}


	function displayBarcodePanel(mode,tagName){
		if(mode){
			hideAll();
			$("#barcodeSpec-div").show();
			$("."+tagName).prop("checked", true);
		}
		else{
			$("#barcodeSpec-div").hide();
			$(".speccheckin").prop("checked", false);
			$(".speclink").prop("checked", false);
		}
	}

	function displayBatchPanel(mode,tagName){
		if(mode){
			hideAll();
			$("#batchSpec-div").show();
			$("."+tagName).prop("checked", true);
		}
		else{
			$("#batchSpec-div").hide();
			$(".speccheckin").prop("checked", false);
			$(".speclink").prop("checked", false);
		}
	}

	function displayNewDetPanel(mode){
		if(mode){
			hideAll();
			$(".form-checkbox").show();
			$('#newdet-div').show();
		}
		else{
			$(".form-checkbox").hide();
			$('#newdet-div').hide();
		}
	}

	function displayBatchActionPanel(mode){
		if(mode){
			hideAll();
			$(".form-checkbox").show();
			$("#batchaction-div").show();
		}
		else{
			$(".form-checkbox").hide();
			$("#batchaction-div").hide();
		}
	}

	function hideAll(){
		displayBarcodePanel(false,null);
		displayBatchPanel(false,null);
		displayNewDetPanel(false);
		displayBatchActionPanel(false);
	}
</script>
<style type="text/css">
	table th{ text-align:center; }
	.radio-span{ margin-left: 5px }
	.info-div{ margin-bottom:10px; }
	#message-span{ margin-left:30px; padding-bottom:2px; }
	.form-checkbox{ display:none; }
	label{ font-weight: bold }
	.field-div{ margin: 10px 0px }
</style>
<div id="outloanspecdiv">
	<div id="menu-div">
		<fieldset>
			<legend>Menu Options</legend>
			<ul>
				<li><a href="#" onclick="displayBatchPanel(true,'speclink');return false;">Link specimens via list of catalog numbers</a></li>
				<li><a href="#" onclick="displayBarcodePanel(true,'speclink');return false;">Link specimens via scanning barcode</a></li>
				<li><a href="#" onclick="displayBatchPanel(true,'speccheckin');return false;">Check-in specimens via list of catalog numbers</a></li>
				<li><a href="#" onclick="displayBarcodePanel(true,'speccheckin');return false;">Check-in specimens via scanning barcode</a></li>
				<li><a href="#" onclick="displayNewDetPanel(true);return false;">Add New Determinations</a></li>
				<li><a href="outgoing.php?formsubmit=exportSpecimenList&loanid=<?php echo $loanId.'&collid='.$collid; ?>">Export Full Specimen List</a></li>
				<li><a href="#" onclick="displayBatchActionPanel(true);return false;">Display batch form select actions</a></li>
			</ul>
		</fieldset>
	</div>
	<div id="batchSpec-div" style="display:none">
		<fieldset>
			<legend>Batch Process Catalog Numbers</legend>
			<div  class="info-div">Process multiple specimens at once by entering a list of catalog numbers on separate lines or delimited by commas.</div>
			<form name="batchaddform" action="outgoing.php" method="post">
				<div class="field-div">
					<label>Processing mode:</label>
					<span class="radio-span"><input class="speclink" name="processmode" type="radio" value="link" /> Specimen Linking</span>
					<span class="radio-span"><input class="speccheckin" name="processmode" type="radio" value="checkin" /> Specimen Check-in</span>
				</div>
				<div class="field-div">
					<label>Catalog numbers:</label><br/>
					<textarea name="catalogNumbers" cols="6" style="width:700px"></textarea>
				</div>
				<div class="field-div">
					<label>Target:</label>
					<span class="radio-span"><input name="targetidentifier" type="radio" value="allid" /> All Identifiers</span>
					<span class="radio-span"><input name="targetidentifier" type="radio" value="catnum" checked /> Catalog Number</span>
					<span class="radio-span"><input name="targetidentifier" type="radio" value="other" /> Other Catalog Numbers</span>
				</div>
				<div class="field-div">
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
					<div style="float:left;margin-top:15px;margin-left:15px">
						<button name="formsubmit" type="submit" value="batchProcessSpecimens">Process Specimens</button>
					</div>
				</div>
			</form>
		</fieldset>
	</div>
	<div id="barcodeSpec-div" style="display:none">
		<fieldset>
			<legend>Barcode Scanning</legend>
			<form name="barcodeaddform" method="post" onsubmit="processSpecimen(this,<?php echo (!$specList?'0':'1'); ?>);return false;">
				<div class="info-div">Processing specimens by scanning barcodes. Barcode reader should includes a "return" after each scan (typically the default)</div>
				<div class="field-div">
					<label>Processing mode:</label>
					<span class="radio-span"><input class="speclink" name="processmode" type="radio" value="link" /> Specimen Linking</span>
					<span class="radio-span"><input class="speccheckin" name="processmode" type="radio" value="checkin" /> Specimen Check-in</span>
				</div>
				<div class="field-div">
					<label>Barcode/Catalog #:</label>
					<input type="text" autocomplete="off" name="catalognumber" maxlength="255" style="width:300px;border:2px solid black;text-align:center;" value="" />
					<span id="message-span"></span>
				</div>
				<div class="field-div">
					<label>Target:</label>
					<span class="radio-span"><input name="targetidentifier" type="radio" value="allid" /> All Identifiers</span>
					<span class="radio-span"><input name="targetidentifier" type="radio" value="catnum" checked /> Catalog Number</span>
					<span class="radio-span"><input name="targetidentifier" type="radio" value="other" /> Other Catalog Numbers</span>
				</div>
				<div style="padding-top:8px;clear:left;float:left;">
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
					<button name="formsubmit" type="submit">Process Specimen</button>
				</div>
			</form>
			<form name="refreshspeclist" action="outgoing.php" method="post" style="float:left; margin-left:10px;">
				<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
				<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
				<input name="tabindex" type="hidden" value="1" />
				<button name="formsubmit" type="submit">Refresh List</button>
			</form>
		</fieldset>
	</div>
	<div id="speclist-div" style="<?php echo (!$specList?'display:none;':''); ?>">
		<form name="speceditform" action="outgoing.php" method="post" onsubmit="return verifySpecEditForm(this)" >
			<div id="newdet-div" style="display:none;">
				<fieldset>
					<legend><b>Add a New Determinations</b></legend>
					<div style='margin:3px;'>
						<b>Identification Qualifier:</b>
						<input type="text" name="identificationqualifier" title="e.g. cf, aff, etc" />
					</div>
					<div style='margin:3px;'>
						<b>Scientific Name:</b>
						<input type="text" id="dafsciname" name="sciname" style="background-color:lightyellow;width:350px;" onfocus="initLoanDetAutocomplete(this.form)" />
						<input type="hidden" id="daftidtoadd" name="tidtoadd" value="" />
						<input type="hidden" name="family" value="" />
					</div>
					<div style='margin:3px;'>
						<b>Author:</b>
						<input type="text" name="scientificnameauthorship" style="width:200px;" />
					</div>
					<div style='margin:3px;'>
						<b>Confidence of Determination:</b>
						<select name="confidenceranking">
							<option value="8">High</option>
							<option value="5" selected>Medium</option>
							<option value="2">Low</option>
						</select>
					</div>
					<div style='margin:3px;'>
						<b>Determiner:</b>
						<input type="text" name="identifiedby" id="identifiedby" style="background-color:lightyellow;width:200px;" />
					</div>
					<div style='margin:3px;'>
						<b>Date:</b>
						<input type="text" name="dateidentified" id="dateidentified" style="background-color:lightyellow;" onchange="detDateChanged(this.form);" />
					</div>
					<div style='margin:3px;'>
						<b>Reference:</b>
						<input type="text" name="identificationreferences" style="width:350px;" />
					</div>
					<div style='margin:3px;'>
						<b>Notes:</b>
						<input type="text" name="identificationremarks" style="width:350px;" />
					</div>
					<div style='margin:3px;'>
						<input type="checkbox" name="makecurrent" value="1" /> Make this the current determination
					</div>
					<div style='margin:3px;'>
						<input type="checkbox" name="printqueue" value="1" /> Add to Annotation Print Queue
					</div>
					<div style='margin:15px;'>
						<div style="float:left;">
							<button type="submit" name="formsubmit" value="addDeterminations" onclick="return verifyLoanDet();">Add New Determinations</button>
						</div>
					</div>
				</fieldset>
			</div>
			<div id="batchaction-div" style="margin:10px;display:none">
				<fieldset style="width:800px">
					<legend>Batch Form Select Actions</legend>
					<div style="float:left;margin-right:20px">
						<button name="formsubmit" type="submit" value="checkinSpecimens">Batch Check-in Specimens</button><br/>
					</div>
					<div style="float:left;">
						<button name="formsubmit" type="submit" value="deleteSpecimens" onclick="return confirm('Are you sure you want to remove selected specimens from this loan?')">Remove Selected Specimens</button><br/>
					</div>
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
					<input name="tabindex" type="hidden" value="1" />
				</fieldset>
			</div>
			<table class="styledtable" style="font-family:Arial;font-size:12px;">
				<tr>
					<th class="form-checkbox"><input type="checkbox" onclick="selectAll(this);" title="Select/Deselect All" /></th>
					<th>&nbsp;</th>
					<th>Catalog Number</th>
					<th>Details</th>
					<th>Date Returned</th>
				</tr>
				<?php
				foreach($specList as $occid => $specArr){
					?>
					<tr>
						<td class="form-checkbox">
							<input name="occid[]" type="checkbox" value="<?php echo $occid; ?>" />
						</td>
						<td>
							<div>
								<a href="#" onclick="openIndPopup(<?php echo $occid; ?>); return false;"><img src="../../images/list.png" style="width:13px" title="Open Specimen Details page" /></a><br/>
								<a href="#" onclick="openEditorPopup(<?php echo $occid; ?>); return false;"><img src="../../images/edit.png" style="width:13px" title="Open Occurrence Editor" /></a>
							</div>
						</td>
						<td>
							<?php
							if($specArr['catalognumber']) echo '<div>'.$specArr['catalognumber'].'</div>';
							if(isset($specArr['othercatalognumbers'])) echo '<div>'.implode('<br/>',$specArr['othercatalognumbers']).'</div>';
							if($specArr['collid'] != $collid) echo '<div style="color:orange">external</div>';
							?>
						</td>
						<td>
							<?php
							if($specArr['sciname']) echo '<i>'.$specArr['sciname'].'</i>; ';
							$loc = $specArr['locality'];
							if(strlen($loc) > 500) $loc = substr($loc,400);
							if($specArr['collector']) echo $specArr['collector'].'; ';
							echo $loc;
							if($specArr['notes']) echo '<div class="notesDiv"><b>Notes:</b> '.$specArr['notes'],'</div>';
							?>
						</td>
						<td><?php
						echo '<div style="float:right"><a href="#" onclick="openCheckinPopup('.$loanId.','.$occid.','.$collid.');return false"><img src="../../images/edit.png" style="width:13px" title="Edit notes" /></a></div>';
						echo $specArr['returndate'];
						?></td>
					</tr>
					<?php
				}
			?>
			</table>
		</form>
	</div>
	<div id="nospecdiv" style="margin:20px;font-size:120%;<?php echo ($specList?'display:none;':''); ?>">There are no specimens registered for this loan.</div>
</div>
