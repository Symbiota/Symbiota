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

function renderValidateCoordinates($cleanManager, $targetRank) {
	global $LANG, $collId, $CLIENT_ROOT, $revalidateAll;

	// Loop Until max or finished results
	if(is_numeric($targetRank)) {
		$cleanManager->removeVerificationByCategory('coordinate', $targetRank);
	} elseif($revalidateAll) {
		$cleanManager->removeVerificationByCategory('coordinate');
	}

	$countryArr = $cleanManager->getUnverifiedByCountry();

	$countryIdMap = [];
	foreach($countryArr as $country => $row) {
		if(array_key_exists($row->geoThesID, $countryArr)) {
			$countryIdMap[$row->geoThesID]['countries'][] = $country;
		} else {
			$countryIdMap[$row->geoThesID] = [
				'countries' => [ $country ],
				'geoThesID' => $row->geoThesID
			];
		}
	}
	if(count($countryIdMap)) {
		$countryIdMap = array_values($countryIdMap);
	}

	// Empty Optimization to verify first then cleanup unidentifiable
	array_push($countryIdMap, ['countries' => [],'geoThesID' => false]);

	$total_proccessed = 0;
	$countryPopulatedCount = 0;
	$statePopulatedCount = 0;
	$countyPopulatedCount = 0;
	$shouldPopulate = $_REQUEST['populate_country'] ?? $_REQUEST['populate_stateProvince'] ?? $_REQUEST['populate_county'] ?? false;

	$start = time();
	$TARGET_OFFSET = 1000;
	$MAX_VALIDATION_BATCH = 50000;

	foreach($countryIdMap as $countryGroups) {
		for($offset = 0; $offset < $MAX_VALIDATION_BATCH; $offset += $TARGET_OFFSET) {
			$validation_array = $cleanManager->verifyCoordAgainstPoliticalV2(
				$countryGroups['countries'],
				$countryGroups['geoThesID']? [$countryGroups['geoThesID']]: [],
				$_REQUEST['populate_country'] ?? false,
				$_REQUEST['populate_stateProvince'] ?? false,
				$_REQUEST['populate_county'] ?? false,
			);
			if($shouldPopulate) {
				foreach($validation_array as $occurrence) {
					if($occurrence['populatedCountry'] ?? false) {
						$countryPopulatedCount++;
					} else if($occurrence['populatedStateProvince'] ?? false) {
						$statePopulatedCount++;
					} else if($occurrence['populatedCounty'] ?? false) {
						$countyPopulatedCount++;
					}
				}
			}
			$count = count($validation_array);

			$total_proccessed += $count;

			if($MAX_VALIDATION_BATCH <= $total_proccessed) {
				// Breaks both loops
				break 2;
			} else if ($count != $TARGET_OFFSET) {
				// Breaks to next geoThesID
				break;
			}
		}
	}

	// This is a clean up step for data evaluated to be
	// missing but should have generated a match based
	// on Country, state/province, and county values
	$cleanManager->findFailedVerificationsOnKnownPolyons();

	$baseReviewLink = ($CLIENT_ROOT ? '/' . $CLIENT_ROOT: '') .
		'/collections/editor/editreviewer.php?&collid=' .
		$collId;

	$linkBuilder = fn($url, $title) => '<a target="blank" href="' . $url . '">' . $title . '</a>';
	echo $total_proccessed . ' ' . $LANG['RECORDS_TOOK'] . ' ' . time() - $start. ' ' . $LANG['SEC'] . '<br/>';
	if($shouldPopulate) {
		echo $linkBuilder($baseReviewLink . '&ffieldname=country', $countryPopulatedCount) . ' ' . $LANG['COUNTRY_POPULATED'] . '<br/>';
		echo $linkBuilder($baseReviewLink . '&ffieldname=stateprovince', $statePopulatedCount) . ' ' . $LANG['STATE_PROVINCE_POPULATED'] . '<br/>';
		echo $linkBuilder($baseReviewLink. '&ffieldname=county', $countyPopulatedCount) . ' ' . $LANG['COUNTY_POPULATED'] . '<br/>';
	}
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
	<script type="text/javascript">
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
				<p>
					<?= $LANG['RECOMMEND_USE_GEOGRAPHIC_CLEANER'] ?>
				</p>
				<p>
					<?= $LANG['TOOL_DESCRIPTION'] ?>
				</p>
				<p>
					<?= $LANG['VALIDATION_COUNT_LIMIT'] ?>
				</p>
				<?php if($dateLastVerified = $cleanManager->getDateLastVerifiedByCategory('coordinate')): ?>
					<p style="margin: 0"><b><?= $LANG['LAST_VER_DATE'] ?>:</b> <?= $dateLastVerified ?></p>
				<?php endif ?>
			</div>

			<?php if($action): ?>
			<fieldset style="padding:20px">
			<?php if($action == 'Validate Coordinates'): ?>
				<?php renderValidateCoordinates($cleanManager, $targetRank); ?>
			<?php elseif($action == 'displayranklist'): ?>
				<legend><b> <?= $LANG['SPEC_RANK_OF'] . ' ' . $ranking ?></b></legend>
				<?php if($action == 'displayranklist'): ?>
				<?php foreach($cleanManager->getOccurrenceRankingArr('coordinate', $ranking) as $occid => $inArr): ?>
					<div>
						<a href="../editor/occurrenceeditor.php?occid=<?= htmlspecialchars($occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE)?>" target="_blank">
							<? htmlspecialchars($occid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>
						</a>
						<?= ' - ' . $LANG['CHECKED_BY'] . ' ' . $inArr['username'] . ' on ' . $inArr['ts'] ?>
					</div>
				<?php endforeach ?>
				<?php else: ?>
					<div style="margin:30px;font-weight:bold;font-size:150%"><?= $LANG['NOTHING_TO_DISPLAY'] ?> </div>
				<?php endif ?>
			<?php endif ?>
			</fieldset>
			<?php endif ?>

			<form action="coordinatevalidator.php" method="post">
				<?php
					//$coordRankingArr = $cleanManager->getRankingStats('coordinate');
					$unverifiedCount = $cleanManager->getUnverifiedCount('coordinate');
					$questionableCounts = $cleanManager->getQuestionableCoordinateCounts()
				?>
				<?php if(count($questionableCounts) > 0): ?>
				<div style="margin-bottom: 1rem">
					<div style="font-weight:bold"><?= $LANG['RANKING_STATISTICS']?></div>
					<table class="styledtable">
					<tr>
						<th><?= 'Issue' ?></th>
						<th><?= 'Questionable Records' ?></th>
					</tr>
					<?php foreach($questionableCounts as $rank => $cnt):?>
						<tr>
							<td><?= (is_numeric($rank)? $cleanManager->questionableRankText($rank): $LANG['UNVERIFIED']) ?></td>
						<td>
							<a href="../editor/occurrencetabledisplay.php?collid=<?= $collId ?>&reset&coordinateRankingIssue=<?= $rank?>" target="blank"><?= number_format($cnt) ?></a>
						</td>
						</tr>
					<?php endforeach ?>
					</table>
				</div>
				<?php endif ?>

				<input name="collid" type="hidden" value="<?= $collId; ?>" />
				<input name="action" type="hidden" value="Validate Coordinates" />

				<div>
				<input type="checkbox" id="populate_country" name="populate_country" <?= ($_REQUEST['populate_country'] ?? false) ? 'checked': '' ?>/>
				<label for="populate_country"><?= $LANG['POPULATE_COUNTRY']?></label>
				</div>

				<div>
					<input type="checkbox" id="populate_stateProvince" name="populate_stateProvince" <?= ($_REQUEST['populate_stateProvince'] ?? false)? 'checked': '' ?>/>
					<label for="populate_stateProvince"><?= $LANG['POPULATE_STATE_PROVINCE']?></label>
				</div>

				<div>
					<input type="checkbox" id="populate_county" name="populate_county" <?= ($_REQUEST['populate_county'] ?? false) ? 'checked': '' ?>/>
					<label for="populate_county"><?= $LANG['POPULATE_COUNTY'] ?></label>
				</div>

				<?php if( $unverifiedCount === 0 ): ?>
					<button name="revalidateAll"><?= $LANG['RE-VALIDATE_ALL_COORDINATES'] ?></button>
				<?php else: ?>
				<button type="submit">
					<?= $LANG['VALIDATE_ALL_COORDINATES'] ?>
					(<?= $unverifiedCount . ' ' . $LANG['UNVERIFIED_RECORDS'] ?>)
				</button>
				<?php endif ?> 

				<?php
					$countryArr = $cleanManager->getUnverifiedByCountry();
					arsort($countryArr);
				?>
				<?php if(count($countryArr)): ?>
					<div>
						<div style="font-weight:bold; margin-top:1rem"><?= $LANG['UNVERIFIED_BY_COUNTRY'] ?></div>
						<table class="styledtable">
							<tr>
								<th><?= $LANG['COUNTRY'] ?></th>
								<th><?= $LANG['COUNT'] ?></th>
							</tr>

							<?php foreach($countryArr as $country => $obj) :?>
								<tr>
								<td>
									<div style="display: flex; align-items: center; gap: 0.5rem">
									<?= $country ?>
									<a style="display: flex; flex-grow: 1; justify-content: end" href="../editor/occurrencetabledisplay.php?collid=<?= $collId ?>&ffcountry=<?= htmlspecialchars($country, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE)?>" target="_blank">
										<img src="../../images/list.png" title="<?= $LANG['VIEW_SPECIMENS'] ?>"/>
									</a>
									</div>
								</td>
								<td><?= number_format($obj->cnt)?></td>
								</tr>
							<?php endforeach ?>
						</table>
					</div>
				<?php endif ?>

			</form>
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
