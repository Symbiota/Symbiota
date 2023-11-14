<div class="specimen-header-margin">
	<h2><?php echo $LANG['SPECIMEN_COLLECTIONS'] ?></h2>
</div>
<form name="collform1" action="harvestparams.php" method="post" onsubmit="return verifyCollForm(this)">
	<div class="select-deselect-input">
		<input id="dballcb" name="db[]" class="specobs" value='all' type="checkbox" onclick="selectAll(this);" checked />
		<label for="dballcb">
			<?php echo $LANG['SELECT_DESELECT'] . ' <a href="misc/collprofiles.php">' . htmlspecialchars($LANG['ALL_COLLECTIONS_CAP'], HTML_SPECIAL_CHARS_FLAGS) . '</a>'; ?>
		</label>
	</div>
	<?php
		$catSelArr = array();
		$collSelArr = array();
		$displayIcons = true;
		if(isset($_POST['cat'])) $catSelArr = $_POST['cat'];
		if(isset($_POST['db'])) $collSelArr = $_POST['db'];
		$targetCatArr = array();
		$targetCatID = (string)$catId;
		if($targetCatID != '') $targetCatArr = explode(',', $catId);
		elseif($GLOBALS['DEFAULTCATID'] != '') $targetCatArr = explode(',', $GLOBALS['DEFAULTCATID']);
		$collCnt = 0;
		$borderStyle = ('margin:10px;padding:10px 20px;border:inset');
		?>
			<div>
		<?php
		if(isset($specArr['cat'])){
			$categoryArr = $specArr['cat'];
			$collTypeLabel = 'Specimens';
			$uniqGrouping = '';
			?>
			<section class="gridlike-form">
				<?php
				// var_dump($categoryArr); // @TODO deleteMe
				foreach($categoryArr as $catid => $catEl){
					// var_dump($catid);
					// var_dump($catEl);
					include_once('./singleCollectionDetails.php');
				}
				?>
			</section>
		<?php
		}
		$hrAndHeaderText = '<div class="specimen-header-margin"><hr/><h2>' . $LANG['OBSERVATION_COLLECTIONS'] . '</h2></div>';
		if($specArr && $obsArr) echo $hrAndHeaderText;
		if(isset($obsArr['cat'])){
			$categoryArr = $obsArr['cat'];
			$collTypeLabel = 'Observations';
			$uniqGrouping = '';
			?>
			<section class="gridlike-form">
				<?php
				foreach($categoryArr as $catid => $catEl){
					include('./singleCollectionDetails.php');
				}
				?>
			</section>
		<?php
		}
		// $collManager->outputFullCollArr($obsArr, $catId, true, false, 'Observation', 'Observations');
	?>
</form>