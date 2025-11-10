<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/checklists/index.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/checklists/index.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/checklists/index.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$pid = array_key_exists('pid', $_REQUEST) ? filter_var($_REQUEST['pid'], FILTER_SANITIZE_NUMBER_INT) : 0;

$clManager = new ChecklistManager();
$clManager->setProj($pid);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . $LANG['SPECIES_INVENTORIES']; ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<style>
		.btn-medium-font {
			font-size: 1rem;
			text-decoration: none;
		}
		.checklist-header {
			display: flex;
			margin-bottom: 0;
			align-items: center;
			gap: 0.5rem;
		}
		.checklist-ul {
			margin-top: 0;
		}
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($checklists_indexMenu)?$checklists_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php"><?= $LANG['NAV_HOME'] ?></a> &gt;&gt;
		<b><?= $LANG['SPECIES_INVENTORIES']; ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['SPECIES_INVENTORIES']; ?></h1>
		<div style="margin:20px;">
			<?php
			if($researchArr = $clManager->getChecklists()){
				//Output is sanitized within getChecklists() class function
				foreach($researchArr as $projID => $projArr){
					?>
					<h2 class="checklist-header">
						<?php
						$projName = $projArr['name'];
						if($projID) echo '<a href="../projects/index.php?pid=' . $projID . '">';
						if($projName == 'Miscellaneous Inventories') $projName = $LANG['MISC_INVENTORIES'];
						echo $projName;
						if($projID) echo '</a>';
						if(!empty($projArr['displayMap'])){
							?>
							<a class="button button-tertiary btn-medium-font" style="gap:0.5rem" href="<?= "clgmap.php?pid=" . $projID ?>" title='<?= $LANG['SHOW_MAP'] ?>'>
								<?= $LANG['MAP'] ?> <img src='../images/world.png' style='width:1em;border:0' alt='<?= $LANG['IMG_OF_GLOBE'] ?>' />
							</a>
							<?php
						}
						?>
					</h2>
					<ul class="checklist-ul">
						<?php
						foreach($projArr['clid'] as $clid => $clName){
							echo '<li><a href="checklist.php?clid=' . $clid . '&pid=' . $projID . '">' . $clName . '</a></li>';
						}
						?>
					</ul>
					<?php
				}
			}
			else echo '<div><b>' . $LANG['NO_INVENTORIES'] . '</b></div>';
			?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>