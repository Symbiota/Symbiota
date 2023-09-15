<!DOCTYPE html>

<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SalixUtilities.php');
include_once($SERVER_ROOT.'/content/lang/collections/specprocessor/salix/salixhandler.'.$LANG_TAG.'.php');
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

<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<title> <?php (isset($LANG['SALIX_WRDST_MNGR'])?$LANG['SALIX_WRDST_MNGR']:'SALIX Wordstat Manager') ?> </title>
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
			<a href="../../../index.php"> <?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?> </a> &gt;&gt;
			<?php
			if($collid){
				?>
				<a href="../../misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>&emode=1"> <?php echo (isset($LANG['COLL_MGMT'])?$LANG['COLL_MGMT']:'Collection Management'); ?> </a> &gt;&gt;
				<?php
			}
			else{
				?>
				<a href="../../../sitemap.php"> <?php echo (isset($LANG['BREADCRUMB_SITEMAP'])?$LANG['BREADCRUMB_SITEMAP']:'Sitemap'); ?> </a> &gt;&gt;
				<?php
			}
			echo '<a href="salixhandler.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&actiontype=' . htmlspecialchars($actionType, HTML_SPECIAL_CHARS_FLAGS) . '&limit=' . htmlspecialchars($limit, HTML_SPECIAL_CHARS_FLAGS) . '">';
			echo '<b>' . (isset($LANG['SALIX_WRDST_MNGR'])?$LANG['SALIX_WRDST_MNGR']:'SALIX Wordstat Manager') . '</b>';
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
					echo '<div style="margin:15px;"><a href="salixhandler.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&actiontype=' . htmlspecialchars($actionType, HTML_SPECIAL_CHARS_FLAGS) . '&limit=' . htmlspecialchars($limit, HTML_SPECIAL_CHARS_FLAGS) . '">' . (isset($LANG['RETURN'])?$LANG['RETURN']:'Return to Main Menu') . '</a></div>';
				}
				else{
					?>
					<div style="border:10px;">
						<form name="salixmanagerform" action="salixhandler.php" method="post" onsubmit="return verifySalixManagerForm(this)">
							<fieldset style="margin:15px;">
							<legend><b> <?php echo (isset($LANG['SALIX_WRDST_MNGR'])?$LANG['SALIX_WRDST_MNGR']:'SALIX Wordstat Manager') ?> </b></legend>
								<?php echo (isset($LANG['ACTIONS'])?$LANG['ACTIONS']:'Actions') ?> :<br/>
								<input id="rndmSelect" name="actiontype" type="radio" value="1" /> <label for="rndmSelect"> <?php echo (isset($LANG['RNDM_SELECT'])?$LANG['RNDM_SELECT']:'Rebuild with randomly selected occurrences') ?> </label> <br/>
								<input id="recentEnter" name="actiontype" type="radio" value="2" /> <label for="recentEnter"> <?php echo (isset($LANG['RECENTLY_ENTERED'])?$LANG['RECENTLY_ENTERED']:'Rebuild with most recently entered occurrences') ?> </label> <br/>
								<input id="appendLast" name="actiontype" type="radio" value="3" checked /> <label for="appendLast"> <?php echo (isset($LANG['APPEND_LAST_BUILD'])?$LANG['APPEND_LAST_BUILD']:'Append using occurrences entered since last build') ?> (<?php echo $salixHanlder->getLastBuildTimestamp(); ?>) </label> <br/><br/>
								<label for="limit"> <?php echo (isset($LANG['LIMIT_TO'])?$LANG['LIMIT_TO']:'Limit to') ?> <span class="skip-link"> <?php echo (isset($LANG['UNIQUE_VALS'])?$LANG['UNIQUE_VALS']:'unique values per column') ?> </span> </label> <input id="limit" name="limit" type="text" value="100000"> <?php echo (isset($LANG['UNIQUE_VALS'])?$LANG['UNIQUE_VALS']:'unique values per column') ?> </input>
								<div style="margin:15px;">
								
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input name="formsubmit" type="submit" value="<?php echo (isset($LANG['BLD_TABLES'])?$LANG['BLD_TABLES']:'Build Wordstat Tables'); ?>" />
								</div>
							</fieldset>
						</form>
					</div>
					<?php
				}
			}
			else{
				echo '<div style="margin:25px;font-weight">' . (isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'You are not authorized to build Word Stats') . '</div>';
			}
			?>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
