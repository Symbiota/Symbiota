<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistManager.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/ident/index.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/ident/index.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/ident/index.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$pid = array_key_exists('pid', $_REQUEST) ? filter_var($_REQUEST['pid'], FILTER_SANITIZE_NUMBER_INT) : '';
if(!$pid && !empty($_REQUEST['proj'])) $pid = filter_var($_REQUEST['proj'], FILTER_SANITIZE_NUMBER_INT);

if($pid === '' && isset($DEFAULT_PROJ_ID)) $pid = $DEFAULT_PROJ_ID;

$clManager = new ChecklistManager();
$clManager->setPid($pid);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['IDKEYS'];?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php"><?= $LANG['NAV_HOME'] ?></a> &gt;&gt;
		<b><?= $LANG['IDKEYLIST']; ?></b>
	</div>
	<div role="main" id="innertext">
		<h1 classes="page-heading"><?= $LANG['IDKEYS']; ?></h1>
	    <div style='margin:20px;'>
	        <?php
	        $projArr = $clManager->getChecklists(true);
	        //Output is sanitized within getChecklists() class function
	        foreach($projArr as $pidKey => $pArr){
				$clArr = $pArr['clid'];
				echo '<div style="margin:3px 0px 0px 15px;">';
				echo '<h3>' . $pArr['name'];
				if(!empty($pArr['displayMap'])){
					echo ' <a href="../checklists/clgmap.php?pid=' . $pidKey . '&target=keys"><img src="../images/world.png" style="width:10px;border:0" /></a>';
				}
				echo '</h3>';
				echo '<div><ul>';
				foreach($clArr as $clid => $clName){
					echo '<li><a href="key.php?clid=' . $clid . '&pid=' . $pidKey . '&taxon=All+Species">' . $clName . '</a></li>';
				}
				echo "</ul></div>";
				echo "</div>";
			}
			?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>