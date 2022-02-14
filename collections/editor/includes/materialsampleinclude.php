<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
include_once($SERVER_ROOT.'/collections/editor/includes/config/materialSampleVars.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occid = $_REQUEST['occid'];
$occIndex = $_REQUEST['occindex'];
$collid = isset($_REQUEST['collid'])?$_REQUEST['collid']:'';

$materialSampleManager = new OccurrenceEditorMaterialSample();

//Sanitation
if(!is_numeric($occid)) $occid = 0;
if(!is_numeric($collid)) $collid = 0;
$materialSampleManager->cleanFormData($_POST);

$materialSampleManager->setOccid($occid);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif($collId && array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])) $isEditor = true;
elseif(array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor'])) $isEditor = true;

$materialSampleArr = $materialSampleManager->getMaterialSampleArr();
$controlTermArr = $materialSampleManager->getMSTypeControlValues();
?>
<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
<script>
	$(document).ready(function() {
		$("#ms_preparedBy").autocomplete({
			source: function( request, response ) {
				$.getJSON( "rpc/getUsers.php", { term: request.term, collid: $("#collid").val() }, response );
			},
			minLength: 2,
			autoFocus: true,
			select: function( event, ui ) {
				if(ui.item) $("#ms_preparedByUid").val(ui.item.id);
			}
		});
	});
</script>
<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $CLIENT_ROOT; ?>/css/symb/occureditormaterialsample.css?ver=2" type="text/css" rel="stylesheet" />
<style type="text/css">
	botton { margin: 10px; }
	.fieldBlock { height: 35px; }
	.fieldBlock label{ display: block }
	.edit-control{ float:right; }
	.display-div{ margin-bottom: 3px; }
	.display-div label{ display: inline; text-decoration: underline; }
</style>
<div style="width:795px;">
	<div class="edit-control">
		<span><a href="#" onclick="$('#formDiv-0').toggle()"><img src="../../images/add.png" /></a></span>
	</div>
	<div style="margin: 20px;">
		See <a href="https://tools.gbif.org/dwca-validator/extension.do?id=http://data.ggbn.org/schemas/ggbn/terms/MaterialSample" target="_blank">GGBN Material Sample Extension</a> documentation
	</div>
	<?php
	if($isEditor){
		$msCnt = count($materialSampleArr);
		$msArr = array();
		do{
			$matSampleID = 0;
			if($msArr) $matSampleID = $msArr['matSampleID'];
			if($matSampleID){
				echo '<fieldset><legend>Material Sample</legend>';
				?>
				<div class="edit-control">
					<span><a href="#" onclick="$('#formDiv-<?php echo $matSampleID; ?>').toggle()"><img src="../../images/edit.png" /></a></span>
				</div>
				<?php
			}
			if($msArr){
				foreach($msArr as $k => $v){
					if($v && isset($MS_LABEL_ARR[$k])) echo '<div class="display-div"><label>'.$MS_LABEL_ARR[$k].'</label>: '.$v.'</div>';
				}
			}
			?>
			<div id="formDiv-<?php echo $matSampleID; ?>" style="display:<?php echo ($msCnt?'none':'block'); ?>">
				<?php
				if($matSampleID) echo '<hr/>';
				else echo '<fieldset><legend>Add New Sample</legend>';
				?>
				<form name="matSampleForm-<?php echo $matSampleID; ?>" action="occurrenceeditor.php" method="post" >
					<div style="clear:both">
						<div class="fieldBlock" id="smSampleTypeDiv">
							<label><?php echo $MS_LABEL_ARR['sampleType']; ?>: </label>
							<span class="edit-elem">
								<?php
								if(isset($controlTermArr['ommaterialsample']['sampleType'])){
									$limitToList = $controlTermArr['ommaterialsample']['sampleType']['l'];
									?>
									<select name="ms_sampleType" required>
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['sampleType']['t'] as $t){
											echo '<option '.($msArr && $msArr['sampleType'] == $t?'selected':'').'>'.$t.'</option>';
										}
										?>
									</select>
									<?php
								}
								else{
									?>
									<input type="text" name="ms_sampleType" value="<?php echo isset($msArr['materialsampletype'])?$msArr['materialsampletype']:''; ?>" required />
									<?php
								}
								?>
							</span>
						</div>
						<div class="fieldBlock" id="smCatalogNumberDiv">
							<label><?php echo $MS_LABEL_ARR['catalogNumber']; ?>: </label>
							<span class="edit-elem">
								<input type="text" name="ms_catalogNumber" value="<?php echo isset($msArr['catalogNumber'])?$msArr['catalogNumber']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smGuidDiv">
							<label><?php echo $MS_LABEL_ARR['guid']; ?>: </label>
							<span class="edit-elem">
								<input type="text" name="ms_guid" value="<?php echo isset($msArr['guid'])?$msArr['guid']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smSampleConditionDiv">
							<label><?php echo $MS_LABEL_ARR['sampleCondition']; ?>: </label>
							<span class="edit-elem">
								<?php
								if(isset($controlTermArr['ommaterialsample']['sampleCondition'])){
									$limitToList = $controlTermArr['ommaterialsample']['sampleCondition']['l'];
									?>
									<select name="ms_sampleCondition">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['sampleCondition']['t'] as $t){
											echo '<option '.($msArr && $msArr['sampleCondition'] == $t?'selected':'').'>'.$t.'</option>';
										}
										?>
									</select>
									<?php
								}
								else{
									?>
									<input type="text" name="ms_sampleCondition" value="<?php echo isset($msArr['sampleCondition'])?$msArr['sampleCondition']:''; ?>" />
									<?php
								}
								?>
							</span>
						</div>
						<div class="fieldBlock" id="smDispositionDiv">
							<label><?php echo $MS_LABEL_ARR['disposition']; ?>: </label>
							<span class="edit-elem">
								<?php
								if(isset($controlTermArr['ommaterialsample']['disposition'])){
									$limitToList = $controlTermArr['ommaterialsample']['disposition']['l'];
									?>
									<select name="ms_disposition">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['disposition']['t'] as $t){
											echo '<option '.($msArr && $msArr['disposition'] == $t?'selected':'').'>'.$t.'</option>';
										}
										?>
									</select>
									<?php
								}
								else{
									?>
									<input type="text" name="ms_disposition" value="<?php echo isset($msArr['disposition'])?$msArr['disposition']:''; ?>" />
									<?php
								}
								?>
							</span>
						</div>
						<div class="fieldBlock" id="smPreservationTypeDiv">
							<label><?php echo $MS_LABEL_ARR['preservationType']; ?>: </label>
							<span class="edit-elem">
								<?php
								if(isset($controlTermArr['ommaterialsample']['preservationType'])){
									$limitToList = $controlTermArr['ommaterialsample']['preservationType']['l'];
									?>
									<select name="ms_preservationType">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['preservationType']['t'] as $t){
											echo '<option '.($msArr && $msArr['preservationType'] == $t?'selected':'').'>'.$t.'</option>';
										}
										?>
									</select>
									<?php
								}
								else{
									?>
									<input type="text" name="ms_preservationType" value="<?php echo isset($msArr['preservationType'])?$msArr['preservationType']:''; ?>" />
									<?php
								}
								?>
							</span>
						</div>
						<div class="fieldBlock" id="smPreparationDateDiv">
							<label><?php echo $MS_LABEL_ARR['preparationDate']; ?>: </label>
							<span class="edit-elem">
								<input type="date" name="ms_preparationDate" value="<?php echo isset($msArr['preparationDate'])?$msArr['preparationDate']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smPreparedByUidDiv">
							<label><?php echo $MS_LABEL_ARR['preparedBy']; ?>: </label>
							<span class="edit-elem">
								<input id="ms_preparedBy" name="ms_preparedBy" type="text" value="<?php echo isset($msArr['preparedBy'])?$msArr['preparedBy']:''; ?>" />
								<input id="ms_preparedByUid" name="ms_preparedByUid" type="hidden" value="<?php echo isset($msArr['preparedByUid'])?$msArr['preparedByUid']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smPreparationDetailsDiv">
							<label><?php echo $MS_LABEL_ARR['preparationDetails']; ?>: </label>
							<span class="edit-elem">
								<input type="text" name="ms_preparationDetails" value="<?php echo isset($msArr['preparationDetails'])?$msArr['preparationDetails']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smIndividualCountDiv">
							<label><?php echo $MS_LABEL_ARR['individualCount']; ?>: </label>
							<span class="edit-elem">
								<input type="text" name="ms_individualCount" value="<?php echo isset($msArr['individualCount'])?$msArr['individualCount']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smSampleSizeDiv">
							<label><?php echo $MS_LABEL_ARR['sampleSize']; ?>: </label>
							<span class="edit-elem">
								<input type="text" name="ms_sampleSize" value="<?php echo isset($msArr['sampleSize'])?$msArr['sampleSize']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smStorageLocationDiv">
							<label><?php echo $MS_LABEL_ARR['storageLocation']; ?>: </label>
							<span class="edit-elem">
								<?php
								if(isset($controlTermArr['ommaterialsample']['storageLocation'])){
									$limitToList = $controlTermArr['ommaterialsample']['storageLocation']['l'];
									?>
									<select name="ms_storageLocation">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['storageLocation']['t'] as $t){
											echo '<option '.($msArr && $msArr['storageLocation'] == $t?'selected':'').'>'.$t.'</option>';
										}
										?>
									</select>
									<?php
								}
								else{
									?>
									<input type="text" name="ms_storageLocation" value="<?php echo isset($msArr['storageLocation'])?$msArr['storageLocation']:''; ?>" />
									<?php
								}
								?>
							</span>
						</div>
						<div class="fieldBlock" id="smRemarksDiv">
							<label><?php echo $MS_LABEL_ARR['remarks']; ?>: </label>
							<span class="edit-elem">
								<input type="text" name="ms_remarks" value="<?php echo isset($msArr['remarks'])?$msArr['remarks']:''; ?>" />
							</span>
						</div>
						<div style="clear:both;">
							<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
							<input name="matSampleID" type="hidden" value="<?php echo $matSampleID; ?>" />
							<input id="collid" name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="occindex" type="hidden" value="<?php echo $occIndex; ?>" />
							<input name="tabtarget" type="hidden" value="3" />
							<?php
							if($msArr){
								echo '<button name="submitaction" type="submit" value="updateMaterialSample">Save Changes</button>';
								echo '<span style="margin-left: 20px"><button name="submitaction" type="submit" value="deleteMaterialSample">Delete Sample</button></span>';
							}
							else echo '<button name="submitaction" type="submit" value="insertMaterialSample">Add Record</button>';
							?>
						</div>
					</div>
				</form>
				<?php if(!$matSampleID) echo '</fieldset>'; ?>
			</div>
			<?php
			if($matSampleID) echo '</fieldset>';
		}while($msArr = array_pop($materialSampleArr));
	}
	?>
</div>