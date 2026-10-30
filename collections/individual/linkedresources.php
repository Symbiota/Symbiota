<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceIndividual.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/individual/linkedresources');

header('Content-Type: text/html; charset=' . $CHARSET);

$indManager = new OccurrenceIndividual();

$occid = isset($_GET['occid']) ? Sanitize::int($_GET['occid']) : 0;
$tid = array_key_exists('tid', $_REQUEST) ? Sanitize::int($_REQUEST['tid']) : 0;
$clid = array_key_exists('clid', $_REQUEST) ? Sanitize::int($_REQUEST['clid']) : 0;

$indManager->setOccid($occid);
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script>
		function addToCart(){
			let occidInput = <?= $occid ?? 0 ?>;
			$.ajax({
				type: "POST",
				url: "../rpc/addtocart.php",
				dataType: "json",
				data: { occid: occidInput },
			}).done(function (data) {
				const cartSpan = document.getElementById("cart-status-span");
				if(data.status){
					cartSpan.textContent = "<?= $LANG['ADDED_TO_CART'] ?>";
				}
				else{
					if(data.error == "ALREADY_IN_CART"){
						cartSpan.textContent = "<?= $LANG['ALREADY_IN_CART'] ?>";
					}
					else cartSpan.textContent = "<?= $LANG['CART_FAILED'] ?>";
					cartSpan.style.color = "#c2410c";
				}
				setTimeout(() => {
					cartSpan.textContent = "";
				}, 3000);

			});
		}
	</script>
	<style>
		.section-title{  }
		.img-icon{ width: 1.1em; margin:0px; }
		#cart-status-span { transition: all 0.3s ease; font-weight: bold; color: green; }
	</style>
</head>
<body>
	<div id='innertext' style='width:95%;min-height:400px;clear:both;background-color:white;'>
		<fieldset>
			<legend><?= $LANG['SPCHECKREL'] ?></legend>
			<?php
			$vClArr = $indManager->getVoucherChecklists();
			$clArr = $indManager->getChecklists(array_keys($vClArr));
			if($IS_ADMIN || $clArr) echo '<div style="float:right"><a href="#" onclick="toggle(\'voucher-block\');return false"><img src="../../images/add.png" /></a></div>';
			if($vClArr){
				echo '<div class="section-title">' . $LANG['VOUCHEROFFOLLOWING'] . '</div>';
				echo '<ul style="margin:15px 0px 25px 0px;">';
				foreach($vClArr as $vClid => $vClArr){
					echo '<li>';
					echo '<a href="../../checklists/checklist.php?showvouchers=1&clid=' . $vClid . '" target="_blank">' . $vClArr['name'] . '</a>&nbsp;&nbsp;';
					if(isset($USER_RIGHTS['ClAdmin']) && in_array($vClid, $USER_RIGHTS['ClAdmin'])){
						echo '<a href="index.php?formsubmit=deletevoucher&delvouch=' . $vClArr['voucherID'] . '&occid=' . $occid . '" title=' . $LANG['DELVOUCHER'] . ' onclick="return confirm(\"' . $LANG['CONFIRMVOUCHER'] . '\")"><img src="../../images/drop.png" style="width:12px;" /></a>';
					}
					echo '</li>';
				}
				echo '</ul>';
			}
			else{
				echo '<div style="margin:15px 0px">' . $LANG['NOTAVOUCHER'] . '</div>';
			}
			if($IS_ADMIN || $clArr){
				?>
				<div style='margin-top:15px; display: none;'  id="voucher-block" class="voucher-block">
					<fieldset>
						<legend><?= $LANG['NEWVOUCHER'] ?></legend>
						<?php
						if($tid){
							?>
							<div style="margin:10px;">
								<form action="index.php" method="post" onsubmit="return verifyVoucherForm(this);">
									<div>
										<?= $LANG['ADDVOUCHERCHECK'] ?>:
										<input name='occid' type='hidden' value='<?= $occid; ?>'>
										<input name='vtid' type='hidden' value='<?= $tid; ?>'>
										<select id='vclid' name='vclid'>
							  				<option value='0'><?= $LANG['SELECTCHECKLIST'] ?></option>
							  				<option value='0'>--------------------------</option>
							  				<?php
								  			foreach($clArr as $clKey => $clValue){
								  				echo "<option value='".$clKey."' ".($clid==$clKey?"SELECTED":"").">$clValue</option>\n";
											}
											?>
										</select>
									</div>
									<div style='margin:5px 0px 0px 10px;'>
										<?= $LANG['NOTES'] ?>:
										<input name="vnotes" type="text" size="50" title="<?= $LANG['VIEWABLEPUBLIC'] ?>" />
									</div>
									<div style='margin:5px 0px 0px 10px;'>
										<?= $LANG['EDITORNOTES'] ?>:
										<input name="veditnotes" type="text" size="50" title="<?= $LANG['VIEWABLEEDITORS']; ?>">
									</div>
									<div>
										<input name="tabindex" type="hidden" value="2" >
										<button type='submit' name='formsubmit' value="addVoucher"><?= $LANG['ADDVOUCHER'] ?></button>
									</div>
								</form>
							</div>
							<?php
						}
						else{
							?>
							<div style='margin:20px;'>
								<?= $LANG['UNABLETOADD'] ?>
							</div>
							<?php
						}
						?>
					</fieldset>
				</div>
				<?php
			}
			?>
		</fieldset>
		<?php

		//Dataset Management
		$datasetManager = new OccurrenceDataset();
		$datasetArr = $datasetManager->getDatasetsByOccurrence($occid);
		?>
		<fieldset>
			<legend><?= $LANG['DATASETLINKAGES'] ?></legend>
			<?php
			if($SYMB_UID){
				?>
				 <div style="float:right">
			 		<span>
			 			<span id="cart-status-span"></span>
						<a href="#" onclick="addToCart();return false;"><input type="image" src="<?= $CLIENT_ROOT ?>/images/cart.png" class="img-icon" title="<?= $LANG['ADD_TO_CART'] ?>"></a>
			 		</span>
				 	<span style="margin-left: 5px">
				 		<a href="#" onclick="toggle('dataset-block');return false"><img src="<?= $CLIENT_ROOT ?>/images/add.png" class="img-icon" title="<?= $LANG['ADD_TO_DATASET'] ?>" ></a>
				 	</span>
				 </div>
				<?php
			}
			$noDatasetDisplayed = true;
			?>
			<div class="section-title"><?= $LANG['MEMBEROF'] ?></div>
			<ul>
				<?php
				if($datasetArr){
					$userRoles = array();
					if($SYMB_UID) $userRoles = $datasetManager->getUserRoles(0, $SYMB_UID);
					foreach($datasetArr as $dsetID => $dsetArr){
						$name = $dsetArr['datasetName'];
						$role = '';
						$notes = '';
						if(!empty($dsetArr['notes'])) $notes = ' - ' . $dsetArr['notes'];
						$displayDataset = $dsetArr['isPublic'];
						if(isset($userRoles[$dsetID])){
							$role = ' (role: ' . implode(', ', $userRoles[$dsetID][$SYMB_UID]['roles']) . ')';
							$displayDataset = true;
						}
						elseif($IS_ADMIN){
							$displayDataset = true;
						}
						if($displayDataset){
							$noDatasetDisplayed = false;
							?>
							<li>
								<a href="../datasets/index.php?datasetid=<?= $dsetID ?>" target="_blank"><?= $name ?></a> <?= $role . $notes ?>
							</li>
							<?php
						}
					}
				}
				if($noDatasetDisplayed) echo '<li>' . $LANG['OCCURRENCENOTLINKED'] . '</li>';
				?>
			</ul>
			<?php
			if($SYMB_UID){
				?>
				<div class="dataset-block" id="dataset-block" style="display: <?= ($datasetArr?'none':'block') ?>;">
					<fieldset>
						<legend><?= $LANG['ADD_TO_DATASET'] ?></legend>
						<form action="../datasets/datasetHandler.php" method="post" onsubmit="return verifyDatasetForm(this);">
							<div style="margin:3px">
								<select name="targetdatasetid">
									<option value=""><?= $LANG['SELECTEXISTING'] ?></option>
									<option value="">----------------------------------</option>
									<?php
									foreach($datasetArr as $dsid => $dsArr){
										if(!array_key_exists('linked',$dsArr)){
											echo '<option value="'.$dsid.'">'.$dsArr['name'].'</option>';
										}
									}
									?>
								</select>
							</div>
							<div style="margin:5px">
								<label><?= $LANG['NOTES'] ?>:</label><br/>
								<input name="notes" type="text" value="" maxlength="250" style="width:90%;" />
							</div>
							<div style="margin:15px">
								<input name="occid" type="hidden" value="<?= $occid; ?>" />
								<input name="sourcepage" type="hidden" value="individual" />
								<button name="action" type="submit" value="addSelectedToDataset" ><?= $LANG['LINKTO'] ?></button>
							</div>
						</form>
					</fieldset>
				</div>
				<?php
			}
			?>
		</fieldset>
	</div>
</body>