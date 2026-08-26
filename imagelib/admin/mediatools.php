<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/MediaManagementTools.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

//Tool refactoring incomplete. Deactivated until it can be addressed.
exit;

$collid = (array_key_exists('collid', $_POST) ? Sanitize::int($_POST['collid']) : '');
$mediaIdStart = (array_key_exists('mediaIdStart', $_POST) ? Sanitize::int($_POST['mediaIdStart']) : 0);
$limit = (array_key_exists('limit', $_POST) ? Sanitize::int($_POST['limit']) : 10000);

$transferThumbnail = empty($_POST['transferThumbnail']) ? 0 : 1;
$transferWeb = empty($_POST['transferWeb']) ? 0 : 1;
$transferLarge = empty($_POST['transferLarge']) ? 0 : 1;
$urlMatchTerm = (array_key_exists('urlMatchTerm', $_POST) ? $_POST['urlMatchTerm'] : '');
$deleteSource = empty($_POST['deleteSource']) ? 0 : 1;
$sourcePathPrefix = (array_key_exists('sourcePathPrefix', $_POST) ? $_POST['sourcePathPrefix'] : '');
$targetPathPrefix = (array_key_exists('targetPathPrefix', $_POST) ? $_POST['targetPathPrefix'] : '');
$urlPrefix = (array_key_exists('urlPrefix', $_POST) ? $_POST['urlPrefix'] : '');
$submit = (array_key_exists('submitbutton', $_POST)?$_POST['submitbutton']:'');


$archiveImages = (array_key_exists('archiveimg', $_POST) ? Sanitize::int($_POST['archiveimg']) : 0);
$delThumb = (array_key_exists('delthumb', $_POST) ? Sanitize::int($_POST['delthumb']) : 0);
$delWeb = (array_key_exists('delweb', $_POST) ? Sanitize::int($_POST['delweb']) : 0);
$delLarge = (array_key_exists('dellarge', $_POST) ? Sanitize::int($_POST['dellarge']) : 0);
$mediaIdStr = (array_key_exists('mediaIdStr', $_POST) ? Sanitize::int($_POST['mediaIdStr']) : '');
$matchTermThumbnail = (array_key_exists('matchTermThumbnail', $_POST)?$_POST['matchTermThumbnail']:'');
$matchTermWeb = (array_key_exists('matchTermWeb', $_POST)?$_POST['matchTermWeb']:'');
$matchTermLarge = (array_key_exists('matchTermLarge', $_POST)?$_POST['matchTermLarge']:'');
$imgRootUrl = (array_key_exists('imgRootUrl', $_POST)?$_POST['imgRootUrl']:'');
$imgRootPath = (array_key_exists('imgRootPath', $_POST)?$_POST['imgRootPath']:'');
$imgSubPath = (array_key_exists('imgSubPath', $_POST)?$_POST['imgSubPath']:'');
$copyover = (!empty($_POST['copyover']) ? 1 : 0);

$toolManager = new MediaMAnagementTools();
$toolManager->setCollid($collid);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title>Media Tools</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET; ?>"/>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function verifyMigrationCode(f){
			if(f.matchTermThumbnail.value == "" && f.matchTermWeb.value == "" && f.matchTermLarge.value == ""){
				alert("You need at least one matching term defined");
				return false;
			}
			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ padding: 10px; margin-bottom: 15px }
		legend{ font-weight: bold }
		.fieldRowDiv{ clear:both; margin: 2px 0px; }
		.fieldDiv{ float:left; margin: 2px 10px 2px 0px; }
		.fieldLabel{  }
		.fieldDiv button{ margin-top: 10px; }
	</style>
</head>
<body>
	<?php
	if($isEditor){
		?>
		<div role="main" id="innertext">
			<h1 class="page-heading">Media Tools</h1>
			<div id="actionDiv">
				<?php
				$mediaIdEnd = 0;
				if($submit){
					if($submit == 'transferImages'){
						?>
						<fieldset>
							<legend>Action Panel</legend>
							<ol>
							<?php
							$toolManager->setVerboseMode(2);
							$toolManager->setTransferThumbnail($transferThumbnail);
							$toolManager->setTransferWeb($transferWeb);
							$toolManager->setTransferLarge($transferLarge);
							$toolManager->setMatchTermThumbnail($matchTermThumbnail);
							$toolManager->setMatchTermWeb($matchTermWeb);
							$toolManager->setMatchTermLarge($matchTermLarge);
							$toolManager->setDeleteSource($deleteSource);
							$toolManager->setImgRootUrl($imgRootUrl);
							$toolManager->setImgRootPath($imgRootPath);
							$toolManager->setImgSubPath($imgSubPath);
							$toolManager->setCopyOverExistingImages($copyover);
							if($collid) $mediaIdStart = $toolManager->migrateCollectionDerivatives($mediaIdStart, $limit);
							else $mediaIdStart = $toolManager->migrateFieldDerivatives($mediaIdStart, $limit);
							?>
							</ol>
						</fieldset>
						<?php
					}
					elseif($submit == 'Process Images'){
						if($archiveImages) $toolManager->setArchiveImages($archiveImages);
						$toolManager->setDeleteThumbnail($delThumb);
						$toolManager->setDeleteWebImage($delWeb);
						$toolManager->setDeleteOriginal($delLarge);
						$toolManager->setMediaIdArr($mediaIdStr);
						$mediaIdEnd = $toolManager->archiveImageFiles($mediaIdStart, $limit);
					}
					else{
						$delThumb = 1;
						$delWeb = 1;
						$delLarge = 1;
					}
				}
				?>
			</div>
			<fieldset>
				<legend>Image Archival/Removal Tools</legend>
				<div>This tool can be used to stash (i.e. archive) or delete images that are currently stored locally (server must have write access to images)</div>
				<form action="mediatools.php" method="post">
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<span class="fieldLabel">Collection ID (collid):</span>
							<select name="collid" required>
								<option value="">Select a Collection</option>
								<option value="">-----------------------------</option>
								<option value="0">Field Images</option>
								<?php
								$collArr = $toolManager->getCollectionMeta();
								foreach($collArr as $id => $collName){
									echo '<option value="'.$id.'" '.($collid==$id?'SELECTED':'').'>'.$collName.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<b>Starting Image ID:</b> <input type="text" name="mediaIdStart" value="<?= $mediaIdEnd; ?>" /><br />
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<b>Batch limit: </b><input type="text" name="limit" value="<?= $limit; ?>" /><br />
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<fieldset>
								<legend>Action</legend>
								<input type="radio" name="archiveimg" value="0" <?= ($archiveImages?'':'CHECKED'); ?> /> Delete Images<br />
								<input type="radio" name="archiveimg" value="1" <?= ($archiveImages?'CHECKED':''); ?> /> Archive Images<br />
							</fieldset>
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<fieldset>
								<legend>Image Targets</legend>
								<input type="checkbox" name="delthumb" value="1" <?= ($delThumb?'CHECKED':''); ?> /> Delete Thumbnail Derivative<br />
								<input type="checkbox" name="delweb" value="1" <?= ($delWeb?'CHECKED':''); ?> /> Delete Web Derivative<br />
								<input type="checkbox" name="dellarge" value="1" <?= ($delLarge?'CHECKED':''); ?> /> Delete Large Derivative<br />
							</fieldset>
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<b>mediaIDs (enter multiple values delimited by commas)</b><br/>
							<textarea name="mediaIdStr" rows="8" cols="100" required></textarea>
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<button name="submitbutton" type="submit" value="Process Images">Process Images</button>
						</div>
					</div>
				</form>
			</fieldset>
			<fieldset>
				<legend>Image Migration Tools</legend>
				<div>This tool can be used to migrate images located on a remote server to the local server that is currently hosting the portal</div>
				<form action="mediatools.php" method="post" onsubmit="return verifyMigrationCode(this)">
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<span class="fieldLabel">Collection ID (collid):</span>
							<select name="collid" required>
								<option value="">Select a Collection</option>
								<option value="">-----------------------------</option>
								<option value="0">Field Images</option>
								<?php
								$collArr = $toolManager->getCollectionMeta();
								foreach($collArr as $id => $collName){
									echo '<option value="'.$id.'" '.($collid==$id?'SELECTED':'').'>'.$collName.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="fieldRowDiv">
						<fieldset>
							<legend>Transfer Target</legend>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<input name="transferThumbnail" type="checkbox" value="1" <?= ($transferThumbnail?'CHECKED':''); ?> />
									<span class="fieldLabel">Transfer Thumbnail</span>
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<input name="transferWeb" type="checkbox" value="1" <?= ($transferWeb?'CHECKED':''); ?> />
									<span class="fieldLabel">Transfer Web View (medium)</span>
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<input name="transferLarge" type="checkbox" value="1" <?= ($transferLarge?'CHECKED':''); ?> />
									<span class="fieldLabel">Transfer Large Image</span>
								</div>
							</div>
							<div class="fieldRowDiv" style="padding-top:10px">
								<div class="fieldDiv">
									<input name="deleteSource" type="checkbox" value="1" <?= ($deleteSource?'CHECKED':''); ?> />
									<span class="fieldLabel">Delete source images</span>
								</div>
							</div>
						</fieldset>
					</div>
					<div class="fieldRowDiv">
						<fieldset>
							<legend>Transfer Source Query Term</legend>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<span class="fieldLabel">Thumbnail Matching Term (thumbnailUrl):</span>
									<input name="matchTermThumbnail" type="text" value="<?= htmlspecialchars($matchTermThumbnail); ?>" style="width:300px" />
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<span class="fieldLabel">Web Image (medium) Matching Term (url):</span>
									<input name="matchTermWeb" type="text" value="<?= htmlspecialchars($matchTermWeb); ?>" style="width:300px" />
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<span class="fieldLabel">Large Image Matching Term (originalurl):</span>
									<input name="matchTermLarge" type="text" value="<?= htmlspecialchars($matchTermLarge); ?>" style="width:300px" />
								</div>
							</div>
						</fieldset>
					</div>
					<div class="fieldRowDiv">
						<fieldset>
							<legend>Path Variables</legend>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<span class="fieldLabel">Image Root URL (imgRootUrl):</span>
									<input name="imgRootUrl" type="text" value="<?= ($imgRootUrl ? htmlspecialchars($imgRootUrl) : $MEDIA_ROOT_URL); ?>" style="width:400px" />
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<span class="fieldLabel">Image Root Path (imgRootPath):</span>
									<input name="imgRootPath" type="text" value="<?= ($imgRootPath ? htmlspecialchars($imgRootPath) : $MEDIA_ROOT_PATH); ?>" style="width:400px" />
								</div>
							</div>
							<div class="fieldRowDiv">
								<div class="fieldDiv">
									<span class="fieldLabel">Target Sub-Path:</span>
									<input name="imgSubPath" type="text" value="<?= htmlspecialchars($imgSubPath) ?>" style="width:400px" />
								</div>
							</div>
						</fieldset>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<input type="checkbox" name="copyover" value="1" <?= ($copyover ? 'checked' : '') ?>>
							<span class="fieldLabel">copyover existing target images</span>
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<span class="fieldLabel">mediaID start:</span>
							<input type="text" name="mediaIdStart" value="<?= $mediaIdStart; ?>" required />
						</div>
					</div>
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<span class="fieldLabel">Batch limit:</span>
							<input type="text" name="limit" value="<?= $limit; ?>" />
						</div>
					</div>
					<div class="fieldRowDiv">
						<button name="submitbutton" type="submit" value="transferImages">Transfer Images</button>
					</div>
				</form>
			</fieldset>
		</div>
		<?php
	}
	else echo '<div>Permissions issue; are you logged in?</div>';
	?>
</body>
