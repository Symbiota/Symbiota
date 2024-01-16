<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAttributes.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/traitattr/occurattributes.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$submitForm = array_key_exists('submitform',$_POST)?$_POST['submitform']:'';
$mode = array_key_exists('mode',$_REQUEST)?$_REQUEST['mode']:1;
$traitID = array_key_exists('traitid',$_REQUEST)?$_REQUEST['traitid']:'';
$paneX = array_key_exists('panex',$_POST)?$_POST['panex']:'575';
$paneY = array_key_exists('paney',$_POST)?$_POST['paney']:'550';
$imgRes = array_key_exists('imgres',$_POST)?$_POST['imgres']:'med';


//Sanitation
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($traitID)) $traitID = '';
if(!is_numeric($paneX)) $paneX = '';
if(!is_numeric($paneY)) $paneY = '';

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN){
		$isEditor = 2;
	}
	elseif($collid){
		//If a page related to collections, one maight want to...
		if(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])){
			$isEditor = 2;
		}
		elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
			$isEditor = 1;
		}
	}
}

$attrManager = new OccurrenceAttributes();
$attrManager->setCollid($collid);
$attrManager->setFilterAttributes($_POST);
$taxonFilter = $attrManager->getFilterAttribute('taxonfilter');
$tidFilter = $attrManager->getFilterAttribute('tidfilter');
$reviewUid = $attrManager->getFilterAttribute('reviewuid');
$reviewDate = $attrManager->getFilterAttribute('reviewdate');
$reviewStatus = $attrManager->getFilterAttribute('reviewstatus');
$sourceFilter = $attrManager->getFilterAttribute('sourcefilter');
$localFilter = $attrManager->getFilterAttribute('localfilter');
$start = $attrManager->getFilterAttribute('start');

$statusStr = '';
if($isEditor){
	if($submitForm == 'Save and Next'){
		$attrManager->setOccid($_POST['targetoccid']);
		if(!$attrManager->addAttributes($_POST,$SYMB_UID)){
			$statusStr = $attrManager->getErrorMessage();
		}
	}
	elseif($submitForm == 'Set Status and Save'){
		$attrManager->setOccid($_POST['targetoccid']);
		$attrManager->editAttributes($_POST);
	}
}
$imgArr = array();
$occid = 0;
$catNum = '';
if($traitID){
	$imgRetArr = array();
	if($mode == 1){
		$imgRetArr = $attrManager->getImageUrls();
		$imgArr = current($imgRetArr);
	}
	elseif($mode == 2){
		$imgRetArr = $attrManager->getReviewUrls($traitID);
		if($imgRetArr) $imgArr = current($imgRetArr);

	}
	if($imgRetArr){
		$catNum = $imgArr['catnum'];
		unset($imgArr['catnum']);
		$occid = key($imgRetArr);
		if($occid) $attrManager->setOccid($occid);
	}
}
?>
<html>
	<head>
		<title>Occurrence Attribute batch Editor</title>
		<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.min.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
		<script src="../../js/jquery.imagetool-1.7.js?ver=160102" type="text/javascript"></script>
		<script type="text/javascript">
			var activeImgIndex = 1;
			var imgArr = [];
			var imgLgArr = [];
			<?php
			$imgDomain = $IMAGE_DOMAIN;
			if(!$imgDomain) $attrManager->getDomain();
			foreach($imgArr as $cnt => $iArr){
				//Regular url
				$url = $iArr['web'];
				if(substr($url,0,1) == '/') $url = $imgDomain.$url;
				echo 'imgArr['.$cnt.'] = "'.$url.'";'."\n";
				//Large Url
				$lgUrl = $iArr['lg'];
				if($lgUrl){
					if(substr($lgUrl,0,1) == '/') $lgUrl = $imgDomain.$lgUrl;
					echo 'imgLgArr['.$cnt.'] = "'.$lgUrl.'";'."\n";
				}
			}
			?>

			$(document).ready(function() {
				setImgRes();
				$("#specimg").imagetool({
					maxWidth: 6000
					,viewportWidth: <?php echo $paneX; ?>
			        ,viewportHeight: <?php echo $paneY; ?>
				});
			});

			function setImgRes(){
				if(imgLgArr[activeImgIndex] != null){
					if($("#imgres1").val() == 'lg') changeImgRes('lg');
				}
				else{
					if(imgArr[activeImgIndex] != null){
						$("#specimg").attr("src",imgArr[activeImgIndex]);
						document.getElementById("imgresmed").checked = true;
						var imgResLgRadio = document.getElementById("imgreslg");
						imgResLgRadio.disabled = true;
						imgResLgRadio.title = "Large resolution image not available";
					}
				}
				if(imgArr[activeImgIndex] != null){
					//Do nothing
				}
				else{
					if(imgLgArr[activeImgIndex] != null){
						$("#specimg").attr("src",imgLgArr[activeImgIndex]);
						document.getElementById("imgreslg").checked = true;
						var imgResMedRadio = document.getElementById("imgresmed");
						imgResMedRadio.disabled = true;
						imgResMedRadio.title = "Medium resolution image not available";
					}
				}
			}

			function changeImgRes(resType){
				if(resType == 'lg'){
					$("#imgres1").val("lg");
					$("#imgres2").val("lg");
			    	if(imgLgArr[activeImgIndex]){
						$("#specimg").attr("src",imgLgArr[activeImgIndex]);
						$( "#imgreslg" ).prop( "checked", true );
			    	}
				}
				else{
					$("#imgres1").val("med");
					$("#imgres2").val("med");
			    	if(imgArr[activeImgIndex]){
						$("#specimg").attr("src",imgArr[activeImgIndex]);
						$( "#imgresmed" ).prop( "checked", true );
			    	}
				}
			}

			function setPortXY(portWidth,portHeight){
				$("#panex1").val(portWidth);
				$("#paney1").val(portHeight);
				$("#panex2").val(portWidth);
				$("#paney2").val(portHeight);
			}

			function nextImage(){
				activeImgIndex = activeImgIndex + 1;
				if(activeImgIndex >= imgArr.length) activeImgIndex = 1;
				$("#specimg").attr("src",imgArr[activeImgIndex]);
				$("#specimg").imagetool({
					maxWidth: 6000
					,viewportWidth: $("#panex1").val()
			        ,viewportHeight: $("#paney1").val()
				});
				//setImgRes();
				$("#labelcnt").html(activeImgIndex);
				return false;
			}

			function skipSpecimen(){
				$("#filterform").submit();
			}

			function verifyFilterForm(f){
				if(f.taxonfilter.value=="All Taxa") f.taxonfilter.value = '';
				if(f.traitid.value == ""){
					alert("An occurrence trait must be selected");
					return false;
				}
				if(f.taxonfilter.value != "" && f.tidfilter.value == ""){
					alert("Taxon filter not syncronized with thesaurus");
					return false;
				}
				return true;
			}

			function nextReviewRecord(startValue){
				var f = document.getElementById("reviewform");
				f.start.value = startValue;
				f.submit();
			}

			function verifySubmitForm(f){

				return true;
			}

			function taxonFilterFocus(formElem){
				if(formElem.value=="All Taxa") formElem.value = '';
			}

		</script>
		<script src="../../js/symb/collections.traitattr.js" type="text/javascript"></script>
		<script src="../../js/symb/shared.js?ver=151229" type="text/javascript"></script>
		<style>
			input{margin:3px}
			select{margin:3px}
		</style>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		if($isEditor == 2){
			echo '<div style="float:right;margin:0px 3px;font-size:90%">';
			if($mode == 1){
				echo '<a href="occurattributes.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&mode=2&traitid=' . htmlspecialchars($traitID, HTML_SPECIAL_CHARS_FLAGS) . '"><img src="../../images/edit.png" style="" />review</a>';
			}
			else{
				echo '<a href="occurattributes.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&mode=1&traitid=' . htmlspecialchars($traitID, HTML_SPECIAL_CHARS_FLAGS) . '"><img src="../../images/edit.png" style="" />edit</a>';
			}
			echo '</div>';
		}
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<a href="../misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>&emode=1">Collection Management</a> &gt;&gt;
			<?php
			if($mode == 2){
				echo '<b>Attribute Reviewer</b>';
			}
			else{
				echo '<b>Attribute Editor</b>';
			}
			?>
		</div>
		<?php
		if($statusStr){
			echo '<div style="color:red">';
			echo $statusStr;
			echo '</div>';
		}
		?>
		<!-- This is inner text! -->
		<div id="innertext" style="position:relative;">
		<?php
		if($collid){
			?>
			<div style="float:right;width:290px;">
				<?php
				$attrNameArr = $attrManager->getTraitNames();
				if($mode == 1){
					?>
					<fieldset style="margin-top:25px">
						<legend><b>Filter</b></legend>
						<form id="filterform" name="filterform" method="post" action="occurattributes.php" onsubmit="return verifyFilterForm(this)" >
							<div>
								<select name="traitid">
									<option value="">Select Trait (required)</option>
									<option value="">------------------------------------</option>
									<?php
									if($attrNameArr){
										foreach($attrNameArr as $ID => $aName){
											echo '<option value="'.$ID.'" '.($traitID==$ID?'SELECTED':'').'>'.$aName.'</option>';
										}
									}
									else{
										echo '<option value="0">No attributes are available</option>';
									}
									?>
								</select>
							</div>
							<div>
								<select name="localfilter" style="width:250px">
									<option value="">All Counties & All States</option>
									<option value="">-----------------------------</option>
									<?php
									$localArr = $attrManager->getLocalFilterOptions();
									foreach($localArr as $localTerm){
										echo '<option '.($localFilter==$localTerm?'selected':'').'>'.$localTerm.'</option>';
									}
									?>
								</select>
							</div>
							<div>
								<input id="taxonfilter" name="taxonfilter" type="text" value="<?php echo ($taxonFilter?$taxonFilter:'All Taxa'); ?>" taxonFilterFocus(this) />
								<input id="tidfilter" name="tidfilter" type="hidden" value="<?php echo $tidFilter; ?>" />
							</div>
							<div>
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input id="panex1" name="panex" type="hidden" value="<?php echo $paneX; ?>" />
								<input id="paney1" name="paney" type="hidden" value="<?php echo $paneY; ?>" />
								<input id="imgres1"  name="imgres" type="hidden" value="<?php echo $imgRes; ?>" />
								<input id="filtersubmit" name="submitform" type="submit" value="Load Images" />
								<span id="verify-span" style="display:none;font-weight:bold;color:green;">verifying taxonomy...</span>
								<span id="notvalid-span" style="display:none;font-weight:bold;color:red;">taxon not valid...</span>
							</div>
							<div style="margin:10px">
								<?php if($traitID) echo '<b>Target Specimens:</b> '.$attrManager->getSpecimenCount(); ?>
							</div>
						</form>
					</fieldset>
				<?php
				}
				elseif($mode == 2){
					?>
					<fieldset style="margin-top:25px">
						<legend><b>Reviewer</b></legend>
						<form id="reviewform" name="reviewform" method="post" action="occurattributes.php" onsubmit="return verifyFilterForm(this)" >
							<div>
								<select name="traitid">
									<option value="">Select Trait (required)</option>
									<option value="">------------------------------------</option>
									<?php
									if($attrNameArr){
										foreach($attrNameArr as $ID => $aName){
											echo '<option value="'.$ID.'" '.($traitID==$ID?'SELECTED':'').'>'.$aName.'</option>';
										}
									}
									else{
										echo '<option value="0">No attributes are available</option>';
									}
									?>
								</select>
							</div>
							<div>
								<select name="reviewuid">
									<option value="">All Editors</option>
									<option value="">-----------------------</option>
									<?php
									$editorArr = $attrManager->getEditorArr();
									foreach($editorArr as $uid => $name){
										echo '<option value="'.$uid.'" '.($uid==$reviewUid?'SELECTED':'').'>'.$name.'</option>';
									}
									?>
								</select>
							</div>
							<div>
								<select name="reviewdate">
									<option value="">All Dates</option>
									<option value="">-----------------------</option>
									<?php
									$dateArr = $attrManager->getEditDates();
									foreach($dateArr as $date){
										echo '<option '.($date==$reviewDate?'SELECTED':'').'>'.$date.'</option>';
									}
									?>
								</select>
							</div>
							<div>
								<select name="reviewstatus">
									<option value="0">Not reviewed</option>
									<option value="5" <?php echo  ($reviewStatus==5?'SELECTED':''); ?>>Expert Needed</option>
									<option value="10" <?php echo  ($reviewStatus==10?'SELECTED':''); ?>>Reviewed</option>
								</select>
							</div>
							<div>
								<select name="sourcefilter">
									<option value="">All Source Types</option>
									<option value="">-----------------------------</option>
									<?php
									$sourceControlArr = $attrManager->getSourceControlledArr();
									foreach($sourceControlArr as $sourceTerm){
										echo '<option '.($sourceFilter==$sourceTerm?'selected':'').'>'.$sourceTerm.'</option>';
									}
									?>
								</select>
							</div>
							<div>
								<select name="localfilter" style="width:250px;">
									<option value="">All Countries & All States</option>
									<option value="">-----------------------------</option>
									<?php
									$localArr = $attrManager->getLocalFilterOptions();
									foreach($localArr as $localTerm){
										echo '<option '.($localFilter==$localTerm?'selected':'').'>'.$localTerm.'</option>';
									}
									?>
								</select>
							</div>
							<div>
								<input id="taxonfilter" name="taxonfilter" type="text" value="<?php echo ($taxonFilter?$taxonFilter:'All Taxa'); ?>" onfocus="taxonFilterFocus(this)" />
								<input id="tidfilter" name="tidfilter" type="hidden" value="<?php echo $tidFilter; ?>" />
							</div>
							<div style="margin:10px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input id="panex1" name="panex" type="hidden" value="<?php echo $paneX; ?>" />
								<input id="paney1" name="paney" type="hidden" value="<?php echo $paneY; ?>" />
								<input id="imgres1" name="imgres" type="hidden" value="<?php echo $imgRes; ?>" />
								<input name="mode" type="hidden" value="2" />
								<input name="start" type="hidden" value="" />
								<input name="submitform" type="submit" value="Get Images" />
							</div>
							<div>
								<?php
								if($traitID){
									$rCnt = $attrManager->getReviewCount($traitID);
									echo '<b>'.($rCnt?$start+1:0).' of '.$rCnt.' records</b>';
									if($rCnt > 1){
										$next = ($start+1);
										if($next >= $rCnt) $next = 0;
										echo ' (<a href="#" onclick="nextReviewRecord('.($next).')">Next record &gt;&gt;</a>)';
									}
								}
								?>
							</div>
						</form>
					</fieldset>
					<?php
				}
				if($imgArr){
					$traitArr = $attrManager->getTraitArr($traitID,($mode==2?true:false));
					$statusCode = 0;
					$notes = '';
					foreach($traitArr[$traitID]['states'] as $stArr){
						if(isset($stArr['statuscode']) && $stArr['statuscode']) $statusCode = $stArr['statuscode'];
						if(isset($stArr['notes']) && $stArr['notes']) $notes = $stArr['notes'];
					}
					?>
					<div id="traitdiv">
						<fieldset style="margin-top:20px">
							<legend><b><?php echo $traitArr[$traitID]['name']; ?></b></legend>
							<form name="submitform" method="post" action="occurattributes.php" onsubmit="return verifySubmitForm(this)" >
								<div style="float:right;margin-right:10px">
									<div class="trianglediv" style="margin:4px 3px;float:right;cursor:pointer" onclick="setAttributeTree(this)" title="Toggle attribute tree open/close">
										<img class="triangleright" src="../../images/triangleright.png" style="" />
										<img class="triangledown" src="../../images/triangledown.png" style="display:none" />
									</div>
								</div>
								<div>
									<?php
									$attrManager->echoFormTraits($traitID);
									?>
								</div>
								<div style="margin:10px 5px;clear:both">
									Notes:
									<input name="notes" type="text" style="width:200px" value="<?php echo $notes; ?>" />
								</div>
								<div style="margin-left:5;">
									Status:
									<select name="setstatus">
										<?php
										if($mode == 2){
											?>
											<option value="0">Not reviewed</option>
											<option value="5">Expert Needed</option>
											<option value="10" selected>Reviewed</option>
											<?php
										}
										else{
											?>
											<option value="0">---------------</option>
											<option value="5">Expert Needed</option>
											<?php
										}
										?>
									</select>
								</div>
								<div style="margin:20px">
									<input name="taxonfilter" type="hidden" value="<?php echo $taxonFilter; ?>" />
									<input name="tidfilter" type="hidden" value="<?php echo $tidFilter; ?>" />
									<input name="traitid" type="hidden" value="<?php echo $traitID; ?>" />
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input id="panex2" name="panex" type="hidden" value="<?php echo $paneX; ?>" />
									<input id="paney2" name="paney" type="hidden" value="<?php echo $paneY; ?>" />
									<input id="imgres2" name="imgres" type="hidden" value="<?php echo $imgRes; ?>" />
									<input name="targetoccid" type="hidden" value="<?php echo $occid; ?>" />
									<input name="mode" type="hidden" value="<?php echo $mode; ?>" />
									<input name="reviewuid" type="hidden" value="<?php echo $reviewUid; ?>" />
									<input name="reviewdate" type="hidden" value="<?php echo $reviewDate; ?>" />
									<input name="reviewstatus" type="hidden" value="<?php echo $reviewStatus; ?>" />
									<?php
									if($mode == 2){
										echo '<input name="submitform" type="submit" value="Set Status and Save" />';
									}
									else{
										echo '<input name="submitform" type="submit" value="Save and Next" disabled />';
									}
									?>
								</div>
							</form>
						</fieldset>
					</div>
					<?php
				}
				?>
			</div>
			<div style="height:600px">
				<?php
				if($imgArr){
					?>
					<div>
						<span><input id="imgresmed" name="resradio"  type="radio" checked onchange="changeImgRes('med')" />Med Res.</span>
						<span style="margin-left:6px;"><input id="imgreslg" name="resradio" type="radio" onchange="changeImgRes('lg')" />High Res.</span>
						<?php
						if($occid){
							if(!$catNum) $catNum = 'Specimen Details';
							echo '<span style="margin-left:50px;">';
							echo '<a href="../individual/index.php?occid=' . htmlspecialchars($occid, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank" title="Specimen Details">' . htmlspecialchars($catNum, HTML_SPECIAL_CHARS_FLAGS) . '</a>';
							echo '</span>';
						}
						$imgTotal = count($imgArr);
						if($imgTotal > 1) echo '<span id="labelcnt" style="margin-left:60px;">1</span> of '.$imgTotal.' images '.($imgTotal>1?'<a href="#" onclick="nextImage()">&gt;&gt; next</a>':'');
						if($occid && $mode != 2) echo '<span style="margin-left:80px" title="Skip Specimen"><a href="#" onclick="skipSpecimen()">SKIP &gt;&gt;</a></span>';
						?>
					</div>
					<div>
						<?php
						$url = $imgArr[1]['web'];
						if(substr($url,0,1) == '/') $url = $imgDomain.$url;
						echo '<img id="specimg" src="'.$url.'" />';
						?>
					</div>
					<?php
				}
				else{
					if($submitForm){
						?>
						<div style="margin:50px;font-size:120%;font-weight: bold">There are no images available matching search criteria</div>
						<?php
					}
					else{
						?>
						<div style="margin-top:50px;font-size:120%;font-weight: bold">
							Select a trait to display images that have not yet been scored
						</div>
						<div style="margin-top:15px;">
							This module allows one to code Occurrence Traits from viewing images of the specimen or observation.
							Upon submitting the filter to the right, images will be displayed randomly for occurrences that have not yet been coded for the selected trait.
							Coded trait attributes can be downloaded and shared via the Darwin Core (DwC) Archive export and publishing tools.
							Traits are included within a <a href="https://tools.gbif.org/dwca-validator/extension.do?id=http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact" target="_blank">Measurement Or Fact</a> DwC Extension file.
						</div>
						<?php
					}
				}
				?>
			</div>
			<?php
		}
		else{
			echo '<div><b>ERROR: collection identifier is not set</b></div>';
		}
		?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>