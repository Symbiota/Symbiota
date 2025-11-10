<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceGeoLocate.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/georef/geolocatetools.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/georef/geolocatetools.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/georef/geolocatetools.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../misc/generaltemplate.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$action = array_key_exists('action',$_POST)?$_POST['action']:'';
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

$geoLocateManager = new OccurrenceGeoLocate.php();
$geoLocateManager->setCollid($collid);

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

$occRecArr = array();
if($isEditor){
	if(array_key_exists('qCountry',$_POST) && $_POST['qCountry']){
		$geoLocateManager->addFilterTerm('country', $_POST['qCountry']);
	}
	if(array_key_exists('qStateProvince',$_POST) && $_POST['qStateProvince']){
		$geoLocateManager->addFilterTerm('stateProvince', $_POST['qStateProvince']);
	}
	if(array_key_exists('qCounty',$_POST) && $_POST['qCounty']){
		$geoLocateManager->addFilterTerm('county', $_POST['qCounty']);
	}
	if(array_key_exists('qLocality',$_POST) && $_POST['qLocality']){
		$geoLocateManager->addFilterTerm('locality', $_POST['qLocality']);
	}

	if($action == '1'){
		$occRecArr = $geoLocateManager->batchConvertTrs();
	}
	elseif($action == '2'){
		$occRecArr = $geoLocateManager->batchConvertTrs();
	}
	elseif($formSubmit == 'Submit Batch Coordinates'){
		$statusStr = $geoLocateManager->loadOccurrences($_POST);
	}

}

?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<title><?= $LANG['GEOLOCATE_PROCESSES'] ?></title>
		<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo htmlspecialchars($CLIENT_ROOT, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>/index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
			<a href="../misc/collprofiles.php?emode=1&collid=<?php echo htmlspecialchars($collId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>"><?= $LANG['COLL_MANAGE_MENU'] ?></a> &gt;&gt;
			<b>Batch GeoLocate Tools</b>
		</div>
		<!-- This is inner text! -->
		<div role="main" id="innertext">
			<h1 class="page-heading"><?= $LANG['GEOLOCATE_PROCESSES']; ?></h1>
		<?php
		if($collId){
			if($isEditor){
				?>
				<fieldset>
					<legend><?= $LANG['MAIN_MENU']; ?></legend>
					<div>
						<?= $LANG['RECORDS_AVAIL_TRS']; ?>: <?php echo $classManager->getTrsOccurrenceCount(); ?>
					</div>
					<div>
						<?= $LANG['RECORDS_AVAIL_BATCH_GEOREF']; ?>: <?php echo $classManager->getOccurrenceCount(); ?>
					</div>
					<form method="post" action="geolocatetools">
						<div>
							<b><u><?= $LANG['FILTER_TERMS']; ?></u></b>
							<div style="margin:0px 10px;">
								<b><?= $LANG['COUNTRY']; ?>:</b> <input name="country" type="text" value="<?php echo $qCountry; ?>" /><br/>
								<b><?= $LANG['STATE_PROVINCE']; ?>:</b> <input name="stateProvince" type="text" value="<?php echo $qStateProvince; ?>" /><br/>
								<b><?= $LANG['COUNTY_PARISH']; ?>:</b> <input name="county" type="text" value="<?php echo $qCounty; ?>" /><br/>
								<b><?= $LANG['LOCALITY']; ?>:</b> <input name="locality" type="text" value="<?php echo $qLocality; ?>" /><br/>
							</div>
						</div>
						<div>
							<b><u><?= $LANG['ACTION']; ?></u></b>
							<div style="margin:0px 10px;">
								<input name="action" type="radio" value="1" /> <?= $LANG['BATCH_PROCESS_TRS_RECS']; ?><br/>
								<input name="action" type="radio" value="2" /> <?= $LANG['BATCH_PROCESS_LOC_RECS']; ?><br/>
								<input name="action" type="radio" value="0" checked /> <?= $LANG['REFRESH_COUNTS']; ?>
							</div>
						</div>
						<div>
							<button name="formsubmit" type="submit" value="Perform Action" ><?= $LANG['PERFORM_ACTION']; ?></button>
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						</div>
					</form>
				</fieldset>
				<?php
				if($occRecArr){
					//Review conversions before submitting
					//Reviews need to be limited to a few hunred
					//More than 100 records can be batch processed, but without review

					?>
					<form name="coordsubmitform" action="geolocatetool" method="post">
						<table class="styledtable" style="font-size:12px;">
							<tr>
								<th>occid</th>
								<th><?= $LANG['MAP_TOOL']; ?></th>
								<th><?= $LANG['LOCALITY']; ?></th>
								<th><?= $LANG['DEC_LAT']; ?></th>
								<th><?= $LANG['DEC_LONG']; ?></th>
								<th><?= $LANG['COORD_ERROR_METERS']; ?></th>
							</tr>
							<?php
							foreach($occRecArr as $occid => $occArr){
								echo '<tr>';
								echo '<td><a href="">' . htmlspecialchars($occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a></td>';
								echo '<td>'.$occArr['loc'].'</td>';
								echo '<td></td>';
								echo '<td><input name="lat-'.$occid.'" type="text" value="'.$occArr['declat'].'" /></td>';
								echo '<td><input name="lng-'.$occid.'" type="text" value="'.$occArr['declng'].' /></td>';
								echo '<td><input name="err-'.$occid.'" type="text" value="'.$occArr['coorderr'].' /></td>';
								echo '</tr>';
							}
							?>
						</table>
						<div>
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<button name="formsubmit" type="submit" value="Submit Batch Coordinates" ><?= $LANG['SUBMIT_BATCH_COORDS']; ?></button>
						</div>
					</form>
					<?php
				}
			}
			else{
				?>
				<div style='font-weight:bold;font-size:120%;'>
					<?= $LANG['NO_PERMISSION_TO_EDIT']; ?>
				</div>
				<?php
			}
		}
		else{
			?>
			<div style='font-weight:bold;font-size:120%;'>
				<?= $LANG['COLLID_IS_NULL']; ?>
			</div>
			<?php
		}


		?>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
