<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCleaner.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/cleaning/index.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

//Sanitation
if(!is_numeric($collid)) $collid = 0;

$cleanManager = new OccurrenceCleaner();
if($collid) $cleanManager->setCollId($collid);
$collMap = current($cleanManager->getCollMap());

$isEditor = 0;
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])) || ($collMap['colltype'] == 'General Observations')){
	$isEditor = 1;
}

//If collection is a general observation project, limit to User
if($collMap['colltype'] == 'General Observations'){
	$cleanManager->setObsUid($SYMB_UID);
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Occurrence Cleaner</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<style type="text/css">
		table.styledtable {  width: 300px }
		table.styledtable td { white-space: nowrap; }
		h3 { text-decoration:underline }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
		<b>Data Cleaning Module</b>
	</div>

	<!-- inner text -->
	<div id="innertext" style="background-color:white;">
		<?php
		if($isEditor){
			echo '<h2>'.$collMap['collectionname'].' ('.$collMap['code'].')</h2>';
			?>
			<div style="color:orange;margin:20px 0px">Downloading a backup of your collection data before running any batch updates is strongly recommended</div>
			<?php
			if($collMap['colltype'] != 'General Observations'){
				?>
				<h3>Duplicate Records</h3>
				<div style="margin:0px 0px 40px 15px;">
					<div>
						These tools will assist in searching this collection of records for duplicate records of the same specimen.
						If duplicate records exist, this feature offers the ability to merge record values, images,
						and data relationships into a single record.
					</div>
					<fieldset style="margin:10px 0px;padding:5px;width:550px">
						<legend style="font-weight:bold"><b>List Duplicates based on...</b></legend>
						<ul>
							<li>
								<a href="duplicatesearch.php?collid=<?php echo $collid; ?>&action=listdupscatalog">
									Catalog Numbers
								</a>
							</li>
							<li>
								<a href="duplicatesearch.php?collid=<?php echo $collid; ?>&action=listdupsothercatalog">
									Other Catalog Numbers
								</a>
							</li>
							<!--
							<li>
								<a href="duplicatesearch.php?collid=<?php echo $collid; ?>&action=listdupsrecordedby">
									Collector/Observer and numbers
								</a>
							</li>
							 -->
						</ul>
					</fieldset>
				</div>
				<?php
			}
			?>

			<h3>Political Geography</h3>
			<div style="margin:0px 0px 40px 15px;">
				<div>
					These tools help standardize country, state/province, and county designations.
					They are also useful for locating and correcting misspelled geographical political units,
					and even mismatched units, such as a state designation that does not match the wrong country.
				</div>
				<fieldset style="margin:10px 0px;padding:5px;width:550px">
					<legend style="font-weight:bold">Statistics and Action Panel</legend>
					<ul>
						<li>
							<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&stat=geography#geographystats" target="_blank">Geographic Distributions</a>
						</li>
						<li>
							<a href="politicalunits.php?collid=<?php echo $collid; ?>">Geography Cleaning Tools</a>
						</li>
					</ul>
				</fieldset>
			</div>
<!--
			<h3>Specimen Coordinates</h3>
			<div style="margin:0px 0px 40px 15px;">
				<div>
					These tools are to aid collection managers in verifying, ranking, and managing coordinate information associated with occurrence records.
				</div>
				<fieldset style="margin:10px 0px;padding:5px;width:550px">
					<legend style="font-weight:bold">Statistics and Action Panel</legend>
					<ul>
						<?php
						$statsArr = $cleanManager->getCoordStats();
						?>
						<li>Georeferenced: <?php echo $statsArr['coord']; ?>
							<?php
							if($statsArr['coord']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NOTNULL" style="margin-left:5px;" title="Open Editor" target="_blank">
									<img src="../../images/edit.png" style="width:10px" />
								</a>
								<?php
							}
							?>
						</li>
						<li>Lacking coordinates: <?php echo $statsArr['noCoord']; ?>
							<?php
							if($statsArr['noCoord']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL" style="margin-left:5px;" title="Open Editor" target="_blank">
									<img src="../../images/edit.png" style="width:10px" />
								</a>
								<a href="../georef/batchgeoreftool.php?collid=<?php echo $collid; ?>" style="margin-left:5px;" title="Open Batch Georeference Tool" target="_blank">
									<img src="../../images/edit.png" style="width:10px" /><span style="font-size:70%;margin-left:-3;">b-geo</span>
								</a>
								<?php
							}
							?>
						</li>
						<li style="margin-left:15px">Lacking coordinates with verbatim coordinates: <?php echo $statsArr['noCoord_verbatim']; ?>
							<?php
							if($statsArr['noCoord_verbatim']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL&q_customfield2=verbatimcoordinates&q_customtype2=NOTNULL" style="margin-left:5px;" title="Open Editor" target="_blank">
									<img src="../../images/edit.png" style="width:10px" />
								</a>
								<?php
							}
							?>
						</li>
						<li style="margin-left:15px">Lacking coordinates without verbatim coordinates: <?php echo $statsArr['noCoord_noVerbatim']; ?>
							<?php
							if($statsArr['noCoord_noVerbatim']){
								?>
								<a href="../editor/occurrencetabledisplay.php?collid=<?php echo $collid; ?>&occindex=0&q_catalognumber=&q_customfield1=decimallatitude&q_customtype1=NULL&q_customfield2=verbatimcoordinates&q_customtype2=NULL" style="margin-left:5px;" title="Open Editor" target="_blank">
									<img src="../../images/edit.png" style="width:10px" />
								</a>
								<?php
							}
							?>
						</li>
						<li>
							<a href="coordinatevalidator.php?collid=<?php echo $collid; ?>">Verify coordinates against political boundaries</a>
						</li>
					</ul>
				</fieldset>
			</div>
 -->
			<h3>Taxonomy</h3>
			<div style="margin:0px 0px 40px 15px;">
				<div>
					These tools are meant to aid in locating and fixing taxonomic errors and inconsistencies.
				</div>
				<fieldset style="margin:10px 0px;padding:5px;width:550px">
					<legend style="font-weight:bold">Statistics and Action panel</legend>
					<ul>
						<li><a href="taxonomycleaner.php?collid=<?php echo $collid; ?>">Analyze taxonomic names...</a></li>
						<li><a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&stat=taxonomy#taxonomystats">Taxonomic Distributions...</a></li>
						<?php
						if($cleanManager->hasDuplicateClusters()){
							echo '<li><a href="../datasets/duplicatemanager.php?collid='.$collid.'&dupedepth=3&action=listdupeconflicts">';
							echo 'Duplicate specimens with potential identification conflicts...';
							echo '</a></li>';
						}
						?>
					</ul>
				</fieldset>
			</div>
			<!--
			<h3>Identification</h3>
			<div style="margin:0px 0px 40px 15px;">
				<div>
					These tools are to aid collection managers in identifications associated with occurrence records.

				</div>
				<div style="margin:15px 0px;color:orange">
					-- IN DEVELOPMENT - more to come soon --
				</div>
				<div>
					<div style="font-weight:bold">Ranking Statistics</div>
					<?php
					/*
					$idRankingArr = $cleanManager->getRankingStats('identification');
					$rankArr = current($idRankingArr);
					echo '<table class="styledtable">';
					echo '<tr><th>Ranking</th><th>Protocol</th><th>Count</th></tr>';
					foreach($rankArr as $rank => $protocolArr){
						foreach($protocolArr as $protocol => $cnt){
							echo '<tr>';
							echo '<td>'.$rank.'</td>';
							echo '<td>'.$protocol.'</td>';
							echo '<td>'.$cnt.'</td>';
							echo '</tr>';
						}
					}
					echo '</table>';
					*/
					?>
				</div>
			</div>
			 -->
			<?php
		}
		else{
			echo '<h2>You are not authorized to access this page</h2>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>