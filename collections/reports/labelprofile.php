<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');

header('Content-Type: text/html; charset='.$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/reports/labelprofile.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$isEditor = 0;
if($SYMB_UID) $isEditor = 1;
if($collid && array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])) $isEditor = 2;
if($IS_ADMIN) $isEditor = 3;
$statusStr = '';
if($isEditor && $action){
	if($action == 'cloneProfile'){
		if(isset($_POST['cloneTarget']) && $_POST['cloneTarget']){
			if(!$labelManager->cloneLabelJson($_POST)){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
		else $statusStr = 'ERROR: you must select a clone target!';
	}
	$applyEdits = true;
	$group = (isset($_POST['group'])?$_POST['group']:'');
	if($group == 'g' && $isEditor < 3) $applyEdits = false;
	if($group == 'c' && $isEditor < 2) $applyEdits = false;
	if($applyEdits){
		if($action == 'saveProfile'){
			if(!$labelManager->saveLabelJson($_POST)){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
		elseif($action == 'deleteProfile'){
			if(!$labelManager->deleteLabelFormat($_POST['group'],$_POST['index'])){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
	}
}
$isGeneralObservation = (($labelManager->getMetaDataTerm('colltype') == 'General Observations')?true:false);
?>
<!DOCTYPE HTML>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
		<title><?= $DEFAULT_TITLE ?> Specimen Label Manager</title>
		<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			var activeProfileCode = "";

			function toggleEditDiv(classTag){
				$('#display-'+classTag).toggle();
				$('#edit-'+classTag).toggle();
			}

			function makeJsonEditable(classTag){
				alert("You should now be able to edit the JSON label definition. Feel free to modify, but note that editing the raw JSON requires knowledge of the JSON format. A simple error may cause label generation to completely fail and your changes to be lost. We recommend creating and editing your JSON in a separate text file, then pasting it into the field below to see if it works.");
				$('#json-'+classTag).prop('readonly', false);
				activeProfileCode = classTag;
			}

			function setJson(json){
				$('#json-'+activeProfileCode).val(json);
			}

			function verifyClone(f){
				if(f.cloneTarget.value == ""){
					alert("Select a clone target!");
					return false;
				}
				return true;
			}

			/**
			* Adds current profile JSON to visual interface
			*/
			function openJsonEditorPopup(classTag){
				activeProfileCode = classTag;
				let editorWindow = window.open('labeljsongui.php','scrollbars=1,toolbar=0,resizable=1,width=1000,height=700,left=20,top=20');
				(editorWindow.opener == null) ? editorWindow.opener = self : '';
				let formatId = "#json-"+classTag;
				let currJson = $("#json-"+classTag).val();
				editorWindow.focus();
				editorWindow.onload = function(){
					let dummy = editorWindow.document.getElementById("dummy");
					dummy.value = currJson;
					dummy.dataset.formatId = formatId;
					editorWindow.loadJson();
				}
			}
		</script>
		<style>
			fieldset{ width:800px; padding:15px; }
			fieldset legend{ font-weight:bold; }
			input[type=text]{ width: 500px; }
			hr{ margin:15px 0px; }
			.custom-styles-textarea{ width: 600px; height: 50px; }
			.json-textarea{ width: 800px; height: 150px; }
			.fieldset-block{ width:700px }
			.field-block{ margin:3px 0px }
			.label{ font-weight: bold; }
			.label-inline{ font-weight: bold; }
			.field-value{  }
			.field-inline{  }
			.edit-icon{ width: 1.1em; }
			#preview-label{ border: 1px solid gray; min-height: 100px; padding: 0.5em; }
			#preview-label.field-block{ line-height: 1.1rem; }
			#preview-label>.field-block>div{ display: inline; }
		</style>
	</head>
	<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<?php
		if($isGeneralObservation) echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
		elseif($collid){
			echo '<a href="../misc/collprofiles.php?collid=' . $collid . '&emode=1">Collection Management Panel</a> &gt;&gt; ';
		}
		?>
		<a href="labelmanager.php?collid=<?= $collid; ?>&emode=1">Label Manager</a> &gt;&gt;
		<b>Label Profile Editor</b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading">Specimen Label Manager</h1>
		<div style="width:700px"><span style="color:orange;font-weight:bold;">
			In development!</span> We are currently working on developing a new system that will allow collection managers and general users to create their own custom label formats
			that can be saved within the collection and user profiles. We are trying our best to develop these tools with minimum disruptions to normal label printing.
			More details to provided in the near future.
		</div>
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:15px;color:red;">
				<?= $statusStr; ?>
			</div>
			<?php
		}
		$labelFormatArr = $labelManager->getLabelFormatArr();
		foreach($labelFormatArr as $group => $groupArr){
			$fieldsetTitle = '';
			if($group == 'g') $fieldsetTitle = 'Portal Profiles ';
			elseif($group == 'c') $fieldsetTitle = $labelManager->getCollName().' Label Profiles ';
			elseif($group == 'u') $fieldsetTitle = 'User Profiles ';
			$fieldsetTitle .= '('.count($groupArr).' formats)';
			?>
			<fieldset>
				<legend><?= $fieldsetTitle; ?></legend>
				<?php
				if($isEditor == 3 || $group == 'u' || ($group == 'c' && $isEditor > 1))
					echo '<div style="float:right" title="Create a new label profile"><img class="edit-icon" src="../../images/add.png" onclick="$(\'#edit-'.$group.'\').toggle()" /></div>';
				$index = '';
				$formatArr = array();
				do{
					$midText = '';
					$labelType = 2;
					$pageSize = '';
					if($formatArr){
						if($index) echo '<hr/>';
						?>
						<div id="display-<?= $group.'-'.$index; ?>">
							<div class="field-block">
								<span class="label">Title:</span>
								<span class="field-value"><?= htmlspecialchars($formatArr['title']); ?></span>
								<?php
								if($isEditor == 3 || $group == 'u' || ($group == 'c' && $isEditor > 1))
									echo '<span title="Edit label profile"> <a href="#" onclick="toggleEditDiv(\''.$group.'-'.$index.'\');return false;"><img class="edit-icon" src="../../images/edit.png" ></a></span>';
								?>
							</div>
							<?php
							if(isset($formatArr['labelHeader']['midText'])) $midText = $formatArr['labelHeader']['midText'];
							$headerStr = $formatArr['labelHeader']['prefix'].' ';
							if($midText==1) $headerStr .= '[COUNTRY];';
							elseif($midText==2) $headerStr .= '[STATE]';
							elseif($midText==3) $headerStr .= '[COUNTY]';
							elseif($midText==4) $headerStr .= '[FAMILY]';
							$headerStr .= ' '.$formatArr['labelHeader']['suffix'];
							if(trim($headerStr)){
								?>
								<div class="field-block">
									<span class="label">Header: </span>
									<span class="field-value"><?= htmlspecialchars(trim($headerStr)); ?></span>
								</div>
								<?php
							}
							if($formatArr['labelFooter']['textValue']){
								?>
								<div class="field-block">
									<span class="label">Footer: </span>
									<span class="field-value"><?= htmlspecialchars($formatArr['labelFooter']['textValue']); ?></span>
								</div>
								<?php
							}
							if($formatArr['labelType']){
								$labelType = $formatArr['labelType'];
								?>
								<div class="field-block">
									<span class="label">Type: </span>
									<span class="field-value"><?= $labelType.(is_numeric($labelType)?' column per page':''); ?></span>
								</div>
								<?php
							}
							if($formatArr['pageSize']){
								$pageSize = $formatArr['pageSize'];
								?>
								<div class="field-block">
									<span class="label">Page size: </span>
									<span class="field-value"><?= $pageSize; ?></span>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
					<form name="labelprofileeditor-<?= $group.(is_numeric($index)?'-'.$index:''); ?>" action="labelprofile.php" method="post" onsubmit="return validateJsonForm(this)">
						<div id="edit-<?= $group.(is_numeric($index)?'-'.$index:''); ?>" style="display:none">
							<div class="field-block">
								<span class="label">Title:</span>
								<span class="field-elem"><input name="title" type="text" value="<?= ($formatArr?htmlspecialchars($formatArr['title']):''); ?>" required /> </span>
								<?php
								if($formatArr) echo '<span title="Edit label profile"> <img class="edit-icon" src="../../images/edit.png" onclick="toggleEditDiv(\''.$group.'-'.$index.'\')" /></span>';
								?>
							</div>
							<fieldset class="fieldset-block">
								<legend>Label Header</legend>
								<div class="field-block">
									<span class="label">Prefix:</span>
									<span class="field-elem">
										<input name="hPrefix" type="text" value="<?= (isset($formatArr['labelHeader']['prefix'])?htmlspecialchars($formatArr['labelHeader']['prefix']):''); ?>" />
									</span>
								</div>
								<div class="field-block">
									<div class="field-elem">
										<span class="field-inline">
											<input name="hMidText" type="radio" value="1" <?= ($midText==1?'checked':''); ?> />
											<span class="label-inline">Country</span>
										</span>
										<span class="field-inline">
											<input name="hMidText" type="radio" value="2" <?= ($midText==2?'checked':''); ?> />
											<span class="label-inline">State</span>
										</span>
										<span class="field-inline">
											<input name="hMidText" type="radio" value="3" <?= ($midText==3?'checked':''); ?> />
											<span class="label-inline">County</span>
										</span>
										<span class="field-inline">
											<input name="hMidText" type="radio" value="4" <?= ($midText==4?'checked':''); ?> />
											<span class="label-inline">Family</span>
										</span>
										<span class="field-inline">
											<input name="hMidText" type="radio" value="0" <?= (!$midText?'checked':''); ?> />
											<span class="label-inline">Blank</span>
										</span>
									</div>
								</div>
								<div class="field-block">
									<span class="label">Suffix:</span>
									<span class="field-elem"><input name="hSuffix" type="text" value="<?= ($formatArr?htmlspecialchars($formatArr['labelHeader']['suffix']):''); ?>" /></span>
								</div>
								<div class="field-block">
									<span class="label">Class names:</span>
									<span class="field-elem"><input name="hClassName" type="text" value="<?= ($formatArr?htmlspecialchars($formatArr['labelHeader']['className']):''); ?>" /></span>
								</div>
								<div class="field-block">
									<span class="label">Style:</span>
									<span class="field-elem"><input name="hStyle" type="text" value="<?= ($formatArr?htmlspecialchars($formatArr['labelHeader']['style']):''); ?>" /></span>
								</div>
							</fieldset>
							<fieldset  class="fieldset-block">
								<legend>Label Footer</legend>
								<div class="field-block">
									<span class="label-inline">Footer text:</span>
									<input name="fTextValue" type="text" value="<?= (isset($formatArr['labelFooter']['textValue'])?htmlspecialchars($formatArr['labelFooter']['textValue']):''); ?>" />
								</div>
								<div class="field-block">
									<span class="label-inline">Class names:</span>
									<input name="fClassName" type="text" value="<?= (isset($formatArr['labelFooter']['className'])?$formatArr['labelFooter']['className']:''); ?>" />
								</div>
								<div class="field-block">
									<span class="label-inline">Style:</span>
									<input name="fStyle" type="text" value="<?= (isset($formatArr['labelFooter']['style'])?$formatArr['labelFooter']['style']:''); ?>" />
								</div>
							</fieldset>
							<div class="field-block">
								<div class="label">Custom Styles:</div>
								<div class="field-block">
									<textarea name="customStyles" class="custom-styles-textarea"><?= (isset($formatArr['customStyles'])?$formatArr['customStyles']:''); ?></textarea>
								</div>
							</div>
							<div class="field-block">
								<div class="label">Default CSS:</div>
								<div class="field-block">
									<input name="defaultCss" type="text" value="<?= (isset($formatArr['defaultCss']) ? $formatArr['defaultCss'] : $CSS_BASE_PATH . '/symbiota/collections/reports/labelhelpers.css'); ?>" />
								</div>
							</div>
							<div class="field-block">
								<div class="label">Custom CSS:</div>
								<div class="field-block">
									<input name="customCss" type="text" value="<?= (isset($formatArr['customCss'])?$formatArr['customCss']:''); ?>" />
								</div>
							</div>
							<div class="field-block">
								<div class="label">Custom JS:</div>
								<div class="field-block">
									<input name="customJS" type="text" value="<?= (isset($formatArr['customJS'])?$formatArr['customJS']:''); ?>" />
								</div>
							</div>
							<fieldset class="fieldset-block">
								<legend>Options</legend>
								<div class="field-block">
									<span class="label-inline">Label type:</span>
									<select name="labelType">
										<option value="1" <?= ($labelType==1?'selected':''); ?>>1 columns per page</option>
										<option value="2" <?= ($labelType==2?'selected':''); ?>>2 columns per page</option>
										<option value="3" <?= ($labelType==3?'selected':''); ?>>3 columns per page</option>
										<option value="4" <?= ($labelType==4?'selected':''); ?>>4 columns per page</option>
										<option value="5" <?= ($labelType==5?'selected':''); ?>>5 columns per page</option>
										<option value="6" <?= ($labelType==6?'selected':''); ?>>6 columns per page</option>
										<option value="7" <?= ($labelType==7?'selected':''); ?>>7 columns per page</option>
										<option value="packet" <?= ($labelType=='packet'?'selected':''); ?>>Packet labels</option>
									</select>
								</div>
								<div class="field-block">
									<span class="label-inline">Page size:</span>
									<select name="pageSize">
										<option value="letter">Letter</option>
										<option value="a4" <?= ($pageSize=='a4'?'SELECTED':''); ?>>A4</option>
										<option value="legal" <?= ($pageSize=='legal'?'SELECTED':''); ?>>Legal</option>
										<option value="tabloid" <?= ($pageSize=='tabloid'?'SELECTED':''); ?>>Ledger/Tabloid</option>
									</select>
								</div>
								<div class="field-block">
									<input name="displaySpeciesAuthor" type="checkbox" value="1" <?= (isset($formatArr['displaySpeciesAuthor'])&&$formatArr['displaySpeciesAuthor']?'checked':''); ?> />
									<span class="label-inline">Display species for infraspecific taxa</span>
								</div>
								<div class="field-block">
									<input name="displayBarcode" type="checkbox" value=1" <?= (isset($formatArr['displayBarcode'])&&$formatArr['displayBarcode']?'checked':''); ?> />
									<span class="label-inline">Display barcode</span>
								</div>
							</fieldset>
							<div class="field-block">
								<div class="label">JSON:
									<span title="Edit JSON label definition">
										<a href="#" onclick="makeJsonEditable('<?= $group.(is_numeric($index)?'-'.$index:''); ?>');return false"><img class="edit-icon" src="../../images/edit.png" ></a>
										<a href="#" onclick="makeJsonEditable('<?= $group.(is_numeric($index)?'-'.$index:''); ?>');return false">(edit via text interface)</a>
									</span>
									<span title="Edit JSON label definition (Visual Interface)">
										<a href="#" onclick="openJsonEditorPopup('<?= $group.(is_numeric($index)?'-'.$index:''); ?>');return false"><img class="edit-icon" src="../../images/editsquare.png" >(edit via visual interface)</a>
									</span>
								</div>
								<div class="field-block">
									<textarea id="json-<?= $group.(is_numeric($index)?'-'.$index:''); ?>" name="json" class="json-textarea" readonly><?= (isset($formatArr['labelBlocks'])?json_encode($formatArr['labelBlocks'],JSON_PRETTY_PRINT):''); ?></textarea>
								</div>
							</div>
							<div style="margin-left:20px;">
								<input type="hidden" name="collid" value="<?= $collid; ?>" />
								<input type="hidden" name="group" value="<?= $group; ?>" />
								<input type="hidden" name="index" value="<?= $index; ?>" />
								<?php
								if($isEditor == 3 || $group == 'u' || ($group == 'c' && $isEditor > 1))
									echo '<span><button name="submitaction" type="submit" value="saveProfile">'.(is_numeric($index)?'Save Label Profile':'Create New Label Profile').'</button></span>';
								if(is_numeric($index)){
									if($isEditor == 3 || $group == 'u' || ($group == 'c' && $isEditor > 1))
										echo '<span style="margin-left:15px"><button name="submitaction" type="submit" value="deleteProfile" onclick="return confirm(\'Are you sure you want to delete this profile?\')">Delete Profile</button></span>';
									?>
									<?php
								}
								?>
							</div>
							<?php if(!is_numeric($index)) echo '<hr/>'; ?>
						</div>
						<?php
						if(is_numeric($index)){
							?>
							<div style="margin:5px;">
								<span style="margin-left:15px"><button name="submitaction" type="submit" value="cloneProfile" onclick="return verifyClone(this.form)">Clone Profile</button></span> to
								<select name="cloneTarget">
									<option value="">Select Target</option>
									<option value="">----------------</option>
									<?php
									if($isEditor == 3) echo '<option value="g">Portal Global Profile</option>';
									if($isEditor > 1) echo '<option value="c">Collection Profile</option>';
									?>
									<option value="u">User Profile</option>
								</select>
							</div>
							<?php
						}
						?>
					</form>
					<?php
					if($groupArr){
						$index = key($groupArr);
						if(is_numeric($index)){
							$formatArr = $groupArr[$index];
							next($groupArr);
						}
					}
				} while(is_numeric($index));
				if(!$formatArr){
					echo '<div>No label profile yet defined. ';
					if($isEditor == 3 || $group == 'u' || ($group == 'c' && $isEditor > 1)) echo 'Click green plus sign to right to create a new profile';
					echo '</div>';
				}
				?>
			</fieldset>
			<?php
		}
		if(!$labelFormatArr) echo '<div>You are not authorized to manage any label profiles. Contact portal administrator for more details.</div>';
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
	</body>
</html>