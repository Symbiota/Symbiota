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
$revalidateAll = array_key_exists('revalidateAll',$_REQUEST) ? true: false;

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
		<a href="../../index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?emode=1&collid=<?= htmlspecialchars($collId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>"><?= $LANG['COLLECTION_MANAGEMENT'] ?></a> &gt;&gt;
		<b><a href="coordinatevalidator.php?collid=<?= htmlspecialchars($collId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>"><?= $LANG['COOR_VALIDATOR'] ?></a></b>
	</div>
	<!-- inner text -->
	<div role="main" id="innertext" style="display: flex; gap: 1rem; flex-direction: column; margin-bottom: 1rem">
		<h1 class="page-heading" style="margin-bottom: 0"><?= $LANG['COOR_VALIDATOR']; ?></h1>
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
					<p style="margin: 0"><b><?= $LANG['LAST_VER_DATE'] ?>:</b> <?= $dateLastVerified ?></p>
				<?php endif ?>
				</div>

				<?php if($action) {
					echo '<fieldset style="padding:20px">';
				if($action == 'Validate Coordinates'){
					// Loop Until max or finished results
					if(is_numeric($targetRank)) {
						$cleanManager->removeVerificationByCategory('coordinate', $targetRank);	
					} elseif($revalidateAll) {
						$cleanManager->removeVerificationByCategory('coordinate');
					}
					$total_proccessed = 0;
					$country_verified_count = 0;
					$state_verified_count = 0;
					$county_verified_count = 0;

					$start = time();
					$TARGET_OFFSET = 1000;
					$MAX_VALIDATION_BATCH = 50000;
					for($offset = 0; $offset < $MAX_VALIDATION_BATCH; $offset += $TARGET_OFFSET) {
						$validation_array = $cleanManager->verifyCoordAgainstPoliticalV2(
							[],
							$_REQUEST['populate_country'] ?? false,
							$_REQUEST['populate_stateProvince'] ?? false,
							$_REQUEST['populate_county'] ?? false,
						);
						foreach($validation_array as $occurrence) {
							switch ($occurrence['rank'] ?? 0) {
								case OccurrenceCleaner::COUNTRY_VERIFIED:
									$country_verified_count++;
									break;
								case OccurrenceCleaner::STATE_PROVINCE_VERIFIED:
									$state_verified_count++;
									break;
								case OccurrenceCleaner::COUNTY_VERIFIED:
									$county_verified_count++;
									break;
								default:
									break;
							}
						}
						$count = count($validation_array);

						$total_proccessed += $count;
						if($count != $TARGET_OFFSET) {
							break;
						}
					}
					echo $total_proccessed . ' ' . $LANG['RECORDS_TOOK'] . ' ' . time() - $start. ' ' . $LANG['SEC'] . '<br/>';
			/*
					echo $country_verified_count . ' Countries verified <br/>';
					echo $state_verified_count . ' State Verified Count <br/>';
					echo $county_verified_count . ' County Verified Count <br/>';
					link here: http://localhost:8000/collections/editor/editreviewer.php
			*/
					}
					elseif($action == 'displayranklist'){
						echo '<legend><b>' . $LANG['SPEC_RANK_OF'] . ' ' . $ranking . '</b></legend>';
						$occurList = array();
						if($action == 'displayranklist'){
							$occurList = $cleanManager->getOccurrenceRankingArr('coordinate', $ranking);
						}
						if($occurList){
							foreach($occurList as $occid => $inArr){
								echo '<div>';
								echo '<a href="../editor/occurrenceeditor.php?occid=' . htmlspecialchars($occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank">' . htmlspecialchars($occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>';
								echo ' - ' . $LANG['CHECKED_BY'] . ' ' . $inArr['username'] . ' on ' . $inArr['ts'];
								echo '</div>';
							}
						}
						else{
							echo '<div style="margin:30px;font-weight:bold;font-size:150%">' . $LANG['NOTHING_TO_DISPLAY'] . '</div>';
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

						<!-- <table class="styledtable"> -->
						<!-- <tr> -->
						<!-- 	<th><?= $LANG['RANKING'] ?></th> -->
						<!-- 	<th><?= $LANG['STATUS'] ?></th> -->
						<!-- 	<th><?= $LANG['COUNT'] ?></th> -->
						<!-- 	<th><?= $LANG['RE-VERIFY'] ?></th> -->
						<!-- </tr> -->
						<!-- <?php foreach($coordRankingArr as $rank => $cnt):?> -->
						<!-- 	<tr> -->
						<!-- 		<td><?= $rank ?></td> -->
						<!-- 		<td><?= (is_numeric($rank)? $cleanManager->coordinateRankingToText($rank): $LANG['UNVERIFIED']) ?></td> -->
						<!-- 		<td><?= number_format($cnt) ?></td> -->
						<!-- 		<td style="width: 1%"><button <?= $cnt > 0? '' : 'disabled="true"'?> type="submit" name="targetRank" value="<?= $rank ?>" class="button"><?= $LANG['RE-VERIFY'] ?></button></td> -->
						<!-- 	</tr> -->
						<!-- <?php endforeach ?> -->
						<!-- </table> -->

						<table class="styledtable">
						<tr>
							<th><?= 'Issue' ?></th>
							<th><?= 'Questionable Records' ?></th>
						</tr>
						<?php foreach($cleanManager->getQuestionableCoordinateCounts() as $rank => $cnt):?>
							<tr>
								<td><?= (is_numeric($rank)? $cleanManager->questionableRankText($rank): $LANG['UNVERIFIED']) ?></td>
							<td>
								<a href="../editor/occurrencetabledisplay.php?collid=<?= $collId ?>&reset&coordinateRankingIssue=<?= $rank?>" target="blank"><?= number_format($cnt) ?></a>
							</td>
							</tr>
						<?php endforeach ?>
						</table>
					</div>

					<!-- <input name="q_country" type="hidden" value="<?= $country ?? ''?>" /> -->
					<input name="collid" type="hidden" value="<?= $collId; ?>" />
					<input name="action" type="hidden" value="Validate Coordinates" />

					<div>
						<input type="checkbox" id="populate_country" name="populate_country" />
					<label for="populate_country"><?= $LANG['POPULATE_COUNTRY']?></label>
					</div>

					<div>
						<input type="checkbox" id="populate_stateProvince" name="populate_stateProvince" />
						<label for="populate_stateProvince"><?= $LANG['POPULATE_STATE_PROVINCE']?></label>
					</div>

					<div>
						<input type="checkbox" id="populate_county" name="populate_county" />
						<label for="populate_county"><?= $LANG['POPULATE_COUNTY'] ?></label>
					</div>

					<?php if( ($coordRankingArr['unverified'] ?? 0) === 0 ): ?>
						<button name="revalidateAll"><?= $LANG['RE-VALIDATE_ALL_COORDINATES'] ?></button>
					<?php else: ?>
						<button type="submit"><?= $LANG['VALIDATE_ALL_COORDINATES'] ?></button>
					<?php endif ?> 
				</form>

			<?php
				$countryArr = $cleanManager->getUnverifiedByCountry();
				arsort($countryArr);
			?>

			<?php if(count($countryArr)): ?>
				<div>
					<div style="font-weight:bold"><?= $LANG['UNVERIFIED_BY_COUNTRY'] ?></div>
					<table class="styledtable">
						<tr>
							<th><?= $LANG['COUNTRY'] ?></th>
							<th><?= $LANG['COUNT'] ?></th>
						</tr>

						<?php foreach($countryArr as $country => $cnt) :?>
							<tr>
							<td>
								<div style="display: flex; align-items: center; gap: 0.5rem">
								<?= $country ?>
								<a style="display: flex; flex-grow: 1; justify-content: end" href="../editor/occurrencetabledisplay.php?collid=<?= $collId ?>&ffcountry=<?= htmlspecialchars($country, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE)?>" target="_blank">
									<img src="../../images/list.png" title="<?= $LANG['VIEW_SPECIMENS'] ?>"/>
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
			<h2><?= $LANG['NOT_AUTHORIZED'] ?></h2>
		<?php else: ?>
			<h2><?= $LANG['NOT_AUTHORIZED'] ?></h2>
		<?php endif ?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
