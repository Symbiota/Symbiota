<?php
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');

$ethnoDataManager = new EthnoDataManager();
$ethnoProjectManager = new EthnoProjectManager();

if($action === 'Delete Data Record'){
	$ethnoDataManager->deleteDataRecord($_POST);
}
elseif($action === 'Delete Data Event Record'){
	$ethnoDataManager->deleteDataEvent($_POST);
}

$ethnoDataManager->setCollid($collId);
$ethnoDataManager->setOccId($occId);
$ethnoPersonnelArr = $ethnoDataManager->getPersonnelArr();
$ethnoCommunityArr = $ethnoDataManager->getCommunityArr();
$ethnoReferenceArr = $ethnoDataManager->getReferenceArr();
$ethnoDataEventArr = $ethnoDataManager->getDataEventArr();
$langArr = $ethnoProjectManager->getLangNameDropDownList($collId);
$ethnoDataManager->setOccTaxonomy();
$ethnoTid = $ethnoDataManager->getTid();
$ethnoNameSemanticTagArr = $ethnoDataManager->getNameSemanticTagArr();
$ethnoUseKingdomId = $ethnoDataManager->getKingdomId();
$ethnoUsePartsUsedTagArr = $ethnoDataManager->getPartsUsedTagArr($ethnoUseKingdomId);
$ethnoUseUseTagArr = $ethnoDataManager->getUseTagArr($ethnoUseKingdomId);
$ethnoDataArr = $ethnoDataManager->getOccDataArr();
?>
<script type="text/javascript">
	function toggleEthnoDiv(targetName){
		var plusDivId = 'plusButton'+targetName;
		var minusDivId = 'minusButton'+targetName;
		var contentDivId = 'content'+targetName;
		var display = document.getElementById(contentDivId).style.display;
		if(display === 'none'){
			document.getElementById(contentDivId).style.display = 'block';
			document.getElementById(plusDivId).style.display = 'none';
			document.getElementById(minusDivId).style.display = 'flex';
		}
		if(display === 'block'){
			document.getElementById(contentDivId).style.display = 'none';
			document.getElementById(plusDivId).style.display = 'flex';
			document.getElementById(minusDivId).style.display = 'none';
		}
	}

	function processEthnoDataEventSelectorChange(){
		var selValue = document.getElementById('ethnoDataEventSelector').value;
		if(selValue === 'new'){
			document.getElementById('newEthnoDataEventForm').style.display = 'block';
			document.getElementById('newEthnoDataCollectionButton').style.display = 'block';
			document.getElementById('existingEthnoDataCollectionButton').style.display = 'none';
		}
		else{
			document.getElementById('newEthnoDataEventForm').style.display = 'none';
			document.getElementById('newEthnoDataCollectionButton').style.display = 'none';
			document.getElementById('existingEthnoDataCollectionButton').style.display = 'block';
		}
	}

	function verifyNewDataEventForm(f){
		if(document.getElementById('ethnoDataEventSelector').value !== 'new') return true;
		var sourceValue = document.getElementById('newEthnoDataEventSourceSelector').value;
		var colsultantVerified = false;
		var referenceVerified = false;
		for(var h=0;h<f.length;h++){
			if(f.elements[h].name === "consultant[]" && f.elements[h].checked){
				colsultantVerified = true;
			}
			if(f.elements[h].name === "refid" && f.elements[h].value){
				referenceVerified = true;
			}
		}
		if(sourceValue === 'elicitation' && !colsultantVerified){
			alert("Please select at least one consultant.");
			return false;
		}
		else if(sourceValue === 'reference' && !referenceVerified){
			alert("Please select a reference.");
			return false;
		}
		else{
			return true;
		}
	}
</script>
<div id="ethnoDataDiv" style="width:795px;">
	<div style="float:right;cursor:pointer;<?php echo (!$ethnoDataArr?'display:none;':''); ?>" onclick="toggle('adddatadiv');" title="Add Vernacular Data">
		<img style="border:0;width:12px;" src="../../../images/add.png" />
	</div>
	<div id="adddatadiv" style="clear:both;<?php echo ($ethnoDataArr?'display:none;':''); ?>">
		<form name="dataaddform" action="<?php echo $CLIENT_ROOT; ?>/ethno/manager/dataeventeditor.php" method="post" onsubmit="return verifyNewDataEventForm(this);">
			<fieldset style="padding:15px;">
				<legend><b>Add Vernacular Data</b></legend>
				<?php
				if($ethnoDataEventArr){
					?>
					<div style="clear:both;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Data collection event:</b></span>
						<select id="ethnoDataEventSelector" name="eventid" style="width:500px;" onchange="processEthnoDataEventSelectorChange();">
							<option value="new">----Create new event----</option>
							<?php
							foreach($ethnoDataEventArr as $k => $v){
								echo '<option value="'.$v['etheventid'].'">'.$v['label'].'</option>';
							}
							?>
						</select>
					</div>
					<?php
				}
				?>
				<div id="newEthnoDataEventForm" style="display:block;">
					<div id="newEthnoDataEventCommunity" style="clear:both;<?php echo ($ethnoDataEventArr?'margin-top:10px;':''); ?>">
						<?php
						if($ethnoCommunityArr){
							?>
							<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('EthnoCommunity');">
								<div id='plusButtonEthnoCommunity' style="display:none;align-items:center;">
									Community: <img style='border:0px;margin-left:8px;width:13px;' src='../../../images/plus.png' />
								</div>
								<div id='minusButtonEthnoCommunity' style="display:flex;align-items:center;">
									Community: <img style='border:0px;margin-left:8px;width:13px;' src='../../../images/minus.png' />
								</div>
							</div>
							<div id="contentEthnoCommunity" style="display:block;padding-left:15px;clear:both;">
								<?php
								foreach($ethnoCommunityArr as $id => $pArr){
									echo '<input type="radio" name="ethComID" value="'.$id.'"> '.$pArr['communityname'].'<br />';
								}
								?>
							</div>
							<?php
						}
						else{
							echo '<div style="clear:both;">';
							echo 'There are no communities associated with this project.<br />';
							echo 'Please go to the <a href="'.$CLIENT_ROOT.'/ethno/manager/editcommunity.php?collid=' .$collId. '" target="_blank">Community Management page</a> to add communities.';
							echo '</div>';
						}
						?>
					</div>
					<div id="newEthnoDataEventPersonnel" style="clear:both;margin-top:10px;">
						<?php
						if($ethnoPersonnelArr){
							?>
							<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('EthnoConsultants');">
								<div id='plusButtonEthnoConsultants' style="display:none;align-items:center;">
									Consultants: <img style='border:0px;margin-left:8px;width:13px;' src='../../../images/plus.png' />
								</div>
								<div id='minusButtonEthnoConsultants' style="display:flex;align-items:center;">
									Consultants: <img style='border:0px;margin-left:8px;width:13px;' src='../../../images/minus.png' />
								</div>
							</div>
							<div id="contentEthnoConsultants" style="display:block;padding-left:15px;clear:both;">
								<?php
								foreach($ethnoPersonnelArr as $id => $pArr){
									$name = $pArr['title'].' '.$pArr['firstname'].' '.$pArr['lastname'];
									echo '<input name="consultant[]" value="'.$id.'" type="checkbox" /> '.$name.'<br />';
								}
								?>
							</div>
							<?php
						}
						else{
							echo '<div style="clear:both;">';
							echo 'There are no consultants associated with this project.<br />';
							echo 'Please go to the <a href="'.$CLIENT_ROOT.'/ethno/manager/editpersonnel.php?collid=' .$collId. '" target="_blank">Personnel Management page</a> to add consultants.';
							echo '</div>';
						}
						?>
					</div>
					<div id="newEthnoDataEventDate" style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Date:</b></span>
						<input name="eventdate" type="text" style="width:500px;" value="<?php echo array_key_exists('eventdate',$occArr)?$occArr['eventdate']:''; ?>" />
					</div>
					<div id="newEthnoDataEventLocation" style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Location:</b></span>
						<textarea name="eventlocation" style="width:500px;height:50px;resize:vertical;"><?php echo array_key_exists('locality',$occArr)?$occArr['locality']:''; ?></textarea>
					</div>
				</div>
				<div style="clear:both;float:right;margin-top:10px;">
					<input type="hidden" name="datasource" value="elicitation" />
					<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
					<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
					<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
					<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
					<input id="newEthnoDataCollectionButton" type="submit" name="submitaction" value="Create New Data Collection Event" />
					<input id="existingEthnoDataCollectionButton" type="submit" style="display:none;" name="submitaction" value="Go To Data Collection Event" />
				</div>
			</fieldset>
		</form>
	</div>
	<?php
	if($ethnoDataArr){
		?>
		<fieldset style="clear:both;padding:15px;">
			<legend><b>Vernacular Data Records</b></legend>
			<?php
			foreach($ethnoDataArr as $dataId => $dataArr){
				$dataPersonelStr = '';
				$dataSemanticsStr = '';
				$dataTypologyStr = '';
				$dataUseStr = '';
				$dataPartsStr = '';
				$dataPersonelArr = $dataArr["personnelArr"];
				$dataSemanticsArr = $dataArr["semanticTags"];
				$dataUseArr = $dataArr["useTags"];
				$dataPartsArr = $dataArr["partsTags"];
				foreach($ethnoPersonnelArr as $id => $pArr){
					if(in_array($id,$dataPersonelArr)){
						$dataPersonelStr .= $pArr['title'].' '.$pArr['firstname'].' '.$pArr['lastname'].'; ';
					}
				}
				if($dataPersonelStr) $dataPersonelStr = substr($dataPersonelStr,0,-2);
				foreach($ethnoNameSemanticTagArr as $id => $stArr){
					if(in_array($id,$dataSemanticsArr)){
						$dataSemanticsStr .= $stArr['ptag'].'; ';
					}
					unset($stArr['ptag']);
					unset($stArr['pdesc']);
					if($stArr){
						foreach($stArr as $cid => $cArr){
							if(in_array($cid,$dataSemanticsArr)){
								$dataSemanticsStr .= $cArr['ctag'].'; ';
							}
						}
					}
				}
				if($dataSemanticsStr) $dataSemanticsStr = substr($dataSemanticsStr,0,-2);
				foreach($ethnoUsePartsUsedTagArr as $id => $text){
					if(in_array($id,$dataPartsArr)){
						$dataPartsStr .= $text.'; ';
					}
				}
				if($dataPartsStr) $dataPartsStr = substr($dataPartsStr,0,-2);
				foreach($ethnoUseUseTagArr as $id => $uArr){
					$tempStr = '';
					$header = $uArr['header'];
					unset($uArr['header']);
					foreach($uArr as $uid => $text){
						if(in_array($uid,$dataUseArr)){
							if(!$tempStr) $tempStr = '<b>'.$header.':</b> ';
							$tempStr .= $text.'; ';
						}
					}
					if($tempStr) $dataUseStr .= $tempStr;
				}
				if($dataUseStr) $dataUseStr = substr($dataUseStr,0,-2);
				if($dataArr["typology"]==='opaque') $dataTypologyStr = 'Opaque';
				elseif($dataArr["typology"]==='transparent') $dataTypologyStr = 'Transparent';
				elseif($dataArr["typology"]==='modifiedopaque') $dataTypologyStr = 'Modified opaque';
				elseif($dataArr["typology"]==='modifiedtransparent') $dataTypologyStr = 'Modified transparent';
				?>
				<div style="float:right;cursor:pointer;" title="Edit Vernacular Data Record">
					<a href="<?php echo $CLIENT_ROOT; ?>/ethno/manager/dataeditor.php?dataid=<?php echo $dataId; ?>&collid=<?php echo $collId; ?>&occid=<?php echo $occId; ?>&occindex=<?php echo $occIndex; ?>&csmode=<?php echo $crowdSourceMode; ?>">
						<img style="border:0;width:15px;" src="../../../images/edit.png" />
					</a>
				</div>
				<div style="margin-top:10px">
					<?php
					if($dataArr["reftitle"]){
						?>
						<div style="font-size:13px;">
							<b>Reference title:</b>
							<?php echo $dataArr["reftitle"]; ?>
						</div>
						<?php
					}
					if($dataArr["refpages"]){
						?>
						<div style="font-size:13px;">
							<b>Reference pages:</b>
							<?php echo $dataArr["refpages"]; ?>
						</div>
						<?php
					}
					if($dataArr["dataeventstr"]){
						?>
						<div style="font-size:13px;">
							<b>Elicitation event:</b>
							<?php echo $dataArr["dataeventstr"]; ?>
						</div>
						<?php
					}
					if($dataPersonelStr){
						?>
						<div style="font-size:13px;">
							<b>Consultants:</b>
							<?php echo $dataPersonelStr; ?>
						</div>
						<?php
					}
					if($dataArr["verbatimVernacularName"]){
						?>
						<div style="font-size:13px;">
							<b>Verbatim vernacular name:</b>
							<?php echo $dataArr["verbatimVernacularName"]; ?>
						</div>
						<?php
					}
					if($dataArr["annotatedVernacularName"]){
						?>
						<div style="font-size:13px;">
							<b>Annotated vernacular name:</b>
							<?php echo $dataArr["annotatedVernacularName"]; ?>
						</div>
						<?php
					}
					if($dataSemanticsStr){
						?>
						<div style="font-size:13px;">
							<b>Semantic tags:</b>
							<?php echo $dataSemanticsStr; ?>
						</div>
						<?php
					}
					if($dataArr["verbatimLanguage"]){
						?>
						<div style="font-size:13px;">
							<b>Verbatim language:</b>
							<?php echo $dataArr["verbatimLanguage"]; ?>
						</div>
						<?php
					}
					if($dataArr["langName"]){
						?>
						<div style="font-size:13px;">
							<b>Glottolog language:</b>
							<?php echo $dataArr["langName"]; ?>
						</div>
						<?php
					}
					if($dataArr["otherVerbatimVernacularName"]){
						?>
						<div style="font-size:13px;">
							<b>Other verbatim vernacular name:</b>
							<?php echo $dataArr["otherVerbatimVernacularName"]; ?>
						</div>
						<?php
					}
					if($dataArr["otherLangName"]){
						?>
						<div style="font-size:13px;">
							<b>Other verbatim vernacular name Glottolog language:</b>
							<?php echo $dataArr["otherLangName"]; ?>
						</div>
						<?php
					}
					if($dataArr["verbatimParse"]){
						?>
						<div style="font-size:13px;">
							<b>Verbatim parse:</b>
							<?php echo $dataArr["verbatimParse"]; ?>
						</div>
						<?php
					}
					if($dataArr["annotatedParse"]){
						?>
						<div style="font-size:13px;">
							<b>Annotated parse:</b>
							<?php echo $dataArr["annotatedParse"]; ?>
						</div>
						<?php
					}
					if($dataArr["verbatimGloss"]){
						?>
						<div style="font-size:13px;">
							<b>Verbatim gloss:</b>
							<?php echo $dataArr["verbatimGloss"]; ?>
						</div>
						<?php
					}
					if($dataArr["annotatedGloss"]){
						?>
						<div style="font-size:13px;">
							<b>Annotated gloss:</b>
							<?php echo $dataArr["annotatedGloss"]; ?>
						</div>
						<?php
					}
					if($dataTypologyStr){
						?>
						<div style="font-size:13px;">
							<b>Typology:</b>
							<?php echo $dataTypologyStr; ?>
						</div>
						<?php
					}
					if($dataArr["translation"]){
						?>
						<div style="font-size:13px;">
							<b>Translation:</b>
							<?php echo $dataArr["translation"]; ?>
						</div>
						<?php
					}
					if($dataArr["taxonomicDescription"]){
						?>
						<div style="font-size:13px;">
							<b>Taxonomic description:</b>
							<?php echo $dataArr["taxonomicDescription"]; ?>
						</div>
						<?php
					}
					if($dataArr["nameDiscussion"]){
						?>
						<div style="font-size:13px;">
							<b>Consultant comments on name:</b>
							<?php echo $dataArr["nameDiscussion"]; ?>
						</div>
						<?php
					}
					if($dataPartsStr){
						?>
						<div style="font-size:13px;">
							<b>Parts used:</b>
							<?php echo $dataPartsStr; ?>
						</div>
						<?php
					}
					if($dataUseStr){
						?>
						<div style="font-size:13px;">
							<b>Uses:</b>
							<?php echo $dataUseStr; ?>
						</div>
						<?php
					}
					if($dataArr["consultantComments"]){
						?>
						<div style="font-size:13px;">
							<b>Consultant comments:</b>
							<?php echo $dataArr["consultantComments"]; ?>
						</div>
						<?php
					}
					if($dataArr["useDiscussion"]){
						?>
						<div style="font-size:13px;">
							<b>Consultant comments on use:</b>
							<?php echo $dataArr["useDiscussion"]; ?>
						</div>
						<?php
					}
					?>
				</div>
				<hr/>
				<?php
			}
			?>
		</fieldset>
		<?php
	}
	?>
</div>
