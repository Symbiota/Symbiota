<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ReferenceManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../references/index.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

Language::load([
	'references/index'
]);

$refId = array_key_exists('refid', $_REQUEST) ? Sanitize::int($_REQUEST['refid']) : 0;
$formSubmit = array_key_exists('formsubmit', $_POST) ? $_POST['formsubmit'] : '';

$refManager = new ReferenceManager();
$refArr = '';
$refExist = false;

$isEditor = false;
if($IS_ADMIN) $isEditor = true;

$statusStr = '';
if($formSubmit && $isEditor){
	if($formSubmit == 'Delete Reference'){
		$statusStr = $refManager->deleteReference($refId);
	}
	elseif($formSubmit == 'Search References'){
		$refArr = $refManager->getRefList($_POST['searchcitation']);
		foreach($refArr as $refName => $valueArr){
			if($valueArr["bibliographicCitation"]){
				$refExist = true;
			}
		}
	}
}
if(!$formSubmit || $formSubmit != 'Search References'){
	$refArr = $refManager->getRefList('');
	foreach($refArr as $refName => $valueArr){
		if($valueArr["bibliographicCitation"]){
			$refExist = true;
		}
	}
}

?>
<!DOCTYPE HTML>
<html lang="<?php echo $LANG_TAG ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?> Reference Management</title>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../js/symb/references.index.js"></script>

</head>
<body>
	<?php
	$displayLeftMenu = (isset($reference_indexMenu)?$reference_indexMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php"><?= htmlspecialchars($LANG['HOME'] ?? 'Home'); ?></a> &gt;&gt;
		<a href="index.php"><b><?= htmlspecialchars($LANG['REF_MGMT'] ?? 'Reference Management'); ?></b></a>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= htmlspecialchars($LANG['REF_MGMT'] ?? 'Reference Management'); ?></h1>
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:15px;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<?php
		}
		?>
		<div id="" style="float:right;width:240px;">
			<form name="filterrefform" action="index.php" method="post">
				<fieldset style="background-color:#f2f2f2;">
					<legend><b><?= htmlspecialchars($LANG['FILTER_LIST'] ?? 'Filter List'); ?></b></legend>
			    	<div>
						<div>
							<b><?= htmlspecialchars($LANG['CITATION'] ?? 'Citation'); ?></b>
							<input type="text" autocomplete="off" name="searchcitation" id="searchcitation" size="25" value="<?php echo ($formSubmit == 'Search References'?$_POST['searchcitation']:''); ?>" />
						</div>
						<div style="padding-top:8px;float:right;">
							<button name="formsubmit" type="submit" value="Search References">
								<?= htmlspecialchars($LANG['FILTER_LIST'] ?? 'Filter List'); ?>
							</button>						
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div id="reflistdiv" style="min-height:200px;">
			<?php 
			if ($isEditor){
			?>
				<div style="float:right;margin:10px;"><a href="refdetails.php"><img src="../images/add.png" style="width:1.3em" alt="<?= htmlspecialchars($LANG['CREATE_NEW_REF'] ?? 'Create New Reference'); ?>" /></a>
				</div>
			<?php	
			}
			if($refExist){
					echo '<div style="font-weight:bold;font-size:120%;">'
						. htmlspecialchars($LANG['REFERENCES'] ?? 'References')
						. '</div>';
					echo '<div><ul>';
				foreach($refArr as $refId => $recArr){
					echo '<li>';
					echo '<a href="refdetails.php?refid=' . htmlspecialchars($refId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '"><b>' . htmlspecialchars($recArr["bibliographicCitation"], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</b></a>';
					echo '</li>';
				}
				echo '</ul></div>';
			}
			elseif(($formSubmit && $formSubmit == 'Search References') && !$refExist){
				echo '<div style="margin-top:10px;">
					<div style="font-weight:bold;font-size:120%;">'
					. htmlspecialchars($LANG['NO_REF_MATCH'] ?? 'There were no references matching your criteria.')
					. '</div></div>';
				}
			else{
				echo '<div style="margin-top:10px;">
					<div style="font-weight:bold;font-size:120%;">'
					. htmlspecialchars($LANG['NO_REF_EXIST'] ?? 'There are currently no references in the database.')
					. '</div></div>';
				}
			?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>