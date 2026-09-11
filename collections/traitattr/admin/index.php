<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTraitAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/traitattr/admin/index');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../../profile/index.php?refurl=../ident/admin/index.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$langId = array_key_exists('langid',$_REQUEST) ? $_REQUEST['langid'] : '';

$charManager = new OccurrenceTraitAdmin();

$traitList = $charManager->getTraitArr();

$isEditor = false;
if($IS_ADMIN || array_key_exists("KeyAdmin",$USER_RIGHTS)){
	$isEditor = true;
}

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
	<title>Occurrence Traits</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../../js/symb/shared.js"></script>
	<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", () => {
    		toggle('addtraitdiv');
		});
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
		<a href='../../../index.php'> <?= $LANG['NAV_HOME'] ?> </a> &gt;&gt;
		<b><?= $LANG['CHAR_MGMT'] ?></b>
	</div>
	<div role="main" id="innertext">
		<div style="float: right;">
			<a href="#" onclick="toggle('addtraitdiv');">
				<img class="icon-img" src="../../../images/add.png" alt="<?= $LANG['ADD_BTN'] ?>" />
			</a>
		</div>
		<h1 class="page-heading">Occurrence Traits</h1>
		<?php
		if($isEditor){
			?>
			<div id="addeditchar">
				<div id="addtraitdiv" style="display:none;margin-bottom:8px;">
					<form name="newcharform" action="chardetails.php" method="post" onsubmit="return validateNewCharForm(this)">
						<fieldset>
							<legend><b>NEW TRAIT</b></legend>
							<div>
							<label for="traitname">TRAIT NAME:</label>
								<input type="text" id="traitname" name="traitname" autocomplete="off" maxlength="255" style="width:400px;" />
							</div>
							<div class="flex-form">
								<div>
								<label for="traittype">TRAIT TYPE:</label>
									<select id="traittype" name="traittype">
										<option value="">---------------</option>
										<option value="1">UM</option>
										<option value="2">TF</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
								</div>
							</div>
							<div class="flex-form">
								<div>
									<label for="traitname">Units:</label>
									<input type="text" id="units" name="units" autocomplete="off" maxlength="255" />
								</div>
								<div>
									<label for="traitname">Description:</label>
									<input type="text" id="description" name="description" autocomplete="off" maxlength="255" />
								</div>
								<div>
									<label for="traitname">Reference URL:</label>
									<input type="text" id="refurl" name="refurl" autocomplete="off" maxlength="255" />
								</div>
								<div>
									<label for="traitname">Notes:</label>
									<input type="text" id="notes" name="notes" autocomplete="off" maxlength="255" />
								</div>
								<div>
									<label for="traitname">isPublic:</label>
									<input type="checkbox" id="isPublic" name="isPublic" />
								</div>
								<div>
								<label for="dynamicproperties">Dynamic Properties/Input Type:</label>
									<select id="dynamicproperties" name="dynamicproperties">
										<option value="">---------------</option>
										<option value="1">Radio Button</option>
										<option value="2">Checkbox</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
								</div>
							</div>
							</div>
							<div style="width:100%;padding-top:6px;">
								<button name="formsubmit" type="submit" value="createCharacter"><?= $LANG['CREATE_BTN'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="traitList" style="padding-left:10px;">
					<?php
					if($traitList){
						echo '<ul>';
						foreach ($traitList as $trait){
							echo '<li><a href="traitdetails.php?traitid=' . $trait['traitID'] . '">' . $trait['traitName'] . '</a></li>';
							var_dump($trait);
						}
						echo '</ul>';
					}
					else{
						echo '<div style="font-weight:bold;font-size:120%;">' . 'No Traits' . '</div>';
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
