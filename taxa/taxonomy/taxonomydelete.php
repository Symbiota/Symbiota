<?php
$LANG = array();
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyEditorManager.php');
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomydelete.'.$LANG_TAG.'.php');

$tid = $_REQUEST["tid"];
$genusStr = array_key_exists('genusstr',$_REQUEST)?$_REQUEST["genusstr"]:'';

//Sanitation
if(!is_numeric($tid)) $tid = 0;
$genusStr = filter_var($genusStr, FILTER_SANITIZE_STRING);

$taxonEditorObj = new TaxonomyEditorManager();
$taxonEditorObj->setTid($tid);
$verifyArr = $taxonEditorObj->verifyDeleteTaxon();
?>
<script>
	$(document).ready(function() {

		$("#remapvalue").autocomplete({
				source: "rpc/gettaxasuggest.php",
				minLength: 2
			}
		);
	});

	function submitRemapTaxonForm(f){
		if(f.remapvalue.value == ""){
			alert("<?php echo (isset($LANG['NO_TARGET_TAXON'])?$LANG['NO_TARGET_TAXON']:'Target taxon does not appear to be null. Please submit a taxon to remap the resources'); ?>");
			return false;
		}
		$.ajax({
			type: "POST",
			url: "rpc/gettid.php",
			data: { sciname: f.remapvalue.value }
		}).done(function( msg ) {
			if(msg == 0){
				alert("<?php echo (isset($LANG['TAXON_NOT_FOUND'])?$LANG['TAXON_NOT_FOUND']:'ERROR: Remapping taxon not found in thesaurus. Is the name spelled correctly?'); ?>");
				f.remaptid.value = "";
			}
			else{
				f.remaptid.value = msg;
				f.submit();
			}
		});
	}
</script>
<div style="min-height:400px; height:auto !important; height:400px; ">
	<div style="margin:15px 0px">
		<?php echo (isset($LANG['TAXON_MUST_BE_EVALUATED'])?$LANG['TAXON_MUST_BE_EVALUATED']:'Taxon record first needs to be evaluated before it can be deleted from the system. The evaluation ensures that the deletion of this record will not interfer with data integrity.'); ?>

	</div>
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['CHILDREN_TAXA'])?$LANG['CHILDREN_TAXA']:'Children Taxa'); ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('child',$verifyArr)){
				$childArr = $verifyArr['child'];
				echo '<div style="color:red;">'.(isset($LANG['CHILDREN_EXIST'])?$LANG['CHILDREN_EXIST']:'Warning: children taxa exist for this taxon. They must be remapped before this taxon can be removed').'</div>';
				foreach($childArr as $childTid => $childSciname){
					echo '<div style="margin:3px 10px;"><a href="taxoneditor.php?tid=' . htmlspecialchars($childTid, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">' . htmlspecialchars($childSciname, HTML_SPECIAL_CHARS_FLAGS) . '</a></div>';
				}
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_CHILDREN'])?$LANG['NO_CHILDREN']:'no children taxa are linked to this taxon'); ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['SYN_LINKS'])?$LANG['SYN_LINKS']:'Synonym Links'); ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('syn',$verifyArr)){
				$synArr = $verifyArr['syn'];
				echo '<div style="color:red;">'.(isset($LANG['SYN_EXISTS'])?$LANG['SYN_EXISTS']:'Warning: synonym links exist for this taxon. They must be remapped before this taxon can be removed').'</div>';
				foreach($synArr as $synTid => $synSciname){
					echo '<div style="margin:3px 10px;"><a href="taxoneditor.php?tid=' . htmlspecialchars($synTid, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">' . htmlspecialchars($synSciname, HTML_SPECIAL_CHARS_FLAGS) . '</a></div>';
				}
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_SYNS'])?$LANG['NO_SYNS']:'no synonyms are linked to this taxon'); ?>
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
				<span style="color:red;"><?php echo (isset($LANG['WARNING'])?$LANG['WARNING']:'Warning').": ".$verifyArr['img'].(isset($LANG['IMGS_LINKED'])?$LANG['IMGS_LINKED']:'images linked to this taxon'); ?></span>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_IMGS'])?$LANG['NO_IMGS']:'no images linked to this taxon'); ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['VERNACULARS'])?$LANG['VERNACULARS']:'Vernaculars'); ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('vern',$verifyArr)){
				$displayStr = implode(', ',$verifyArr['vern']);
				?>
				<span style="color:red;"><?php echo (isset($LANG['LINKED_VERNACULAR'])?$LANG['LINKED_VERNACULAR']:'Warning, linked vernacular names'); ?>:</span> <?php echo $displayStr; ?>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_VERNACULAR'])?$LANG['NO_VERNACULAR']:'no vernacular names linked to this taxon'); ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['TEXT_DESCRIPTIONS'])?$LANG['TEXT_DESCRIPTIONS']:'Text Descriptions'); ?></b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('tdesc',$verifyArr)){
				?>
				<span style="color:red;"><?php echo (isset($LANG['DESC_EXISTS'])?$LANG['DESC_EXISTS']:'Warning, linked text descriptions exist'); ?>:</span>
				<ul>
					<?php
					echo '<li>'.implode('</li><li>',$verifyArr['tdesc']).'</li>';
					?>

				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_DESCS'])?$LANG['NO_DESCS']:'no text descriptions linked to this taxon'); ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['OCC_RECORDS'])?$LANG['OCC_RECORDS']:'Occurrence records'); ?>:</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('occur',$verifyArr)){
				?>
				<span style="color:red;"><?php echo (isset($LANG['LINKED_OCC_EXIST'])?$LANG['LINKED_OCC_EXIST']:'Warning, linked occurrence records exist'); ?>:</span>
				<ul>
					<?php
					foreach($verifyArr['occur'] as $occid){
						echo '<li>';
						echo '<a href="../../collections/individual/index.php?occid=' . htmlspecialchars($occid, HTML_SPECIAL_CHARS_FLAGS) . '">#' . htmlspecialchars($occid, HTML_SPECIAL_CHARS_FLAGS) . '</a>';
						echo '</li>';
					}
					?>
				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_OCCS_LINKED'])?$LANG['NO_OCCS_LINKED']:'no occurrence records linked to this taxon'); ?>
				<?php
			}
			?>
			<?php
			if(array_key_exists('dets',$verifyArr)){
				?>
				<span style="color:red;"><?php echo (isset($LANG['DETS_EXIST'])?$LANG['DETS_EXIST']:'Warning, linked determination records exist'); ?>:</span>
				<ul>
					<?php
					foreach($verifyArr['dets'] as $occid){
						echo '<li>';
						echo '<a href="../../collections/individual/index.php?occid=' . htmlspecialchars($occid, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">#' . htmlspecialchars($occid, HTML_SPECIAL_CHARS_FLAGS) . '</a>';
						echo '</li>';
					}
					?>
				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_DETS_LINKED'])?$LANG['NO_DETS_LINKED']:'no occurrence determinations linked to this taxon'); ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['CHECKLISTS'])?$LANG['CHECKLISTS']:'Checklists'); ?>:</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('cl',$verifyArr)){
				$clArr = $verifyArr['cl'];
				?>
				<span style="color:red;"><?php echo (isset($LANG['CHECKLISTS_EXIST'])?$LANG['CHECKLISTS_EXIST']:'Warning, linked checklists exist'); ?>:</span>
				<ul>
					<?php
					foreach($clArr as $k => $v){
						echo '<li><a href="../../checklists/checklist.php?clid=' . htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">';
						echo $v;
						echo '</a></li>';
					}
					?>
				</ul>
				<?php
			}
			else{
				echo '<span style="color:green;">'.(isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved').':</span> ';
				echo (isset($LANG['NO_CHECKLISTS'])?$LANG['NO_CHECKLISTS']:'no checklists linked to this taxon');
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b>Morphological Characters (Key):</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('kmdecr',$verifyArr)){
				echo '<span style="color:red;">';
				echo (isset($LANG['WARNING'])?$LANG['WARNING']:'Warning').': '.$verifyArr['kmdecr'].(isset($LANG['LINKED_MORPHO'])?$LANG['LINKED_MORPHO']:'linked morphological characters');
				echo '</span>';
			}
			else{
				echo '<span style="color:green;">'.(isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved').':</span> ';
				echo (isset($LANG['NO_MORPHO'])?$LANG['NO_MORPHO']:'no morphological characters linked to this taxon');
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['LINKED_RESOURCES'])?$LANG['LINKED_RESOURCES']:'Linked Resources'); ?>:</b>
		<div style="margin:10px">
			<?php
			if(array_key_exists('link',$verifyArr)){
				?>
				<span style="color:red;"><?php echo (isset($LANG['LINKED_RESOURCES_EXIST'])?$LANG['LINKED_RESOURCES_EXIST']:'Warning: linked resources exists'); ?></span>
				<ul>
					<?php
					echo '<li>'.implode('</li><li>',$verifyArr['link']).'</li>';
					?>

				</ul>
				<?php
			}
			else{
				?>
				<span style="color:green;"><?php echo (isset($LANG['APPROVED'])?$LANG['APPROVED']:'Approved'); ?>:</span> <?php echo (isset($LANG['NO_RESOURCES'])?$LANG['NO_RESOURCES']:'no resources linked to this taxon'); ?>
				<?php
			}
			?>
		</div>
	</div>
	<div style="margin:15px;">
		<fieldset style="padding:15px;">
			<legend><b><?php echo (isset($LANG['REMAP_RESOURCES'])?$LANG['REMAP_RESOURCES']:'Remap Resources to Another Taxon'); ?></b></legend>
			<form name="remaptaxonform" method="post" action="taxoneditor.php">
				<div style="margin-bottom:5px;">
					<?php echo (isset($LANG['TARGET_TAXON'])?$LANG['TARGET_TAXON']:'Target taxon'); ?>:
					<input id="remapvalue" name="remapvalue" type="text" value="" style="width:550px;" /><br/>
					<input name="remaptid" type="hidden" value="" />
				</div>
				<div>
					<button name="submitbutton" type="button" onclick="submitRemapTaxonForm(this.form)"><?php echo (isset($LANG['REMAP_TAXON'])?$LANG['REMAP_TAXON']:'Remap Taxon'); ?></button>
					<input name="submitaction" type="hidden" value="remapTaxon" />
					<input name="tid" type="hidden" value="<?php echo $tid; ?>" />
					<input name="genusstr" type="hidden" value="<?php echo $genusStr; ?>" />
				</div>
			</form>
		</fieldset>
	</div>
	<div style="margin:15px;">
		<fieldset style="padding:15px;">
			<legend><b><?php echo (isset($LANG['DELETE_TAX_AND_RES'])?$LANG['DELETE_TAX_AND_RES']:'Delete Taxon and Existing Resources'); ?></b></legend>
			<div style="margin:10px 0px;">
			</div>
			<form name="deletetaxonform" method="post" action="taxoneditor.php" onsubmit="return confirm('<?php echo (isset($LANG['SURE_DELETE'])?$LANG['SURE_DELETE']:'Are you sure you want to delete this taxon? Action can not be undone!'); ?>')">
				<?php
				$deactivateStr = '';
				if(array_key_exists('child',$verifyArr)) $deactivateStr = 'disabled';
				if(array_key_exists('syn',$verifyArr)) $deactivateStr = 'disabled';
				if($verifyArr['img'] > 0) $deactivateStr = 'disabled';
				if(array_key_exists('tdesc',$verifyArr)) $deactivateStr = 'disabled';
				echo '<button name="submitaction" type="submit" value="deleteTaxon" '.$deactivateStr.'>'.(isset($LANG['DELETE_TAXON'])?$LANG['DELETE_TAXON']:'Delete Taxon').'</button>';
				?>
				<input name="tid" type="hidden" value="<?php echo $tid; ?>" />
				<input name="genusstr" type="hidden" value="<?php echo $genusStr; ?>" />
				<div style="margin:15px 5px">
					<?php
					if($deactivateStr){
						?>
						<div style="font-weight:bold;">
							<?php echo (isset($LANG['CANNOT_DELETE_TAXON'])?$LANG['CANNOT_DELETE_TAXON']:'Taxon cannot be deleted until all children, synonyms, images, and text descriptions are removed or remapped to another taxon.'); ?>
						</div>
						<?php
					}
					else{
						if(array_key_exists('vern',$verifyArr)){
							?>
							<div style="color:red;">
								<?php echo (isset($LANG['VERNACULARS_DELETE'])?$LANG['VERNACULARS_DELETE']:'Warning: Vernaculars will be deleted with taxon'); ?>
							</div>
							<?php
						}
						if(array_key_exists('kmdecr',$verifyArr)){
							?>
							<div style="color:red;">
								<?php echo (isset($LANG['MORPH_DELETE'])?$LANG['MORPH_DELETE']:'Warning: Morphological Key Characters will be deleted with taxon'); ?>
							</div>
							<?php
						}
						if(array_key_exists('cl',$verifyArr)){
							?>
							<div style="color:red;">
								<?php echo (isset($LANG['CHECKLIST_DELETE'])?$LANG['CHECKLIST_DELETE']:'Warning: Links to checklists will be deleted with taxon'); ?>
							</div>
							<?php
						}
						if(array_key_exists('link',$verifyArr)){
							?>
							<div style="color:red;">
								<?php echo (isset($LANG['LINKED_RES_DELETE'])?$LANG['LINKED_RES_DELETE']:'Warning: Linked Resources will be deleted with taxon'); ?>
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
