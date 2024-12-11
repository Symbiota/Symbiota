<div class="specimen-header-margin">
	<h2><?php echo $LANG['SPECIMEN_COLLECTIONS'] ?></h2>
</div>
	<div class="select-deselect-input">
		<?php 
			$checkedStatus = $collectionSource == '' ? 'checked' : '';
		?>
		<input data-chip="<?php echo $LANG['ALL_COLLECTIONS'] ?>" id="dballcb" name="db[]" class="specobs all-neon-colls" value='all' type="checkbox" onclick="selectAll(this);" <?php echo $checkedStatus ?> />
		<label for="dballcb">
			<?php echo $LANG['SELECT_DESELECT'] . ' <a href="' . $CLIENT_ROOT .'/collections/misc/collprofiles.php">' . htmlspecialchars($LANG['ALL_COLLECTIONS_CAP'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a>'; ?>
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
		// echo implode(',', $targetCatArr);
		foreach($targetCatArr as $targetCatId => $targetCat){
			echo $targetCat;
			// $shouldExpand = str_replace('-','',$targetCat) == $DEFAULTCATID;
			// echo $shouldExpand;
			// if(!$shouldExpand){
				?>
				<script type="text/javascript">
					console.log('deleteMe got here and the category is: ');
					console.log(<?php echo $targetCat; ?>);
					document.addEventListener("DOMContentLoaded", function(event) { 
						toggleCat(<?php echo $targetCat . '-'; ?>);
					});
				</script>
				<?php
			// }
		}
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
				if(isset($specArr['coll'])){
					$collArr = $specArr['coll'];
					?>
					<table style="float:left;width:80%;">
						<?php
						foreach($collArr as $collid => $cArr){
							include('./singleCollectionWithoutCategoryDetails.php');
							$collCnt++;
						}
						?>
					</table>
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
				if(isset($obsArr['coll'])){
					$collArr = $obsArr['coll'];
					?>
					<table style="float:left;width:80%;">
						<?php
						foreach($collArr as $collid => $cArr){
							include('./singleCollectionWithoutCategoryDetails.php');
							$collCnt++;
						}
						?>
					</table>
					<?php
				}
			?>
			</div>