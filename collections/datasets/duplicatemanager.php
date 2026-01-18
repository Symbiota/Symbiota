<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDuplicate.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('collections/datasets/duplicatemanager');

header("Content-Type: text/html; charset=".$CHARSET);

$collId = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$dupeDepth = array_key_exists('dupedepth', $_REQUEST) ? filter_var($_REQUEST['dupedepth'], FILTER_SANITIZE_NUMBER_INT) : 0;
$start = array_key_exists('start', $_REQUEST) ? filter_var($_REQUEST['start'], FILTER_SANITIZE_NUMBER_INT) : 0;
$limit = array_key_exists('limit', $_REQUEST) ? filter_var($_REQUEST['limit'], FILTER_SANITIZE_NUMBER_INT) : 1000;
$action = array_key_exists('action', $_REQUEST) ? htmlspecialchars($_REQUEST['action'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';
$formSubmit = array_key_exists('formsubmit' , $_POST) ? $_POST['formsubmit'] : '';

if(!$SYMB_UID){
	header('Location: ../../profile/index.php?refurl=../collections/datasets/duplicatemanager.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));
}

$dupManager = new OccurrenceDuplicate();
$collMap = $dupManager->getCollMap($collId);

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}
elseif(isset($collMap['colltype']) && $collMap['colltype'] == 'General Observations' && array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollEditor'])){
	$isEditor = 1;
}

//If collection is a general observation project, limit to User
if(isset($collMap['colltype']) && $collMap['colltype'] == 'General Observations') $dupManager->setObsUid($SYMB_UID);

if($isEditor && $formSubmit){
	if($formSubmit == 'clusteredit'){
		$duplicateID = filter_var($_POST['dupid'], FILTER_SANITIZE_NUMBER_INT);
		if($dupManager->editCluster($duplicateID, $_POST['title'], $_POST['description'], $_POST['notes'])){
			$statusStr = $LANG['EDIT_SUCCESS'];
		}
		else{
			$statusStr = $LANG['EDIT_ERROR'] . ': ' . $dupManager->getErrorStr();
		}
	}
	elseif($formSubmit == 'clusterdelete'){
		$duplicateID = filter_var($_POST['deldupid'], FILTER_SANITIZE_NUMBER_INT);
		if($dupManager->deleteCluster($duplicateID)){
			$statusStr = $LANG['DELETE_SUCCESS'];
		}
		else{
			$statusStr = $LANG['DELETE_ERROR'] . ': ' . $dupManager->getErrorStr();
		}
	}
	elseif($formSubmit == 'occdelete'){
		$duplicateID = filter_var($_POST['dupid'], FILTER_SANITIZE_NUMBER_INT);
		$occid = filter_var($_POST['occid'], FILTER_SANITIZE_NUMBER_INT);
		if($dupManager->deleteOccurFromCluster($duplicateID, $occid)){
			$statusStr = $LANG['DELETE_SUCCESS'];
		}
		else{
			$statusStr = $LANG['DELETE_ERROR'] . ': ' . $dupManager->getErrorStr();
		}
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $DEFAULT_TITLE.' '.$LANG['DUP_CLUSTERING'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript">
		function openOccurPopup(occid) {
			occWindow=open("../individual/index.php?occid="+occid,"occwin"+occid,"resizable=1,scrollbars=1,toolbar=0,width=900,height=600,left=20,top=20");
			if(occWindow.opener == null) occWindow.opener = self;
		}

		function toggle(target){
			var ele = document.getElementById(target);
			if(ele){
				if(ele.style.display=="block"){
					ele.style.display="none";
		  		}
			 	else {
			 		ele.style.display="block";
			 	}
			}
			else{
				var divObjs = document.getElementsByTagName("div");
			  	for (i = 0; i < divObjs.length; i++) {
			  		var divObj = divObjs[i];
			  		if(divObj.getAttribute("class") == target || divObj.getAttribute("className") == target){
						if(divObj.style.display=="none"){
							divObj.style.display="inline";
						}
					 	else {
					 		divObj.style.display="none";
					 	}
					}
				}
			}
		}
	</script>
    <style>
		table.styledtable td { white-space: nowrap; }
		button { margin: 10px; }
    </style>
</head>
<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php"> <?= $LANG['HOME'] ?> </a> &gt;&gt;
		<?php
		if(isset($collMap['colltype']) && $collMap['colltype'] == 'General Observations'){
			echo '<a href="../../profile/viewprofile.php?tabindex=1">' . $LANG['PERS_MANAGE_MENU'] . '</a> &gt;&gt; ';
		}
		else{
			echo '<a href="../misc/collprofiles.php?collid=' . $collId . '&emode=1">' . $LANG['COL_MANAGE'] . '</a> &gt;&gt; ';
		}
		if($action){
			echo '<a href="duplicatemanager.php?collid=' . $collId . '">' . $LANG['DUP_MANAGE'] . '</a> &gt;&gt; ';
			echo '<b>'.$LANG['DUP_CLUSTERS'].'</b>';
		}
		else{
			echo '<b>'.$LANG['DUP_MANAGE'].'</b>';
		}
		?>
	</div>

	<!-- inner text -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['DUPLICATE_MANAGER'] ?></h1>
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:20px;color:<?= (substr($statusStr,0,5)=='ERROR'?'red':'green');?>">
				<?= $statusStr ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			if(!$action){
				?>
				<section>
					<div>
						<?= $LANG['DUP_EXPLANATION'] ?>
					</div>
					<div style="margin:25px;">
						<a href="duplicatemanager.php?collid=<?= $collId ?>&action=listdupes">
							<?= $LANG['SPEC_DUP_CLUSTERS'] ?>
						</a>
					</div>
					<div style="margin:25px;">
						<a href="duplicatemanager.php?collid=<?= $collId ?>&dupedepth=2&action=listdupes">
							<?= $LANG['DUP_CLUSTERS_CONFLICTING'] ?>
						</a>
					</div>
					<div style="margin:25px;">
						<a href="duplicatemanager.php?collid=<?= $collId ?>&action=batchlinkdupes">
							<?= $LANG['BATCH_LINK_DUPS'] ?>
						</a> - <?= $LANG['BATCH_LINK_EXPLANATION'] ?>
					</div>
					<div style="margin:25px;">
						<a href="<?= $CLIENT_ROOT . '/collections/editor/batchDuplicateGeorefCopy.php?collid=' . $collId ?>">
							<?= $LANG['BATCH_COPY_GEOREFERENCE_DUPLICATES'] ?>
						</a> - <?= $LANG['BATCH_COPY_GEOREFERENCE_DUPLICATES_EXPLANATION'] ?>
					</div>
					<?php
					if(!empty($ACTIVATE_EXSICCATI) && $collMap['colltype'] == 'Preserved Specimens'){
						?>
						<div style="margin:25px;">
							<a href="../exsiccati/index.php?collid=<?= $collId ?>" target="_blank">
								<?= $LANG['EXS_DUPS'] ?>
							</a> - <?= $LANG['EXS_DUP_EXPLANATION'] ?>
						</div>
						<div style="margin:25px;">
							<a href="../exsiccati/index.php?collid=<?= $collId ?>&formsubmit=dlexs">
								<?= $LANG['EXS_DOWNLOAD'] ?>
							</a> - <?= $LANG['EXS_DOWNLOAD_EXPLANATION'] ?>
						</div>
						<?php
					}
					?>
				</section>
				<?php
			}
			else{
				if($action == 'batchlinkdupes'){
					?>
					<ul>
						<?php
						$dupManager->batchLinkDuplicates($collId,true);
						?>
					</ul>
					<?php
				}
				elseif($action == 'listdupes'){
					$clusterArr = $dupManager->getDuplicateClusterList($collId, $dupeDepth, $start, $limit);
					$totalCnt = $clusterArr['cnt'] ?? 0;
					unset($clusterArr['cnt']);
					if($clusterArr){
						$paginationStr = '<span>';
						if($start) $paginationStr .= '<a href="duplicatemanager.php?collid=' . $collId . '&dupedepth=' . $dupeDepth . '&action=' . $action . '&start=' . ($start - $limit) . '&limit=' . $limit . '">';
						$paginationStr .= '&lt;&lt; '.$LANG['PREVIOUS'];
						if($start) $paginationStr .= '</a>';
						$paginationStr .= '</span>';
						$paginationStr .= ' || '.($start+1).' - '.(count($clusterArr)<$limit?$totalCnt:($start + $limit)).' || ';
						$paginationStr .= '<span>';
						if($totalCnt >= ($start+$limit)) $paginationStr .= '<a href="duplicatemanager.php?collid=' . $collId . '&dupedepth=' . $dupeDepth . '&action=' . $action . '&start=' . ($start + $limit) . '&limit=' . $limit . '">';
						$paginationStr .= $LANG['NEXT'].' &gt;&gt;';
						if($totalCnt >= ($start+$limit)) $paginationStr .= '</a>';
						$paginationStr .= '</span>';
						?>
						<div style="clear:both;font-weight:bold;font-size:140%;">
							<?= $collMap['collectionname'] ?>
						</div>
						<div style="float:right;">
							<?= $paginationStr ?>
						</div>
						<div style="font-weight:bold;margin-left:15px;">
							<?= $totalCnt . ' ' . $LANG['DUP_CLUSTERS'] . ' ' . ($dupeDepth ? $LANG['WITH_ID_DIFFERENCES'] : '') ?>
						</div>
						<div style="margin:20px 0px;clear:both;">
							<?php
							foreach($clusterArr as $dupId => $dupArr){
								?>
								<div style="clear:both;margin:10px 0px;">
									<div style="font-weight:bold;font-size:120%;">
										<?= $dupArr['title'] ?>
										<span onclick="toggle('editdiv-<?= $dupId ?>')" title="<?= $LANG['DISP_EDIT_CONTROLS'] ?>"><img src="../../images/edit.png" style="width:1.2em;" /></span>
									</div>
									<?php
									if(isset($dupArr['desc'])) echo '<div style="margin-left:10px;">'.$dupArr['desc'].'</div>';
									if(isset($dupArr['notes'])) echo '<div style="margin-left:10px;">'.$dupArr['notes'].'</div>';
									?>
									<div class="editdiv-<?= $dupId ?>" style="display:none;">
										<fieldset style="margin:20px;padding:15px;">
											<legend><b>Edit Cluster</b></legend>
											<form name="dupeditform-<?= $dupId ?>" method="post" action="duplicatemanager.php">
												<b>Title:</b> <input name="title" type="text" value="<?= $dupArr['title'] ?>" style="width:300px;" required ><br/>
												<b>Description:</b> <input name="description" type="text" value="<?= $dupArr['desc'] ?>" style="width:400px;" /><br/>
												<b>Notes:</b> <input name="notes" type="text" value="<?= $dupArr['notes'] ?>" style="width:400px;" /><br/>
												<input name="dupid" type="hidden" value="<?= $dupId ?>" />
												<input name="collid" type="hidden" value="<?= $collId ?>" />
												<input name="dupedepth" type="hidden" value="<?= $dupeDepth ?>" >
												<input name="start" type="hidden" value="<?= $start ?>" />
												<input name="limit" type="hidden" value="<?= $limit ?>" />
												<input name="action" type="hidden" value="<?= $action ?>" />
												<input name="formsubmit" type="hidden" value="clusteredit" />
												<button name="submit" type="submit" value="Save Edits" ><?= $LANG['SAVE_EDITS'] ?></button>
											</form>
											<form name="dupdelform-<?= $dupId ?>" method="post" action="duplicatemanager.php" onsubmit="return confirm('<?= $LANG['SURE_DEL_DUP'] ?>');">
												<input name="deldupid" type="hidden" value="<?= $dupId ?>" />
												<input name="collid" type="hidden" value="<?= $collId ?>" />
												<input name="dupedepth" type="hidden" value="<?= $dupeDepth ?>" >
												<input name="start" type="hidden" value="<?= $start ?>" />
												<input name="limit" type="hidden" value="<?= $limit ?>" />
												<input name="action" type="hidden" value="<?= $action ?>" />
												<input name="formsubmit" type="hidden" value="clusterdelete" />
												<button class="button-danger" name="submit" type="submit" value="Delete Cluster" ><?= $LANG['DEL_CLUSTER'] ?></button>
											</form>
										</fieldset>
									</div>
									<div style="margin:7px 10px;">
										<?php
										unset($dupArr['title']);
										unset($dupArr['desc']);
										unset($dupArr['notes']);
										foreach($dupArr as $occid => $oArr){
											?>
											<div style="margin:10px">
												<div style="float:left;">
													<a href="#" onclick="openOccurPopup(<?= $occid ?>); return false;"><b><?= htmlspecialchars($oArr['id'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?></b></a> =&gt;
													<?= $oArr['recby'] ?>
												</div>
												<div class="editdiv-<?= $dupId ?>" style="display:none;float:left;" title="<?= $LANG['DEL_SPEC_FROM_CLUSTER'] ?>">
													<form name="dupdelform-<?= $dupId.'-'.$occid ?>" method="post" action="duplicatemanager.php" onsubmit="return confirm('<?= $LANG['SURE_DEL_SPEC_FROM_CLUSTER'] ?>');" style="display:inline;">
														<input name="dupid" type="hidden" value="<?= $dupId ?>" />
														<input name="occid" type="hidden" value="<?= $occid ?>" />
														<input name="collid" type="hidden" value="<?= $collId ?>" />
														<input name="dupedepth" type="hidden" value="<?= $dupeDepth ?>" >
														<input name="start" type="hidden" value="<?= $start ?>" />
														<input name="limit" type="hidden" value="<?= $limit ?>" />
														<input name="action" type="hidden" value="<?= $action ?>" />
														<input name="formsubmit" type="hidden" value="occdelete" />
														<input name="submit" type="image" src="../../images/del.png" style="width:1.2em;" />
													</form>
												</div>
												<div style="margin-left:15px;clear:both;">
													<?php
													echo '<b>'.$oArr['sciname'].'</b><br/>';
													if($oArr['idby']) echo $LANG['DET_BY'].': '.$oArr['idby'].' '.$oArr['dateid'];
													?>
												</div>
											</div>
											<?php
										}
										?>
									</div>
								</div>
								<?php
							}
							?>
						</div>
						<?php
						echo $paginationStr;
					}
					else{
						 echo '<div><b>'.$LANG['NO_DUP_CLUSTERS'].'</b></div>';
					}
				}
				?>
				<div>
					<a href="duplicatemanager.php?collid=<?= $collId ?>"><?= $LANG['RETURN_MAIN'] ?></a>
				</div>
				<?php
			}
		}
		else{
			echo '<h2>'.$LANG['NOT_AUTH'].'</h2>';
		}
		?>
	</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
