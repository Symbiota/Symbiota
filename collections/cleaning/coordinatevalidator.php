<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCleaner.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/cleaning/coordinatevalidator.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT.'/content/lang/collections/cleaning/coordinatevalidator.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/collections/cleaning/coordinatevalidator.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collId = array_key_exists('collid',$_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : false;
$queryCountry = array_key_exists('q_country',$_REQUEST)?$_REQUEST['q_country']:'';
$ranking = array_key_exists('ranking',$_REQUEST)?$_REQUEST['ranking']:'';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$targetRank = array_key_exists('targetRank',$_REQUEST) ? filter_var($_REQUEST['targetRank'], FILTER_SANITIZE_NUMBER_INT) : false;

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/cleaning/coordinatevalidator.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

//Sanitation
if($action && !preg_match('/^[a-zA-Z\s]+$/',$action)) $action = '';

$cleanManager = new OccurrenceCleaner();
if($collId) $cleanManager->setCollId($collId);
$collMap = $cleanManager->getCollMap();

$statusStr = '';
$isEditor = 0;
$coordRankingArr = [];

if($IS_ADMIN || ($collId && array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collId,$USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}

?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?>Validator</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script>
		function selectAllCollections(cb,classNameStr){
			boxesChecked = true;
			if(!cb.checked){
				boxesChecked = false;
			}
			var dbElements = document.getElementsByName("collid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(classNameStr == '' || dbElement.className.indexOf(classNameStr) > -1){
					dbElement.checked = boxesChecked;
				}
			}
		}

		function checkSelectCollidForm(f){
			var dbElements = document.getElementsByName("collid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(dbElement.checked) return true;
			}
		   	alert("Please select at least one collection!");
	      	return false;
		}
	</script>
	<style type="text/css">
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../../sitemap.php">Sitemap</a> &gt;&gt;
		<b><a href="coordinatevalidator.php?collid=<?= htmlspecialchars($collId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>">Coordinate Validator</a></b>
	</div>
	<!-- inner text -->
	<div role="main" id="innertext" style="display: flex; gap: 1rem; flex-direction: column; margin-bottom: 1rem">
		<h1 class="page-heading" style="margin-bottom: 0"><?php echo $LANG['COOR_VALIDATOR']; ?></h1>
		<?php if($statusStr): ?>
			<hr/>
			<div style="margin:20px;color:<?php echo (substr($statusStr,0,5)=='ERROR'?'red':'green');?>">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
		<?php endif ?>

		<?php if($isEditor && $collId): ?>
				<div>
					<p style="margin: 0">
						<?= $LANG['TOOL_DESCRIPTION'] ?>
					</p>
					<ul>
						<li><?= $LANG['COORDINATES_OUTSIDE_COUNTY_LIMITS'] ?></li>
						<li><?= $LANG['WRONG_COUNTY_ENTERED'] ?></li>
						<li><?= $LANG['COUNTY_MISSPELLED'] ?></li>
					</ul>
					<p style="margin: 0">
						<?= $LANG['VALIDATION_COUNT_LIMIT'] ?>
					</p>
				<?php if($dateLastVerified = $cleanManager->getDateLastVerifiedByCategory('coordinate')): ?>
					<p style="margin: 0"><b>Last Verification Date:</b> <?= $dateLastVerified ?></p>
				<?php endif ?>
				</div>

				<?php if($action) {
					echo '<fieldset style="padding:20px">';
				if($action == 'Validate Coordinates'){
					// Loop Until max or finished results
					if(is_numeric($targetRank)) {
						$cleanManager->removeVerificationByCategory('coordinate', $targetRank);
					}
					$total_proccessed = 0;
					$start = time();
					$TARGET_OFFSET = 1000;
					$MAX_VALIDATION_BATCH = 50000;
					for($offset = 0; $offset < $MAX_VALIDATION_BATCH; $offset += $TARGET_OFFSET) {
						$count = count($cleanManager->verifyCoordAgainstPoliticalV2(
							[],
							$_REQUEST['populate_country'] ?? false,
							$_REQUEST['populate_stateProvince'] ?? false,
							$_REQUEST['populate_county'] ?? false,
						));
						$total_proccessed += $count;

						if($count != $TARGET_OFFSET) {
							break;
						}
					}
					echo $total_proccessed . ' records took ' . time() - $start. ' seconds';
					}
					elseif($action == 'displayranklist'){
						echo '<legend><b>Specimen with rank of '.$ranking.'</b></legend>';
						$occurList = array();
						if($action == 'displayranklist'){
							$occurList = $cleanManager->getOccurrenceRankingArr('coordinate', $ranking);
						}
						if($occurList){
							foreach($occurList as $occid => $inArr){
								echo '<div>';
								echo '<a href="../editor/occurrenceeditor.php?occid=' . htmlspecialchars($occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank">' . htmlspecialchars($occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>';
								echo ' - checked by '.$inArr['username'].' on '.$inArr['ts'];
								echo '</div>';
							}
						}
						else{
							echo '<div style="margin:30px;font-weight:bold;font-size:150%">Nothing to be displayed</div>';
						}
					}
					echo '</fieldset>';
				}?>
				<form action="coordinatevalidator.php" method="post">
					<?php
						$coordRankingArr = $cleanManager->getRankingStats('coordinate');
					?>
					<div style="margin-bottom: 1rem">
						<div style="font-weight:bold"><?= $LANG['RANKING_STATISTICS']?></div>

						<table class="styledtable">
						<tr>
							<th><?= $LANG['RANKING'] ?></th>
							<th><?= $LANG['STATUS'] ?></th>
							<th><?= $LANG['COUNT'] ?></th>
							<th><?= 'Re-Verify' ?></th>
						</tr>
						<?php foreach($coordRankingArr as $rank => $cnt):?>
							<tr>
								<td><?= $rank ?></td>
								<td><?= (is_numeric($rank)? $cleanManager->coordinateRankingToText($rank): $LANG['UNVERIFIED']) ?></td>
								<td><?= number_format($cnt) ?></td>
								<td style="width: 1%"><button <?= $cnt > 0? '' : 'disabled="true"'?> type="submit" name="targetRank" value="<?= $rank ?>" class="button">Re-Verify</button></td>
							</tr>
						<?php endforeach ?>
						</table>
					</div>

					<!-- <input name="q_country" type="hidden" value="<?= $country ?? ''?>" /> -->
					<input name="collid" type="hidden" value="<?= $collId; ?>" />
					<input name="action" type="hidden" value="Validate Coordinates" />

					<div>
						<input type="checkbox" id="populate_country" name="populate_country" checked />
					<label for="populate_country"><?= $LANG['POPULATE_COUNTRY']?></label>
					</div>

					<div>
						<input type="checkbox" id="populate_stateProvince" name="populate_stateProvince" checked />
						<label for="populate_stateProvince"><?= $LANG['POPULATE_STATE_PROVINCE']?></label>
					</div>

					<div>
						<input type="checkbox" id="populate_county" name="populate_county" checked />
						<label for="populate_county"><?= $LANG['POPULATE_COUNTY'] ?></label>
					</div>

					<button type="submit" <?= ($coordRankingArr['unverified'] ?? 0) === 0? 'disabled="true"': '' ?> ><?= $LANG['VALIDATE_ALL_COORDINATES'] ?></button>
					<?php if( ($coordRankingArr['unverified'] ?? 0) === 0 ): ?>
						<p><?= $LANG['ALL_COORDINATES_VALIDATED'] ?></p>
					<?php endif ?> 
				</form>

			<?php
				$countryArr = $cleanManager->getUnverifiedByCountry();
				arsort($countryArr);
			?>

			<?php if(count($countryArr)): ?>
				<div>
					<div style="font-weight:bold">Non-verified listed by Country</div>
					<table class="styledtable">
						<tr>
							<th>Country</th>
							<th>Count</th>
						</tr>

						<?php foreach($countryArr as $country => $cnt) :?>
							<tr>
							<td>
								<div style="display: flex; align-items: center; gap: 0.5rem">
								<?= $country ?>
								<a style="display: flex; flex-grow: 1; justify-content: end" href="../editor/occurrencetabledisplay.php?collid=<?= $collId ?>&ffcountry=<?= htmlspecialchars($country, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE)?>" target="_blank">
									<img src="../../images/list.png"/>
								</a>
								</div>
							</td>
							<td><?= number_format($cnt)?></td>
							</tr>
						<?php endforeach ?>
					</table>
				</div>
			<?php endif ?>
		<?php elseif(!$collId): ?>
			<h2>You are not authorized to access this page</h2>
		<?php else: ?>
			<h2>You are not authorized to access this page</h2>
		<?php endif ?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
