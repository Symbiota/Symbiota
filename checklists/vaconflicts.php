<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistVoucherReport.php');
@include_once($SERVER_ROOT.'/content/lang/checklists/vaconflicts.'.$LANG_TAG.'.php');

$action = array_key_exists("submitaction",$_REQUEST)?$_REQUEST["submitaction"]:"";
$clid = array_key_exists("clid",$_REQUEST)?$_REQUEST["clid"]:0;
$pid = array_key_exists("pid",$_REQUEST)?$_REQUEST["pid"]:"";
$startPos = (array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0);

$vManager = new ChecklistVoucherReport();
$vManager->setClid($clid);

$isEditor = false;
if($IS_ADMIN || (array_key_exists("ClAdmin",$USER_RIGHTS) && in_array($clid,$USER_RIGHTS["ClAdmin"]))){
	$isEditor = true;
}

?>
<script>
	function selectAll(cbox){
		var boxesChecked = true;
		if(!cbox.checked) boxesChecked = false;
		var f = cbox.form;
		for(var i=0;i<f.length;i++){
			if(f.elements[i].name == "occid[]") f.elements[i].checked = boxesChecked;
		}
	}

	function validateBatchConflictForm(f){
		var formVerified = false;
		for(var h=0;h<f.length;h++){
			if(f.elements[h].name == "occid[]" && f.elements[h].checked){
				formVerified = true;
				break;
			}
		}
		if(!formVerified){
			alert("<?php echo (isset($LANG['SELECT_ONE'])?$LANG['SELECT_ONE']:'At least one voucher record needs to be selected'); ?>");
			return false;
		}
		f.submit();
	}
</script>
<div id="innertext" style="background-color:white;">
	<div style="margin-bottom:10px;">
		<?php
		echo (isset($LANG['EXPLAIN_PARAGRAPH'])?$LANG['EXPLAIN_PARAGRAPH']:'List of specimen vouchers where the current identifications conflict with the checklist.
		Voucher conflicts are typically due to recent annotations of specimens located within a collection. Click on Checklist ID to open the editing pane for that record');
		?>
	</div>
	<?php
	if($conflictArr = $vManager->getConflictVouchers()){
		echo '<div style="font-weight:bold;">Conflict Count: '.count($conflictArr).'</div>';
		?>
		<form name="batchConflictForm" method="post" action="voucheradmin.php">
			<table class="styledtable" style="font-family:Arial;font-size:12px;">
				<tr>
					<th><input type="checkbox" onclick="selectAll(this)" /></th>
					<th><b><?php echo (isset($LANG['CHECK_ID'])?$LANG['CHECK_ID']:'Checklist ID'); ?></b></th>
					<th><b><?php echo (isset($LANG['VOUCHER_SPEC'])?$LANG['VOUCHER_SPEC']:'Voucher Specimen'); ?></b></th>
					<th><b><?php echo (isset($LANG['CORRECTED_ID'])?$LANG['CORRECTED_ID']:'Corrected Specimen ID'); ?></b></th>
					<th><b><?php echo (isset($LANG['IDED_BY'])?$LANG['IDED_BY']:'Identified By'); ?></b></th>
				</tr>
				<?php
				foreach($conflictArr as $id => $vArr){
					?>
					<tr>
						<td>
							<input name="occid[]" type="checkbox" value="<?php echo $vArr['occid']; ?>" />
						</td>
						<td>
							<a href="#" onclick="return openPopup('clsppeditor.php?tid=<?php echo htmlspecialchars($vArr['tid'], HTML_SPECIAL_CHARS_FLAGS) . "&clid=" . htmlspecialchars($vArr['clid'], HTML_SPECIAL_CHARS_FLAGS); ?>','editorwindow');">
								<?php echo $vArr['listid']; ?>
							</a>
							<?php
							if($vArr['clid'] != $clid) echo '<br/>'.(isset($LANG['FROM_CHILD'])?$LANG['FROM_CHILD']:'(from child checklists)');
							?>
						</td>
						<td>
							<a href="#" onclick="return openPopup('../collections/individual/index.php?occid=<?php echo htmlspecialchars($vArr['occid'], HTML_SPECIAL_CHARS_FLAGS); ?>','occwindow');">
								<?php echo $vArr['recordnumber']; ?>
							</a>
						</td>
						<td>
							<?php echo $vArr['specid'] ?>
						</td>
						<td>
							<?php echo $vArr['identifiedby'] ?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<div>
				<input name="removetaxa" type="checkbox" value="1" checked />
				<?php echo (isset($LANG['REMOVE_TAXA'])?$LANG['REMOVE_TAXA']:'Remove taxa from checklist if all vouchers are removed'); ?>
			</div>
			<div style="margin: 10px 0px">
				<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
				<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
				<input name="tabindex" type="hidden" value="2" />
				<input name="submitaction" type="hidden" value="resolveconflicts" />
				<b><?php echo (isset($LANG['BATCH_ACTION'])?$LANG['BATCH_ACTION']:'Batch Action'); ?>:</b>
				<button name="submitbutton" type="button" value="Link Vouchers to Corrected Identification" onclick="return validateBatchConflictForm(this.form)"><?php echo (isset($LANG['LINK_VOUCHERS'])?$LANG['LINK_VOUCHERS']:'Link Vouchers to Corrected Identification'); ?></button><br/>
				<div>* <?php echo (isset($LANG['CORRECTED_WILL_ADD'])?$LANG['CORRECTED_WILL_ADD']:'Corrected taxon will be added to checklist if not yet present'); ?></div>
			</div>
		</form>
		<?php
	}
	else echo '<h3>'.(isset($LANG['NO_CONFLICTS'])?$LANG['NO_CONFLICTS']:'No conflicts exist').'</h3>';
	?>
</div>