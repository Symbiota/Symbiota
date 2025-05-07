<?php
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.'.$LANG_TAG.'.php'))
	include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.en.php');
?>

<script>
	$(function() {
		$( "#zoomInfoDialog" ).dialog({
			autoOpen: false,
			position: { my: "left top", at: "right bottom", of: "#zoomInfoDiv" }
		});

		$( "#zoomInfoDiv" ).click(function() {
			$( "#zoomInfoDialog" ).dialog( "open" );
		});
	});

	function floatImgPanel(){
		$( "#labelProcFieldset" ).css('position', 'fixed');
		$( "#labelProcFieldset" ).css('top', '20px');
		var pos = $( "#labelProcDiv" ).position();
		var posLeft = pos.left - $(window).scrollLeft();
		$( "#labelProcFieldset" ).css('left', posLeft);
		$( "#floatImgDiv" ).hide();
		$( "#draggableImgDiv" ).hide();
		$( "#anchorImgDiv" ).show();
	}

	function draggableImgPanel(){
		$( "#labelProcFieldset" ).draggable();
		$( "#labelProcFieldset" ).draggable({ cancel: "#labelprocessingdiv" });
		$( "#labelHeaderDiv" ).css('cursor', 'move');
		$( "#labelProcFieldset" ).css('top', '10px');
		$( "#labelProcFieldset" ).css('left', '5px');
		$( "#floatImgDiv" ).hide();
		$( "#draggableImgDiv" ).hide();
		$( "#anchorImgDiv" ).show();
	}

	function anchorImgPanel(){
		$( "#draggableImgDiv" ).show();
		$( "#floatImgDiv" ).show();
		$( "#anchorImgDiv" ).hide();
		$( "#labelProcFieldset" ).css('position', 'static');
		$( "#labelProcFieldset" ).css('top', '');
		$( "#labelProcFieldset" ).css('left', '');
		try {
			$( "#labelProcFieldset" ).draggable( "destroy" );
			$( "#labelHeaderDiv" ).css('cursor', 'default');
		}
		catch(err) {
		}
	}
</script>
<style>
	.ocr-box{ padding: 5px; float:left; }
	.ocr-box button{ margin: 5px; }
</style>
<div id="labelProcDiv" style="width:100%;height:1050px;position:relative">
	<fieldset id="labelProcFieldset" style="background-color:white;">
		<legend><b><?= $LANG['LABEL_PROCESSING'] ?></b></legend>
		<div id="labelHeaderDiv" style="margin-top:-10px;height:15px;position:relative">
			<div style="float:left;margin-top:3px;margin-right:15px"><a id="zoomInfoDiv" href="#"><?= $LANG['ZOOM'] ?></a></div>
			<div id="zoomInfoDialog">
				<?= $LANG['ZOOM_DIRECTIONS'] ?>
			</div>
			<div style="float:left;margin-right:15px">
				<div id="draggableImgDiv" style="float:left" title="<?= $LANG['MAKE_DRAGGABLE'] ?>"><a href="#" onclick="draggableImgPanel()"><img src="../../images/draggable.png" style="width:1.3em" /></a></div>
				<div id="floatImgDiv" style="float:left;margin-left:10px" title="<?= $LANG['ALLOW_REMAIN_ACTIVE'] ?>"><a href="#" onclick="floatImgPanel()"><img src="../../images/floatdown.png" style="width:1.3em" /></a></div>
				<div id="anchorImgDiv" style="float:left;margin-left:10px;display:none" title="<?= $LANG['ANCHOR_IMG'] ?>"><a href="#" onclick="anchorImgPanel()"><img src="../../images/anchor.png" style="width:1.3em" /></a></div>
			</div>
			<div style="float:left;;padding-right:10px;margin:2px 20px 0px 0px;"><?= $LANG['ROTATE'] ?>: <a href="#" onclick="rotateImage(-90)">&nbsp;L&nbsp;</a> &lt;&gt; <a href="#" onclick="rotateImage(90)">&nbsp;R&nbsp;</a></div>
			<div style="float:right;margin:0px 3px;">
				<div><input id="imgresmed" name="resradio"  type="radio" checked onchange="changeImgRes('med')" /><?= $LANG['MED_RES'] ?>.</div>
				<div><input id="imgreslg" name="resradio" type="radio" onchange="changeImgRes('lg')" /><?= $LANG['HIGH_RES'] ?>.</div>
			</div>
		</div>
		<div id="labelprocessingdiv" style="clear:both;">
			<?php
			$imgCnt = 1;
			foreach($imgArr as $imgCnt => $iArr){
				$iUrl = $iArr['web'];
				$imgId = $iArr['mediaid'];
				?>
				<div id="labeldiv-<?= $imgCnt ?>" style="display:<?= ($imgCnt==1?'block':'none') ?>;">
					<div>
						<img id="activeimg-<?= $imgCnt ?>" src="<?= $iUrl ?>" style="width:400px;height:400px" />
					</div>
					<?php
					if(array_key_exists('error', $iArr)){
						echo '<div style="font-weight:bold;color:red">'.$iArr['error'].'</div>';
					}
					?>
					<div style="width:100%;clear:both;">
						<fieldset class="ocr-box">
							<legend>Tesseract OCR</legend>
							<input type="checkbox" id="ocrfull-tess" value="1" /> <?= $LANG['OCR_WHOLE_IMG'] ?><br/>
							<input type="checkbox" id="ocrbest" value="1" /> <?= $LANG['OCR_ANALYSIS'] ?>
							<div>
								<button value="OCR Image" onclick="ocrImage(this,'tess', <?= $imgId.','.$imgCnt ?>);" ><?= $LANG['OCR_IMAGE'] ?></button>
								<img id="workingcircle-tess-<?= $imgCnt ?>" src="../../images/workingcircle.gif" style="display:none;" />
							</div>
						</fieldset>
						<?php
						if(!empty($DIGILEAP_OCR_ACTIVATED)){
							?>
							<fieldset class="ocr-box">
								<legend>DigiLeap OCR</legend>
								<input type="checkbox" id="ocrfull-digi" value="1" /> <?= $LANG['OCR_WHOLE_IMG'] ?><br/>
								<div>
									<button value="OCR Image" onclick="ocrImage(this,'digi', <?= $imgId.','.$imgCnt ?>);" ><?= $LANG['OCR_IMAGE'] ?></button>
									<img id="workingcircle-digi-<?= $imgCnt ?>" src="../../images/workingcircle.gif" style="display:none;" />
								</div>
							</fieldset>
							<?php
						}
						?>
						<div style="float:right;margin-right:20px;font-weight:bold;">
							<?= $LANG['IMAGE'].' '.$imgCnt.' '.$LANG['OF'].' ';
							echo count($imgArr);
							if(count($imgArr)>1){
								echo '<a href="#" onclick="return nextLabelProcessingImage('.($imgCnt+1).');">=&gt;&gt;</a>';
							}
							?>
						</div>
					</div>
					<div style="width:100%;clear:both;">
						<?php
						$fArr = array();
						if(array_key_exists($imgId,$fragArr)){
							$fArr = $fragArr[$imgId];
						}
						?>
						<div id="tfadddiv-<?= $imgCnt ?>" style="display:none;">
							<form id="ocraddform-<?= $imgCnt ?>" name="ocraddform-<?= $imgId ?>" method="post" action="occurrenceeditor.php">
								<div>
									<textarea name="rawtext" rows="12" cols="48" style="width:97%;background-color:#F8F8F8;"></textarea>
								</div>
								<div title="OCR Notes">
									<b><?= $LANG['NOTES'] ?>:</b>
									<input name="rawnotes" type="text" value="" style="width:97%;" />
								</div>
								<div title="OCR Source">
									<b><?= $LANG['SOURCE'] ?>:</b>
									<input name="rawsource" type="text" value="" style="width:97%;" />
								</div>
								<div style="float:left">
									<input type="hidden" name="imgid" value="<?= $imgId ?>" />
									<input type="hidden" name="occid" value="<?= $occId ?>" />
									<input type="hidden" name="collid" value="<?= $collId ?>" />
									<input type="hidden" name="occindex" value="<?= $occIndex ?>" />
									<input type="hidden" name="csmode" value="<?= $crowdSourceMode ?>" />
									<button name="submitaction" type="submit" value="Save OCR" ><?= $LANG['SAVE_OCR'] ?></button>
								</div>
							</form>
							<div style="font-weight:bold;float:right;"><?= '&lt;'.$LANG['NEW'].'&gt; '.$LANG['OF'].' '.count($fArr) ?></div>
						</div>
						<div id="tfeditdiv-<?= $imgCnt ?>" style="clear:both;">
							<?php
							if(array_key_exists($imgId,$fragArr)){
								$fragCnt = 1;
								$targetPrlid = '';
								if(isset($newPrlid) && $newPrlid) $targetPrlid = $newPrlid;
								if(array_key_exists('editprlid',$_REQUEST)) $targetPrlid = $_REQUEST['editprlid'];
								foreach($fArr as $prlid => $rArr){
									$displayBlock = 'none';
									if($targetPrlid){
										if($prlid == $targetPrlid){
											$displayBlock = 'block';
										}
									}
									elseif($fragCnt==1){
										$displayBlock = 'block';
									}
									?>
									<div id="tfdiv-<?= $imgCnt . '-' . $fragCnt ?>" style="display:<?= $displayBlock ?>;">
										<form id="tfeditform-<?= $prlid ?>" name="tfeditform-<?= $prlid ?>" method="post" action="occurrenceeditor.php">
											<div>
												<textarea name="rawtext" rows="12" cols="48" style="width:97%"><?= $rArr['raw'] ?></textarea>
											</div>
											<div title="OCR Notes">
												<b><?= $LANG['NOTES'] ?>:</b>
												<input name="rawnotes" type="text" value="<?= $rArr['notes'] ?>" style="width:97%;" />
											</div>
											<div title="OCR Source">
												<b><?= $LANG['SOURCE'] ?>:</b>
												<input name="rawsource" type="text" value="<?= $rArr['source'] ?>" style="width:97%;" />
											</div>
											<div style="float:left;margin-left:10px;">
												<input type="hidden" name="editprlid" value="<?= $prlid ?>" />
												<input type="hidden" name="collid" value="<?= $collId ?>" />
												<input type="hidden" name="occid" value="<?= $occId ?>" />
												<input type="hidden" name="occindex" value="<?= $occIndex ?>" />
												<input type="hidden" name="csmode" value="<?= $crowdSourceMode ?>" />
												<button name="submitaction" type="submit" value="Save OCR Edits" ><?= $LANG['SAVE_OCR_EDITS'] ?></button>
											</div>
											<div style="float:left;margin-left:20px;">
												<input type="hidden" name="iurl" value="<?= $iUrl ?>" />
												<input type="hidden" id="cnumber" name="cnumber" value="<?= array_key_exists('catalognumber',$occArr)?$occArr['catalognumber']:'' ?>" />
												<?php
												if(!empty($NLP_SALIX_ACTIVATED)){
													?>
													<button name="salixocr" onclick="nlpSalix(this, <?= $prlid ?>)"><?= $LANG['SALIX_PARSER'] ?></button>
													<img id="workingcircle_salix-<?= $prlid ?>" src="../../images/workingcircle.gif" style="display:none;">
													<?php
												}
												if(!empty($NLP_LBCC_ACTIVATED)){
													?>
													<button id="nlplbccbutton" name="nlplbccbutton" onclick="nlpLbcc(this, <?= $prlid ?>)"><?= $LANG['LBCC_PARSER'] ?></button>
													<img id="workingcircle_lbcc-<?= $prlid ?>" src="../../images/workingcircle.gif" style="display:none;">
													<?php
												}
												?>
											</div>
										</form>
										<div style="float:right;font-weight:bold;margin-right:20px;">
											<?php
											echo $fragCnt.' of '.count($fArr);
											if(count($fArr) > 1){
												?>
												<a href="#" onclick="return nextRawText(<?= $imgCnt.','.($fragCnt+1) ?>)">=&gt;&gt;</a>
												<?php
											}
											?>
										</div>
										<div style="clear:both;">
											<form name="tfdelform-<?= $prlid ?>" method="post" action="occurrenceeditor.php" style="margin-left:10px;width:100px;" >
												<input type="hidden" name="delprlid" value="<?= $prlid ?>" />
												<input type="hidden" name="collid" value="<?= $collId ?>" />
												<input type="hidden" name="occid" value="<?= $occId ?>" /><br/>
												<input type="hidden" name="occindex" value="<?= $occIndex ?>" />
												<input type="hidden" name="csmode" value="<?= $crowdSourceMode ?>" />
												<button name="submitaction" type="submit" value="Delete OCR" ><?= $LANG['DELETE_OCR'] ?></button>
											</form>
										</div>
									</div>
									<?php
									$fragCnt++;
								}
							}
							?>
						</div>
					</div>
				</div>
				<?php
				$imgCnt++;
			}
			?>
		</div>
	</fieldset>
</div>