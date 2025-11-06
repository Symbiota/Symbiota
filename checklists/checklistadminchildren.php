<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistAdmin.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/checklists/checklistadminchildren.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/checklists/checklistadminchildren.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/checklists/checklistadminchildren.en.php');

$clid = array_key_exists('clid', $_REQUEST) ? filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$pid = array_key_exists('pid', $_REQUEST) ? filter_var($_REQUEST['pid'], FILTER_SANITIZE_NUMBER_INT) : '';
$targetClid = array_key_exists('targetclid', $_REQUEST) ? filter_var($_REQUEST['targetclid'], FILTER_SANITIZE_NUMBER_INT) : '';
$transferMethod = array_key_exists('transmethod', $_REQUEST) ? filter_var($_REQUEST['transmethod'], FILTER_SANITIZE_NUMBER_INT) : 0;
$parentClid = array_key_exists('parentclid', $_REQUEST) ? filter_var($_REQUEST['parentclid'], FILTER_SANITIZE_NUMBER_INT) : '';
$targetPid = array_key_exists('targetpid', $_REQUEST) ? filter_var($_REQUEST['targetpid'], FILTER_SANITIZE_NUMBER_INT) : '';
$copyAttributes = array_key_exists('copyattributes', $_REQUEST) ? filter_var($_REQUEST['copyattributes'], FILTER_SANITIZE_NUMBER_INT) : '';

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

$clArr = $clManager->getUserChecklistArr();
$childArr = $clManager->getChildrenChecklist()
?>
<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>

<script>
	$("#taxon").autocomplete({
		source: function( request, response ) {
			$.getJSON( "<?= $CLIENT_ROOT; ?>/rpc/taxasuggest.php", { term: request.term }, response );
		},
		minLength: 3,
		autoFocus: true,
		select: function( event, ui ) {
			if(ui.item){
				$("#parsetid").val(ui.item.id);
			}
		}
	});
</script>
<style>
	.section-div{ margin-bottom: 3px; }
	#taxa{ width:400px }
	#parsetid{ width:100px }
	button{ margin:20px; }
</style>
<!-- inner text -->
<div role="main" id="innertext" style="background-color:white;">
	<div style="float:right;">
		<a href="#" onclick="toggle('addchilddiv')"><img src="../images/add.png" style="width:1.5em;" /></a>
	</div>
	<h2><?= $LANG['CHILD_CHECKLIST'] ?></h2>
	<div style="margin:25px;clear:both;">
		<?= $LANG['CHILD_DESCRIBE'] ?>
	</div>
	<div id="addchilddiv" style="margin:15px;display:none;">
		<fieldset style="padding:15px;">
			<legend><b><?= $LANG['LINK_NEW'] ?></b></legend>
			<form name="addchildform" target="checklistadmin.php" method="post" onsubmit="validateAddChildForm(this)">
				<div style="margin:10px;">
					<select name="clidadd">
						<option value=""><?= $LANG['SELECT_CHILD'] ?></option>
						<option value="">-------------------------------</option>
						<?php
						foreach($clArr as $k => $name){
							if(!isset($childArr[$k])) echo '<option value="'.$k.'">'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div style="margin:10px;">
					<button name="submitaction" type="submit" value="addChildChecklist"><?= $LANG['ADD_CHILD'] ?></button>
					<input name="clid" type="hidden" value="<?= $clid ?>" />
					<input name="pid" type="hidden" value="<?= $pid ?>" />
					<input name="tabindex" type="hidden" value="2" />
				</div>
			</form>
		</fieldset>
	</div>
	<div style="margin:15px;">
		<ul>
			<?php
			$displayExclusionCreateQucikLink = true;
			if($childArr){
				foreach($childArr as $k => $cArr){
					$k = htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					$clName = htmlspecialchars($cArr['name'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					?>
					<li>
						<a href="checklist.php?clid=<?= $k ?>" target="_blank"><?= $clName ?></a>
						<?php
						if($cArr['pclid'] == $clid){
							$confirmStr = $LANG['SURE'] . $clName . $LANG['AS_CHILD'];
							echo '<a href="checklistadmin.php?submitaction=delchild&tabindex=2&cliddel=' . $k . '&clid=' . $clid . '&pid=' . $pid . '" onclick="return confirm(\'' . $confirmStr . '\')">';
							echo '<img src="../images/del.png" style="width:1em;" /></a>';
							echo '</a>';
						}
						?>
					</li>
					<?php
					if($cArr['type'] == 'excludespp') $displayExclusionCreateQucikLink = false;
				}
			}
			else{
				echo '<div style="font-size:110%;">' . $LANG['NO_CHILDREN'] . '</div>';
			}
			?>
		</ul>
	</div>
	<?php
	if($displayExclusionCreateQucikLink){
		?>
		<div style="margin:15px;">
			<a href="../profile/viewprofile.php?excludeparent=<?= $clid ?>" target="_blank"><?= $LANG['CREATE_EXCLUSION_LIST'] ?></a>
		</div>
		<?php
	}
	?>
	<h2><?= $LANG['PARENTS'] ?></h2>
		<ul>
			<?php
			if($parentArr = $clManager->getParentChecklists()){
				foreach($parentArr as $k => $name){
					$k = htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					$name = htmlspecialchars($name, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					?>
					<li>
						<a href="checklist.php?clid=<?= $k ?>" target="_blank"><?= $name ?></a>
					</li>
					<?php
				}
			}
			else{
				echo '<div style="font-size:110%;">' . $LANG['NO_PARENTS'] . '</div>';
			}
			?>
		</ul>
	</div>
	<hr>
	<div style="margin:20px 0px;">
		<fieldset>
			<legend><?= $LANG['BATCH_PARSE_SP_LIST']; ?></legend>
			<div style="margin:10px 0px;"><?= $LANG['BATCH_PARSE_DESCRIBE']; ?></div>
			<form name="parsechecklistform" target="checklistadmin.php" method="post" onsubmit="validateParseChecklistForm(this)">
				<div class="section-div">
					<label for="taxon"><?= $LANG['TAXONOMICNODE'] ?>:</label>
					<input id="taxon" name="taxon" type="text" required />
					<label for="parsetid"><?= $LANG['PARSETID'] ?>:</label>
					<input id="parsetid" name="parsetid" type="text" required >
				</div>
				<div class="section-div">
					<label for="targetclid"><?= $LANG['TARGETCHECKLIST'] ?>:</label>
					<select name="targetclid" id="targetclid" required>
						<option value=""><?= $LANG['SELECTTARGETCHECKLIST'] ?></option>
						<option value="0"><?= $LANG['CREATENEWCHECKLIST'] ?></option>
						<option value="">--------------------------</option>
						<?php
						foreach($clArr as $k => $name){
							if(!isset($childArr[$k])) echo '<option value="'.$k.'" '.($targetClid == $k?'SELECTED':'').'>'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div class="section-div">
					<label><?= $LANG['TRANSFER_METHOD'] ?>:</label>
					<input name="transmethod" id="transtaxa" type="radio" value="0" <?php if(!$transferMethod) echo 'checked'; ?>>
					<label for="transtaxa"><?= $LANG['TRANSFERTAXA'] ?></label>
					<input name="transmethod" id="copytaxa" type="radio" value="1" <?php if($transferMethod == 1) echo 'checked'; ?>>
					<label for="copytaxa"><?= $LANG['COPYTAXA'] ?></label>
				</div>
				<div class="section-div">
					<label><?= $LANG['LINK_PARENT_CHECKLIST'] ?>:</label>
					<select name="parentclid">
						<option value=""><?= $LANG['NOPARENTCHECKLIST'] ?></option>
						<option value="0" <?php if($parentClid === 0) echo 'SELECTED'; ?>><?= $LANG['CREATENEWCHECKLIST'] ?></option>
						<option value="">--------------------------</option>
						<?php
						foreach($clArr as $k => $name){
							if(!isset($childArr[$k])) echo '<option value="' . $k . '" ' . ($parentClid == $k?'SELECTED':'') . '>' . $name . '</option>';
						}
						?>
					</select>
				</div>
				<div class="section-div">
					<label><?= $LANG['ADD_TO_PROJECT'] ?>:</label>
					<select name="targetpid">
						<option value="">--<?= $LANG['NO_ACTION'] ?>--</option>
						<option value="0"><?= $LANG['NEWPROJECT'] ?></option>
						<option value="">--------------------------</option>
						<?php
						$projArr = $clManager->getUserProjectArr();
						foreach($projArr as $k => $name){
							echo '<option value="'.$k.'" '.($targetPid == $k?'SELECTED':'').'>'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div class="section-div">
					<input name="copyattributes" id="copyattributes" type="checkbox" value="1" <?php if($copyAttributes) echo 'checked'; ?>>
					<label for="copyattributes"><?= $LANG['COPYPERMISSIONANDGENERAL'] ?></label>
				</div>
				<div class="section-div">
					<input name="tabindex" type="hidden" value="2" >
					<button name="submitaction" type="submit" value="parseChecklist"><?= $LANG['PARSE_CHECKLIST'] ?></button>
				</div>
			</form>
			<div><a href="<?= $CLIENT_ROOT ?>/taxa/taxonomy/taxonomydisplay.php" target="_blank"><?= $LANG['OPEN_TAX_THES_EXPLORE'] ?></a></div>
		</fieldset>
	</div>
</div>