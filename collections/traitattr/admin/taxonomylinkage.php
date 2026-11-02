<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTraitAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/traitattr/admin/taxonomylinkage');

header('Content-Type: text/html; charset=' . $CHARSET);

$traitID = array_key_exists('traitid', $_REQUEST) ? Sanitize::int($_REQUEST['traitid']) : 0;

$keyManager = new OccurrenceTraitAdmin();
$keyManager->setTraitID($traitID);
$tLinks = $keyManager->getTaxonRelevance();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#relevanceinput" ).autocomplete({
				source: "rpc/taxasuggest.php",
				minLength: 2,
				autoFocus: true,
				select: function( event, ui ) {
					if(ui.item){
						$( "#relevancetidinput" ).val(ui.item.id);
					}
					else{
						$( "#relevancetidinput" ).val("");
					}
				},
				change: function( event, ui ) {
					if($( "#relevancetidinput" ).val() == ""){
						$.ajax({
							type: "POST",
							url: "rpc/taxonvalidation.php",
							data: { term: $( this ).val() }
						}).done(function( msg ) {
							if(msg == ""){
								alert("<?= $LANG['ALERT_NO_TAXON']; ?>");
							}
							else{
								$( "#relevancetidinput" ).val(msg);
							}
						});
					}
				}
			});
		});

		$( "#relevanceinput" ).focus(function() {
			$( "#relevancetidinput" ).val("");
		});

		function validateRelevanceForm(f){
			if(f.relsciname.value == ""){
				alert("<?= $LANG['ALERT_EMPTY_TAXON'] ?>");
				return false;
			}
			if(f.tid.value == ""){
				alert("<?= $LANG['ALERT_NO_TAXON_IDEN'] ?> " + f.relsciname.value);
				return false;
			}
			return true;
		}
	</script>
</head>
<body>
	<h3><?= $LANG['LINK_CHAR_TAXA'] ?></h3>
	<div id="tlinkdiv" style="margin:15px;">
		<div style="margin:10px;">
			<b><?= $LANG['CHAR_TAXON_RELEVANCE'] ?></b> -
			<?= $LANG['TAG_TAXON_NODE'] ?>
		</div>
		<div style="margin:20px;">
			<?php
			if($tLinks){
				if(isset($tLinks['include'])){
					?>
					<fieldset style="padding:20px;">
						<legend><b><?= $LANG['RELEVANT_TAXA'] ?></b></legend>
						<?php
						foreach($tLinks['include'] as $tid => $tArr){
							?>
							<div style="margin:3px;clear:both;">
								<?php
								echo '<div style="float:left;"><b>'.$tArr['sciname'].'</b></div> ';
								?>
								<form name="delTaxonForm" action="traitdetails.php" method="post" style="float:left;margin-left:5px;" onsubmit="return comfirm('Are you sure you want to delete this relationship?')">
									<input name="traitID" type="hidden" value="<?= $traitID ?>" />
									<input name="tid" type="hidden" value="<?= $tid ?>" />
									<input name="formsubmit" type="hidden" value="deltaxon" />
									<input type="image" src="../../images/del.png" style="width:1.3em;" />
								</form>
							</div>
							<?php
						}
						?>
					</fieldset>
					<?php
				}
				if(isset($tLinks['exclude'])){
					?>
					<fieldset style="padding:20px;">
						<legend><b><?= $LANG['EXLUDING_TAXA'] ?></b></legend>
						<?php
						foreach($tLinks['exclude'] as $tid => $tArr){
							?>
							<div style="margin:3px;">
								<?php
								echo '<div style="float:left;"><b>'.$tArr['sciname'].'</b></div> ';
								?>
								<form name="delTaxonForm" action="traitdetails.php" method="post" style="float:left;margin-left:5px;" onsubmit="return comfirm('Are you sure you want to delete this relationship?')">
									<input name="traitID" type="hidden" value="<?= $traitID ?>" />
									<input name="tid" type="hidden" value="<?= $tid ?>" />
									<input name="formsubmit" type="hidden" value="delTaxon" />
									<input type="image" src="../../images/del.png" style="width:1.3em;" />
								</form>
							</div>
							<?php
						}
						?>
					</fieldset>
					<?php
				}
			}
			else{
				?>
				<div style="font-weight:bold">
					<?= $LANG['NO_CHAR_LINK'] ?>
				</div>
				<?php
			}
			?>
		</div>
		<div style="margin:20px;">
			<form name="taxonAddForm" action="traitdetails.php" method="post" onsubmit="return validateRelevanceForm(this)">
				<fieldset style="padding:20px;">
					<legend><b><?= $LANG['ADD_TAXON_RELEVANCE'] ?></b></legend>
					<div style="height:15px;">
						<div style="margin:3px;">
							<label for="relevanceinput"><b><?= $LANG['TAXON_NAME'] ?>:</b></label>
							<input type="text" id="relevanceinput" name="relsciname" style="width:300px" />
							<input type="hidden" id="relevancetidinput" name="tid" />
						</div>
						<div style="float:left;margin:3px;">
							<label for="relation"><b><?= $LANG['TAXON_RELEVANCE'] ?>:</b></label>
							<select id="relation" name="relation">
								<option value="include"><?= $LANG['RELEVANT'] ?></option>
								<option value="exclude"><?= $LANG['EXCLUDE'] ?></option>
							</select>
						</div>
					</div>
					<br>
					<div style="margin:15px;">
						<input name="traitID" type="hidden" value="<?= $traitID ?>" />
						<button name="formsubmit" type="submit" value="addTaxon"><?= $LANG['SAVE_TAXON_RELEVANCE'] ?></button>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</body>
</html>
