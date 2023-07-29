<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyEditorManager.php');
include_once($SERVER_ROOT.'/content/lang/taxa/taxonomy/taxonomychildren.'.$LANG_TAG.'.php');

$tid = $_REQUEST['tid'];
$taxAuthId = array_key_exists('taxauthid', $_REQUEST)?$_REQUEST['taxauthid']:1;

//Sanitation
if(!is_numeric($tid)) $tid = 0;
if(!is_numeric($taxAuthId)) $taxAuthId = 1;

$taxonEditorObj = new TaxonomyEditorManager();
$taxonEditorObj->setTid($tid);
$taxonEditorObj->setTaxAuthId($taxAuthId);

$childrenArr = $taxonEditorObj->getChildren();
?>
<script>
</script>
<div style="min-height:400px; height:auto !important; height:400px; ">
	<div style="margin:15px;">
		<b><?php echo (isset($LANG['CHILDREN_TAXA'])?$LANG['CHILDREN_TAXA']:'Children Taxa'); ?></b>
		<div style="margin:10px">
			<?php
			if($childrenArr){
				foreach($childrenArr as $childTid => $childArr){
					echo '<div style="margin:3px 10px;">';
					echo '<a href="taxoneditor.php?tid=' . htmlspecialchars($childTid, HTML_SPECIAL_CHARS_FLAGS) . '"><i>' . htmlspecialchars($childArr['sciname'], HTML_SPECIAL_CHARS_FLAGS) . '</i> ' . htmlspecialchars($childArr['author'], HTML_SPECIAL_CHARS_FLAGS) . '</a>';
					if($childArr['accTid'] && $childArr['accTid'] != $childTid){
						echo '<span style="margin-left:10px">&#10140; <a href="taxoneditor.php?tid=' . htmlspecialchars($childArr['accTid'], HTML_SPECIAL_CHARS_FLAGS) . '"><i>' . htmlspecialchars($childArr['accSciname'], HTML_SPECIAL_CHARS_FLAGS) . '</i> ' . htmlspecialchars($childArr['accAuthor'], HTML_SPECIAL_CHARS_FLAGS) . '</a></span>';
					}
					echo '</div>';
				}
				echo '<div style="margin-top:20px;">* '.(isset($LANG['SHOWING_DIRECT'])?$LANG['SHOWING_DIRECT']:'Showing direct children only').'</div>';
			}
			else echo '<div style="margin:15px">'.(isset($LANG['NO_CHILDREN'])?$LANG['NO_CHILDREN']:'No children taxa exist for this taxon').'</div>';
			?>
		</div>
	</div>
</div>
