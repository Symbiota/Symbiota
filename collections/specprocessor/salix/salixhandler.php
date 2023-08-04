<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SalixUtilities.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID){
	header('Location: ../../../profile/index.php?refurl=../collections/specprocessor/salix/salixhandler.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));
}

$action = (isset($_REQUEST['formsubmit'])?$_REQUEST['formsubmit']:'');
$verbose = (isset($_REQUEST['verbose'])?$_REQUEST['verbose']:1);
$collid = (isset($_REQUEST['collid'])?$_REQUEST['collid']:0);
$actionType = (isset($_REQUEST['actiontype'])?$_REQUEST['actiontype']:1);
$limit = (isset($_REQUEST['limit'])?$_REQUEST['limit']:100000);

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN){
		$isEditor = 1;
	}
	elseif($collid){
		if(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])){
			$isEditor = 1;
		}
	}
}
?>
<!DOCTYPE html >
<html>
	<head>
		<title>SALIX Wordstat Manager</title>
		<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
			function verifySalixManagerForm(this){

				return true;
			}
		</script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/shared.js?ver=140310" type="text/javascript"></script>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class='navpath'>
			<a href="../../../index.php">Home</a> &gt;&gt;
			<?php
			if($collid){
				?>
				<a href="../../misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>&emode=1">Collection Management</a> &gt;&gt;
				<?php
			}
			else{
				?>
				<a href="../../../sitemap.php">Sitemap</a> &gt;&gt;
				<?php
			}
			echo '<a href="salixhandler.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&actiontype=' . htmlspecialchars($actionType, HTML_SPECIAL_CHARS_FLAGS) . '&limit=' . htmlspecialchars($limit, HTML_SPECIAL_CHARS_FLAGS) . '">';
			echo '<b>SALIX Wordstat Manager</b>';
			echo '</a>';
			?>
		</div>

		<!-- This is inner text! -->
		<div id="innertext">
			<?php
			if($isEditor){
				$salixHanlder = new SalixUtilities();
				$salixHanlder->setVerbose($verbose);
				if($action == 'Build Wordstat Tables'){
					$salixHanlder->buildWordStats($collid,$actionType,$limit);
					echo '<div style="margin:15px;"><a href="salixhandler.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&actiontype=' . htmlspecialchars($actionType, HTML_SPECIAL_CHARS_FLAGS) . '&limit=' . htmlspecialchars($limit, HTML_SPECIAL_CHARS_FLAGS) . '">Return to Main Menu</a></div>';
				}
				else{
					?>
					<fieldset style="border:10px;">
						<legend></legend>
						<form name="salixmanagerform" action="salixhandler.php" method="post" onsubmit="return verifySalixManagerForm(this)">
							<div style="margin:15px;">
								<b>Actions</b><br/>
								<input name="actiontype" type="radio" value="1" /> Rebuild with randomly selected occurrences<br/>
								<input name="actiontype" type="radio" value="2" /> Rebuild with most recently entered occurrences<br/>
								<input name="actiontype" type="radio" value="3" checked />Append using occurrences entered since last build (<?php echo $salixHanlder->getLastBuildTimestamp(); ?>)<br/><br/>
								Limit to <input name="limit" type="text" value="100000" /> unique values per column
							</div>
							<div style="margin:15px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="formsubmit" type="submit" value="Build Wordstat Tables" />
							</div>
						</form>
					</fieldset>
					<?php
				}
			}
			else{
				echo '<div style="margin:25px;font-weight">You are not authorized to build Word Stats</div>';
			}
			?>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
