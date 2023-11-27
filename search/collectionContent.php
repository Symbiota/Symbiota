<div class="specimen-header-margin">
	<h2><?php echo $LANG['SPECIMEN_COLLECTIONS'] ?></h2>
</div>
	<button id="collection-accept-button" class="btn" style="width:auto !important">Accept and close</button>
	<div class="select-deselect-input">
		<input data-chip="All Collections" id="dballcb" name="db[]" class="specobs all-neon-colls" value='all' type="checkbox" onclick="selectAll(this);" checked />
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
				foreach($categoryArr as $catid => $catEl){
					include('./singleCollectionGroupDetails.php');
					$collCnt++;
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
					include('./singleCollectionGroupDetails.php');
					$collCnt++;
				}
				?>
			</section>
		<?php
		}
	?>