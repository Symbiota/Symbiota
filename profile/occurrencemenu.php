<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
if($LANG_TAG != 'en' && !file_exists($SERVER_ROOT . '/content/lang/profile/occurrencemenu.' . $LANG_TAG . '.php')) $LANG_TAG = 'en';
include_once($SERVER_ROOT . '/content/lang/profile/occurrencemenu.' . $LANG_TAG . '.php');
header('Content-Type: text/html; charset=' . $CHARSET);
unset($_SESSION['editorquery']);

$specHandler = new ProfileManager();
$specHandler->setUid($SYMB_UID);

$genArr = array();
$cArr = array();
$oArr = array();
$collArr = $specHandler->getCollectionArr();
foreach($collArr as $id => $collectionArr){
	if($collectionArr['colltype'] == 'General Observations') $genArr[$id] = $collectionArr;
	elseif($collectionArr['colltype'] == 'Preserved Specimens') $cArr[$id] = $collectionArr;
	elseif($collectionArr['colltype'] == 'Observations') $oArr[$id] = $collectionArr;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
	<head>
		<title><?php echo $DEFAULT_TITLE . ' ' . $LANG['OCCURRENCE_MENU'];?></title>	
		<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/symbiota/main.css" type="text/css" rel="stylesheet">
</head>
	<div style="margin:10px;">
	<?php
	if($SYMB_UID){
		if(!$collArr) echo '<div style="margin:40px 15px;font-weight:bold">' . $LANG['NO_PROJECTS'] . '</div>';
		foreach($genArr as $collId => $secArr){
			$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode']?'-' . $secArr['collectioncode']:'') . ')';
			?>
			<section class="fieldset-like">
				<h1>
					<span>
						<?php echo $cName; ?>
					</span>
				</h1>
				<div style="margin-left:10px">
					<?php
					echo $LANG['TOTAL_RECORDS'] . ': ' . $specHandler->getPersonalOccurrenceCount($collId);
					?>
				</div>
				<ul>
					<li>
						<a href="../collections/editor/occurrencetabledisplay.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['DISPLAY_ALL'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<li>
						<a href="../collections/editor/occurrencetabledisplay.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>&displayquery=1">
							<?php echo htmlspecialchars($LANG['SEARCH_RECORDS'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<li>
						<a href="../collections/editor/occurrenceeditor.php?gotomode=1&collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['ADD_RECORD'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<li>
						<a href="../collections/reports/labelmanager.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['PRINT_LABELS'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<li>
						<a href="../collections/reports/annotationmanager.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['PRINT_ANNOTATIONS'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<li>
						<a href="../collections/editor/observationsubmit.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['SUBMIT_OBSERVATION'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<li>
						<a href="../collections/editor/editreviewer.php?display=1&collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['REVIEW_EDITS'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<?php
					if (!empty($ACTIVATE_DUPLICATES)) {
						?>
						<li>
							<a href="../collections/datasets/duplicatemanager.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
								<?php echo htmlspecialchars($LANG['DUP_CLUSTER'], HTML_SPECIAL_CHARS_FLAGS); ?>
							</a>
						</li>
						<?php
					}
					?>
					<li>
						<a href="#" onclick="newWindow = window.open('personalspecbackup.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>','bucollid','scrollbars=1,toolbar=0,resizable=1,width=400,height=200,left=20,top=20');">
							<?php echo htmlspecialchars($LANG['DOWNLOAD_BACKUP'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					<li>
						<a href="../collections/misc/commentlist.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['VIEW_COMMENTS'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
						<?php if($commCnt = $specHandler->unreviewedCommentsExist($collId)) echo '- <span style="color:orange">' . $commCnt . ' ' . $LANG['UNREVIEWED'] . '</span>'; ?>
					</li>
					<!--
					<li>
						<a href="../collections/cleaning/index.php?collid=<?php echo htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS); ?>">
							<?php echo htmlspecialchars($LANG['DATA_CLEANING'], HTML_SPECIAL_CHARS_FLAGS); ?>
						</a>
					</li>
					-->
				</ul>
			</section>
			<?php
		}
		if($cArr){
			?>
			<section class="fieldset-like">
				<h1>
					<span>
						<?php echo $LANG['COL_MANAGE']; ?>
					</span>
				</h1>
				<ul>
					<?php
					foreach($cArr as $collId => $secArr){
						$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode'] ? '-' . $secArr['collectioncode'] : '') . ')';
						echo '<li><a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . htmlspecialchars($cName, HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
					}
					?>
				</ul>
			</section>
			<?php
		}
		if($oArr){
			?>
			<section class="fieldset-like">
				<h1><span><?php echo $LANG['OBS_MANAGEMENT'] ?></span></h1>
				<ul>
					<?php
					foreach($oArr as $collId => $secArr){
						$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode'] ? '-' . $secArr['collectioncode'] : '') . ')';
						echo '<li><a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($collId, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . htmlspecialchars($cName, HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
					}
					?>
				</ul>
			</section>
			<?php
		}
		$genAdminArr = array();
		if($genArr && isset($USER_RIGHTS['CollAdmin'])){
			$genAdminArr = array_intersect_key($genArr,array_flip($USER_RIGHTS['CollAdmin']));
			if($genAdminArr){
				?>
				<section class="fieldset-like">
					<h1><span><?php echo $LANG['GEN_OBS_ADMIN'] ?></span></h1>
					<ul>
						<?php
						foreach($genAdminArr as $id => $secArr){
							$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode'] ? '-' . $secArr['collectioncode'] : '') . ')';
							echo '<li><a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($id, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . htmlspecialchars($cName, HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
						}
						?>
					</ul>
				</section>
				<?php
			}
		}
		?>
		<section class="fieldset-like">
			<h1><span><?php echo $LANG['MISC_TOOLS'] ?></span></h1>
			<ul>
				<li><a href="../collections/datasets/index.php"><?php echo htmlspecialchars($LANG['DATASET_MANAGEMENT'], HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
				<?php
				if((count($cArr)+count($oArr)) > 1){
					?>
					<li><a href="../collections/georef/batchgeoreftool.php"><?php echo htmlspecialchars($LANG['CROSS_COL_GEOREF'], HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
					<?php
					if(isset($USER_RIGHTS['CollAdmin']) && count(array_diff($USER_RIGHTS['CollAdmin'],array_keys($genAdminArr))) > 1){
						?>
						<li><a href="../collections/cleaning/taxonomycleaner.php"><?php echo htmlspecialchars($LANG['CROSS_COL_TAXON'], HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
						<?php
					}
				}
				?>
			</ul>
		</section>
		<?php
	}
	?>
	</div>
</html>