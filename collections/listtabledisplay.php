<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceListManager.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/listtabledisplay.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT.'/content/lang/collections/listtabledisplay.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/collections/listtabledisplay.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$page = array_key_exists('page',$_REQUEST) ? filter_var($_REQUEST['page'], FILTER_SANITIZE_NUMBER_INT) : 1;
$tableCount= array_key_exists('tablecount',$_REQUEST) ? filter_var($_REQUEST['tablecount'], FILTER_SANITIZE_NUMBER_INT) : 1000;
$sortField1 = array_key_exists('sortfield1',$_REQUEST) ? $_REQUEST['sortfield1'] : '';
$sortField2 = array_key_exists('sortfield2',$_REQUEST) ? $_REQUEST['sortfield2'] : '';
$sortOrder = !empty($_REQUEST['sortorder']) ? 'desc' : '';
$comingFrom =  (array_key_exists('comingFrom', $_REQUEST) ? $_REQUEST['comingFrom'] : '');
if($comingFrom != 'harvestparams' && $comingFrom != 'newsearch'){
	//If not set via a valid input variable, use setting set within symbini
	$comingFrom = !empty($SHOULD_USE_HARVESTPARAMS) ? 'harvestparams' : 'newsearch';
}

$collManager = new OccurrenceListManager();
$searchVar = $collManager->getQueryTermStr();
$searchVar .= '&comingFrom=' . $comingFrom;
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET ?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['COL_RESULTS'] ?></title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
			if($searchVar){
				?>
				sessionStorage.querystr = "<?= $searchVar ?>";
				<?php
			}
			?>
		});
	</script>
	<script src="../js/symb/collections.list.js?ver=1" type="text/javascript"></script>
	<script src="../js/symb/shared.js?ver=1" type="text/javascript"></script>
	<style>
		table.styledtable td { white-space: nowrap; }
		.fieldset-like { margin: 1rem; }
		#sort-div { width: 750px; margin: 20px; margin-bottom:5px; }
		#sort-inner-div { display: flex; }
		#sort-inner-div div { margin-right: 10px; }
		#sort-div label { display: block; }
		#sort-div button { margin: 15px }
	</style>
</head>
<body style="margin-left: 0px; margin-right: 0px;background-color:white;">
	<div>
		<div style="width:65rem;">
			<h1 class="page-heading left-breathing-room-rel" style="margin-bottom: 0px"><?= $LANG['SEARCH_RES_TABLE'] ?></h1>
			<div style="margin-bottom:5px;">
				<div style="float:right;">
					<div style="float:left">
						<button class="icon-button" style="margin:5px;padding:5px;" onclick="toggleElement('#sort-div', 'block')" title="<?= $LANG['DISPLAY_SORT'] ?>" aria-label="<?= $LANG['DISPLAY_SORT'] ?>">
							<img src="<?= $CLIENT_ROOT ?>/images/sort-cream.svg" style="width:1.3em;height:1.3em" alt="<?= $LANG['DISPLAY_SORT'] ?>" >
						</button>
					</div>
					<form action="list.php" method="post" style="float:left">
						<input name="comingFrom" type="hidden" value="<?= $comingFrom; ?>" />
						<input name="sortfield1" type="hidden" value="<?= htmlspecialchars($sortField1, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" />
						<input name="sortfield2" type="hidden" value="<?= htmlspecialchars($sortField2, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>" />
						<input name="sortorder" type="hidden" value="<?= $sortOrder ?>" />
						<input name="searchvar" type="hidden" value="<?= $searchVar ?>" />
						<button type="submit" class="icon-button" style="margin:5px;padding:5px;" title="<?= $LANG['LIST_DISPLAY'] ?>"  aria-label="<?= $LANG['LIST_DISPLAY'] ?>">
							<svg xmlns="http://www.w3.org/2000/svg" style="width:1.3em;height:1.3em" alt="<?= $LANG['LIST_DISPLAY'] ?>" height="24" viewBox="0 -960 960 960" width="24"> <path d="M280-600v-80h560v80H280Zm0 160v-80h560v80H280Zm0 160v-80h560v80H280ZM160-600q-17 0-28.5-11.5T120-640q0-17 11.5-28.5T160-680q17 0 28.5 11.5T200-640q0 17-11.5 28.5T160-600Zm0 160q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520q17 0 28.5 11.5T200-480q0 17-11.5 28.5T160-440Zm0 160q-17 0-28.5-11.5T120-320q0-17 11.5-28.5T160-360q17 0 28.5 11.5T200-320q0 17-11.5 28.5T160-280Z"/></svg>
						</button>
					</form>
					<form action="download/index.php" method="post" style="float:left" onsubmit="targetPopup(this)">
						<button class="icon-button" style="margin:5px;padding:5px;" title="<?= $LANG['DOWNLOAD_SPECIMEN_DATA']; ?>" aria-label="<?= $LANG['DOWNLOAD_SPECIMEN_DATA']; ?>">
							<svg xmlns="http://www.w3.org/2000/svg" style="width:1.3em;height:1.3em" alt="<?= $LANG['DOWNLOAD_SPECIMEN_DATA']; ?>" height="24" viewBox="0 -960 960 960" width="24"> <path d="M480-320 280-520l56-58 104 104v-326h80v326l104-104 56 58-200 200ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"/></svg>
						</button>
						<input name="searchvar" type="hidden" value="<?= $searchVar; ?>" />
						<input name="dltype" type="hidden" value="specimen" />
					</form>
					<div style="float:left">
						<button class="icon-button" style="margin:5px;padding:5px;" onclick="copyUrl()" title="<?= $LANG['COPY_TO_CLIPBOARD'] ?>" aria-label="<?= $LANG['COPY_TO_CLIPBOARD'] ?>">
							<svg xmlns="http://www.w3.org/2000/svg" style="width:1.3em;height:1.3em" alt="<?= $LANG['COPY_TO_CLIPBOARD'] ?>" height="24" viewBox="0 -960 960 960" width="24"> <path d="M360-240q-33 0-56.5-23.5T280-320v-480q0-33 23.5-56.5T360-880h360q33 0 56.5 23.5T800-800v480q0 33-23.5 56.5T720-240H360Zm0-80h360v-480H360v480ZM200-80q-33 0-56.5-23.5T120-160v-560h80v560h440v80H200Zm160-240v-480 480Z"/></svg>
						</button>
					</div>
				</div>
				<div id="sort-div" style="display:<?= ($sortField1?'block':'none') ?>">
					<section class="fieldset-like" style="margin:0px">
						<h2><span><?= $LANG['SORT'] ?></span></h2>
						<form name="sortform" action="listtabledisplay.php" method="post">
							<div id="sort-inner-div">
								<div>
									<label for="sortfield1"><?= $LANG['SORT_BY'] ?>:</label>
									<select name="sortfield1" id="sortfield1">
										<option value=""></option>
										<?php
										$sortFields = array('c.collectionname' => $LANG['COLLECTION'], 'o.catalogNumber' => $LANG['CATALOG_NUMBER'], 'o.family' => $LANG['FAMILY'], 'o.sciname' => $LANG['SCINAME'], 'o.recordedBy' => $LANG['COLLECTOR'],
											'o.recordNumber' => $LANG['NUMBER'], 'o.eventDate' => $LANG['EVENT_DATE'], 'o.country' => $LANG['COUNTRY'], 'o.StateProvince' => $LANG['STATE_PROVINCE'], 'o.county' => $LANG['COUNTY'], 'o.minimumElevationInMeters' => $LANG['ELEVATION']);
										foreach($sortFields as $k => $v){
											echo '<option value="'.$k.'" '.($k==$sortField1?'SELECTED':'').'>'.$v.'</option>';
										}
										?>
									</select>
								</div>
								<div>
									<label for="sortfield2"><?= $LANG['SORT_THEN_BY'] ?>:</label>
									<select name="sortfield2" id="sortfield2">
										<option value=""></option>
										<?php
										foreach($sortFields as $k => $v){
											echo '<option value="'.$k.'" '.($k==$sortField2?'SELECTED':'').'>'.$v.'</option>';
										}
										?>
									</select>
								</div>
								<div>
									<label for="sortorder"> <?= $LANG['SORT_ORDER'] ?>: </label>
									<select id="sortorder" name="sortorder">
										<option value=""><?= $LANG['SORT_ASCENDING'] ?></option>
										<option value="desc" <?= ($sortOrder=="desc"?'SELECTED':''); ?>><?= $LANG['SORT_DESCENDING'] ?></option>
									</select>
								</div>
								<div>
									<input name="searchvar" type="hidden" value="<?= $searchVar ?>" />
									<button name="formsubmit" type="submit" ><?= $LANG['SORT'] ?></button>
								</div>
							</div>
						</form>
					</section>
				</div>
			</div>
			<div style="clear:both;">
				<?php
				$searchVar .= '&sortfield1=' . htmlspecialchars($sortField1, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&sortfield2=' . htmlspecialchars($sortField2, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&sortorder=' . $sortOrder;
				$collManager->addSort($sortField1, $sortOrder);
				if($sortField2) $collManager->addSort($sortField2, $sortOrder);
				$recArr = $collManager->getSpecimenMap($page, $tableCount);

				$targetClid = $collManager->getSearchTerm("targetclid");

				$qryCnt = $collManager->getRecordCnt();
				$navStr = '<div style="float:right;">';
				if($page > 1){
					$navStr .= '<a href="listtabledisplay.php?' . $searchVar . '&page=' . ($page-1) . '" title="' . $LANG['PAGINATION_PREVIOUS'] . ' ' . $tableCount . ' ' . $LANG['PAGINATION_RECORDS'] . '">&lt;&lt;</a>';
				}
				$navStr .= ' | ';
				$navStr .= ($page==1 ? 1 : (($page-1)*$tableCount)).'-'.($qryCnt<$tableCount*$page ? $qryCnt : $tableCount*$page).' '.$LANG['PAGINATION_OF'].' '.$qryCnt.' '.$LANG['PAGINATION_RECORDS'];
				$navStr .= ' | ';
				if($qryCnt > ($page*$tableCount)){
					$navStr .= '<a href="listtabledisplay.php?' . $searchVar . '&page=' . ($page+1) . '" title="' . $LANG['PAGINATION_NEXT'] . ' ' . $tableCount . ' ' . $LANG['PAGINATION_RECORDS'] . '">&gt;&gt;</a>';
				}
				$navStr .= '</div>';
				?>
				<div style="float:right">
					<?php
					echo $navStr;
					?>
				</div>
				<div class="navpath">
					<a href="../index.php"><?= $LANG['NAV_HOME'] ?></a> &gt;&gt;
					<?php
					if($comingFrom == 'harvestparams'){
						?>
						<a href="index.php"><?= $LANG['NAV_COLLECTIONS'] ?></a> &gt;&gt;
						<a href="<?= $CLIENT_ROOT . '/collections/harvestparams.php' ?>"><?= $LANG['NAV_SEARCH'] ?></a> &gt;&gt;
						<?php
					}else{
						?>
						<a href="<?= $CLIENT_ROOT . '/collections/search/index.php' ?>"><?= $LANG['NAV_SEARCH'] ?></a> &gt;&gt;
						<?php
					}
					?>
					<b><?= $LANG['SPEC_REC_TAB'] ?></b>
				</div>
			</div>
		</div>
		<form name="occurListForm" method="post" action="datasets/index.php" onsubmit="return validateOccurListForm(this)" target="_blank">
			<?php include('datasetinclude.php'); ?>
			<div id="tablediv">
				<?php
				if($recArr){
					?>
					<div style="clear:both;height:5px;"></div>
					<table class="styledtable" style="font-size:12px;">
						<tr>
							<th><?= $LANG['SYMB_ID'] ?></th>
							<th><?= $LANG['COLLECTION'] ?></th>
							<th><?= $LANG['CATALOG_NUMBER'] ?></th>
							<th><?= $LANG['FAMILY'] ?></th>
							<th><?= $LANG['SCINAME'] ?></th>
							<th><?= $LANG['COLLECTOR'] ?></th>
							<th><?= $LANG['NUMBER'] ?></th>
							<th><?= $LANG['EVENT_DATE'] ?></th>
							<th><?= $LANG['COUNTRY'] ?></th>
							<th><?= $LANG['STATE_PROVINCE'] ?></th>
							<th><?= $LANG['COUNTY'] ?></th>
							<th><?= $LANG['LOCALITY'] ?></th>
							<th><?= $LANG['DEC_LAT'] ?></th>
							<th><?= $LANG['DEC_LONG'] ?></th>
							<th><?= $LANG['HABITAT'] ?></th>
							<th><?= $LANG['SUBSTRATE'] ?></th>
							<th><?= $LANG['ELEVATION'] ?></th>
						</tr>
						<?php
						$recCnt = 0;
						foreach($recArr as $occid => $occArr){
							$isEditor = false;
							if($SYMB_UID && ($IS_ADMIN
									|| (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($occArr['collid'],$USER_RIGHTS['CollAdmin']))
									|| (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($occArr['collid'],$USER_RIGHTS['CollEditor'])))){
								$isEditor = true;
							}
							$collection = $occArr['instcode'];
							if($occArr['collcode']) $collection .= ':'.$occArr['collcode'];
							if($occArr['sciname']) $occArr['sciname'] = '<i>'.$occArr['sciname'].'</i> ';
							?>
							<tr <?= ($recCnt%2?'class="alt"':''); ?>>
								<td>
									<div class="dataset-div" style="float:left;display:none"><input name="occid[]" type="checkbox" value="<?= $occid; ?>" /></div>
									<?php
									echo '<a href="#" onclick="return openIndPU('.$occid.",".($targetClid ? $targetClid : "0").');">'.$occid.'</a> ';
									if($isEditor || ($SYMB_UID && $SYMB_UID == $occArr['obsuid'])){
										echo '<a href="editor/occurrenceeditor.php?occid=' . $occid . '" target="_blank">';
										echo '<img src="../images/edit.png" style="height:1.3em;" title="' . $LANG['EDIT_REC'] . '" />';
										echo '</a>';
									}
									if(isset($occArr['has_image'])) {
											echo '<img src="../images/image.png" style="height:1.3em;margin-left:5px;" title="' . $LANG['HAS_IMAGE'] . '" />';
									}
									if(isset($occArr['has_audio'])) {
											echo '<img src="../images/speaker_thumbnail.png" style="height:1.3em;margin-left:5px;" title="' . $LANG['HAS_AUDIO'] . '" />';
									}
									?>
								</td>
								<td><?= $collection; ?></td>
								<td><?= $occArr['catnum']; ?></td>
								<td><?= $occArr['family']; ?></td>
								<td><?= $occArr['sciname'].($occArr['author']?' '.$occArr['author'] : ''); ?></td>
								<td><?= $occArr['collector']; ?></td>
								<td><?= (array_key_exists('collnum',$occArr) ? $occArr['collnum'] : ''); ?></td>
								<td><?= (array_key_exists('date',$occArr) ? $occArr['date'] : ''); ?></td>
								<td><?= $occArr['country']; ?></td>
								<td><?= $occArr['state']; ?></td>
								<td><?= $occArr['county']; ?></td>
								<td>
								<?php
								$locStr = preg_replace('/<div.*?>.*?<\/div>/', '', $occArr['locality']);
								if(strlen($locStr)>80) $locStr = substr($locStr,0,80).'...';
								echo $locStr;
								?></td>
								<td><?php if(isset($occArr['declat'])) echo $occArr['declat']; ?></td>
								<td><?php if(isset($occArr['declong'])) echo $occArr['declong']; ?></td>
								<td><?php if(isset($occArr['habitat'])) echo ((strlen($occArr['habitat'])>80) ? substr($occArr['habitat'],0,80).'...':$occArr['habitat']); ?></td>
								<td><?php if(isset($occArr['substrate'])) echo ((strlen($occArr['substrate'])>80) ? substr($occArr['substrate'],0,80).'...':$occArr['substrate']); ?></td>
								<td><?= (array_key_exists('elev',$occArr) ? $occArr['elev'] : ''); ?></td>
							</tr>
							<?php
							$recCnt++;
						}
						?>
					</table>
					<div style="clear:both;height:5px;"></div>
					<div style="width:790px;"><?= $navStr; ?></div>
					*<?= $LANG['CLICK_SYMB'] ?>
					<?php
				}
				else{
					echo '<div style="font-weight:bold;font-size:120%;">' . $LANG['NONE_FOUND'] . '</div>';
				}
				?>
			</div>
		</form>
	</div>
</body>
</html>
