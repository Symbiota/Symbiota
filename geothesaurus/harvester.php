<?php
include_once ('../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
//header("Content-Type: text/html; charset=".$CHARSET);

$geoThesID = array_key_exists('geoThesID', $_REQUEST) ? $_REQUEST['geoThesID'] : '';
$gbAction = array_key_exists('gbAction', $_REQUEST) ? $_REQUEST['gbAction'] : '';
$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

// Sanitation
if(!is_numeric($geoThesID)) $geoThesID = 0;
$submitAction = filter_var($submitAction, FILTER_SANITIZE_STRING);

$geoManager = new GeographicThesaurus();

$isEditor = false;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor && $submitAction) {
	if($submitAction == 'transferDataFromLkupTables'){
		if($geoManager->transferDeprecatedThesaurus()) $statusStr = '<span style="color:green;">Geographic Lookup tables transferred into new Geographic Thesaurus</span>';
		else $statusStr = '<span style="color:green;">'.implode('<br/>',$geoManager->getWarningArr()).'<span style="color:green;">';
	}
	elseif($submitAction == 'submitCountryForm'){
		$geoManager->addGeoBoundary($_POST['geoid'][0]);
	}
}
//https://gadm.org/download_country.html
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Geographic Thesaurus Havester</title>
	<?php
	$activateJQuery = true;
	include_once ($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">

	</script>
	<style type="text/css">
		fieldset{ margin: 10px; padding: 15px; }
		legend{ font-weight: bold; }
		label{ text-decoration: underline; }
		#edit-legend{ display: none }
		.field-div{ margin: 3px 0px }
		.editIcon{  }
		.editTerm{ }
		.editFormElem{ display: none }
		#editButton-div{ display: none }
		#unitDel-div{ display: none }
		.button-div{ margin: 15px }
		.link-div{ margin:0px 30px }
		#status-div{ margin:15px; padding: 15px; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php">Home</a> &gt;&gt;
		<a href="index.php">Geographic Thesaurus Listing</a> &gt;&gt;
		<b>Geographic Harvester</b>
	</div>
	<div id='innertext'>
		<?php
		if($statusStr){
			echo '<div id="status-div">'.$statusStr.'</div>';
		}

		if($statusReport = $geoManager->getThesaurusStatus()){
			$geoRankArr = $geoManager->getGeoRankArr();
			echo '<fieldset style="width: 800px">';
			echo '<legend>Active Geographic Thesaurus</legend>';
			if(isset($statusReport['active'])){
				foreach($statusReport['active'] as $geoRank => $cnt){
					echo '<div><b>'.$geoRankArr[$geoRank].':</b> '.$cnt.'</div>';
				}
				echo '<div style="margin-top:20px"><a href="index.php">Goto Geographic Thesaurus</a></div>';
			}
			else echo '<div>Active thesaurus is empty</div>';
			echo '</fieldset>';
			if(isset($statusReport['lkup'])){
				?>
				<fieldset>
					<legend>Geopraphic Lookup Tables - deprecated</legend>
					<p>There appears to be records within the deprecated Geographic lookup tables that are no longer used.<br/>Do you want to transfer this data into the new geographic thesaurus?</p>
					<?php
					foreach($statusReport['lkup'] as $k => $v){
						echo '<div><b>'.$k.':</b> '.$v.'</div>';
					}
					?>
					<hr/>
					<form name="transThesForm" action="harvester.php" method="post" style="margin-top:15px">
						<button name="submitaction" type="submit" value="transferDataFromLkupTables">Transfer Lookup Tables</button>
					</form>
				</fieldset>
				<?php
			}
			?>
			<fieldset>
				<legend>geoBoundaries Harvesting Tools</legend>
				<ul>
					<li><a href="harvester.php?gbAction=gbListCountries">List All Countries</a></li>
				</ul>
				<?php
				if($gbAction){
					if($gbAction == 'gbListCountries'){
						?>
						<div>
							<div style="float:right;margin-left:15px"><input name="displayRadio" type="radio" onclick="$('.nopoly').hide();" /> Show no polygon only</div>
							<div style="float:right;margin-left:15px"><input name="displayRadio" type="radio" onclick="$('.nodb').hide();" /> Show not in database only</div>
							<div style="float:right;margin-left:15px"><input name="displayRadio" type="radio" onclick="$('.nopoly').show();$('.nodb').show();" /> Show all</div>
						</div>
						<table class="styledtable">
							<tr>
								<th>Name</th><th>ISO</th><th>In Database</th><th>Has Polygon</th><th>ID</th><th>Canonical</th><th>License</th><th>Region</th><th>Full Link</th><th>Preview Image</th>
							</tr>
							<?php
							$countryList = $geoManager->getGBCountryList();
							foreach($countryList as $iso => $cArr){
								echo '<tr class="'.(isset($cArr['geoThesID'])?'nodb':'').(isset($cArr['polygon'])?' nopoly':'').'">';
								echo '<td><a href="harvester.php?gbAction='.$iso.'">'.$cArr['name'].'</a></td>';
								echo '<td>'.$iso.'</td>';
								echo '<td>'.(isset($cArr['geoThesID'])?'Yes':'No').'</td>';
								echo '<td>'.(isset($cArr['polygon'])?'Yes':'No').'</td>';
								echo '<td>'.$cArr['id'].'</td>';
								echo '<td>'.$cArr['canonical'].'</td>';
								echo '<td>'.$cArr['license'].'</td>';
								echo '<td>'.$cArr['region'].'</td>';
								echo '<td><a href="'.$cArr['link'].'" target="_blank">link</a></td>';
								echo '<td><a href="'.$cArr['img'].'" target="_blank">IMG</a></td>';
								echo '</tr>';
							}
							?>
						</table>
						<?php
					}
					else{
						?>
						<form name="" method="post" action="harvester.php">
							<table class="styledtable">
								<tr>
									<th></th><th>Type</th><th>ID</th><th>In Database</th><th>Has Polygon</th><th>Canonical</th><th>Region</th><th>License</th><th>Full Link</th><th>Preview Image</th>
								</tr>
								<?php
								$geoList = $geoManager->getGBGeoList($gbAction);
								foreach($geoList as $type => $gArr){
									echo '<tr class="'.(isset($gArr['geoThesID'])?'nodb':'').(isset($gArr['polygon'])?' nopoly':'').'">';
									echo '<td><input name="geoid[]" type="checkbox" value="'.$gArr['id'].'" '.(isset($gArr['polygon'])?'DISABLED':'').' /></td>';
									echo '<td>'.$type.'</td>';
									echo '<td>'.$gArr['id'].'</td>';
									echo '<td>'.(isset($gArr['geoThesID'])?'Yes':'No').'</td>';
									echo '<td>'.(isset($gArr['polygon'])?'Yes':'No').'</td>';
									echo '<td>'.$gArr['canonical'].'</td>';
									echo '<td>'.$gArr['region'].'</td>';
									echo '<td>'.$gArr['license'].'</td>';
									echo '<td><a href="'.$gArr['link'].'" target="_blank">link</a></td>';
									echo '<td><a href="'.$gArr['img'].'" target="_blank">IMG</a></td>';
									echo '</tr>';
								}
								?>
							</table>
							<input name="gbAction" type="hidden" value="<?php echo $gbAction; ?>" />
							<button name="submitaction" type="submit" value="submitCountryForm">Add Boundaries</button>
						</form>
						<?php
					}
				}
			?>
			</fieldset>
			<?php
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>