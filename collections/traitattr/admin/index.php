<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTraitAdmin.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/traitattr/admin/index');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../../profile/index.php?refurl=../ident/admin/index.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$langId = array_key_exists('langid',$_REQUEST) ? $_REQUEST['langid'] : '';

$traitManager = new OccurrenceTraitAdmin();

$traitList = $traitManager->getTraitArr(null, ['projectGroup ASC', 'traitName ASC']);
$groupArr = $traitManager->getProjectGroups();

$isEditor = false;
if($IS_ADMIN || array_key_exists("KeyAdmin",$USER_RIGHTS)){
	$isEditor = true;
}

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
	<title><?= $LANG['OCCUR_TRAITS'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../../js/symb/shared.js"></script>
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
		<b><?= $LANG['TRAIT_MGMT']  ?></b>
	</div>
	<div role="main" id="innertext">
		<div style="float: right;">
			<button type="button" class="clear-icon-button" onclick="toggle('addtraitdiv');" title="<?= $LANG['ADD_BTN'] ?>" aria-label="<?= $LANG['ADD_BTN'] ?>">
				<img class="icon-img" src="../../../images/add.png" />
			</button>
		</div>
		<h1 class="page-heading"><?= $LANG['OCCUR_TRAITS'] ?></h1>
		<?php
		if($isEditor){
			?>
			<div id="addeditchar">
				<div id="addtraitdiv" style="display:none;margin-bottom:8px;">
					<form name="newtraitform" action="traitdetails.php" method="post">
						<fieldset>
							<legend><b><?= $LANG['NEW_TRAIT'] ?></b></legend>
							<div>
								<label for="traitname"><?= $LANG['TRAIT_NAME'] ?>:</label>
								<input type="text" id="traitname" name="traitname" autocomplete="off" maxlength="255" style="width:400px;" required/>
							</div>
							<div class="flex-form">
								<div>
									<label for="traittype"><?= $LANG['TYPE'] ?>:</label>
									<select id="traittype" name="traittype" >
										<option value="UM">UM</option>
										<option value="OM">OM</option>
										<option value="TF">TF</option>
										<option value="NU">NU</option>
									</select>
								</div>
								<div>
									<label for="units"><?= $LANG['UNITS'] ?>:</label>
									<input type="text" id="units" name="units" autocomplete="off" maxlength="255" />
								</div>
								<div>
									<label for="projectGroup"><?= $LANG['PROJECT_GROUP'] ?>:</label>
									<input type="text" id="projectGroup" name="projectGroup" list="groups" autocomplete="off" maxlength="255" />
									<datalist id="groups">
										<?php
										foreach($groupArr as $group) {
											echo '<option value="'. $group['projectGroup'].'">';
										}
										?>
									</datalist>
								</div>
							</div>
							<div class="flex-form">
								<div>
									<label for="description"><?= $LANG['DESC'] ?>:</label>
									<input type="text" id="description" name="description" autocomplete="off" maxlength="255" />
								</div>
								<div>
									<label for="refurl"><?= $LANG['REF_URL'] ?>:</label>
									<input type="text" id="refurl" name="refurl" autocomplete="off" maxlength="255" />
								</div>
								<div>
									<label for="notes"><?= $LANG['NOTES'] ?>:</label>
									<input type="text" id="notes" name="notes" autocomplete="off" maxlength="255" />
								</div>
								<div>
								<label for="dynamicproperties"><?= $LANG['INPUT_TYPE'] ?>:</label>
									<select id="dynamicproperties" name="dynamicproperties">
										<option value="radio"><?= $LANG['RADIO_BTN'] ?></option>
										<option value="checkbox"><?= $LANG['CHECKBOX'] ?></option>
										<option value="select"><?= $LANG['SELECT'] ?></option>
									</select>
								</div>
								<div style="padding-top:4px;">
									<label for="isPublic"><?= $LANG['IS_PUBLIC'] ?>:</label>
									<input type="hidden" name="isPublic" value="0"/>
									<input type="checkbox" id="isPublic" name="isPublic" value="1" checked/>
								</div>
								<div style="padding-top:4px;"">
									<label for="includeInSearch"><?= $LANG['IN_SEARCH'] ?>:</label>
									<input type="hidden" name="includeInSearch" value="0"/>
									<input type="checkbox" id="includeInSearch" name="includeInSearch" value="1" checked/>
								</div>
							</div>
							<div style="width:100%;padding-top:6px;">
								<button name="formsubmit" type="submit" value="createTrait"><?= $LANG['CREATE_BTN'] ?></button>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="traitList" style="padding-left:10px;">
					<?php
					if($traitList){
						foreach($groupArr as $group) {
							echo '<h2>' . Sanitize::outString($group['projectGroup']) . '</h2><ul>';
							foreach ($traitList as $trait){
								if ($trait['projectGroup'] === $group['projectGroup'])
								echo '<li><a href="traitdetails.php?traitid=' . $trait['traitID'] . '">' . $trait['traitName'] . '</a></li>';
							}
							echo '</ul>';
						}
						echo '<h2>'. $LANG['NO_GRP_ASSSIGNED'] . '</h2><ul>';
						foreach ($traitList as $trait){
							if (empty($trait['projectGroup'])) {
								echo '<li><a href="traitdetails.php?traitid=' . $trait['traitID'] . '">' . $trait['traitName'] . '</a></li>';
							}
						}
						echo '</ul>';
					}
					else{
						echo '<div style="font-weight:bold;font-size:120%;">' . $LANG['NO_TRAITS'] . '</div>';
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
