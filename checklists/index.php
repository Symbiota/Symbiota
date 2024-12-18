<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/checklists/index.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT . '/content/lang/checklists/index.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/checklists/index.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']:0;

//Sanitation
$pid = htmlspecialchars($pid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
if(!is_numeric($pid)) $pid = 0;


$clManager = new ChecklistManager();
$clManager->setProj($pid);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE . $LANG['SPECIES_INVENTORIES']; ?></title>
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
		<a href="../index.php"><?php echo htmlspecialchars($LANG['NAV_HOME'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?></a> &gt;&gt;
		<b><?php echo $LANG['SPECIES_INVENTORIES']; ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['SPECIES_INVENTORIES']; ?></h1>
        <div style="margin:20px;">
			<?php
            $researchArr = $clManager->getChecklists();
			if($researchArr){
				foreach($researchArr as $pid => $projArr){
					?>
					<h2 class="checklist-header">
						<?php
						$projName = $projArr['name'];
						if($pid) echo '<a href="../projects/index.php?pid=' . $pid . '">';
						if($projName == 'Miscellaneous Inventories') $projName = $LANG['MISC_INVENTORIES'];
						echo $projName;
						if($pid) echo '</a>';
						if(!empty($projArr['displayMap'])){
							?>
							<a class="button button-tertiary btn-medium-font" style="gap:0.5rem" href="<?= "clgmap.php?pid=" . $pid ?>" title='<?= $LANG['SHOW_MAP'] ?>'>
								<?= $LANG['MAP'] ?> <img src='../images/world.png' style='width:1em;border:0' alt='<?= $LANG['IMG_OF_GLOBE'] ?>' />
							</a>
							<?php
						}
						?>
					</h2>
					<ul class="checklist-ul">
						<?php
						foreach($projArr['clid'] as $clid => $clName){
							echo '<li><a href="checklist.php?clid=' . htmlspecialchars($clid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&pid=' . htmlspecialchars($pid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '">' . htmlspecialchars($clName, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a></li>';
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
