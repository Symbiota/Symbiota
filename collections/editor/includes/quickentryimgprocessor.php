<?php
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/imgprocessor.en.php');
?>
	
<script src="../../js/symb/collections.editor.imgtools.js?ver=3" type="text/javascript"></script>
<script>
	function nextProcessingImage(){
		document.getElementById("labeldiv-"+(imgCnt+1)).style.display = "none";
		var imgObj = document.getElementById("labeldiv-"+imgCnt+1);
		if(imgCnt > imgLen){
			imgObj = document.getElementById("labeldiv-1");
			imgCnt = "1";
		}
		imgObj.style.display = "block";
		
		initImageTool("activeimg-"+imgCnt);
		activeImgIndex = imgCnt;
		
		return false;
	}
	$(function() {
		$( "#zoomInfoDialog" ).dialog({
			autoOpen: false,
			position: { my: "left top", at: "right bottom", of: "#zoomInfoDiv" }
		});

		$( "#zoomInfoDiv" ).click(function() {
			$( "#zoomInfoDialog" ).dialog( "open" );
		});
	});
	function rotateImage(rotationAngle){
		var imgObj = document.getElementById("activeimg-<?php echo ($imgCnt); ?>");
		var imgAngle = 0;
		if(imgObj.style.transform){
			var transformValue = imgObj.style.transform;
			imgAngle = parseInt(transformValue.substring(7));
		}
		imgAngle = imgAngle + rotationAngle;
		if(imgAngle < 0) imgAngle = 360 + imgAngle;
		else if(imgAngle == 360) imgAngle = 0;
		imgObj.style.transform = "rotate("+imgAngle+"deg)";
		$(imgObj).imagetool("option","rotationAngle",imgAngle);
		$(imgObj).imagetool("reset");
	}

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

</style>
<div id="labelProcDiv" style="width:100%;height:425px;position:relative;">
	<fieldset id="labelProcFieldset" style="background-color:#F2F2F3;">
		<div id="labelHeaderDiv" style="margin-top:0px;height:15px;position:relative">
			<div style="float:left;margin-top:3px;margin-right:15px"><a id="zoomInfoDiv" href="#"><?php echo $LANG['ZOOM']; ?></a></div>
			<div id="zoomInfoDialog" style="background-color:white;">
				<?php echo $LANG['ZOOM_DIRECTIONS']; ?>
			</div>
			<div style="float:left;margin-right:15px">
				<div id="draggableImgDiv" style="float:left" title="<?php echo $LANG['MAKE_DRAGGABLE']; ?>"><a href="#" onclick="draggableImgPanel()"><img src="../../images/draggable.png" style="width:15px" /></a></div>
				<div id="anchorImgDiv" style="float:left;margin-left:10px;display:none" title="<?php echo $LANG['ANCHOR_IMG']; ?>"><a href="#" onclick="anchorImgPanel()"><img src="../../images/anchor.png" style="width:15px" /></a></div>
			</div>
			<div style="float:left;;padding-right:10px;margin:2px 20px 0px 0px;"><?php echo $LANG['ROTATE']; ?>: <a href="#" onclick="rotateImage(-90)">&nbsp;L&nbsp;</a> &lt;&gt; <a href="#" onclick="rotateImage(90)">&nbsp;R&nbsp;</a></div>
		</div>
		<div id="labelprocessingdiv" style="clear:both;">
			<!-- <div><?php // echo($imgUrl) ?></div> -->
				<div id="labeldiv-<?php echo $imgCnt; ?>" style="display:'block'; ?>;">
					<div>
						<img id="activeimg-<?php echo ($imgCnt); ?>" src="<?php echo($imgUrl) ?>" style="height:400px;" onload="initImageTool('activeimg-<?php echo ($imgCnt); ?>')" />
					<div style="width:100%;clear:both;">
						<div style="float:right;margin-right:20px;font-weight:bold;">
							<?php echo $LANG['IMAGE'].' '.($imgCnt-1).' '.$LANG['OF'].' ';
							echo count($imgArr);
							if(count($imgArr)>1){
								echo '<a href="#" onclick="return nextProcessingImage();">=&gt;&gt;</a>';
							}
							?>
						</div>
					</div>
				</div>
		</div>
	</fieldset>
</div>