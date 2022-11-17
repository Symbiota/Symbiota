<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageCleaner.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../imagelib/admin/thumbnailbuilder.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:'';
$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:0;
$buildMediumDerivatives = array_key_exists('buildmed',$_POST)?$_POST['buildmed']:0;
$evaluateOrientation = array_key_exists('evalorientation',$_POST)?$_POST['evalorientation']:0;

//Sanitation
if(!is_numeric($collid)) $collid = '';
if(!is_numeric($tid)) $tid = 0;
if(!is_numeric($buildMediumDerivatives)) $buildMediumDerivatives = 0;
if(!is_numeric($evaluateOrientation)) $evaluateOrientation = 0;
$action = filter_var($action,FILTER_SANITIZE_STRING);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif($collid){
	if(array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
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
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Thumbnail Builder</title>
	<?php
	$activateJQuery = false;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript">
		function resetRebuildForm(f){
			f.catNumLow.value = "";
			f.catNumHigh.value = "";
			f.catNumList.value = "";
		}
	</script>
	<style type="text/css">
		fieldset{ padding: 10px }
		fieldset legend{ font-weight: bold }
		.fieldRowDiv{ clear:both; margin: 2px 0px; }
		.fieldRowDiv button{ margin-top: 10px; }
		.fieldDiv{ float:left; margin: 2px 10px 2px 0px; }
		.fieldLabel{ }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php">Home</a> &gt;&gt;
		<?php
		if($collid) echo '<a href="../../collections/misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Menu</a> &gt;&gt;';
		else echo '<a href="../../sitemap.php">Sitemap</a> &gt;&gt;';
		?>
		<b>Thumbnail Builder</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor){
			echo '<h2>Thumbnail Maintenance Tool';
			if($collid) echo ' - '.$imgManager->getCollectionName();
			elseif($collid==='0') echo ' - field images';
			echo '</h2>';
			if($action && $action != 'none'){
				if($action == 'resetprocessing'){
					$imgManager->resetProcessing();
				}
				else{
					?>
					<fieldset style="margin:10px;padding:15px">
						<legend><b>Processing Panel</b></legend>
						<div style="font-weight:bold;">Start processing...</div>
						<?php
						if($action == 'buildThumbnails') $imgManager->buildThumbnailImages();
						elseif($action == 'Refresh Thumbnails'){
							echo '<div style="margin-bottom:10px;">Number of images to be refreshed: '.$imgManager->getProcessingCnt($_POST).'</div>';
							$imgManager->refreshThumbnails($_POST);
						}
						?>
						<div style="margin-top:10px;font-weight:bold;">Finished!</div>
					</fieldset>
					<?php
				}
			}
			?>
			<fieldset style="margin:30px 10px;padding:15px;">
				<legend><b>Thumbnail Builder</b></legend>
				<div>
					<?php
					$reportArr = $imgManager->getReportArr();
					if($reportArr){
						echo '<b>Images counts without thumbnails and/or basic web image display</b> - This function will build thumbnail images for all occurrence images mapped from an external server.';
						if($tid) echo '<div style="margin:5px 25px">Taxa Filter: '.$imgManager->getSciname().' (tid: '.$tid.')</div>';
						echo '<ul>';
						foreach($reportArr as $id => $retArr){
							echo '<li>';
							echo '<a href="thumbnailbuilder.php?collid='.$id.'&tid='.$tid.'&action=none">';
							echo $retArr['name'];
							echo '</a>';
							echo ': '.$retArr['cnt'].' images';
							echo '</li>';
						}
						echo '</ul>';
					}
					else{
						echo '<div>All images have properly mapped thumbnails. Nothing needs to be done.</div>';
					}
					?>
				</div>
				<div style="margin:25px;">
					<?php
					if($reportArr){
						if($collid && $action == 'buildThumbnails' && $reportArr[$collid]['cnt']){
							//Thumbnails have been processed but there are still some that missed processing
							?>
							<div>There appears to be some images that are not processing, perhaps because they have been tagged as being handled by another process.<br/>
							Click the reset processing button to do a full reset of all images for reprocessing. This process can take a few minutes, so be patient. </div>
							<div style="margin:10px">
								<form name="resetform" action="thumbnailbuilder.php" method="post">
									<input name="collid" type="hidden" value="<?php echo $collid; ?>">
									<input name="tid" type="hidden" value="<?php echo $tid; ?>">
									<button name="action" type="submit" value="resetprocessing">Reset Proccessing</button>
								</form>
							</div>
							<?php
						}
						else{
							?>
							<form name="tnbuilderform" action="thumbnailbuilder.php" method="post">
								<div class="fieldRowDiv">
									<div class="fieldDiv">
										<input name="buildmed" type="checkbox" value="1" <?php echo ($buildMediumDerivatives?'checked':''); ?> />
										<span class="fieldLabel"> include medium-sized image derivatives in addition to thumbnails</span>
									</div>
								</div>
								<div class="fieldRowDiv">
									<div class="fieldDiv">
										<input name="evalorientation" type="checkbox" value="1" <?php echo ($evaluateOrientation?'checked':''); ?> />
										<span class="fieldLabel"> rotate image derivatives based on orientation tag</span>
									</div>
								</div>
								<div class="fieldRowDiv">
									<input name="collid" type="hidden" value="<?php echo $collid; ?>">
									<input name="tid" type="hidden" value="<?php echo $tid; ?>">
									<button name="action" type="submit" value="buildThumbnails">Build Thumbnails</button>
								</div>
							</form>
							<?php
						}
					}
					?>
				</div>
			</fieldset>
			<?php
			if($collid){
				if($remoteImgCnt = $imgManager->getRemoteImageCnt()){
					?>
					<fieldset style="margin:30px 10px;padding:15px">
						<legend><b>Thumbnail Re-Mapper</b></legend>
						<form name="tnrebuildform" action="thumbnailbuilder.php" method="post">
							<div style="margin-bottom:20px;">
								This tool will iterate through the remotely mapped images and refresh locally stored image derivatives.
								Default action is to only rebuild derivatives when the creation date of the source image is more recent than the original build date.
								The alternative option is to force the rebuild of all images.
							</div>
							<div style="margin-bottom:10px;">
								Number images available for refresh: <?php echo $remoteImgCnt; ?>
							</div>
							<div style="margin-bottom:10px;">
								Catalog Number Range: <input name="catNumLow" type="text" value="<?php echo (isset($_POST['catNumLow'])?$_POST['catNumLow']:''); ?>" /> -
								<input name="catNumHigh" type="text" value="<?php echo (isset($_POST['catNumHigh'])?$_POST['catNumHigh']:''); ?>" />
							</div>
							<div style="margin-bottom:10px;vertical-align:top;height:90px">
								<div style="float:left">Catalog Number List: </div>
								<div style="margin-left:5px;float:left"><textarea name="catNumList" rows="5" cols="50"><?php echo (isset($_POST['catNumList'])?$_POST['catNumList']:''); ?></textarea></div>
							</div>
							<div style="margin-bottom:10px;">
								<input name="evaluate_ts" type="radio" value="1" checked /> Only process images where the source file is more recent than thumbnails<br/>
								<input name="evaluate_ts" type="radio" value="0" /> Force rebuild all images
							</div>
							<div class="fieldRowDiv">
								<input name="buildmed" type="checkbox" value="1" <?php echo ($buildMediumDerivatives?'checked':''); ?> />
								<span class="fieldLabel"> include medium-sized image derivatives in addition to thumbnails</span>
							</div>
							<div style="margin-bottom:10px;">
								<input name="evalorientation" type="checkbox" value="1" <?php echo ($evaluateOrientation?'checked':''); ?> /> rotate images based on orientation tag
							</div>
							<div style="margin:20px;clear:both">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="action" type="submit" value="Refresh Thumbnails" />
								<input type="button" value="Reset" onclick="resetRebuildForm(this.form)" />
							</div>
						</form>
					</fieldset>
					<?php
				}
			}
		}
		else{
			echo '<div><b>ERROR: improper permissions</b></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>