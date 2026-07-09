<?php
$LANG = array();
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/TaxonomyEditorManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('taxa/taxonomy/taxonomydelete');

$tid = Sanitize::int($_REQUEST['tid']) ?? '';
$genusStr = $_REQUEST['genusstr'] ?? '';

$taxonEditorObj = new TaxonomyEditorManager();
$taxonEditorObj->setTid($tid);
$verifyArr = $taxonEditorObj->verifyDeleteTaxon();
?>
<script>
	$(document).ready(function() {
		setTaxaSuggestRootPath("<?= $CLIENT_ROOT ?>");
		initiateTaxaSuggest("remapvalue", "remaptid");
	});

	function validateRemapTaxonForm(f){
		if(f.remapvalue.value == ""){
			alert("<?= $LANG['NO_TARGET_TAXON'] ?>");
			return false;
		}
		if(f.remaptid.value == ""){
			alert("<?= $LANG['NO_TARGET_TAXON'] ?>");
			return false;
		}
		return true;
	}
</script>
<div style="min-height:400px; height:auto !important; height:400px; ">
	<div style="margin:15px 0px">
		<?= $LANG['TAXON_MUST_BE_EVALUATED'] ?>

	</div>
	<div style="margin:15px;">
		<b><?= $LANG['CHILDREN_TAXA'] ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('child',$verifyArr)){
				$childArr = $verifyArr['child'];
				echo '<div style="color:red;">' . $LANG['CHILDREN_EXIST'] . '</div>';
				foreach($childArr as $childTid => $childSciname){
					echo '<div style="margin:3px 10px;"><a href="taxoneditor.php?tid=' . $childTid . '" target="_blank">' . Sanitize::outString($childSciname) . '</a></div>';
				}
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_CHILDREN'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?= $LANG['SYN_LINKS'] ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('syn',$verifyArr)){
				$synArr = $verifyArr['syn'];
				echo '<div style="color:red;">' . $LANG['SYN_EXISTS'] . '</div>';
				foreach($synArr as $synTid => $synSciname){
					echo '<div style="margin:3px 10px;"><a href="taxoneditor.php?tid=' . $synTid . '" target="_blank">' . Sanitize::outString($synSciname) . '</a></div>';
				}
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_SYNS'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b>Images</b>
		<div style="margin:10px">
			<?php
			if($verifyArr['img'] > 0){
				?>
				<span style="color:red;"><?= $LANG['WARNING'] . ": " . $verifyArr['img'] . ' ' . $LANG['IMGS_LINKED'] ?></span>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_IMGS'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b>Taxon Maps</b>
		<div style="margin:10px">
			<?php
			if($verifyArr['map'] > 0){
				?>
				<span style="color:red;"><?= $LANG['WARNING'] . ': ' . $verifyArr['map'] . ' ' . $LANG['MAPS_LINKED'] ?></span>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>: </span> <?= $LANG['NO_MAPS'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?= $LANG['VERNACULARS'] ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('vern',$verifyArr)){
				$displayStr = implode(', ',$verifyArr['vern']);
				?>
				<span style="color:red;"><?= $LANG['LINKED_VERNACULAR'] ?>:</span> <?= $displayStr ?>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_VERNACULAR'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?= $LANG['TEXT_DESCRIPTIONS'] ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('tdesc',$verifyArr)){
				?>
				<span style="color:red;"><?= $LANG['DESC_EXISTS'] ?>:</span>
				<ul>
					<?php
					echo '<li>'.implode('</li><li>',$verifyArr['tdesc']).'</li>';
					?>

				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_DESCS'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?= $LANG['OCC_RECORDS'] ?>:</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('occur',$verifyArr)){
				?>
				<span style="color:red;"><?= $LANG['LINKED_OCC_EXIST'] ?>:</span>
				<ul>
					<?php
					foreach($verifyArr['occur'] as $occid){
						echo '<li>';
						echo '<a href="../../collections/individual/index.php?occid=' . $occid . '">#' . $occid . '</a>';
						echo '</li>';
					}
					?>
				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_OCCS_LINKED'] ?>
				<?php
			}
			?>
			<?php
			if(array_key_exists('dets',$verifyArr)){
				?>
				<span style="color:red;"><?= $LANG['DETS_EXIST'] ?>:</span>
				<ul>
					<?php
					foreach($verifyArr['dets'] as $occid){
						echo '<li>';
						echo '<a href="../../collections/individual/index.php?occid=' . $occid . '" target="_blank">#' . $occid . '</a>';
						echo '</li>';
					}
					?>
				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_DETS_LINKED'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?= $LANG['CHECKLISTS'] ?>:</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('cl',$verifyArr)){
				$clArr = $verifyArr['cl'];
				?>
				<span style="color:red;"><?= $LANG['CHECKLISTS_EXIST'] ?>:</span>
				<ul>
					<?php
					foreach($clArr as $clid => $clTitle){
						echo '<li><a href="../../checklists/checklist.php?clid=' . $k . '" target="_blank">';
						echo Sanitize::outString($clTitle);
						echo '</a></li>';
					}
					?>
				</ul>
				<?php
			}
			else{
				echo '<span style="color:green;">' . $LANG['APPROVED'] . ':</span> ';
				echo $LANG['NO_CHECKLISTS'];
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?= $LANG['MORPHO_CHARACTERS'] ?>:</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('kmdecr',$verifyArr)){
				echo '<span style="color:red;">';
				echo $LANG['WARNING'] . ': ' . $verifyArr['kmdecr'] . ' ' . $LANG['LINKED_MORPHO'];
				echo '</span>';
			}
			else{
				echo '<span style="color:green;">' . $LANG['APPROVED'] . ':</span> ';
				echo $LANG['NO_MORPHO'];
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?= $LANG['LINKED_RESOURCES'] ?>:</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('link',$verifyArr)){
				?>
				<span style="color:red;"><?= $LANG['LINKED_RESOURCES_EXIST'] ?></span>
				<ul>
					<?php
					echo '<li>'.implode('</li><li>',$verifyArr['link']).'</li>';
					?>

				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?= $LANG['APPROVED'] ?>:</span> <?= $LANG['NO_RESOURCES'] ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<fieldset style="padding:15px;">
			<legend><b><?= $LANG['REMAP_RESOURCES'] ?></b></legend>
			<form name="remaptaxonform" method="post" action="taxoneditor.php" onsubmit="validateRemapTaxonForm(this.form)">
				<span style="color:red;"><?= $LANG['WARNING_REMAP'] ?></span>
				<div style="margin-top:5px;margin-bottom:5px;">
					<?= $LANG['TARGET_TAXON'] ?>:
					<input id="remapvalue" name="remapvalue" type="text" value="" style="width:550px;" /><br/>
					<input name="remaptid" type="hidden" value="" />
				</div>
				<div>
					<button name="submitaction" type="submit" value="remapTaxon"><?= $LANG['REMAP_TAXON'] ?></button>
					<input name="tid" type="hidden" value="<?= $tid ?>" />
					<input name="genusstr" type="hidden" value="<?= Sanitize::outString($genusStr) ?>" />
				</div>
			</form>
		</fieldset>
	</div>
	<div style="margin:15px;">
		<fieldset style="padding:15px;">
			<legend><b><?= $LANG['DELETE_TAX_AND_RES'] ?></b></legend>
			<div style="margin:10px 0px;">
			</div>
			<form name="deletetaxonform" method="post" action="taxoneditor.php" onsubmit="return confirm('<?= $LANG['SURE_DELETE'] ?>')">
				<?php
				$deactivateStr = '';
				if(array_key_exists('child',$verifyArr)) $deactivateStr = 'disabled';
				if(array_key_exists('syn',$verifyArr)) $deactivateStr = 'disabled';
				if($verifyArr['img'] > 0) $deactivateStr = 'disabled';
				if(array_key_exists('tdesc',$verifyArr)) $deactivateStr = 'disabled';
				echo '<button class="button-danger" name="submitaction" type="submit" value="deleteTaxon" ' . $deactivateStr . '>' . $LANG['DELETE_TAXON'] . '</button>';
				?>
				<input name="tid" type="hidden" value="<?= $tid ?>" />
				<input name="genusstr" type="hidden" value="<?= Sanitize::outString($genusStr) ?>" />
				<div style="margin:15px 5px">
					<?php
					if($deactivateStr){
						?>
						<div style="font-weight:bold;">
							<?= $LANG['CANNOT_DELETE_TAXON'] ?>
						</div>
						<?php
					}
					else{
						if(array_key_exists('vern',$verifyArr)){
							?>
							<div style="color:red;">
								<?= $LANG['VERNACULARS_DELETE'] ?>
							</div>
							<?php
						}
						if(array_key_exists('kmdecr',$verifyArr)){
							?>
							<div style="color:red;">
								<?= $LANG['MORPH_DELETE'] ?>
							</div>
							<?php
						}
						if(array_key_exists('cl',$verifyArr)){
							?>
							<div style="color:red;">
								<?= $LANG['CHECKLIST_DELETE'] ?>
							</div>
							<?php
						}
						if(array_key_exists('link',$verifyArr)){
							?>
							<div style="color:red;">
								<?= $LANG['LINKED_RES_DELETE'] ?>
							</div>
							<?php
						}
					}
					?>
				</div>
			</form>
		</fieldset>
	</div>
</div>
