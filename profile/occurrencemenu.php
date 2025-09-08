<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/profile/occurrencemenu.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT.'/content/lang/profile/occurrencemenu.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/profile/occurrencemenu.en.php');

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
	</head>
	<div style="margin:10px;">
		<h1 class="page-heading screen-reader-only"><?php echo $LANG['OCCURRENCE_MENU']; ?></h1>
		<?php
		if($SYMB_UID){
			if(!$collArr) echo '<div style="margin:40px 15px;font-weight:bold">' . $LANG['NO_PROJECTS'] . '</div>';
			foreach($genArr as $collId => $secArr){
				$collId = filter_var($collId, FILTER_SANITIZE_NUMBER_INT); //This really is not needed because DB output is an int, but may be needed to satisfy scanners
				$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode']?'-' . $secArr['collectioncode']:'') . ')';
				?>
				<section class="fieldset-like">
					<h2>
						<span>
							<?php echo $cName; ?>
						</span>
					</h2>
					<div style="margin-left:10px">
						<?php
						echo $LANG['TOTAL_RECORDS'] . ': ' . $specHandler->getPersonalOccurrenceCount($collId);
						?>
					</div>
					<ul>
						<li>
							<a href="../collections/editor/occurrencetabledisplay.php?collid=<?= $collId ?>">
								<?= $LANG['DISPLAY_ALL'] ?>
							</a>
						</li>
						<li>
							<a href="../collections/editor/occurrencetabledisplay.php?collid=<?= $collId ?>&displayquery=1">
								<?= $LANG['SEARCH_RECORDS'] ?>
							</a>
						</li>
						<li>
							<a href="../collections/editor/occurrenceeditor.php?gotomode=1&collid=<?= $collId ?>">
								<?= $LANG['ADD_RECORD'] ?>
							</a>
						</li>
						<li>
							<a href="../collections/reports/labelmanager.php?collid=<?= $collId ?>">
								<?= $LANG['PRINT_LABELS'] ?>
							</a>
						</li>
						<li>
							<a href="../collections/reports/annotationmanager.php?collid=<?= $collId ?>">
								<?= $LANG['PRINT_ANNOTATIONS'] ?>
							</a>
						</li>
						<li>
							<a href="../collections/editor/observationsubmit.php?collid=<?= $collId ?>">
								<?= $LANG['SUBMIT_OBSERVATION'] ?>
							</a>
						</li>
						<li>
							<a href="../collections/editor/editreviewer.php?display=1&collid=<?= $collId ?>">
								<?= $LANG['REVIEW_EDITS'] ?>
							</a>
						</li>
						<?php
						if (!empty($ACTIVATE_DUPLICATES)) {
							?>
							<li>
								<a href="../collections/datasets/duplicatemanager.php?collid=<?= $collId ?>">
									<?= $LANG['DUP_CLUSTER'] ?>
								</a>
							</li>
							<?php
						}
						?>
						<li>
							<a href="#" onclick="newWindow = window.open('personalspecbackup.php?collid=<?= $collId ?>','bucollid','scrollbars=1,toolbar=0,resizable=1,width=600,height=400,left=20,top=20');">
								<?= $LANG['DOWNLOAD_BACKUP'] ?>
							</a>
						</li>
						<li>
							<a href="../collections/misc/commentlist.php?collid=<?= $collId ?>">
								<?= $LANG['VIEW_COMMENTS'] ?>
							</a>
							<?php if($commCnt = $specHandler->unreviewedCommentsExist($collId)) echo '- <span style="color:orange">' . $commCnt . ' ' . $LANG['UNREVIEWED'] . '</span>'; ?>
						</li>
						<!--
						<li>
							<a href="../collections/cleaning/index.php?collid=<?= $collId ?>">
								<?= $LANG['DATA_CLEANING'] ?>
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
					<h2>
						<span>
							<?php echo $LANG['COL_MANAGE']; ?>
						</span>
					</h2>
					<ul>
						<?php
						foreach($cArr as $collId => $secArr){
							$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode'] ? '-' . $secArr['collectioncode'] : '') . ')';
							echo '<li><a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($collId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&emode=1">' . htmlspecialchars($cName, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a></li>';
						}
						?>
					</ul>
				</section>
				<?php
			}
			if($oArr){
				?>
				<section class="fieldset-like">
					<h2><span><?php echo $LANG['OBS_MANAGEMENT'] ?></span></h2>
					<ul>
						<?php
						foreach($oArr as $collId => $secArr){
							$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode'] ? '-' . $secArr['collectioncode'] : '') . ')';
							echo '<li><a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($collId, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&emode=1">' . htmlspecialchars($cName, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a></li>';
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
						<h2><span><?php echo $LANG['GEN_OBS_ADMIN'] ?></span></h2>
						<ul>
							<?php
							foreach($genAdminArr as $id => $secArr){
								$cName = $secArr['collectionname'] . ' (' . $secArr['institutioncode'] . ($secArr['collectioncode'] ? '-' . $secArr['collectioncode'] : '') . ')';
								echo '<li><a href="../collections/misc/collprofiles.php?collid=' . htmlspecialchars($id, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&emode=1">' . htmlspecialchars($cName, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a></li>';
							}
							?>
						</ul>
					</section>
					<?php
				}
			}
			?>
			<section class="fieldset-like">
				<h2><span><?php echo $LANG['MISC_TOOLS'] ?></span></h2>
				<ul>
					<li><a href="../collections/datasets/index.php"><?= $LANG['DATASET_MANAGEMENT'] ?></a></li>
					<?php
					if((count($cArr)+count($oArr)) > 1){
						?>
						<li><a href="../collections/georef/batchgeoreftool.php"><?= $LANG['CROSS_COL_GEOREF'] ?></a></li>
						<?php
						if(isset($USER_RIGHTS['CollAdmin']) && count(array_diff($USER_RIGHTS['CollAdmin'],array_keys($genAdminArr))) > 1){
							?>
							<li><a href="../collections/cleaning/taxonomycleaner.php"><?= $LANG['CROSS_COL_TAXON'] ?></a></li>
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
