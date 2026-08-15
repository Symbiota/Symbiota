<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/KeyCharAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('ident/admin/index');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ident/admin/index.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$langId = array_key_exists('langid',$_REQUEST) ? $_REQUEST['langid'] : '';

$charManager = new KeyCharAdmin();
$charManager->setLangId($langId);

$charArr = $charManager->getCharacterArr();
$headingArr = $charManager->getHeadingArr();

$isEditor = false;
if($IS_ADMIN || array_key_exists("KeyAdmin",$USER_RIGHTS)){
	$isEditor = true;
}

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
	<title> <?= $LANG['TAXON_CHARACTERS']; ?> </title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/symb/shared.js"></script>
	<script type="text/javascript">
		function validateNewCharForm(f){
			if(f.charname.value == ""){
				alert("<?= $LANG['ALERT_NAME'] ?>");
				return false;
			}
			if(f.chartype.value == ""){
				alert("<?= $LANG['ALERT_TYPE'] ?>");
				return false;
			}
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert("<?= $LANG['ALERT_SORT'] ?>");
				return false;
			}
			return true;
		}

		function openHeadingAdmin(){
			newWindow = window.open("headingadmin.php","headingWin","scrollbars=1,toolbar=0,resizable=1,width=800,height=600,left=50,top=50");
			if (newWindow.opener == null) newWindow.opener = self;
		}

	</script>
	<style>
		.icon-img{ width: 1.3em }
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'> <?= $LANG['NAV_HOME'] ?> </a> &gt;&gt;
		<b><?= $LANG['CHAR_MGMT'] ?></b>
	</div>
	<div role="main" id="innertext">
		<div style="float: right;">
			<a href="#" onclick="toggle('addchardiv');">
				<img class="icon-img" src="../../images/add.png" alt="<?= $LANG['ADD_BTN'] ?>" />
			</a>
		</div>
		<h1 class="page-heading"><?= $LANG['TAXON_CHARACTERS']; ?></h1>
		<?php
		if($isEditor){
			?>
			<div id="addeditchar">
				<div id="addchardiv" style="display:none;margin-bottom:8px;">
					<form name="newcharform" action="chardetails.php" method="post" onsubmit="return validateNewCharForm(this)">
						<fieldset>
							<legend><b><?= $LANG['NEW_CHAR'] ?></b></legend>
							<div>
							<label for="charname"><?= $LANG['CHAR_NAME'] ?>:</label>
								<input type="text" id="charname" name="charname" autocomplete="off" maxlength="255" style="width:400px;" />
							</div>
							<div class="flex-form">
								<div>
									<label for="chartype"><?= $LANG['TYPE'] ?>:</label>
									<select id="chartype" name="chartype">
										<option value="UM"><?= $LANG['MULTI_STATE'] ?></option>
									</select>
								</div>
								<div>
								<label for="difficultyrank"><?= $LANG['DIFFICULTY'] ?>:</label>
									<select id="difficultyrank" name="difficultyrank">
										<option value="">---------------</option>
										<option value="1"><?= $LANG['EASY'] ?></option>
										<option value="2"><?= $LANG['INTERMEDIATE'] ?></option>
										<option value="3"><?= $LANG['ADVANCED'] ?></option>
										<option value="4"><?= $LANG['HIDDEN'] ?></option>
									</select>
								</div>
								<div>
									<label for="hid"> <?= $LANG['GROUPING'] ?>: </label>
									<select id="hid" name="hid" style="max-width:300px;">
										<option value=""> <?= $LANG['NOT_ASSIGNED'] ?> </option>
										<option value="">---------------------</option>
										<?php
										$hArr = $headingArr;
										asort($hArr);
										foreach($hArr as $k => $v){
											echo '<option value="'.$k.'">'.$v['name'].'</option>';
										}
										?>
									</select>
									<a href="#" onclick="openHeadingAdmin(); return false;"> <img class="icon-img" src="../../images/edit.png" alt="<?= $LANG['EDIT_BTN'] ?>" /></a>
								</div>
							</div>
							<div class="flex-form">
								<div>
									<label for="sortsequence"><?= $LANG['SORT_SQNCE'] ?></label>
									<input type="text" id="sortsequence" name="sortsequence" autocomplete="off" />
								</div>
							</div>
							<div style="width:100%;padding-top:6px;">
								<button name="formsubmit" type="submit" value="createCharacter"><?= $LANG['CREATE_BTN'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="charlist" style="padding-left:10px;">
					<?php
					if($charArr){
						foreach($headingArr as $hid => $hArr){
							if(array_key_exists($hid, $charArr)){
								?>
								<h2><?= Sanitize::outString($hArr['name']) ?></h2>
								<div>
									<ul>
										<?php
										$charList = $charArr[$hid];
										foreach($charList as $cid => $charName){
											if ($charName) echo '<li><a href="chardetails.php?cid=' . $cid . '">' . Sanitize::outString($charName) . '</a></li>';
										}
										?>
									</ul>
								</div>
								<?php
							}
						}
						if(array_key_exists(0, $charArr)){
							$noHeaderArr = $charArr[0];
							?>
							<h2> <?= $LANG['NO_GRP_ASSSIGNED'] ?> </h2>
							<div>
								<ul>
									<?php
									foreach($noHeaderArr as $cid => $charName){
										echo '<li><a href="chardetails.php?cid=' . $cid . '">' . Sanitize::outString($charName) . '</a></li>';
									}
									?>
								</ul>
							</div>
							<?php
						}
					}
					else{
						echo '<div style="font-weight:bold;font-size:120%;">' . $LANG['NO_CHAR'] . '</div>';
					}
					?>
				</div>
			</div>
			<?php
		}
		else{
			echo '<h2>' . $LANG['NO_AUTH'] .'</h2>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
