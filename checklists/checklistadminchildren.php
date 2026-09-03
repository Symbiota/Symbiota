<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ChecklistAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('checklists/checklistadminchildren');

$clid = array_key_exists('clid', $_REQUEST) ? Sanitize::int($_REQUEST['clid']) : 0;
$pid = array_key_exists('pid', $_REQUEST) ? Sanitize::int($_REQUEST['pid']) : '';
$targetClid = array_key_exists('targetclid', $_REQUEST) ? Sanitize::int($_REQUEST['targetclid']) : '';
$transferMethod = array_key_exists('transmethod', $_REQUEST) ? Sanitize::int($_REQUEST['transmethod']) : '';
$parentClid = array_key_exists('parentclid', $_REQUEST) ? Sanitize::int($_REQUEST['parentclid']) : '';
$targetPid = array_key_exists('targetpid', $_REQUEST) ? Sanitize::int($_REQUEST['targetpid']) : '';
$copyAttributes = array_key_exists('copyattributes', $_REQUEST) ? Sanitize::int($_REQUEST['copyattributes']) : '';

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

$clArr = $clManager->getUserChecklistArr();
$childArr = $clManager->getChildrenChecklist()
?>
<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=1" type="text/javascript"></script>
<script>
	$(document).ready(function() {
		//Auto-complete for taxon search field
		const taxonInput = document.querySelector("#taxon");
		if(taxonInput){
			taxonInput.addEventListener("focus", (event) => {
				taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
				taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
				taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
				taxaSuggest.initiate("taxon", function(result){
					if(result.item){
						$("#parsetid").val(result.item.id);
					}
					else{
						$("#parsetid").val("");
						if(this.value != ""){
							alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
						}
					}
				});
			});
		}

	});

	function validateParseChecklistForm(f){
		if(f.taxon.value != "" && f.parsetid.value == ""){
			alert("<?= $LANG['SELECT_FROM_LIST'] ?>");
			return false;
		}
		return true;
	}
</script>
<style>
	.section-div{ margin: 5px 0px; }
	#taxa{ width:400px }
	#parsetid{ width:100px }
	fieldset{ padding: 15px; }
	legend{ font-weight: bold; }
	label{ font-weight: bold; }
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
		<fieldset>
			<legend><?= $LANG['LINK_NEW'] ?></legend>
			<form name="addchildform" target="checklistadmin.php" method="post">
				<div style="margin:10px;">
					<select name="clidadd" required>
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
					$clName = Sanitize::outString($cArr['name']);
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
			<a href="../profile/viewprofile.php?tabindex=1&excludeparent=<?= $clid ?>" target="_blank"><?= $LANG['CREATE_EXCLUSION_LIST'] ?></a>
		</div>
		<?php
	}
	?>
	<h2><?= $LANG['PARENTS'] ?></h2>
	<ul>
		<?php
		if($parentArr = $clManager->getParentChecklists()){
			foreach($parentArr as $k => $name){
				?>
				<li>
					<a href="checklist.php?clid=<?= $k ?>" target="_blank"><?= Sanitize::outString($name) ?></a>
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
		<div style="margin:10px 0px 20px 0px; "><?= $LANG['BATCH_PARSE_DESCRIBE'] ?></div>
		<form name="parsechecklistform" target="checklistadmin.php" method="post" onsubmit="return validateParseChecklistForm(this)">
			<div class="section-div">
				<label for="taxon"><?= $LANG['TAXONOMICNODE'] ?>:</label>
				<input id="taxon" name="taxon" type="text" style="width: 350px" />
				<label for="parsetid"><?= $LANG['PARSETID'] ?>:</label>
				<input id="parsetid" name="parsetid" type="text" >
			</div>
			<div class="section-div">
				<label for="targetclid"><?= $LANG['TARGETCHECKLIST'] ?>:</label>
				<select name="targetclid" id="targetclid" required>
					<option value=""><?= $LANG['SELECTTARGETCHECKLIST'] ?></option>
					<option value="0"><?= $LANG['CREATENEWCHECKLIST'] ?></option>
					<option value="">--------------------------</option>
					<?php
					foreach($clArr as $k => $name){
						echo '<option value="' . $k . '" ' . ($targetClid == $k?'SELECTED':'') . '>' . Sanitize::outString($name) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="section-div">
				<label for="transtaxa"><?= $LANG['TRANSFER_METHOD'] ?>:</label>
				<input name="transmethod" id="transtaxa" type="radio" value="0" style="margin-left: 5px" <?php if($transferMethod === 0) echo 'checked'; ?> required>
				<span><?= $LANG['TRANSFERTAXA'] ?></span>
				<input name="transmethod" id="copytaxa" type="radio" value="1" style="margin-left: 5px" <?php if($transferMethod == 1) echo 'checked'; ?> required>
				<span><?= $LANG['COPYTAXA'] ?></span>
			</div>
			<div class="section-div">
				<label for="parentclid"><?= $LANG['LINK_PARENT_CHECKLIST'] ?>:</label>
				<select name="parentclid" id="parentclid">
					<option value=""><?= $LANG['NO_PARENT_CHECKLIST'] ?></option>
					<option value="<?= $clid ?>"><?= $LANG['CURRENT_CHECKLIST'] ?></option>
					<option value="">--------------------------</option>
					<?php
					foreach($clArr as $k => $name){
						echo '<option value="' . $k . '" ' . ($parentClid == $k?'SELECTED':'') . '>' . $name . '</option>';
					}
					?>
				</select>
			</div>
			<div class="section-div">
				<label for="targetpid"><?= $LANG['ADD_TO_PROJECT'] ?>:</label>
				<select name="targetpid" id="targetpid">
					<option value="">--<?= $LANG['NO_ACTION'] ?>--</option>
					<option value="0"><?= $LANG['NEWPROJECT'] ?></option>
					<option value="">--------------------------</option>
					<?php
					$projArr = $clManager->getUserProjectArr();
					foreach($projArr as $k => $name){
						echo '<option value="' . $k . '" ' . ($targetPid == $k?'SELECTED':'').'>' . Sanitize::outString($name) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="section-div">
				<input name="copyattributes" id="copyattributes" type="checkbox" value="1" <?php if($copyAttributes) echo 'checked'; ?>>
				<span for="copyattributes"><?= $LANG['COPYPERMISSIONANDGENERAL'] ?></span>
			</div>
			<div class="section-div">
				<input name="tabindex" type="hidden" value="2" >
				<button name="submitaction" type="submit" value="parseChecklist"><?= $LANG['SUBMIT_FORM'] ?></button>
			</div>
		</form>
		<div><a href="<?= $CLIENT_ROOT ?>/taxa/taxonomy/taxonomydisplay.php" target="_blank"><?= $LANG['OPEN_TAX_THES_EXPLORE'] ?></a></div>
	</fieldset>
</div>
