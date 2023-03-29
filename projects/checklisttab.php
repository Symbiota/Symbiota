<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImInventories.php');
header('Content-Type: text/html; charset='.$CHARSET);

$pid = $_REQUEST['pid'];

//Sanitation
if(!is_numeric($pid)) $pid = 0;

$projManager = new ImInventories();
$projManager->setPid($pid);
$clAddArr = $projManager->getChecklistArr();
$clRemoveArr = $projManager->getChecklistArr($pid);
?>
<div id="cltab">
	<div style="margin:10px;">
		<form name="claddform" action="index.php" method="post" onsubmit="return validateChecklistForm(this)">
			<fieldset class="form-color">
				<legend><b>Add a Checklist</b></legend>
				<select name="clid" style="width:450px;">
					<option value="">Select Checklist to Add</option>
					<option value="">-----------------------------------------</option>
					<?php
					foreach($clAddArr as $clid => $clArr){
						if(in_array($clid, $USER_RIGHTS['ClAdmin']) || $clArr['access'] == 'public'){
							if(!array_key_exists($clid, $clRemoveArr)){
								echo '<option value="'.$clid.'">'.$clArr['name'].($clArr['access'] == 'private'?' (private)':'').'</option>';
							}
						}
					}
					?>
				</select><br/>
				<input type="hidden" name="pid" value="<?php echo $pid;?>">
				<button type="submit" name="projsubmit" value="Add Checklist">Add Checklist</button>
			</fieldset>
		</form>
	</div>
	<div style="margin:10px;">
		<form name="cldeleteform" action="index.php" method="post" onsubmit="return validateChecklistForm(this)">
			<fieldset class="form-color">
				<legend><b>Delete a Checklist</b></legend>
				<select name="clid" style="width:450px;">
					<option value="">Select Checklist to Delete</option>
					<option value="">-----------------------------------------</option>
					<?php
					foreach($clRemoveArr as $clid => $clArr){
						echo '<option value="'.$clid.'">'.$clArr['name'].'</option>';
					}
					?>
				</select><br/>
				<input type="hidden" name="pid" value="<?php echo $pid;?>">
				<button type="submit" name="projsubmit" value="Delete Checklist">Delete Checklist</button>
			</fieldset>
		</form>
	</div>
</div>