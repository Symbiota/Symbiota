<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistVoucherManager.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/checklists/clsppeditor.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT . '/content/lang/checklists/clsppeditor.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/checklists/clsppeditor.en.php');
header('Content-Type: text/html; charset='.$CHARSET);

$clid = array_key_exists('clid', $_REQUEST) ? filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$tid = array_key_exists('tid', $_REQUEST) ? filter_var($_REQUEST['tid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$tabIndex = array_key_exists('tabindex', $_POST) ? filter_var($_POST['tabindex'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';

$isEditor = false;
if($IS_ADMIN || (array_key_exists('ClAdmin', $USER_RIGHTS) && in_array($clid, $USER_RIGHTS['ClAdmin']))){
	$isEditor = true;
}

$vManager = new ChecklistVoucherManager();

$statusStr = '';
$vManager->setTid($tid);
$vManager->setClid($clid);
$followUpAction = '';

if($action == 'remapTaxon'){
	$rareLocality = '';
	if(!empty($_POST['cltype']) && $_POST['cltype'] == 'rarespp' && !empty($_POST['locality'])) $rareLocality = $_POST['locality'];
	if($vManager->remapTaxon($_POST['renametid'], $rareLocality)){
		$followUpAction = 'reloadParentPage()';
	}
	else $statusStr = $vManager->getErrorMessage();
}
elseif($action == 'editChecklist'){
	if($vManager->editClData($_POST)){
		$followUpAction = 'reloadParentPage()';
	}
	else $statusStr = $vManager->getErrorMessage();
}
elseif($action == 'deleteTaxon'){
	$rareLocality = '';
	if(!empty($_POST['cltype']) && $_POST['cltype'] == 'rarespp' && !empty($_POST['locality'])) $rareLocality = $_POST['locality'];
	if($vManager->deleteTaxon($_POST, $rareLocality)){
		$followUpAction = 'reloadParentPage()';
	}
	else $statusStr = $vManager->getErrorMessage();
}
elseif($action == 'editVoucher'){
	$voucherID = filter_var($_POST['voucherID'], FILTER_SANITIZE_NUMBER_INT);
	if(!$vManager->editVoucher($voucherID, $_POST['notes'], $_POST['editornotes'])){
		$statusStr = $vManager->getErrorMessage();
	}
}
elseif($action == 'deleteVoucher'){
	if(!empty($_POST['voucherID'])){
		if(!$vManager->deleteVoucher($_POST['voucherID'])){
			$statusStr = $vManager->getErrorMessage();
		}
	}
}
$clArray = $vManager->getChecklistData();
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<title><?= $LANG['SPEC_DETAILS'] . ': ' . ($vManager->getTaxonName() ?? $LANG['UNKNOWN_TAXON']) . ' ' . $LANG['OF'] . ' ' . $vManager->getClName() ?? $LANG['UNKNOWN_COLLECTION']; ?></title>
		<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script type="text/javascript">

			$(document).ready(function() {
				$("#renamesciname").autocomplete({
					source: function( request, response ) {
						$.getJSON( "rpc/speciessuggest.php", { term: request.term }, response );
					},
					minLength: 3,
					autoFocus: true,
					select: function( event, ui ) {
						if(ui.item){
							$( "#renamesciname" ).val(ui.item.value);
							$( "#renametid" ).val(ui.item.id);
						}
					},
					change: function( event, ui ) {
						if(ui.item === null) {
							$( "#renametid" ).val("");
							if($( "#renamesciname" ).val() != ""){
								alert('<?= $LANG['SELECT_TAXON'] ?>');
								f.renamesciname.focus();
							}
						}
					}
				});

				$('#tabs').tabs({
					active: <?= $tabIndex; ?>
				});

			});

			function validateRenameForm(f){
				if (f.renamesciname.value !== "" && f.renametid.value !== ""){
					f.submit();
				}
				else if(f.renamesciname.value == ""){
					alert("<?= $LANG['NAME_BLANK']; ?>");
				}
				else {
					alert('<?= $LANG['SELECT_TAXON'] ?>');
				}
				f.renamesciname.focus();
				return false;
			}

			function openPopup(urlStr,windowName){
				newWindow = window.open(urlStr,windowName,'scrollbars=1,toolbar=0,resizable=1,width=800,height=650,left=20,top=20');
				if (newWindow.opener == null) newWindow.opener = self;
			}

			function reloadParentPage(){
				window.opener.optionform.submit();
				self.close();
			}
		</script>
		<script type="text/javascript" src="../js/symb/shared.js?ver=140107"></script>
		<style>
			body{ background-color: #FFFFFF; }
			.edit-icon{ width: 1em; margin: 0em; }
			label{ font-weight:bold; float:left; margin-right: 3px; }
		</style>
	</head>
	<body onload="<?= $followUpAction ?>" >
		<a class="screen-reader-only" href="#popup-innertext"><?= $LANG['SKIP_NAV'] ?></a>
		<!-- This is inner text! -->
		<div id='popup-innertext'>
			<h1 class="page-heading"><?= '<i>' . ($vManager->getTaxonName() ?? $LANG['UNKNOWN_TAXON']) . '</i> ' . $LANG['IN'] . ' ' . ($vManager->getClName() ?? $LANG['UNKNOWN_COLLECTION']); ?></h1>
			<?php
			if($statusStr){
				?>
				<hr />
				<div style='color:red;font-weight:bold;'>
					<?= $statusStr;?>
				</div>
				<hr />
				<?php
			}
			if($isEditor && $clArray){
				?>
				<div id="tabs" style="margin:10px;">
					<nav>
				    <ul>
						<li><a href="#gendiv"><?= $LANG['GEN_EDIT'] ?></a></li>
						<li><a href="#voucherdiv"><?= $LANG['VOUCHER_EDIT'] ?></a></li>
						<!--
						<li><a href="#coorddiv">Coordinate Admin</a></li>
						-->
					</ul>
					</nav>
					<div id="gendiv">
						<form name='editcl' action="clsppeditor.php" method='post' >
							<fieldset style='margin:5px;padding:15px'>
				   			<legend><b><?= $LANG['EDIT_CHECKLIST']; ?></b></legend>
				   			<div style="clear:both;margin:3px;">
									<label><?= $LANG['HABITAT'] ?>:</label>
									<div style="float:left;">
										<input name='habitat' type='text' value="<?= $clArray['habitat'] ?>" size='70' maxlength='250' aria-label="<?= $LANG['HABITAT']; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<label><?= $LANG['ABUNDANCE'] ?>:</label>
									<div style="float:left;">
										<input type="text"  name="abundance" value="<?= $clArray["abundance"]; ?>" aria-label="<?= $LANG['ABUNDANCE']; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<label><?= $LANG['NOTES'] ?>:</label>
									<div style="float:left;">
										<input name='notes' type='text' value="<?= $clArray["notes"];?>" size='65' maxlength='2000' aria-label="<?= $LANG['NOTES']; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<label><?= $LANG['EDITOR_NOTES'] ?>:</label>
									<div style="float:left;">
										<input name='internalnotes' type='text' value="<?= $clArray["internalnotes"];?>" size='65' maxlength='250' aria-label="<?= $LANG['INTERNAL_NOTES']; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<label><?= $LANG['SOURCE'] ?>:</label>
									<div style="float:left;">
										<input name='source' type='text' value="<?= $clArray["source"];?>" size='65' maxlength='250' aria-label="<?= $LANG['SOURCE']; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<label><?= $LANG['OVERRIDE'] ?>:</label>
									<div style="float:left;">
										<input name='familyoverride' type='text' value="<?= $clArray["familyoverride"];?>" size='65' maxlength='250' aria-label="<?= $LANG['OVERRIDE']; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<input name='tid' type="hidden" value="<?= $vManager->getTid() ?>" />
									<input name='taxon' type="hidden" value="<?= $vManager->getTaxonName() ?>" />
									<input name='clid' type="hidden" value="<?= $vManager->getClid() ?>" />
									<input name='clname' type="hidden" value="<?= $vManager->getClName() ?>" />
									<button type="submit" name="action" value="editChecklist"><?= $LANG['SUBMIT_EDITS'] ?></button>
								</div>
							</fieldset>
						</form>
						<hr />
						<form name="renametaxonform" action="clsppeditor.php" method="post" onsubmit="return validateRenameForm(this)">
							<fieldset style='margin:5px;padding:15px;'>
								<legend><b><?= $LANG['RENAME_TRANSFER']; ?></b></legend>
								<div style='margin-top:2px;'>
									<label><?= $LANG['TARGET_TAXON'] ?>:</label>
									<div style='float:left;'>
										<input id="renamesciname" name='renamesciname' type="text" size="50" aria-label="<?= $LANG['OVERRIDE']; ?>" />
										<input id="renametid" name="renametid" type="hidden" value="" />
									</div>
								</div>
								<div style="clear:both;margin-top:2px;">
									<b>*</b> <?= $LANG['VOUCHERS_TRANSFER']; ?>
								</div>
								<div style="margin:15px">
									<input name="tid" type="hidden" value="<?= $vManager->getTid(); ?>" />
									<input name="clid" type="hidden" value="<?= $vManager->getClid(); ?>" />
									<input name="cltype" type="hidden" value="<?= $clArray['cltype']; ?>" />
									<input name="locality" type="hidden" value="<?= $clArray['locality']; ?>" />
									<input name="action" type="hidden" value="remapTaxon" />
									<button type="submit" name="submitaction"><?= $LANG['RENAME']; ?></button>
								</div>
							</fieldset>
						</form>
						<hr />
						<form action="clsppeditor.php" method="post" name="deletetaxon" onsubmit="return window.confirm('<?= $LANG['ARE_YOU_SURE']; ?>');">
							<fieldset style='margin:5px;padding:15px;'>
						   	<legend><b><?= $LANG['DELETE'] ?></b></legend>
								<input type="hidden" name='tid' value="<?= $vManager->getTid(); ?>" />
								<input type="hidden" name='clid' value="<?= $vManager->getClid(); ?>" />
								<input type="hidden" name='cltype' value="<?= $clArray['cltype']; ?>" />
								<input type="hidden" name='locality' value="<?= $clArray['locality']; ?>" />
								<button class="button-danger" type="submit" name="action" value="deleteTaxon"><?= $LANG['DELETE_TAXON']; ?></button>
							</fieldset>
						</form>
					</div>
					<div id="voucherdiv">
						<?php
						if($OCCURRENCE_MOD_IS_ACTIVE){
							?>
							<div style="float:right;margin-top:10px;">
								<a href="../collections/list.php?mode=voucher&db=all&usethes=1&reset=1&taxa=<?= urlencode(htmlspecialchars($vManager->getTaxonName(), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE)) . "&targetclid=" . $vManager->getClid() . "&targettid=" . $tid; ?>">
									<img src="../images/link.png" alt="<?= $LANG['TO_COLLECTIONS_LINK']; ?>" style="border:0px;" />
								</a>
							</div>
							<h2><?= $LANG['VOUCHER_INFO']; ?></h2>
							<?php
							$vArray = $vManager->getVoucherData();
							if(!$vArray){
								echo '<div>' . $LANG['NO_VOUCHERS'] . ' </div>';
							}
							?>
							<div>
								<?php
								foreach($vArray as $voucherID => $iArray){
									?>
									<li>
										<a href="#" onclick="openPopup('../collections/individual/index.php?occid=<?= $iArray['occid'] ?>','indpane')">
											<?= $iArray['occid'] ?>
										</a>:
										<?php
										if($iArray['catalognumber']) echo $iArray['catalognumber'].', ';
										echo '<b>'.$iArray['collector'].'</b>, ';
										if($iArray['eventdate']) echo $iArray['eventdate'].', ';
										if($iArray['sciname']) echo $iArray['sciname'];
										echo ($iArray['notes']?', '.$iArray['notes']:'').($iArray['editornotes']?', '.$iArray['editornotes']:'');
										?>
										<a href="#" onclick="toggle('vouch-<?= $voucherID ?>')"><img class="edit-icon" src="../images/edit.png" alt="<?= $LANG['EDIT_VOUCHER']; ?>"></a>
										<form action="clsppeditor.php" method='post' name='delform' style="display:inline;" onsubmit="return confirm('<?= $LANG['SURE_DELETE']; ?>');">
											<input type="hidden" name='tid' value="<?= $vManager->getTid(); ?>" />
											<input type="hidden" name='clid' value="<?= $vManager->getClid(); ?>" />
											<input type="hidden" name='voucherID' value="<?= $voucherID; ?>" />
											<input type="hidden" name='tabindex' value="1" />
											<input type="hidden" name='action' value="deleteVoucher" />
											<input type="image" name="action" src="../images/del.png" class="edit-icon" title="<?= $LANG['DELETE_TAXON']; ?>" alt="<?= $LANG['REMOVE'] . ' ' . $voucherID; ?>" aria-label="<?= $LANG['REMOVE_TAXON']; ?>">
										</form>
										<div id="vouch-<?= $voucherID;?>" style='margin:10px;clear:both;display:none;'>
											<form action="clsppeditor.php" method='post' name='editvoucher'>
												<fieldset style='margin:5px 0px 5px 5px;'>
													<legend><b><?= $LANG['EDIT_VOUCHER']; ?></b></legend>
													<input type="hidden" name='tid' value="<?= $vManager->getTid() ?>" />
													<input type="hidden" name='clid' value="<?= $vManager->getClid() ?>" />
													<input type="hidden" name='voucherID' value="<?= $voucherID ?>" />
													<input type="hidden" name='tabindex' value="1" />
													<div style='margin-top:0.5em;'>
														<b><?= $LANG['NOTES']; ?>:</b>
														<input name='notes' type='text' value="<?= $iArray["notes"];?>" size='60' maxlength='250'  aria-label="<?= $LANG['NOTES']; ?>" />
													</div>
													<div style='margin-top:0.5em;'>
														<b><?= $LANG['EDITOR_NOTES_DISPLAY']; ?>:</b>
														<input name='editornotes' type='text' value="<?= $iArray["editornotes"];?>" size='30' maxlength='50'  aria-label="<?= $LANG['EDITOR_NOTES']; ?>" />
													</div>
													<div style='margin-top:0.5em;'>
														<button type='submit' name='action' value="editVoucher"><?= $LANG['SUBMIT_V_EDITS']; ?></button>
													</div>
												</fieldset>
											</form>
										</div>

									<?php
								}
								?>
							</div>
							<?php
						}
						?>
					</div>
					<!--
					<div id="coorddiv">

					</div>
					-->
				</div>
				<?php
			}
			else{
				echo '<div>' . $LANG['NO_DATA'] . '</div>';
			}
			?>
		</div>
	</body>
</html>
