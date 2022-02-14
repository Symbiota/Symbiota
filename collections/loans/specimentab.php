<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/outgoing.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$loanId = $_REQUEST['loanid'];

$loanManager = new OccurrenceLoans();
if($collid) $loanManager->setCollId($collid);
$specList = $loanManager->getSpecList($loanId);
?>
<div id="outloanspecdiv">
	<div class="addSpecimenDiv">
		<fieldset>
			<legend>Link Specimens as a Batch Process <a href="#" onclick="toggle('infoBatchDiv')"><img src="../../images/info2.png" style="width:15px" /></a></legend>
			<div id="infoBatchDiv" style="margin-bottom:10px;display:none">Link multiple specimens at once by entering a list of catalog numbers on separate lines or delimited by commas.</div>
			<form name="batchaddform" action="outgoing.php" method="post">
				<div>
					<div style="float:left;margin:0px 15px"><input name="targetidentifier" type="radio" value="catnum" checked /> Target Catalog Number</div>
					<div style="float:left;"><input name="targetidentifier" type="radio" value="other" /> Target Other Catalog Numbers</div>
				</div>
				<div style="clear:both"><textarea name="catalogNumbers" cols="6" style="width:600px"></textarea></div>
				<div>
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
					<div style="float:left;margin-top:15px;margin-left:15px"><button name="formsubmit" type="submit" value="batchLinkSpecimens">Batch Link Specimens</button></div>
					<div style="float:right;margin-top:15px;"><a href="#" onclick="$('.addSpecimenDiv').toggle();">Link specimens using barcode <img src="../../images/barcode.png" style="width: 15px" /></a></div>
				</div>
			</form>
		</fieldset>
	</div>
	<div class="addSpecimenDiv" style="display:none">
		<fieldset>
			<legend>Link Specimens via Barcode <a href="#" onclick="toggle('infoBarcodeDiv')"><img src="../../images/info2.png" style="width: 14px" /></a></legend>
			<form name="barcodeaddform" method="post" onsubmit="addSpecimen(this,<?php echo (!$specList?'0':'1'); ?>);return false;">
				<div id="infoBarcodeDiv" style="display:none;margin-bottom:10px;">Scan a set of barcodes to link a stack of specimens. If the barcode reader includes a "return" after the barcode (typically the default), you will not need to click the Add Specimen button </div>
				<div style="float:left;padding-bottom:2px;">
					<b>Barcode/Catalog #: </b><input type="text" autocomplete="off" name="catalognumber" maxlength="255" style="width:300px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="" />
				</div>
				<div id="addspecsuccess" style="float:left;margin-left:30px;padding-bottom:2px;color:green;display:none;">
					SUCCESS: Specimen record added to loan.
				</div>
				<div id="addspecerr1" style="float:left;margin-left:30px;padding-bottom:2px;color:red;display:none;">
					ERROR: No specimens found with that catalog number.
				</div>
				<div id="addspecerr2" style="float:left;margin-left:30px;padding-bottom:2px;color:red;display:none;">
					ERROR: More than one specimen located with same catalog number.
				</div>
				<div id="addspecerr3" style="float:left;margin-left:30px;padding-bottom:2px;color:orange;display:none;">
					Warning: Specimen already linked to loan.
				</div>
				<div style="padding-top:10px;clear:left;float:left;">
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
					<input name="formsubmit" type="submit" value="Add Specimen" />
				</div>
			</form>
			<div id="refreshbut" style="float:left;padding-top:10px;margin-left:10px;">
				<form style="margin-bottom:0px;" name="refreshspeclist" action="outgoing.php" method="post">
					<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="tabindex" type="hidden" value="1" />
					<input name="formsubmit" type="submit" value="Refresh List" />
				</form>
			</div>
			<div style="float:right;margin-top:10px"><a href="#" onclick="$('.addSpecimenDiv').toggle();">Batch link using list <img src="../../images/list.png" style="width: 15px" /></a></div>
		</fieldset>
	</div>
	<div id="speclistdiv" style="<?php echo (!$specList?'display:none;':''); ?>">
		<form name="speceditform" action="outgoing.php" method="post" onsubmit="return verifySpecEditForm(this)" >
			<table class="styledtable" style="font-family:Arial;font-size:12px;">
				<tr>
					<th style="width:25px;text-align:center;"><input type="checkbox" onclick="selectAll(this);" title="Select/Deselect All" /></th>
					<th style="width:100px;text-align:center;">Catalog Number</th>
					<th style="width:375px;text-align:center;">Details</th>
					<th style="width:90px;text-align:center;">Date Returned</th>
				</tr>
				<?php
				foreach($specList as $occid => $specArr){
					$classStr = '';
					//if($specArr['catalognumber']) $classStr = $specArr['catalognumber'];
					//if(str_replace(array(',',';'),' ',$specArr['othercatalognumbers'])) $classStr .= ' '.$specArr['othercatalognumbers'];
					?>
					<tr>
						<td>
							<span class="<?php echo $classStr; ?>">
								<input name="occid[]" type="checkbox" value="<?php echo $occid; ?>" />
							</span>
						</td>
						<td>
							<div style="float:right">
								<a href="#" onclick="openIndPopup(<?php echo $occid; ?>); return false;"><img src="../../images/list.png" style="width:13px" title="Open Specimen Details page" /></a><br/>
								<a href="#" onclick="openEditorPopup(<?php echo $occid; ?>); return false;"><img src="../../images/edit.png" style="width:13px" title="Open Occurrence Editor" /></a>
							</div>
							<?php
							if($specArr['catalognumber']) echo '<div>'.$specArr['catalognumber'].'</div>';
							if($specArr['othercatalognumbers']) echo '<div>'.$specArr['othercatalognumbers'].'</a></div>';
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
						$editorLink = 'specnoteseditor.php?loanid='.$loanId.'&occid='.$occid.'&collid='.$collid;
						echo '<div style="float:right"><a href="#" onclick="openPopup(\''.$editorLink.'\');return false"><img src="../../images/edit.png" style="width:13px" title="Edit notes" /></a></div>';
						echo $specArr['returndate'];
						?></td>
					</tr>
					<?php
				}
			?>
			</table>
			<div id="newdetdiv" style="display:none;clear:both">
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
							<input type="submit" name="formsubmit" onclick="verifyLoanDet();" value="Add New Determinations" />
						</div>
					</div>
				</fieldset>
			</div>
			<div style="clear:both">
				<div style="margin:10px;">
					<fieldset style="float:left;width:200px">
						<legend>Actions</legend>
						<input name="applytask" type="radio" value="check" title="Check-in Specimens" CHECKED />Check-in Specimens<br/>
						<input name="applytask" type="radio" value="delete" title="Delete Specimens" />Delete Specimens from Loan

						<button name="formsubmit" type="submit" value="performSpecimenAction">Perform Action</button>
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
						<input name="tabindex" type="hidden" value="1" />
					</fieldset>
					<span style="margin-left:25px;">
						<button name="formsubmit" type="submit" value="exportSpecimenList" onclick="skipFormVerification = true">Export Specimen List</button>
					</span>
				</div>
				<div style="margin:10px;float:right;">
					<div id="detAddToggleDiv" onclick="toggle('newdetdiv');">
						<a href="#" onclick="return false;">Add New Determinations</a>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div id="nospecdiv" style="margin:20px;font-size:120%;<?php echo ($specList?'display:none;':''); ?>">There are no specimens registered for this loan.</div>
</div>
