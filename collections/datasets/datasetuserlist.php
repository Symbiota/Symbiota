<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/datasets/datasetuserlist');

header("Content-Type: text/html; charset=" . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/datasets/datasetuserlist.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

$datasetManager = new OccurrenceDataset();

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
		<title><?= $DEFAULT_TITLE; ?> <?= $LANG['OCC_DAT_MNG'] ?> </title>
		<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../js/symb/shared.js"></script>
		<script type="text/javascript" src="../../js/tinymce/tinymce.min.js"></script>
		<script type="text/javascript">
			// Adds WYSIWYG editor to description field
			tinymce.init({
				selector: '#description',
				plugins: 'link lists image',
				menubar: '',
				toolbar: ['undo redo | bold italic underline | link | alignleft aligncenter alignright | formatselect | bullist numlist | indent outdent | blockquote | image'],
				branding: false,
        		default_link_target: "_blank",
				paste_as_text: true,
				setup: function (editor) {
					editor.on('init', function () {
					editor.iframeElement.setAttribute(
						'aria-label',
						'Description (Displayed publicly)'
					);
				});
			}
			});
		</script>
		<script type="text/javascript">
			function validateAddForm(f){
				if(f.adduser.value == ""){
					alert("Enter a user (login or last name)");
					return false
				}
				if(f.adduser.value.indexOf(" [#") == -1){
					$.ajax({
						url: "../rpc/getuserlist.php",
						dataType: "json",
						data: {
							term: f.adduser.value
						},
						success: function(data) {
							if(data && data != ""){
								f.adduser.value = data;
								alert("Located login: "+data);
								f.submit();
							}
							else{
								alert("Unable to locate user");
							}
						}
					});
					return false;
				}
				return true;
			}
		</script>
		<style>
			fieldset{ padding:15px;margin:15px; }
			legend{ font-weight: bold; }
			.dataset-item{ margin-bottom: 10px }
			.img-icon{ width: 1.1em }
		</style>
	</head>
	<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="index.php"><?= $LANG['PUB_DAT_LIST'] ?></a> &gt;&gt;
		<b><?= $LANG['USER_LIST'] ?></b>
	</div>
	<div role="main" id="innertext">
		<?php
		$dataSetArr = $datasetManager->getDatasetsByUser();
		?>
		<div>
		<div style="float:right;margin:10px;" title="<?= $LANG['CRT_NEW_DAT'] ?>" >
	 		<a href="#" onclick="toggle('adddiv');return false;"><img src="../../images/add.png" class="img-icon" alt="<?= $LANG['ADD_BUTTON'] ?>"/></a>
		</div>
		<h1 class="page-heading"> <?= $LANG['OCC_DAT_MNG'] ?> </h1>
		<div> <?= $LANG['TOOL_DESCR'] ?> </div>
		<div id=adddiv style="display:none">
			<fieldset>
				<legend><b> <?= $LANG['CRT_NEW_DAT'] ?> </b></legend>
				<form name="adminform" action="datasetmanager.php" method="post">
					<div>
						<label for="datasetName"><?= $LANG['NAME'] ?></label>
						<input id="datasetName" name="datasetName" type="text" style="width:90%" required />
					</div>
					<div>
						<span>
							<input type="radio" name="isPublic" id="isPrivate" value="0" aria-label="<?= $LANG['PRIVATE'] ?>" />
							<label for="isPrivate"><?= $LANG['PRIVATE'] ?></label>
						</span>
						<span>
							<input type="radio" name="isPublic" id="isPublic" value="1" aria-label="<?= $LANG['PUB_VISIBLE'] ?>" />
							<label for="isPublic"><?= $LANG['PUB_VISIBLE'] ?></label>
						</span>
					</div>
					<div>
						<label for="description"><?= $LANG['DESCRIPTION'] ?></label>
						<textarea name="description" id="description" cols="100" rows="10"></textarea>
					</div>
					<div>
						<label for="bibliographicCitation"><?= $LANG['CITATION'] ?></label>
						<input name="bibliographicCitation" id="bibliographicCitation" type="text" value="" style="width:100%" aria-label="<?= $LANG['CITATION'] ?>" />
					</div>
					<div>
						<label for="category"><?= $LANG['CATEGORY'] ?></label>
						<input name="category" id="category" type="text" style="width:350px" aria-label="<?= $LANG['CATEGORY'] ?>" />
					</div>
					<div>
						<label for="datasetIdentifier"><?= $LANG['DATASET_IDENTIFIER'] ?></label>
						<input name="datasetIdentifier" id="dynamicProperties" type="text" style="width:350px" aria-label="<?= $LANG['DATASET_IDENTIFIER'] ?>" />
					</div>
					<div>
						<label for="notes"><?= $LANG['NOTES_INTERNAL'] ?></label>
						<input id="notes" name="notes" type="text" style="width:90%;" />
					</div>
					<div style="margin:15px">
						<button name="submitaction" type="submit" value="createNewDataset"> <?= $LANG['CRT_NEW_DAT'] ?> </button>
					</div>
				</form>
			</fieldset>
		</div>
		<?php
		if($dataSetArr){
			?>
			<fieldset>
				<legend><b> <?= $LANG['OWNED'] ?> </b></legend>
				<?php
				$ownedByExists = false;
				foreach($dataSetArr as $dsid => $dsArr){
					if(array_key_exists('owner', $dsArr['role'])){
						$ownedByExists = true;
						?>
						<div class="dataset-item">
							<div>
								<a href="index.php?datasetid=<?= $dsid; ?>" title="<?= $LANG['MNG_EDIT'] ?>">
									<?php
									echo '<b>' . $dsArr['datasetName'] . ' (#' . $dsid . ')</b>';
									?>
								</a>
								<span style="margin-left: 5px"><a href="datasetmanager.php?datasetid=<?= $dsid ?>"><img src="../../images/edit.png" class="img-icon"></a></span>
							</div>
							<div style="margin-left:15px;">
								<?php
								echo ($dsArr['notes'] ? '<div>' . $dsArr['notes'] . '</div>' : '');
								echo '<div>Created: ' . $dsArr['initialTimestamp'] . '</div>';
								?>
							</div>
						</div>
						<?php
					}
				}
				if(!$ownedByExists){
					echo '<div style="font-weight:bold;">' . $LANG['NO_OWNED'] . '</div>';
				}
				?>
			</fieldset>
			<fieldset>
				<legend><?= $LANG['SHARED'] ?> </legend>
				<?php
				$sharedByExists = false;
				foreach($dataSetArr as $dsid => $dsArr){
					if(!array_key_exists('owner', $dsArr['role'])){
						$sharedByExists = true;
						?>
						<div class="dataset-item">
							<div>
								<a href="index.php?datasetid=<?= $dsid ?>" title="Access Dataset"><?= '<b>' . $dsArr['datasetName'] . ' (#' . $dsid . ')</b>' ?></a>
								<?php
								echo ' - ' . implode(', ', $dsArr['role']);
								if(count($dsArr['role']) > 1 || !array_key_exists('DatasetReader', $dsArr['role'])){
									?>
									<span style="margin-left: 5px"><a href="datasetmanager.php?datasetid=<?= $dsid ?>"><img src="../../images/edit.png" class="img-icon"></a></span>
									<?php
								}
								?>
							</div>
							<div style="margin-left:15px;">
								<?php
								echo ($dsArr['notes'] ? $dsArr['notes'] . '<br/>' : '');
								echo 'Created: ' . $dsArr['initialTimestamp'];
								?>
							</div>
						</div>
						<?php
					}
				}
				if(!$sharedByExists){
					echo '<div style="font-weight:bold;">' . $LANG['NO_SHARED'] . '</div>';
				}
				?>
			</fieldset>
			<?php
		}
		else{
			?>
			<div style="margin:20px">
				<div style="font-weight:bold"> <?= $LANG['NO_LOGIN'] ?> </div>
				<div style="margin-top:15px"><a href="#" onclick="toggle('adddiv');"> <?= $LANG['CRT_NEW_DAT'] ?> </a></div>
			</div>
			<?php
		}
		?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
	</body>
</html>
