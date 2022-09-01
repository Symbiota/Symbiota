<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistVoucherManager.php');
@include_once($SERVER_ROOT.'/content/lang/checklists/clsppeditor.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:0;
$tabIndex = array_key_exists('tabindex',$_POST)?$_POST['tabindex']:0;
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

//Sanitation
if(!is_numeric($clid)) $clid = 0;
if(!is_numeric($tid)) $tid = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;

$isEditor = false;
if($IS_ADMIN || (array_key_exists("ClAdmin",$USER_RIGHTS) && in_array($clid,$USER_RIGHTS["ClAdmin"]))){
	$isEditor = true;
}

$vManager = new ChecklistVoucherManager();

$status = "";
$vManager->setTid($tid);
$vManager->setClid($clid);
$followUpAction = '';

if($action == "Rename Taxon"){
	$rareLocality = '';
	if($_POST['cltype'] == 'rarespp') $rareLocality = $_POST['locality'];
	$vManager->renameTaxon($_POST["renametid"],$rareLocality);
	$followUpAction = "removeTaxon()";
}
elseif($action == "Submit Checklist Edits"){
	$eArr = Array();
	$eArr["habitat"] = $_POST["habitat"];
	$eArr["abundance"] = $_POST["abundance"];
	$eArr["notes"] = $_POST["notes"];
	$eArr["internalnotes"] = $_POST["internalnotes"];
	$eArr["source"] = $_POST["source"];
	$eArr["familyoverride"] = $_POST["familyoverride"];
	$status = $vManager->editClData($eArr);
	$followUpAction = "self.close()";
}
elseif($action == "Delete Taxon From Checklist"){
	$rareLocality = '';
	if($_POST['cltype'] == 'rarespp') $rareLocality = $_POST['locality'];
	$status = $vManager->deleteTaxon($rareLocality);
	$followUpAction = "removeTaxon()";
}
elseif($action == "Submit Voucher Edits"){
	$status = $vManager->editVoucher($_POST["occid"],$_POST["notes"],$_POST["editornotes"]);
}
elseif(array_key_exists('oiddel',$_POST)){
	$status = $vManager->removeVoucher($_POST['oiddel']);
}
elseif( $action == "Add Voucher"){
	//For processing requests sent from /collections/individual/index.php
	$status = $vManager->addVoucher($_POST["voccid"],$_POST["vnotes"],$_POST["veditnotes"]);
}
$clArray = $vManager->getChecklistData();
?>
<html>
	<head>
		<title><?php echo (isset($LANG['SPEC_DETAILS'])?$LANG['SPEC_DETAILS']:'Species Details'); ?>: <?php echo $vManager->getTaxonName()." of ".$vManager->getClName(); ?></title>
		<?php
		$activateJQuery = true;
		if(file_exists($SERVER_ROOT.'/includes/head.php')){
			include_once($SERVER_ROOT.'/includes/head.php');
		}
		else{
			echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
		}
		?>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery-ui.js"></script>
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
					alert("<?php echo (isset($LANG['NAME_BLANK'])?$LANG['NAME_BLANK']:'Scientific name field is blank'); ?>");
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
						alert("<?php echo (isset($LANG['SCINAME_ERROR'])?$LANG['SCINAME_ERROR']:'ERROR: Scientific name does not exist in database. Did you spell it correctly? If so, it may have to be added to taxa table.'); ?>");
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
			<h1><?php echo "<i>".$vManager->getTaxonName()."</i> of ".$vManager->getClName(); ?></h1>
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
						<li><a href="#gendiv"><?php echo (isset($LANG['GEN_EDIT'])?$LANG['GEN_EDIT']:'General Editing'); ?></a></li>
						<li><a href="#voucherdiv"><?php echo (isset($LANG['VOUCHER_EDIT'])?$LANG['VOUCHER_EDIT']:'Voucher Admin'); ?></a></li>
						<!--
						<li><a href="#coorddiv">Coordinate Admin</a></li>
						-->
				    </ul>
					<div id="gendiv">
						<form name='editcl' action="clsppeditor.php" method='post' >
							<fieldset style='margin:5px;padding:15px'>
				   			<legend><b><?php echo (isset($LANG['EDIT_CHECKLIST'])?$LANG['EDIT_CHECKLIST']:'Edit Checklist Information'); ?></b></legend>
				   			<div style="clear:both;margin:3px;">
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo (isset($LANG['HABITAT'])?$LANG['HABITAT']:'Habitat'); ?>:
									</div>
									<div style="float:left;">
										<input name='habitat' type='text' value="<?php echo $clArray["habitat"];?>" size='70' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo (isset($LANG['ABUNDANCE'])?$LANG['ABUNDANCE']:'Abundance'); ?>:
									</div>
									<div style="float:left;">
										<input type="text"  name="abundance" value="<?php echo $clArray["abundance"]; ?>" />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?>:
									</div>
									<div style="float:left;">
										<input name='notes' type='text' value="<?php echo $clArray["notes"];?>" size='65' maxlength='2000' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo (isset($LANG['EDITOR_NOTES'])?$LANG['EDITOR_NOTES']:'Editor Notes'); ?>:
									</div>
									<div style="float:left;">
										<input name='internalnotes' type='text' value="<?php echo $clArray["internalnotes"];?>" size='65' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo (isset($LANG['SOURCE'])?$LANG['SOURCE']:'Source'); ?>:
									</div>
									<div style="float:left;">
										<input name='source' type='text' value="<?php echo $clArray["source"];?>" size='65' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<div style='width:100px;font-weight:bold;float:left;'>
										<?php echo (isset($LANG['OVERRIDE'])?$LANG['OVERRIDE']:'Family Override'); ?>:
									</div>
									<div style="float:left;">
										<input name='familyoverride' type='text' value="<?php echo $clArray["familyoverride"];?>" size='65' maxlength='250' />
									</div>
								</div>
								<div style='clear:both;margin:3px;'>
									<input name='tid' type='hidden' value="<?php echo $vManager->getTid();?>" />
									<input name='taxon' type='hidden' value="<?php echo $vManager->getTaxonName();?>" />
									<input name='clid' type='hidden' value="<?php echo $vManager->getClid();?>" />
									<input name='clname' type='hidden' value="<?php echo $vManager->getClName();?>" />
									<button type="submit" name="action" value="Submit Checklist Edits"><?php echo (isset($LANG['SUBMIT_EDITS'])?$LANG['SUBMIT_EDITS']:'Submit Checklist Edits'); ?></button>
								</div>
							</fieldset>
						</form>
						<hr />
						<form name="renametaxonform" action="clsppeditor.php" method="post" onsubmit="return validateRenameForm(this)">
							<fieldset style='margin:5px;padding:15px;'>
								<legend><b><?php echo (isset($LANG['RENAME_TRANSFER'])?$LANG['RENAME_TRANSFER']:'Rename Taxon / Transfer Vouchers'); ?></b></legend>
								<div style='margin-top:2px;'>
									<div style='width:130px;font-weight:bold;float:left;'>
										<?php echo (isset($LANG['TARGET_TAXON'])?$LANG['TARGET_TAXON']:'Target Taxon'); ?>:
									</div>
									<div style='float:left;'>
										<input id="renamesciname" name='renamesciname' type="text" size="50" />
										<input id="renametid" name="renametid" type="hidden" value="" />
									</div>
								</div>
								<div style="clear:both;margin-top:2px;">
									<b>*</b> <?php echo (isset($LANG['VOUCHERS_TRANSFER'])?$LANG['VOUCHERS_TRANSFER']:'Note that vouchers &amp; notes will transfer to new taxon'); ?>
								</div>
								<div style="margin:15px">
									<input name='tid' type='hidden' value="<?php echo $vManager->getTid(); ?>" />
									<input name='clid' type='hidden' value="<?php echo $vManager->getClid(); ?>" />
									<input name='cltype' type='hidden' value="<?php echo $clArray['cltype']; ?>" />
									<input name='locality' type='hidden' value="<?php echo $clArray['locality']; ?>" />
									<input name="action" type="hidden" value="Rename Taxon" />
									<button type="submit" name="renamesubmit" value="Rename and Transfer"><?php echo (isset($LANG['RENAME'])?$LANG['RENAME']:'Rename and Transfer'); ?></button>
								</div>
							</fieldset>
						</form>
						<hr />
						<form action="clsppeditor.php" method="post" name="deletetaxon" onsubmit="return window.confirm('<?php echo (isset($LANG['ARE_YOU_SURE'])?$LANG['ARE_YOU_SURE']:'Are you sure you want to delete this taxon from checklist?'); ?>');">
							<fieldset style='margin:5px;padding:15px;'>
						   	<legend><b><?php echo (isset($LANG['DELETE'])?$LANG['DELETE']:'Delete'); ?></b></legend>
								<input type='hidden' name='tid' value="<?php echo $vManager->getTid(); ?>" />
								<input type='hidden' name='clid' value="<?php echo $vManager->getClid(); ?>" />
								<input type='hidden' name='cltype' value="<?php echo $clArray['cltype']; ?>" />
								<input type='hidden' name='locality' value="<?php echo $clArray['locality']; ?>" />
								<button type="submit" name="action" value="Delete Taxon From Checklist"><?php echo (isset($LANG['DELETE_TAXON'])?$LANG['DELETE_TAXON']:'Delete Taxon From Checklist'); ?></button>
							</fieldset>
						</form>
					</div>
					<div id="voucherdiv">
						<?php
						if($OCCURRENCE_MOD_IS_ACTIVE){
							?>
							<div style="float:right;margin-top:10px;">
								<a href="../collections/list.php?mode=voucher&db=all&usethes=1&reset=1&taxa=<?php echo $vManager->getTaxonName()."&targetclid=".$vManager->getClid()."&targettid=".$tid;?>">
									<img src="../images/link.png"  style="border:0px;" />
								</a>
							</div>
							<h3><?php echo (isset($LANG['VOUCHER_INFO'])?$LANG['VOUCHER_INFO']:'Voucher Information'); ?></h3>
							<?php
							$vArray = $vManager->getVoucherData();
							if(!$vArray){
								echo '<div>'.(isset($LANG['NO_VOUCHERS'])?$LANG['NO_VOUCHERS']:'No vouchers for this species has been assigned to checklist').' </div>';
							}
							?>
							<ul>
								<?php
								foreach($vArray as $occid => $iArray){
									?>
									<li>
										<a href="#" onclick="openPopup('../collections/individual/index.php?occid=<?php echo $occid; ?>','indpane')"><?php echo $occid; ?></a>:
										<?php
										if($iArray['catalognumber']) echo $iArray['catalognumber'].', ';
										echo '<b>'.$iArray['collector'].'</b>, ';
										if($iArray['eventdate']) echo $iArray['eventdate'].', ';
										if($iArray['sciname']) echo $iArray['sciname'];
										echo ($iArray['notes']?', '.$iArray['notes']:'').($iArray['editornotes']?', '.$iArray['editornotes']:'');
										?>
										<a href="#" onclick="toggle('vouch-<?php echo $occid;?>')"><img src="../images/edit.png" /></a>
										<form action="clsppeditor.php" method='post' name='delform' style="display:inline;" onsubmit="return confirm('<?php echo (isset($LANG['SURE_DELETE'])?$LANG['SURE_DELETE']:'Are you sure you want to delete this voucher record?'); ?>');">
											<input type='hidden' name='tid' value="<?php echo $vManager->getTid();?>" />
											<input type='hidden' name='clid' value="<?php echo $vManager->getClid();?>" />
											<input type='hidden' name='oiddel' id='oiddel' value="<?php echo $occid;?>" />
											<input type='hidden' name='tabindex' value="1" />
											<input type="image" name="action" src="../images/del.png" style="width:15px;" value="Delete Voucher" title="<?php echo (isset($LANG['DELETE_TAXON'])?$LANG['DELETE_TAXON']:'Delete Voucher'); ?>" />
										</form>
										<div id="vouch-<?php echo $occid;?>" style='margin:10px;clear:both;display:none;'>
											<form action="clsppeditor.php" method='post' name='editvoucher'>
												<fieldset style='margin:5px 0px 5px 5px;'>
													<legend><b><?php echo (isset($LANG['EDIT_VOUCHER'])?$LANG['EDIT_VOUCHER']:'Edit Voucher'); ?></b></legend>
													<input type='hidden' name='tid' value="<?php echo $vManager->getTid();?>" />
													<input type='hidden' name='clid' value="<?php echo $vManager->getClid();?>" />
													<input type='hidden' name='occid' value="<?php echo $occid;?>" />
													<input type='hidden' name='tabindex' value="1" />
													<div style='margin-top:0.5em;'>
														<b>Notes:</b>
														<input name='notes' type='text' value="<?php echo $iArray["notes"];?>" size='60' maxlength='250' />
													</div>
													<div style='margin-top:0.5em;'>
														<b>Editor Notes (editor display only):</b>
														<input name='editornotes' type='text' value="<?php echo $iArray["editornotes"];?>" size='30' maxlength='50' />
													</div>
													<div style='margin-top:0.5em;'>
														<button type='submit' name='action' value="<?php echo (isset($LANG['SUBMIT_V_EDITS'])?$LANG['SUBMIT_V_EDITS']:'Submit Voucher Edits'); ?>">Submit Voucher Edits</button>
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
				echo '<div>'.(isset($LANG['NO_DATA'])?$LANG['NO_DATA']:'Failed to return data or you might not have proper editing rights').'</div>';
			}
			?>
		</div>
	</body>
</html>