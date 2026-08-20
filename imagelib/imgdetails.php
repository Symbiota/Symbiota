<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/Media.php');
include_once($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('imagelib/imgdetails');

header('Content-Type: text/html; charset=' . $CHARSET);

$mediaID = array_key_exists('mediaid', $_REQUEST) ? filter_var($_REQUEST['mediaid'], FILTER_SANITIZE_NUMBER_INT) : 0;
if (!$mediaID && array_key_exists('imgid', $_REQUEST)) $mediaID = filter_var($_REQUEST['imgid'], FILTER_SANITIZE_NUMBER_INT);
$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';
$eMode = array_key_exists('emode', $_REQUEST) ? filter_var($_REQUEST['emode'], FILTER_SANITIZE_NUMBER_INT) : 0;

$imgArr = Media::getMedia($mediaID);

$isEditor = false;
if ($IS_ADMIN || ($imgArr && ($imgArr['username'] === $USERNAME || ($imgArr['creatorUid'] && $imgArr['creatorUid'] == $SYMB_UID)))) {
	$isEditor = true;
}

$status = '';

if ($isEditor) {
	if ($action == 'Submit Image Edits') {
		Media::update($mediaID, $_POST, StorageFactory::make());
	} elseif ($action == 'Transfer Image') {
		if($targettid = $_REQUEST['targettid'] ?? false) {
			Media::update($mediaID, ['tid' => $targettid ], StorageFactory::make());

			if($errors = Media::getErrors()) {
				$status = 'Errors:<br/>' . implode('<br/>', $errors);
			} else {
				header('Location: ../taxa/profile/tpeditor.php?tid=' . $_REQUEST['targettid'] . '&tabindex=1');
			}
		} else {
			$status = "ERROR: " . $LANG['MEDIA_TRANSFER_REQUIRES_TAXON_ID'];
		}
	} elseif ($action == 'Delete Image') {
		$remove_files = $_REQUEST['removeimg'] ?? false;
		try {
			Media::delete(intval($mediaID), boolval($remove_files));
			if($errors = Media::getErrors()) {
				$status = 'Errors:<br/>' . implode('<br/>', $errors);
			} else if($_REQUEST['tid'] ?? false) {
				header('Location: ../taxa/profile/tpeditor.php?tid=' . $_REQUEST['tid'] . '&tabindex=1');
			} else {
				header('Location: index.php');
			}
		} catch(Throwable $th) {
			$status = "ERROR: " . $th->getMessage();
		}

	}
	$imgArr = Media::getMedia($mediaID);
}
$serverPath = GeneralUtil::getDomain();
if ($imgArr) {
	$imgUrl = $imgArr['url'];
	$origUrl = $imgArr['originalUrl'];
	$metaUrl = $imgArr['url'];
	if (array_key_exists('MEDIA_DOMAIN', $GLOBALS)) {
		if ($imgUrl !== null && substr($imgUrl, 0, 1) == '/') {
			$imgUrl = $GLOBALS['MEDIA_DOMAIN'] . $imgUrl;
			$metaUrl = $GLOBALS['MEDIA_DOMAIN'] . $metaUrl;
		}
		if ($origUrl !== null && $origUrl && substr($origUrl, 0, 1) == '/') {
			$origUrl = $GLOBALS['MEDIA_DOMAIN'] . $origUrl;
		}
	}
	if ($metaUrl !== null && substr($metaUrl, 0, 1) == '/') {
		$metaUrl = $serverPath . $metaUrl;
	}
}

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET; ?>" />
	<?php
	if ($imgArr) {
		?>
		<meta property="og:title" content="<?= $imgArr["sciname"]; ?>" />
		<meta property="og:site_name" content="<?= $DEFAULT_TITLE; ?>" />
		<meta property="og:image" content="<?= $metaUrl; ?>" />
		<?php
	}
	?>
	<title><?= $DEFAULT_TITLE . " Image Details: #" . $mediaID; ?></title>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/googleanalytics.php');
	?>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=1" type="text/javascript"></script>
	<script src="../js/symb/shared.js" type="text/javascript"></script>
	<script>
		$(document).ready(function() {
			const taxaInput = document.querySelector("#taxa");
			if(taxaInput){
				taxaInput.addEventListener("focus", (event) => {
					taxaSuggestConfig.clientRoot = clientRoot;
					initiateTaxaSuggest("taxa", function(result) {
						$("#targettid").val(result.item.id);
					});
				});
			}
		});

		function verifyEditForm(f) {
			if (f.originalUrl.value.replace(/\s/g, "") == "") {
				window.alert("<?= $LANG['ERROR_FILE_PATH'] ?>");
				return false;
			}
			return true;
		}

		function verifyChangeTaxonForm(f) {
			var sciName = f.targettaxon.value.replace(/^\s+|\s+$/g, "");
			if (sciName == "") {
				window.alert("<?= $LANG['ENTER_TAXON_NAME'] ?>");
			} else {
				validateTaxon(f, true, form => form.targettid.value = form.tid.value);
			}
			return false; //Submit takes place in the validateTaxon method
		}

		function openOccurrenceSearch(target) {
			occWindow = open("../collections/misc/occurrencesearch.php?targetid=" + target, "occsearch", "resizable=1,scrollbars=1,toolbar=0,width=750,height=750,left=400,top=40");
			if (occWindow.opener == null) occWindow.opener = self;
		}
	</script>
	<style type="text/css">
		body {
			min-width: 400px;
		}

		#imageedit {
			min-width: 800px;
			padding: 10px;
			background-color: #FFFFFF;
		}
	</style>
</head>

<body>
	<?php
	//$displayLeftMenu = (isset($taxa_imgdetailsMenu)?$taxa_imgdetailsMenu:false);
	//include($SERVER_ROOT.'/includes/header.php');
	?>
	<!--
	<div class="navpath">
		<a href="../index.php">Home</a> &gt;&gt;
		<a href="index.php">Image Browser</a> &gt;&gt;
		<a href="search.php">Image Search</a> &gt;&gt;
		<?php
		//if(isset($imgArr['tid']) && $imgArr['tid']) echo '<a href="../taxa/index.php?tid=' . $imgArr['tid'] . '">Image Search</a> &gt;&gt;';
		//echo '<b>Image Profile: image <a href="imgdetails.php?mediaid=' . $mediaID . '">#' . $mediaID . '</a></b>';
		?>
	</div>
	 -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['IMG_DETAILS']; ?></h1>
		<?php
		if ($imgArr) {
			?>
			<div style="width:100%;float:right;clear:both;margin-top:10px;">
				<?php
				if ($SYMB_UID && ($IS_ADMIN || array_key_exists("TaxonProfile", $USER_RIGHTS))) {
					?>
					<div style="float:right;margin-right:15px;" title="<?= $LANG['TAXON_PROFILE_EDITING'] ?>">
						<a href="../taxa/profile/tpeditor.php?tid=<?= htmlspecialchars($imgArr['tid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>&tabindex=1" target="_blank">
							<img src="../images/edit.png" style="width:1.3em;border:0px;" /><span style="font-size:70%"><?= $LANG['TP'] ?></span>
						</a>
					</div>
					<?php
				}
				if ($imgArr['occid']) {
					?>
					<div style="float:right;margin-right:15px;" title="<?= $LANG['EDITING_PRIVILEGES'] ?>">
						<a href="../collections/editor/occurrenceeditor.php?occid=<?= htmlspecialchars($imgArr['occid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>&tabtarget=2" target="_blank">
							<img src="../images/edit.png" style="width:1.3em;border:0px;" /><span style="font-size:70%"><?= $LANG['SPEC'] ?></span>
						</a>
					</div>
					<?php
				} else {
					if ($isEditor) {
						?>
						<div style="float:right;margin-right:15px;">
							<a href="#" onclick="toggle('imageedit');return false" title="<?= $LANG['EDIT_IMAGE'] ?>">
								<img src="../images/edit.png" style="width:1.3em;border:0px;" /><span style="font-size:70%"><?= $LANG['IMG'] ?></span>
							</a>
						</div>
						<?php
					}
				}
				?>
			</div>
			<?php
		}
		if ($status) {
			?>
			<hr />
			<div style="color:red;">
				<?= $status; ?>
			</div>
			<hr />
			<?php
		}
		if ($imgArr) {
			if ($isEditor && !$imgArr['occid']) {
				?>
				<div id="imageedit" style="display:<?= ($eMode ? 'block' : 'none'); ?>;">
					<form name="editform" action="imgdetails.php" method="post" target="_self" onsubmit="return verifyEditForm(this);">
						<fieldset style="margin:5px 0px 5px 5px;">
							<legend><b><?= $LANG['EDIT_IMAGE_DETAILS'] ?></b></legend>
							<div style="margin-top:2px;">
								<b><?= $LANG['CAPTION'] ?>:</b>
								<input name="caption" type="text" value="<?= $imgArr["caption"]; ?>" style="width:250px;" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['CREATOR_USER_ID'] ?>:</b>
								<select name="creatorUid" name="creatorUid">
									<option value=""><?= $LANG['SELECT_CREATOR'] ?></option>
									<option value="">---------------------------------------</option>
									<?= Media::renderCreatorOptions($imgArr['creatorUid']) ?>
								</select>
								* <?= $LANG['USER_REGISTERED_SYSTEM'] ?>
								<a href="#" onclick="toggle('iepor');return false;" title="<?= $LANG['DISPLAY_CREATOR_FIELD'] ?>">
									<img src="../images/editplus.png" style="border:0px;width:1.5em;" />
								</a>
							</div>
							<div id="iepor" style="margin-top:2px;display:<?= ($imgArr["creator"] ? 'block' : 'none'); ?>;">
								<b><?= $LANG['CREATOR_OVERRIDE'] ?>:</b>
								<input name="creator" type="text" value="<?= $imgArr["creator"]; ?>" style="width:250px;" />
								* <?= $LANG['OVERRIDE_SELECTION'] ?>
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['MANAGER'] ?>:</b>
								<input name="owner" type="text" value="<?= $imgArr["owner"]; ?>" style="width:250px;" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['SOURCE_URL'] ?>:</b>
								<input name="sourceUrl" type="text" value="<?= $imgArr["sourceUrl"]; ?>" style="width:450px;" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['COPYRIGHT'] ?>:</b>
								<input name="copyright" type="text" value="<?= $imgArr["copyright"]; ?>" style="width:450px;" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['RIGHTS'] ?>:</b>
								<input name="rights" type="text" value="<?= $imgArr["rights"]; ?>" style="width:450px;" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['LOCALITY'] ?>:</b>
								<input name="locality" type="text" value="<?= $imgArr["locality"]; ?>" style="width:550px;" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['OCCURRENCE_RECORD'] ?> #:</b>
								<input id="imgdisplay-<?= $mediaID; ?>" name="displayoccid" type="text" value="" disabled style="width:70px" />
								<input id="imgoccid-<?= $mediaID; ?>" name="occid" type="hidden" value="" />
								<span onclick="openOccurrenceSearch('<?= $mediaID; ?>');return false"><a href="#"><?= $LANG['LINK_OCCUR_RECORD'] ?></a></span>
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['NOTES'] ?>:</b>
								<input name="notes" type="text" value="<?= $imgArr["notes"]; ?>" style="width:550px;" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['SORT_SEQUENCE'] ?>:</b>
								<input name="sortSequence" type="text" value="<?= $imgArr["sortSequence"] ?? ''; ?>" size="5" />
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['LARGE_IMAGE'] ?>:</b><br />
								<input name="originalUrl" type="text" value="<?= $imgArr["originalUrl"]; ?>" style="width:90%;" />
								<?php
								if ($imgArr["originalUrl"] && stripos($imgArr["originalUrl"], $MEDIA_ROOT_URL) === 0) {
									?>
									<div style="margin-left:80px;">
										<input type="checkbox" name="renameorigurl" value="1" />
										<?= $LANG['RENAME_LARGE_IMAGE_FILE'] ?>
									</div>
									<input name="old_originalurl" type="hidden" value="<?= $imgArr["originalUrl"]; ?>" />
									<?php
								}
								?>
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['THUMBNAIL'] ?>:</b><br />
								<input name="thumbnailUrl" type="text" value="<?= $imgArr["thumbnailUrl"]; ?>" style="width:90%;" />
								<?php
								if ($imgArr["thumbnailUrl"] && stripos($imgArr["thumbnailUrl"], $MEDIA_ROOT_URL) === 0) {
									?>
									<div style="margin-left:70px;">
										<input type="checkbox" name="renametnurl" value="1" />
										<?= $LANG['RENAME_THUMBNAIL_IMAGE_FILE'] ?>
									</div>
									<input name="old_thumbnailurl" type="hidden" value="<?= $imgArr["thumbnailUrl"]; ?>" />
									<?php
								}
								?>
							</div>
							<div style="margin-top:2px;">
								<b><?= $LANG['WEB_IMAGE'] ?>:</b><br />
								<input name="url" type="text" value="<?= $imgArr["url"]; ?>" style="width:90%;" />
								<?php
								if ($imgArr["url"] && stripos($imgArr["url"], $MEDIA_ROOT_URL) === 0) {
									?>
									<div style="margin-left:70px;">
										<input type="checkbox" name="renameweburl" value="1" />
										<?= $LANG['RENAME_WEB_IMAGE_FILE'] ?>
									</div>
									<input name="old_url" type="hidden" value="<?= $imgArr["url"]; ?>" />
									<?php
								}
								?>
							</div>
							<input name="mediaid" type="hidden" value="<?= $mediaID; ?>" />
							<div style="margin-top:2px;">
								<button type="submit" name="submitaction" id="editsubmit" value="Submit Image Edits"><?= $LANG['SUBMIT_IMAGE_EDITS'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="changetaxonform" action="imgdetails.php" method="post" target="_self" onsubmit="return verifyChangeTaxonForm(this);">
						<fieldset style="margin:5px 0px 5px 5px;">
							<legend><b><?= $LANG['TRANSFER_IMAGE_TO_DIFF_NAME'] ?></b></legend>
							<div style="font-weight:bold;">
								<?= $LANG['TRANSFER_TO_TAXON'] ?>:
								<input type="text" id="taxa" name="targettaxon" size="40" />
								<input type="hidden" id="tid" name="targettid" value="" />
								<input type="hidden" name="sourcetid" value="<?= $imgArr["tid"]; ?>" />
								<input type="hidden" name="mediaid" value="<?= $mediaID; ?>" />

								<input type="hidden" name="submitaction" value="Transfer Image" />
								<button type="submit" name="submitaction" value="Transfer Image"><?= $LANG['TRANSFER_IMAGE'] ?></button>
							</div>
						</fieldset>
					</form>
					<form name="deleteform" action="imgdetails.php" method="post" target="_self" onsubmit="return window.confirm('<?= $LANG['DELETE_IMAGE_FROM_SERVER'] ?>');">
						<fieldset style="margin:5px 0px 5px 5px;">
							<legend><b><?= $LANG['AUTHORIZED_REMOVE_IMAGE'] ?></b></legend>
							<input name="mediaid" type="hidden" value="<?= $mediaID; ?>" />
							<div style="margin-top:2px;">
								<button class="button-danger" type="submit" name="submitaction" id="submit" value="Delete Image"><?= $LANG['DELETE_IMAGE'] ?></button>
							</div>
							<input type="hidden" name="tid" value="<?= $imgArr["tid"]; ?>" />
							<input name="removeimg" type="checkbox" value="1" /> <?= $LANG['REMOVE_IMG_FROM_SERVER'] ?>
							<div style="margin-left:20px;color:red;">
								<?= $LANG['BOX_CHECKED_IMG_DELETED'] ?>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			?>
			<div>
				<div style="width:350px;padding:10px;float:left;">
					<?php
					$imgDisplay = $imgUrl;
					$mediaType = MediaType::tryFrom($imgArr['mediaType']);
					if ((!$imgDisplay || $imgDisplay == 'empty') && $origUrl) $imgDisplay = $origUrl;
					?>
					<?php if ($mediaType === MediaType::Image): ?>
						<a href="<?= htmlspecialchars($imgDisplay, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>">
							<img src="<?= htmlspecialchars($imgDisplay, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" style="width:300px;" />
						</a>
						<?php
						if ($origUrl) echo '<div><a href="' . htmlspecialchars($origUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . $LANG['CLICK_IMAGE'] . '</a></div>';
						?>
					<?php elseif ($mediaType === MediaType::Audio): ?>
						<audio controls style="margin-top: 5rem">
							<source src="<?= $origUrl ?>" type="<?= $imgArr['format'] ?>">
							Your browser does not support the audio element.
						</audio>
					<?php endif ?>
				</div>
				<div style="padding:10px;float:left;">
					<div style="clear:both;margin-top:40px;">
						<b><?= $LANG['SCIENTIFIC_NAME'] ?>:</b> <?= '<a href="../taxa/index.php?taxon=' . htmlspecialchars($imgArr["tid"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '"><i>' . htmlspecialchars($imgArr["sciname"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</i> ' . htmlspecialchars($imgArr["author"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>'; ?>
					</div>
					<?php
					if ($imgArr['caption']) echo '<div><b>' . $LANG['CAPTION'] . ':</b> ' . $imgArr['caption'] . '</div>';
					if ($imgArr['creatorDisplay']) {
						echo '<div><b>' . $LANG['CREATOR'] . ':</b> ';
						if (!$imgArr['creator']) {
							$phLink = 'search.php?imagetype=all&phuid=' . $imgArr['creatorUid'] . '&submitaction=search';
							echo '<a href="' . htmlspecialchars($phLink, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">';
						}
						echo $imgArr['creatorDisplay'];
						if (!$imgArr['creator']) echo '</a>';
						echo '</div>';
					}
					if ($imgArr['owner']) echo '<div><b>' . $LANG['MANAGER'] . ':</b> ' . $imgArr['owner'] . '</div>';
					if ($imgArr['sourceUrl']) echo '<div><b>' . $LANG['IMAGE_SOURCE'] . ':</b> <a href="' . htmlspecialchars($imgArr['sourceUrl'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank">' . htmlspecialchars($imgArr['sourceUrl'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a></div>';
					if ($imgArr['locality']) echo '<div><b>' . $LANG['LOCALITY'] . ':</b> ' . $imgArr['locality'] . '</div>';
					if ($imgArr['notes']) echo '<div><b>' . $LANG['NOTES'] . ':</b> ' . $imgArr['notes'] . '</div>';
					if ($imgArr['rights']) echo '<div><b>' . $LANG['RIGHTS'] . ':</b> ' . $imgArr['rights'] . '</div>';
					if ($imgArr['copyright']) {
						echo '<div>';
						echo '<b>' . $LANG['COPYRIGHT'] . ':</b> ';
						if (stripos($imgArr['copyright'], 'http') === 0) echo '<a href="' . htmlspecialchars($imgArr['copyright'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . htmlspecialchars($imgArr['copyright'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>';
						else echo $imgArr['copyright'];
						echo '</div>';
					} else {
						echo '<div><a href="../includes/usagepolicy.php#images">' . $LANG['COPYRIGHT_DETAILS'] . '</a></div>';
					}
					if ($imgArr['occid']) echo '<div><a href="../collections/individual/index.php?occid=' . htmlspecialchars($imgArr['occid'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . $LANG['DISPLAY_SPECIMEN_DETAILS'] . '</a></div>';
					if ($imgUrl) echo '<div><a href="' . htmlspecialchars($imgUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . $LANG['OPEN_MEDIUM_SIZED_IMAGE'] . '</a></div>';
					if ($origUrl) echo '<div><a href="' . htmlspecialchars($origUrl, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . $LANG['OPEN_LARGE_IMAGE'] . '</a></div>';
					$emailAddress = $ADMIN_EMAIL;
					if ($emailAddress) {
						?>
						<div style="margin-top:20px;">
							<?= $LANG['ERROR_COMMENT_ABOUT_IMAGE'] ?> <br /><?= $LANG['SEND_EMAIL'] ?>:
							<?php
							$emailSubject = $DEFAULT_TITLE . ' ' . $LANG['IMG_NO'] . ' ' . $mediaID;
							$emailBody = 'Image being referenced: ' . urlencode($serverPath . $CLIENT_ROOT . '/imagelib/imgdetails.php?mediaid=' . $mediaID);
							$emailRef = 'subject=' . $emailSubject . '&cc=' . $ADMIN_EMAIL . '&body=' . $emailBody;
							echo '<a href="mailto:' . $ADMIN_EMAIL . '?' . $emailRef . '">' . $emailAddress . '</a>';
							?>
						</div>
						<?php
					}
					?>
				</div>
				<div style="clear:both;"></div>
			</div>
			<?php
		} else {
			echo '<h2 style="margin:30px;">' . $LANG['UNABLE_TO_LOCATE'] . '</h2>';
		}
		?>
	</div>
	<?php
	//include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>

</html>
