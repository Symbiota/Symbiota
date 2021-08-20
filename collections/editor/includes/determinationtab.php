<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorDeterminations.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occId = $_GET['occid'];
$occIndex = $_GET['occindex'];
$identBy = $_GET['identby'];
$dateIdent = $_GET['dateident'];
$sciName = $_GET['sciname'];
$crowdSourceMode = $_GET['csmode'];
$editMode = $_GET['em'];
$collId = $_GET['collid'];
$isGenObs = $_GET['isgenobs'];

$annotatorname = $_GET['annotatorname'];
$annotatoremail = $_GET['annotatoremail'];
$catalognumber = $_GET['catalognumber'];
$institutioncode = $_GET['institutioncode'];

$occManager = new OccurrenceEditorDeterminations();

$occManager->setOccId($occId);
$detArr = $occManager->getDetMap($identBy, $dateIdent, $sciName);
$idRanking = $occManager->getIdentificationRanking();

$specImgArr = $occManager->getImageMap();  // find out if there are images in order to show/hide the button to display/hide images.

//Bring in config variables
if($isGenObs){
	if(file_exists('config/occurVarGenObs'.$SYMB_UID.'.php')){
		//Specific to particular collection
		include('config/occurVarGenObs'.$SYMB_UID.'.php');
	}
	elseif(file_exists('config/occurVarGenObsDefault.php')){
		//Specific to Default values for portal
		include('config/occurVarGenObsDefault.php');
	}
}
else{
	if($collId && file_exists('config/occurVarColl'.$collId.'.php')){
		//Specific to particular collection
		include('config/occurVarColl'.$collId.'.php');
	}
	elseif(file_exists('config/occurVarDefault.php')){
		//Specific to Default values for portal
		include('config/occurVarDefault.php');
	}
	if($crowdSourceMode && file_exists('config/crowdSourcingVar.php')){
		//Specific to Crowdsourcing
		include('config/crowdSourcingVar.php');
	}
}

?>
<div id="determdiv" style="width:795px;">
	<div style="margin:15px 0px 40px 15px;">
		<div>
			<b><u>Identification Confidence Ranking</u></b>
			<?php
			if($editMode < 3){
				?>
				<a href="#" title="Modify current identification ranking" onclick="toggle('idrankeditdiv');toggle('idrankdiv');return false;">
					<img src="../../images/edit.png" style="border:0px;width:12px;" />
				</a>
				<?php
			}
			?>
		</div>
		<?php
		if($editMode < 3){
			?>
			<div id="idrankeditdiv" style="display:none;margin:15px;">
				<form name="editidrankingform" action="occurrenceeditor.php" method="post">
					<div style='margin:3px;'>
						<b><?php echo (defined('IDCONFIDENCELABEL')?IDCONFIDENCELABEL:'Confidence of Determination'); ?>:</b>
						<select name="confidenceranking">
							<?php
							$currentRanking = 5;
							if($idRanking) $currentRanking = $idRanking['ranking'];
							?>
							<option value="10" <?php echo ($currentRanking==10?'SELECTED':''); ?>>10 - Absolute</option>
							<option value="9" <?php echo ($currentRanking==9?'SELECTED':''); ?>>9 - High</option>
							<option value="8" <?php echo ($currentRanking==8?'SELECTED':''); ?>>8 - High</option>
							<option value="7" <?php echo ($currentRanking==7?'SELECTED':''); ?>>7 - High</option>
							<option value="6" <?php echo ($currentRanking==6?'SELECTED':''); ?>>6 - Medium</option>
							<option value="5" <?php echo ($currentRanking==5?'SELECTED':''); ?>>5 - Medium</option>
							<option value="4" <?php echo ($currentRanking==4?'SELECTED':''); ?>>4 - Medium</option>
							<option value="3" <?php echo ($currentRanking==3?'SELECTED':''); ?>>3 - Low</option>
							<option value="2" <?php echo ($currentRanking==2?'SELECTED':''); ?>>2 - Low</option>
							<option value="1" <?php echo ($currentRanking==1?'SELECTED':''); ?>>1 - Low</option>
							<option value="0" <?php echo ($currentRanking==0?'SELECTED':''); ?>>0 - Unlikely</option>
						</select>
					</div>
					<div style='margin:3px;'>
						<b><?php echo (defined('IDENTIFICATIONREMARKSLABEL')?IDENTIFICATIONREMARKSLABEL:'Notes'); ?>:</b>
						<input name="notes" type="text" value="<?php echo ($idRanking?$idRanking['notes']:''); ?>" style="width:90%;" />
					</div>
					<div style='margin:15px;'>
						<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
						<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
						<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
						<input type="hidden" name="ovsid" value="<?php echo ($idRanking?$idRanking['ovsid']:''); ?>" />
						<input type="submit" name="submitaction" value="Submit Verification Edits" />
					</div>
				</form>
			</div>
			<?php
		}
		?>
		<div id="idrankdiv" style="margin:15px;">
			<?php
			if($idRanking){
				echo '<div>';
				echo '<b>Rank: </b> '.$idRanking['ranking'];
				if($idRanking['ranking'] < 4){
					echo ' - low ';
				}
				elseif($idRanking['ranking'] < 8){
					echo ' - medium ';
				}
				elseif($idRanking['ranking'] > 7){
					echo ' - high ';
				}
				echo '</div>';
				echo '<div><b>Set by:</b> '.($idRanking['username']?$idRanking['username']:'undefined').'</div>';
				if($idRanking['notes']) echo '<div><b>Notes:</b> '.$idRanking['notes'].'</div>';
			}
			else{
				echo 'not ranked';
			}
			?>
		</div>
	</div>
	<div style="clear:both">
		<fieldset style="margin:15px;padding:15px;">
			<legend><b>Determination History</b></legend>
			<div style="float:right;">
				<a href="#" onclick="toggle('newdetdiv');return false;" title="Add New Determination" ><img style="border:0px;width:12px;" src="../../images/add.png" /></a>
			</div>
			<?php
			if(!$detArr){
				?>
				<div style="font-weight:bold;margin:10px;font-size:120%;">
					There are no historic annotations for this specimen
				</div>
				<?php
			}
			?>
			<div id="newdetdiv" style="display:<?php echo ($detArr?'none':''); ?>;">
				<form name="detaddform" action="occurrenceeditor.php" method="post" onsubmit="return verifyDetForm(this)">
					<fieldset style="margin:15px;padding:15px;">
						<legend><b>Add a New Determination</b></legend>
						<div style="float:right;margin:-7px -4px 0px 0px;font-weight:bold;">
							<span id="imgProcOnSpanDet" style="display:block;">
								<?php
								if($specImgArr){
									?>
									<a href="#" onclick="toggleImageTdOn();return false;">&gt;&gt;</a>
									<?php
								}
								?>
							</span>
							<span id="imgProcOffSpanDet" style="display:none;">
								<?php
								if($specImgArr){
									?>
									<a href="#" onclick="toggleImageTdOff();return false;">&lt;&lt;</a>
									<?php
								}
								?>
							</span>
						</div>
						<?php
						if($editMode == 3){
							?>
							<div style="color:red;margin:10px;">
								While you are a Taxonomy Editor for this taxon, you have not been given explicit editing rights for this collection.
								You can submit new determinations, but they will need to be approved by the collection manager
								before they are applied.
							</div>
							<?php
						}
						?>
						<div style='margin:3px;'>
							<b><?php echo (defined('IDENTIFICATIONQUALIFIERLABEL')?IDENTIFICATIONQUALIFIERLABEL:'Identification Qualifier'); ?>:</b>
							<input type="text" name="identificationqualifier" title="e.g. cf, aff, etc" />
						</div>
						<div style='margin:3px;'>
							<b><?php echo (defined('SCIENTIFICNAMELABEL')?SCIENTIFICNAMELABEL:'Scientific Name'); ?>:</b>
							<input type="text" id="dafsciname" name="sciname" style="background-color:lightyellow;width:350px;" onfocus="initDetAutocomplete(this.form)" />
							<input type="hidden" id="daftidtoadd" name="tidtoadd" value="" />
							<input type="hidden" name="family" value="" />
						</div>
						<div style='margin:3px;'>
							<b><?php echo (defined('SCIENTIFICNAMEAUTHORSHIPLABEL')?SCIENTIFICNAMEAUTHORSHIPLABEL:'Author'); ?>:</b>
							<input type="text" name="scientificnameauthorship" style="width:200px;" />
						</div>
						<div style='margin:3px;'>
							<b><?php echo (defined('IDCONFIDENCELABEL')?IDCONFIDENCELABEL:'Confidence of Determination'); ?>:</b>
							<select name="confidenceranking">
								<option value="8">High</option>
								<option value="5" selected>Medium</option>
								<option value="2">Low</option>
							</select>
						</div>
						<div style='margin:3px;'>
							<b><?php echo (defined('IDENTIFIEDBYLABEL')?IDENTIFIEDBYLABEL:'Determiner'); ?>:</b>
							<input type="text" name="identifiedby" style="background-color:lightyellow;width:200px;" />
						</div>
						<div style='margin:3px;'>
							<b><?php echo (defined('DATEIDENTIFIEDLABEL')?DATEIDENTIFIEDLABEL:'Date'); ?>:</b>
							<input type="text" name="dateidentified" style="background-color:lightyellow;" onchange="detDateChanged(this.form);" />
						</div>
						<div style='margin:3px;'>
							<b><?php echo (defined('IDENTIFICATIONREFERENCELABEL')?IDENTIFICATIONREFERENCELABEL:'Reference'); ?>:</b>
							<input type="text" name="identificationreferences" style="width:350px;" />
						</div>
						<div style='margin:3px;'>
							<b><?php echo (defined('IDENTIFICATIONREMARKSLABEL')?IDENTIFICATIONREMARKSLABEL:'Notes'); ?>:</b>
							<input type="text" name="identificationremarks" style="width:350px;" />
						</div>
						<div style='margin:3px;'>
							<input type="checkbox" name="makecurrent" value="1" /> Make this the current determination
						</div>
						<div style='margin:3px;'>
							<input type="checkbox" name="printqueue" value="1" /> Add to Annotation Print Queue
						</div>
						<div style='margin:15px;'>
							<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
							<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />

							<input type="hidden" name="annotatorname" value="<?php echo $annotatorname; ?>" />
							<input type="hidden" name="annotatoremail" value="<?php echo $annotatoremail; ?>" />
							<input type="hidden" name="catalognumber" value="<?php echo $catalognumber; ?>" />
							<input type="hidden" name="institutioncode" value="<?php echo $institutioncode; ?>" />
							<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
							<?php
							if (isset($_GET['collectioncode']))
								echo '<input type="hidden" name="collectioncode" value="'.$_GET['collectioncode'].'" />';
							?>

							<div style="float:left;">
								<input type="submit" name="submitaction" value="Submit Determination" />
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<?php
			foreach($detArr as $detId => $detRec){
				$canEdit = 0;
				if($editMode < 3 || !$detRec['appliedstatus']) $canEdit = 1;
				?>
				<div id="detdiv-<?php echo $detId;?>">
					<div>
						<?php
						if($detRec['identificationqualifier']) echo $detRec['identificationqualifier'].' ';
						echo '<b><i>'.$detRec['sciname'].'</i></b> '.$detRec['scientificnameauthorship'];
						if($detRec['iscurrent']){
							if($detRec['appliedstatus']){
								echo '<span style="margin-left:10px;color:red;">CURRENT DETERMINATION</span>';
							}
						}
						if($canEdit){
							?>
							<a href="#" onclick="toggle('editdetdiv-<?php echo $detId;?>');return false;" title="Edit Determination"><img style="border:0px;width:12px;" src="../../images/edit.png" /></a>
							<?php
						}
						if(!$detRec['appliedstatus']){
							?>
							<span style="color:red;margin-left:15px;">
								Applied Status Pending
							</span>
							<?php
						}
						?>
					</div>
					<div style='margin:3px 0px 0px 15px;'>
						<b><?php echo (defined('IDENTIFIEDBYLABEL')?IDENTIFIEDBYLABEL:'Determiner'); ?>:</b> <?php echo $detRec['identifiedby']; ?>
						<span style="margin-left:40px;">
							<b><?php echo (defined('DATEIDENTIFIEDLABEL')?DATEIDENTIFIEDLABEL:'Date'); ?>:</b> <?php echo $detRec['dateidentified']; ?>
						</span>
					</div>
					<?php
					if($detRec['identificationreferences']){
						?>
						<div style='margin:3px 0px 0px 15px;'>
							<b><?php echo (defined('IDENTIFICATIONREFERENCELABEL')?IDENTIFICATIONREFERENCELABEL:'Reference'); ?>:</b> <?php echo $detRec['identificationreferences']; ?>
						</div>
						<?php
					}
					if($detRec['identificationremarks']){
						?>
						<div style='margin:3px 0px 0px 15px;'>
							<b><?php echo (defined('IDENTIFICATIONREMARKSLABEL')?IDENTIFICATIONREMARKSLABEL:'Notes'); ?>:</b> <?php echo $detRec['identificationremarks']; ?>
						</div>
						<?php
					}
					if($detRec['printqueue']){
						?>
						<div style='margin:3px 0px 0px 15px;color:orange'>
							Added to Print Queue
						</div>
						<?php
					}
					?>
				</div>
				<?php
				if($canEdit){
					?>
					<div id="editdetdiv-<?php echo $detId;?>" style="display:none;margin:15px 5px;">
						<fieldset>
							<legend><b>Edit Determination</b></legend>
							<form name="deteditform" action="occurrenceeditor.php" method="post" onsubmit="return verifyDetForm(this);">
								<div style='margin:3px;'>
									<b><?php echo (defined('IDENTIFICATIONQUALIFIERLABEL')?IDENTIFICATIONQUALIFIERLABEL:'Identification Qualifier'); ?>:</b>
									<input type="text" name="identificationqualifier" value="<?php echo $detRec['identificationqualifier']; ?>" title="e.g. cf, aff, etc" />
								</div>
								<div style='margin:3px;'>
									<b><?php echo (defined('SCIENTIFICNAMELABEL')?SCIENTIFICNAMELABEL:'Scientific Name'); ?>:</b>
									<input type="text" id="defsciname-<?php echo $detId;?>" name="sciname" value="<?php echo $detRec['sciname']; ?>" style="background-color:lightyellow;width:350px;" onfocus="initDetAutocomplete(this.form)" />
									<input type="hidden" id="deftidtoadd" name="tidtoadd" value="" />
									<input type="hidden" name="family" value="" />
								</div>
								<div style='margin:3px;'>
									<b><?php echo (defined('SCIENTIFICNAMEAUTHORSHIPLABEL')?SCIENTIFICNAMEAUTHORSHIPLABEL:'Author'); ?>:</b>
									<input type="text" name="scientificnameauthorship" value="<?php echo $detRec['scientificnameauthorship']; ?>" style="width:200px;" />
								</div>
								<div style='margin:3px;'>
									<b><?php echo (defined('IDENTIFIEDBYLABEL')?IDENTIFIEDBYLABEL:'Determiner'); ?>:</b>
									<input type="text" name="identifiedby" value="<?php echo $detRec['identifiedby']; ?>" style="background-color:lightyellow;width:200px;" />
								</div>
								<div style='margin:3px;'>
									<b><?php echo (defined('DATEIDENTIFIEDLABEL')?DATEIDENTIFIEDLABEL:'Date'); ?>:</b>
									<input type="text" name="dateidentified" value="<?php echo $detRec['dateidentified']; ?>" style="background-color:lightyellow;" />
								</div>
								<div style='margin:3px;'>
									<b><?php echo (defined('IDENTIFICATIONREFERENCELABEL')?IDENTIFICATIONREFERENCELABEL:'Reference'); ?>:</b>
									<input type="text" name="identificationreferences" value="<?php echo $detRec['identificationreferences']; ?>" style="width:350px;" />
								</div>
								<div style='margin:3px;'>
									<b><?php echo (defined('IDENTIFICATIONREMARKSLABEL')?IDENTIFICATIONREMARKSLABEL:'Notes'); ?>:</b>
									<input type="text" name="identificationremarks" value="<?php echo $detRec['identificationremarks']; ?>" style="width:350px;" />
								</div>
								<div style='margin:3px;'>
									<b>Sort Sequence:</b>
									<input type="text" name="sortsequence" value="<?php echo $detRec['sortsequence']; ?>" style="width:40px;" />
								</div>
								<div style='margin:3px;'>
									<input type="checkbox" name="printqueue" value="1" <?php if($detRec['printqueue']) echo 'checked'; ?> /> Added to Print Queue
								</div>
								<div style='margin:15px;'>
									<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
									<input type="hidden" name="detid" value="<?php echo $detId; ?>" />
									<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
									<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
									<input type="submit" name="submitaction" value="Submit Determination Edits" />
								</div>
							</form>
							<?php
							if($editMode < 3 && !$detRec['iscurrent']){
								?>
								<div style="padding:15px;background-color:lightgreen;width:280px;margin:15px;">
									<form name="detremapform" action="occurrenceeditor.php" method="post">
										<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
										<input type="hidden" name="detid" value="<?php echo $detId; ?>" />
										<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
										<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
										<?php
										if($detRec['appliedstatus']){
											?>
											<input type="submit" name="submitaction" value="Make Determination Current" />
											<?php
										}
										else{
											?>
											<input type="submit" name="submitaction" value="Apply Determination" /><br/>
											<input type="checkbox" name="makecurrent" value="1" <?php echo ($detRec['iscurrent']?'checked':''); ?> /> Make Current
											<?php
										}
										?>
									</form>
								</div>
								<?php
							}
							?>
							<div style="padding:15px;background-color:lightblue;width:155px;margin:15px;">
								<form name="detdelform" action="occurrenceeditor.php" method="post" onsubmit="return window.confirm('Are you sure you want to delete this specimen determination?');">
									<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
									<input type="hidden" name="detid" value="<?php echo $detId; ?>" />
									<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
									<input type="hidden" name=" <?php echo $crowdSourceMode; ?>" />
									<input type="submit" name="submitaction" value="Delete Determination" />
								</form>
							</div>
						</fieldset>
					</div>
					<?php
				}
				?>
				<hr style='margin:10px 0px 10px 0px;' />
				<?php
			}
			?>
		</fieldset>
	</div>
</div>