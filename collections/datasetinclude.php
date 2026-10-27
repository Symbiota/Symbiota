<?php
if($SYMB_UID){
	include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
	?>
	<script>
		function selectAllDataset(cbElem){
			var boxesChecked = true;
			if(!cbElem.checked) boxesChecked = false;
			var f = cbElem.form;
			for(var i=0;i<f.length;i++){
				if(f.elements[i].name == "occid[]") f.elements[i].checked = boxesChecked;
			}
		}
	</script>
	<div id="dataset-tools" class="dataset-div" style="clear:both;display:none;margin:10px 0px">
		<fieldset>
			<legend><b><?= $LANG['DATASET_MANAGEMENT'] ?></b></legend>
			<div style="padding:5px;float:left;"><?= $LANG['DATASET_TARGET'] ?>: </div>
			<div style="padding:5px;float:left;">
				<select name="targetdatasetid" onchange="datasetSelectChanged(this)" required>
					<option value="">------------------------</option>
					<?php
					$dsManager = new OccurrenceDataset();
					if($datasetArr = $dsManager->getDatasetsByUser()){
						foreach($datasetArr as $datasetID => $datasetArr){
							if($datasetArr['role'] != 'DatasetReader'){
								//Only add if user can add occurrences
								echo '<option value="' . $datasetID.'">' . $datasetArr['datasetName'] . '</option>';
							}
						}
					}
					else echo '<option value="">' . $LANG['NO_DATASETS'] . '</option>';
					?>
					<option value="">----------------------------------</option>
					<option value="--newDataset"><?= $LANG['CREATE_DATASET'] ?></option>
				</select>
			</div>
			<div style="clear:both;margin:5px 0px">
				<span class="checkbox-elem"><input name="selectall" type="checkbox" onclick="selectAllDataset(this)" /></span>
				<span style="padding:10px;"><?= $LANG['SELECT_ALL_RECORDS'] ?></span>
			</div>
			<div style="clear:both;">
				<input name="searchvar" type="hidden" value="<?= $searchVar; ?>" />
				<div style="padding:5px 0px;float:left;"><button name="action" type="submit" value="addSelectedToDataset" onclick="return hasSelectedOccid(this.form)"><?= $LANG['ADD_SELECTED'] ?></button></div>
				<div style="padding:5px;float:left;"><button name="action" type="submit" value="addAllToDataset"><?= $LANG['ADD_COMPLETE_QUERY'] ?></button></div>
			</div>
		</fieldset>
	</div>
	<?php
}
?>