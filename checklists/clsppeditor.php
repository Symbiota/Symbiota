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

$status = '';
$vManager->setTid($tid);
$vManager->setClid($clid);
$followUpAction = '';

if($action == 'renameTransfer'){
	$rareLocality = '';
	if($_POST['cltype'] == 'rarespp') $rareLocality = $_POST['locality'];
	if($vManager->renameTaxon($_POST['renametid'], $rareLocality)){
		$followUpAction = 'removeTaxon()';
	}
	else echo $vManager->getErrorMessage();
}
elseif($action == 'editChecklist'){
	$eArr = Array();
	$eArr['habitat'] = $_POST['habitat'];
	$eArr['abundance'] = $_POST['abundance'];
	$eArr['notes'] = $_POST['notes'];
	$eArr['internalnotes'] = $_POST['internalnotes'];
	$eArr['source'] = $_POST['source'];
	$eArr['familyoverride'] = $_POST['familyoverride'];
	$status = $vManager->editClData($eArr);
	$followUpAction = 'self.close()';
}
elseif($action == 'deleteTaxon'){
	$rareLocality = '';
	if($_POST['cltype'] == 'rarespp') $rareLocality = $_POST['locality'];
	$status = $vManager->deleteTaxon($rareLocality);
	$followUpAction = 'removeTaxon()';
}
elseif($action == 'editVoucher'){
	if(!$vManager->editVoucher($_POST['voucherID'], $_POST['notes'], $_POST['editornotes'])){
		$status = $vManager->getErrorMessage();
	}
}
elseif($action == 'deleteVoucher'){
	if(!$vManager->deleteVoucher($_POST['voucherID'])){
		$status = $vManager->getErrorMessage();
	}
}
elseif($action == 'Add Voucher'){
	//For processing requests sent from /collections/individual/index.php
	$status = $vManager->addVoucher($_POST['voccid'],$_POST['vnotes'],$_POST['veditnotes']);
}
$clArray = $vManager->getChecklistData();
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<title><?php echo $LANG['SPEC_DETAILS'] . ': ' . $vManager->getTaxonName() . " " . $LANG['OF'] . " " . $vManager->getClName(); ?></title>
		<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
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
					}
				});

				$('#tabs').tabs({
					active: <?php echo $tabIndex; ?>
				});

			});

			function validateRenameForm(f){
				if(f.renamesciname.value == ""){
					alert("<?php echo $LANG['NAME_BLANK']; ?>");
				}
				else{
					checkScinameExistence(f);
				}
				return false;
			}

			function checkScinameExistence(f){
				$.ajax({
					type: "POST",
					url: "rpc/gettid.php",
					data: { sciname: f.renamesciname.value }
				}).done(function( renameTid ) {
					if(renameTid){
						if(f.renametid.value == "") f.renametid.value = renameTid;
						f.submit();
					}
					else{
						alert("<?php echo $LANG['SCINAME_ERROR']; ?>");
						f.renametid.value = "";
					}
				});
			}

			function openPopup(urlStr,windowName){
				newWindow = window.open(urlStr,windowName,'scrollbars=1,toolbar=0,resizable=1,width=800,height=650,left=20,top=20');
				if (newWindow.opener == null) newWindow.opener = self;
			}

			function removeTaxon(){
				window.opener.$("#tid-<?php echo $tid; ?>").hide();
				self.close();
			}
		</script>
		<script type="text/javascript" src="../js/symb/shared.js?ver=140107"></script>
		<style type="text/css">
			body{ background-color: #FFFFFF; }
		</style>
	</head>
	<body onload="<?php  if(!$status) echo $followUpAction; ?>" >
		<!-- This is inner text! -->
		<div id='popup-innertext'>
			<h1 class="page-heading"><?php echo "<i>" . $vManager->getTaxonName() . "</i> " . $LANG['IN'] . " " . $vManager->getClName(); ?></h1>
			<?php
			if($status){
				?>
				<hr />
				<div style='color:red;font-weight:bold;'>
					<?php echo $status;?>
				</div>
				<hr />
				<?php
			}
			if($isEditor && $clArray){
				?>
				<div id="tabs" style="margin:10px;">
				    <ul>
						<li><a href="#gendiv"><?php echo htmlspecialchars($LANG['GEN_EDIT'], HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
						<li><a href="#voucherdiv"><?php echo htmlspecialchars($LANG['VOUCHER_EDIT'], HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
						<!--
						<li><a href="#coorddiv">Coordinate Admin</a></li>
						-->
				    </ul>
					<div id="gendiv">
						<form name='editcl' action="clsppeditor.php" method='post' >
							<fieldset style='margin:5px;padding:15px'>
				   			<legend><b><?php echo $LANG['EDIT_CHECKLIST']; ?></b></legend>
				   			<div style="clear:both;margin:3px;">
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo $LANG['HABITAT']; ?>:
									</div>
									<div style="float:left;">
										<input name='habitat' type='text' value="<?php echo $clArray["habitat"];?>" size='70' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo $LANG['ABUNDANCE']; ?>:
									</div>
									<div style="float:left;">
										<input type="text"  name="abundance" value="<?php echo $clArray["abundance"]; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo $LANG['NOTES']; ?>:
									</div>
									<div style="float:left;">
										<input name='notes' type='text' value="<?php echo $clArray["notes"];?>" size='65' maxlength='2000' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo $LANG['EDITOR_NOTES']; ?>:
									</div>
									<div style="float:left;">
										<input name='internalnotes' type='text' value="<?php echo $clArray["internalnotes"];?>" size='65' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo $LANG['SOURCE']; ?>:
									</div>
									<div style="float:left;">
										<input name='source' type='text' value="<?php echo $clArray["source"];?>" size='65' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo $LANG['OVERRIDE']; ?>:
									</div>
									<div style="float:left;">
										<input name='familyoverride' type='text' value="<?php echo $clArray["familyoverride"];?>" size='65' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<input name='tid' type="hidden" value="<?php echo $vManager->getTid();?>" />
									<input name='taxon' type="hidden" value="<?php echo $vManager->getTaxonName();?>" />
									<input name='clid' type="hidden" value="<?php echo $vManager->getClid();?>" />
									<input name='clname' type="hidden" value="<?php echo $vManager->getClName();?>" />
									<button type="submit" name="action" value="editChecklist"><?php echo $LANG['SUBMIT_EDITS']; ?></button>
								</div>
							</fieldset>
						</form>
						<hr />
						<form name="renametaxonform" action="clsppeditor.php" method="post" onsubmit="return validateRenameForm(this)">
							<fieldset style='margin:5px;padding:15px;'>
								<legend><b><?php echo $LANG['RENAME_TRANSFER']; ?></b></legend>
								<div style='margin-top:2px;'>
									<div style='width:130px;font-weight:bold;float:left;'>
										<?php echo $LANG['TARGET_TAXON']; ?>:
									</div>
									<div style='float:left;'>
										<input id="renamesciname" name='renamesciname' type="text" size="50" />
										<input id="renametid" name="renametid" type="hidden" value="" />
									</div>
								</div>
								<div style="clear:both;margin-top:2px;">
									<b>*</b> <?php echo $LANG['VOUCHERS_TRANSFER']; ?>
								</div>
								<div style="margin:15px">
									<input name="tid" type="hidden" value="<?php echo $vManager->getTid(); ?>" />
									<input name="clid" type="hidden" value="<?php echo $vManager->getClid(); ?>" />
									<input name="cltype" type="hidden" value="<?php echo $clArray['cltype']; ?>" />
									<input name="locality" type="hidden" value="<?php echo $clArray['locality']; ?>" />
									<input name="action" type="hidden" value="renameTransfer" />
									<button type="submit" name="submitaction"><?php echo $LANG['RENAME']; ?></button>
								</div>
							</fieldset>
						</form>
						<hr />
						<form action="clsppeditor.php" method="post" name="deletetaxon" onsubmit="return window.confirm('<?php echo $LANG['ARE_YOU_SURE']; ?>');">
							<fieldset style='margin:5px;padding:15px;'>
						   	<legend><b><?php echo (isset($LANG['DELETE'])?$LANG['DELETE']:'Delete'); ?></b></legend>
								<input type="hidden" name='tid' value="<?php echo $vManager->getTid(); ?>" />
								<input type="hidden" name='clid' value="<?php echo $vManager->getClid(); ?>" />
								<input type="hidden" name='cltype' value="<?php echo $clArray['cltype']; ?>" />
								<input type="hidden" name='locality' value="<?php echo $clArray['locality']; ?>" />
								<button type="submit" name="action" value="deleteTaxon"><?php echo $LANG['DELETE_TAXON']; ?></button>
							</fieldset>
						</form>
					</div>
					<div id="voucherdiv">
						<?php
						if($OCCURRENCE_MOD_IS_ACTIVE){
							?>
							<div style="float:right;margin-top:10px;">
								<a href="../collections/list.php?mode=voucher&db=all&usethes=1&reset=1&taxa=<?php echo htmlspecialchars($vManager->getTaxonName(), HTML_SPECIAL_CHARS_FLAGS) . "&targetclid=" . htmlspecialchars($vManager->getClid(), HTML_SPECIAL_CHARS_FLAGS) . "&targettid=" . htmlspecialchars($tid, HTML_SPECIAL_CHARS_FLAGS);?>">
									<img src="../images/link.png"  style="border:0px;" />
								</a>
							</div>
							<h3><?php echo $LANG['VOUCHER_INFO']; ?></h3>
							<?php
							$vArray = $vManager->getVoucherData();
							if(!$vArray){
								echo '<div>' . $LANG['NO_VOUCHERS'] . ' </div>';
							}
							?>
							<ul>
								<?php
								foreach($vArray as $voucherID => $iArray){
									?>
									<li>
										<a href="#" onclick="openPopup('../collections/individual/index.php?occid=<?php echo htmlspecialchars($iArray['occid'], HTML_SPECIAL_CHARS_FLAGS); ?>','indpane')"><?php echo htmlspecialchars($iArray['occid'], HTML_SPECIAL_CHARS_FLAGS); ?></a>:
										<?php
										if($iArray['catalognumber']) echo $iArray['catalognumber'].', ';
										echo '<b>'.$iArray['collector'].'</b>, ';
										if($iArray['eventdate']) echo $iArray['eventdate'].', ';
										if($iArray['sciname']) echo $iArray['sciname'];
										echo ($iArray['notes']?', '.$iArray['notes']:'').($iArray['editornotes']?', '.$iArray['editornotes']:'');
										?>
										<a href="#" onclick="toggle('vouch-<?php echo $voucherID;?>')"><img src="../images/edit.png" /></a>
										<form action="clsppeditor.php" method='post' name='delform' style="display:inline;" onsubmit="return confirm('<?php echo $LANG['SURE_DELETE']; ?>');">
											<input type="hidden" name='tid' value="<?php echo $vManager->getTid();?>" />
											<input type="hidden" name='clid' value="<?php echo $vManager->getClid();?>" />
											<input type="hidden" name='voucherID' value="<?php echo $voucherID;?>" />
											<input type="hidden" name='tabindex' value="1" />
											<input type="hidden" name='action' value="deleteVoucher" />
											<input type="image" name="action" src="../images/del.png" style="width:15px;" title="<?php echo $LANG['DELETE_TAXON']; ?>" />
										</form>
										<div id="vouch-<?php echo $voucherID;?>" style='margin:10px;clear:both;display:none;'>
											<form action="clsppeditor.php" method='post' name='editvoucher'>
												<fieldset style='margin:5px 0px 5px 5px;'>
													<legend><b><?php echo $LANG['EDIT_VOUCHER']; ?></b></legend>
													<input type="hidden" name='tid' value="<?php echo $vManager->getTid();?>" />
													<input type="hidden" name='clid' value="<?php echo $vManager->getClid();?>" />
													<input type="hidden" name='voucherID' value="<?php echo $voucherID;?>" />
													<input type="hidden" name='tabindex' value="1" />
													<div style='margin-top:0.5em;'>
														<b><?php echo $LANG['NOTES']; ?>:</b>
														<input name='notes' type='text' value="<?php echo $iArray["notes"];?>" size='60' maxlength='250' />
													</div>
													<div style='margin-top:0.5em;'>
														<b><?php echo $LANG['EDITOR_NOTES_DISPLAY']; ?>:</b>
														<input name='editornotes' type='text' value="<?php echo $iArray["editornotes"];?>" size='30' maxlength='50' />
													</div>
													<div style='margin-top:0.5em;'>
														<button type='submit' name='action' value="editVoucher"><?php echo $LANG['SUBMIT_V_EDITS']; ?></button>
													</div>
												</fieldset>
											</form>
										</div>
									</li>
									<?php
								}
								?>
							</ul>
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