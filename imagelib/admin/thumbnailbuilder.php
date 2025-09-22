<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageCleaner.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/imagelib/admin/thumbnailbuilder.'.$LANG_TAG.'.php'))
	include_once($SERVER_ROOT.'/content/lang/imagelib/admin/thumbnailbuilder.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/imagelib/admin/thumbnailbuilder.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../imagelib/admin/thumbnailbuilder.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : '';
$tid = array_key_exists('tid', $_REQUEST) ? filter_var($_REQUEST['tid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$buildMediumDerivatives = array_key_exists('buildmed', $_POST) ? filter_var($_POST['buildmed'], FILTER_SANITIZE_NUMBER_INT) : 0;
$evaluateOrientation = array_key_exists('evalorientation', $_POST) ? filter_var($_POST['evalorientation'], FILTER_SANITIZE_NUMBER_INT) : 0;
$limit = array_key_exists('limit', $_POST) ? filter_var($_POST['limit'], FILTER_SANITIZE_NUMBER_INT) : '';
$action = array_key_exists('action', $_REQUEST) ? htmlspecialchars($_REQUEST['action'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : '';

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif($collid){
	if(array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin'])){
		$isEditor = true;
	}
}

$imgManager = new ImageCleaner();
$imgManager->setCollid($collid);
$imgManager->setTid($tid);
$imgManager->setBuildMediumDerivative($buildMediumDerivatives);
$imgManager->setTestOrientation($evaluateOrientation);

//Set default actions
if(!$buildMediumDerivatives && $imgManager->getManagementType() == 'Live Data') $buildMediumDerivatives = true;
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE.' '.$LANG['THUMB_BUILDER']; ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript">
		function resetRebuildForm(f){
			f.catNumLow.value = "";
			f.catNumHigh.value = "";
			f.catNumList.value = "";
		}
	</script>
	<style>
		fieldset{ padding: 10px }
		fieldset legend{ font-weight: bold }
		.fieldRowDiv{ clear:both; margin: 2px 0px; }
		.fieldRowDiv button{ margin-top: 10px; }
		.fieldDiv{ float:left; margin: 2px 10px 2px 0px; }
		.fieldLabel{ }
		hr{ margin: 10px 0px; }
		button{ margin: 10px; display: inline }
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php"><?= $LANG['HOME']; ?></a> &gt;&gt;
		<?php
		if($collid) echo '<a href="../../collections/misc/collprofiles.php?collid=' . $collid . '&emode=1">' . $LANG['COL_MAN_MENU'] . '</a> &gt;&gt;';
		else echo '<a href="../../sitemap.php">' . $LANG['SITEMAP'] . '</a> &gt;&gt;';
		?>
		<b> <?= $LANG['THUMB_BUILDER'] ?> </b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<?php
		if($isEditor){
			echo '<h1 class="page-heading">'.$LANG['THUMB_MAINT_TOOL'];
			if($collid) echo ' - '.$imgManager->getCollectionName();
			elseif($collid === '0') echo ' - '.$LANG['FIELD_IMAGES'];
			echo '</h1>';
			if($action && $action != 'none'){
				if($action == 'resetprocessing'){
					$imgManager->resetProcessing();
				}
				else{
					?>
					<fieldset style="margin:10px;padding:15px">
						<legend><b><?= $LANG['PROCESSING_PANEL']; ?></b></legend>
						<div style="font-weight:bold;"><?= $LANG['START_PROCESSING']; ?>...</div>
						<?php
						if($action == 'buildThumbnails') $imgManager->buildThumbnailImages($limit);
						elseif($action == 'Refresh Thumbnails'){
							echo '<div style="margin-bottom:10px;">' . $LANG['NUM_IMGS_REFRESHED'] . ': ' . $imgManager->getProcessingCnt($_POST) . '</div>';
							$imgManager->refreshThumbnails($_POST);
						}
						?>
						<div style="margin-top:10px;font-weight:bold;"><?= $LANG['FINISHED']; ?></div>
					</fieldset>
					<?php
				}
			}
			?>
			<section class="fieldset-like">
				<h2> <span> <?= $LANG['THUMB_BUILDER']; ?> </span> </h2>
				<div>
					<?php
					$reportArr = $imgManager->getReportArr();
					if($reportArr){
						echo '<b>'.$LANG['IMG_COUNT_EXPLAIN'].'</b> - '.$LANG['THUMB_IMG_EXPLAIN'];
						if($tid) echo '<div style="margin:5px 25px">'.$LANG['TAX_FILTER'].': '.$imgManager->getSciname().' (tid: '.$tid.')</div>';
						echo '<ul>';
						foreach($reportArr as $id => $retArr){
							echo '<li>';
							echo '<a href="thumbnailbuilder.php?collid=' . htmlspecialchars($id, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&tid=' . $tid . '&action=none">';
							echo $retArr['name'];
							echo '</a>';
							echo ': '.$retArr['cnt'].' images';
							echo '</li>';
						}
						echo '</ul>';
					}
					else{
						echo '<div>'.$LANG['ALL_THUMBS_DONE'].'</div>';
					}
					?>
				</div>
				<div style="margin:25px;">
					<?php
					if($reportArr){
						?>
						<form name="tnbuilderform" action="thumbnailbuilder.php" method="post">
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<input id="buildmed" name="buildmed" type="checkbox" value="1" <?= ($buildMediumDerivatives?'checked':''); ?> />
									<label for="buildmed" class="fieldLabel"> <?= $LANG['INCLUDE_MED']; ?> </label>
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<input id="evalorientation" name="evalorientation" type="checkbox" value="1" <?= ($evaluateOrientation?'checked':''); ?> />
									<label for="evalorientation" class="fieldLabel"> <?= $LANG['ROTATE_IMGS']; ?> </label>
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<label for="limit"> <?= $LANG['PROCESSING_LIMIT']; ?>: </label>
									<input id="limit" name="limit" type="number" min=0 value="<?= intval($limit) ?>" />
								</div>
							</div>
							<div class="fieldRowDiv">
								<input name="collid" type="hidden" value="<?= $collid; ?>">
								<input name="tid" type="hidden" value="<?= $tid; ?>">
								<button name="action" type="submit" value="buildThumbnails"><?= $LANG['BUILD_THUMBS']; ?></button>
							</div>
						</form>
						<?php
						if($collid && $action == 'buildThumbnails' && $reportArr[$collid]['cnt']){
							//Thumbnails have been processed but there are still some that missed processing
							?>
							<hr>
							<div><?= $LANG['NOT_PROCESSING_ERROR']; ?> </div>
							<div class="fieldRowDiv">
								<form name="resetform" action="thumbnailbuilder.php" method="post">
									<input name="collid" type="hidden" value="<?= $collid; ?>">
									<input name="tid" type="hidden" value="<?= $tid; ?>">
									<button name="action" type="submit" value="resetprocessing"><?= $LANG['RESET_PROCESSING']; ?></button>
								</form>
							</div>
							<?php
						}
					}
					?>
				</div>
				</section>
			<?php
			if($collid){
				if($remoteImgCnt = $imgManager->getRemoteImageCnt()){
					?>
					<fieldset style="margin:30px 10px;padding:15px">
						<legend><b><?= $LANG['THUMB_REMAPPER']; ?></b></legend>
						<form name="tnrebuildform" action="thumbnailbuilder.php" method="post">
							<div style="margin-bottom:20px;">
								<?= $LANG['THUMB_REMAP_EXPLAIN']; ?>
							</div>
							<div style="margin-bottom:10px;">
								<?= $LANG['IMAGES_AVAIL_REFRESH'].': '.$remoteImgCnt; ?>
							</div>
							<div class="fieldRowDiv">
								<?= $LANG['CATNUM_RANGE'] ?>: <input name="catNumLow" type="text" value="<?= (isset($_POST['catNumLow']) ? htmlspecialchars($_POST['catNumLow'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : ''); ?>" /> -
								<input name="catNumHigh" type="text" value="<?= (isset($_POST['catNumHigh']) ? htmlspecialchars($_POST['catNumHigh'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : ''); ?>" />
							</div>
							<div class="fieldRowDiv" style="vertical-align:top;height:90px">
								<div style="float:left"><?= $LANG['CATNUM_LIST']; ?>: </div>
								<div style="margin-left:5px;float:left"><textarea name="catNumList" rows="5" cols="50"><?= (isset($_POST['catNumList']) ? htmlspecialchars($_POST['catNumList'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) : ''); ?></textarea></div>
							</div>
							<div class="fieldRowDiv">
								<fieldset>
									<input name="evaluate_ts" type="radio" value="1" checked /> <?= $LANG['ONLY_PROCESS_RECENT']; ?><br/>
									<input name="evaluate_ts" type="radio" value="0" /> <?= $LANG['FORCE_REBUILD']; ?>
								</fieldset>
							</div>
							<div class="fieldRowDiv">
								<input name="buildmed" type="checkbox" value="1" <?= ($buildMediumDerivatives?'checked':''); ?> />
								<span class="fieldLabel"> <?= $LANG['INCLUDE_MED']; ?></span>
							</div>
							<div class="fieldRowDiv">
								<input id="evalorientation" name="evalorientation" type="checkbox" value="1" <?= ($evaluateOrientation?'checked':''); ?> /> <?= $LANG['ROTATE_IMGS']; ?>

							</div>
							<div style="margin:20px;clear:both">
								<input name="collid" type="hidden" value="<?= $collid; ?>" />
								<button name="action" type="submit" value="Refresh Thumbnails"><?= $LANG['REFRESH_THUMBS']; ?></button>
								<button type="button" onclick="resetRebuildForm(this.form)"><?= $LANG['RESET']; ?></button>
							</div>
						</form>
					</fieldset>
					<?php
				}
			}
		}
		else{
			echo '<div><b>'.$LANG['ERROR_PERMISSIONS'].'</b></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
