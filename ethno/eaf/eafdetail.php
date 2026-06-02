<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoMediaManager.php');
header('Content-Type: text/html; charset='.$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$mediaid = array_key_exists('mediaid',$_REQUEST)?$_REQUEST['mediaid']:0;
$tabIndex = array_key_exists("tabindex",$_REQUEST)?$_REQUEST["tabindex"]:0;
$action = array_key_exists('action',$_POST)?$_POST['action']:'';

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($mediaid)) $mediaid = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;

$isEditor = false;
if($SYMB_UID){
	if($IS_ADMIN){
		$isEditor = true;
	}
	elseif($collid && ((array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])) || (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])))){
		$isEditor = true;
	}
}

$eafManager = new EthnoMediaManager();
$eafManager->setCollid($collid);
$eafManager->setMediaid($mediaid);

$statusStr = '';
$tierArr = array();
$lineTimeArr = array();
$eafArr = array();
$xml = '';
$time_slot_array = array();
$presSettingArr = array();
$tierSettingArr = array();
$tierDataArr = array();
$tSettingArr = array();
$media_type_tag = "audio";
$media_mime_type = "audio/mpeg";
$media_player_height = '';

$eafArr = $eafManager->getEAFInfoArr();
$settingsArr = $eafArr['displaySettings'];
if($eafArr){
	$media_file = $eafArr['url'];
	$eaf_file = $eafArr['eaffile'];
	$player_title = $eafArr['description'];
	$start_at_time = 0;
	$start_at_time_end = 0;
	$file_path = $SERVER_ROOT.$eaf_file;
	if(file_exists($file_path)){
		$xml = simplexml_load_string(file_get_contents($file_path));
	}
	if($settingsArr){
		$settingsArr = json_decode($settingsArr, true);
		$presSettingArr = $settingsArr['presenters'];
		$tierSettingArr = $settingsArr['tiers'];
		foreach($tierSettingArr as $i => $tArr){
			$tSettingArr[$tArr['id']]['display'] = $tArr['display'];
			$tSettingArr[$tArr['id']]['color'] = (array_key_exists('color',$tArr)?$tArr['color']:'');
		}
	}
}
if($xml){
	foreach ($xml->TIME_ORDER->TIME_SLOT as $time_slot){
		$time_slot_array[(int)substr($time_slot['TIME_SLOT_ID'], 2)] = (int)$time_slot['TIME_VALUE'];
		//$time_slot_array[] = $time_slot['TIME_VALUE'];
	}
	foreach ($xml->TIER as $a_tier){
		$parent_ref = trim(stripslashes((string)$a_tier['PARENT_REF']));
		if(!$parent_ref){
			foreach ($a_tier->ANNOTATION as $a_nnotation){
				$startTime = (int)substr($a_nnotation->ALIGNABLE_ANNOTATION['TIME_SLOT_REF1'], 2);
				$stopTime = (int)substr($a_nnotation->ALIGNABLE_ANNOTATION['TIME_SLOT_REF2'], 2);
				if(!$start_at_time_end || ($startTime > 1 && (($time_slot_array[$startTime-1]/1000) < $start_at_time))){
					$start_at_time = ($startTime>1?($time_slot_array[$startTime-1]/1000):0);
					$start_at_time_end = ($time_slot_array[$stopTime-1]/1000);
				}
				$lineTimeArr[(string)$a_nnotation->ALIGNABLE_ANNOTATION['ANNOTATION_ID']]['start'] = $startTime;
				$lineTimeArr[(string)$a_nnotation->ALIGNABLE_ANNOTATION['ANNOTATION_ID']]['stop'] = $stopTime;
			}
		}

	}
	foreach ($xml->TIER as $a_tier){
		$tierID = trim(stripslashes((string)$a_tier['TIER_ID']));
		if($tierID){
			$spkr = '';
			$participant = trim(stripslashes((string)$a_tier['PARTICIPANT']));
			$speaker = ($participant?$participant:$tierID);
			$speaker = str_replace("  "," ",$speaker);
			$speaker_parts = explode(" ", $speaker);
			foreach($speaker_parts as $sp_prt){
				$spkr .= trim(substr($sp_prt, 0, 1));
			}
			if(strlen($spkr) > 3) $spkr = substr($spkr, 0, 3);
			foreach ($a_tier->ANNOTATION as $a_nnotation){
				$parent_ref = stripslashes((string)$a_tier['PARENT_REF']);
				$line_id = $parent_ref?(string)$a_nnotation->REF_ANNOTATION['ANNOTATION_ID']:(string)$a_nnotation->ALIGNABLE_ANNOTATION['ANNOTATION_ID'];
				$line_ref = $parent_ref?(string)$a_nnotation->REF_ANNOTATION['ANNOTATION_REF']:(string)$a_nnotation->ALIGNABLE_ANNOTATION['ANNOTATION_ID'];
				$line_value = trim($parent_ref?(string)$a_nnotation->REF_ANNOTATION->ANNOTATION_VALUE:(string)$a_nnotation->ALIGNABLE_ANNOTATION->ANNOTATION_VALUE);
				$line_value = htmlspecialchars($line_value,ENT_QUOTES);
				$line_value = str_replace("&lt;","<",$line_value);
				$line_value = str_replace("&gt;",">",$line_value);
				if($line_ref){
					if(array_key_exists($tierID,$tSettingArr)){
						$tierDataArr[$tierID]['display'] = $tSettingArr[$tierID]['display'];
						$tierDataArr[$tierID]['color'] = ($tSettingArr?$tSettingArr[$tierID]['color']:'');
					}
					else{
						$tierDataArr[$tierID]['display'] = 'bottom';
						$tierDataArr[$tierID]['color'] = '';
					}
					$tierDataArr[$tierID]['lines'][$line_id]['lineref'] = $line_ref;
					$tierDataArr[$tierID]['lines'][$line_id]['start'] = ($lineTimeArr[$line_ref]['start']>1?($time_slot_array[$lineTimeArr[$line_ref]['start']-1]/1000):0);
					$tierDataArr[$tierID]['lines'][$line_id]['stop'] = ($time_slot_array[$lineTimeArr[$line_ref]['stop']-1]/1000);
					$tierDataArr[$tierID]['lines'][$line_id]['value'] = $line_value;
				}
			}
		}
	}
	$tierArr = $eafManager->createTierArr($xml);
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - EAF Detail</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<style type="text/css">
		div[id^='txt-current_'] { background-color: #eeeeee; }

		#player-title-box{
			width: 100%;
			margin-left: auto;
			margin-right: auto;
			text-align: center;
		}

		#txt_sync_content{
			padding: 0px 36px;
		}

		#spkr_keys_box{
			margin-top: 0px;
			margin-bottom: 18px;
			width: 100%;
			margin-left: auto;
			margin-right: auto;
			min-height: 22px;
		}

		.spkr_key{
			font-family: Seravek, Arial, Helvetica, sans-serif;
			font-size: 18px;
			margin-right: 8px;
			display: inline;
		}

		.spkr_name{
			font-family: Seravek, Arial, Helvetica, sans-serif;
			font-size: 18px;
			margin-right: 8px;
			display: inline;
			font-style: italic;
		}

		#sync_player{
			width: 100%;
			margin-left: auto;
			margin-right: auto;
			border: solid 1px #ffffff;
		}

		#gloss-box{
			width: 100%;
			min-height: 65px;
			margin-left: auto;
			margin-right: auto;
			margin-top: 12px;
		}

		div.glossTable {
			width: 100%;
			text-align: left;
			min-height: 30px;
			margin-bottom: 12px;
			padding-left: 12px;
			padding-right: 12px;
		}
		.divTable.glossTable .divTableRow {
			font-size: 18px;
			line-height: 26px;
		}
		.divTable.glossTable .divTableCell {
			font-family: Seravek, Arial, Helvetica, sans-serif;
		}
		.divTable.glossTable .spkr {
			width: 55px;
		}
		div.scrollTable {
			width: 100%;
			height: 470px;
			margin-left: auto;
			margin-right: auto;
			overflow-x: hidden;
			overflow-y: visible;
			background-color: #ffffff;
			border: solid 1px #cccccc;
		}
		div.scrollTableCont {
			margin: 0;
			padding: 0;
			border: 0;
			outline: 0;
			font-size: 100%;
			vertical-align: baseline;
			background: transparent;
		}
		.divTable.scrollTableCont .divTableRow {
			font-size: 18px;
			line-height: 26px;
			margin: 0;
		}
		.divTable.scrollTableCont .divTableCell {
			font-family: Seravek, Arial, Helvetica, sans-serif;
		}
		.divTable.scrollTableCont .spkr {
			width: 55px;
			margin-right: 4px;
			font-weight: normal;
			padding-left: 12px;
		}
		.divTable.scrollTableCont .spkr:hover {
			cursor: pointer;
			font-weight: bold;
		}
		.divTable.scrollTableCont .spkn {
			margin-left: 8px;
			padding-right: 12px;
		}

		.divTable{ display: table; }
		.divTableRow { display: table-row; }
		.divTableCell { display: table-cell;}
	</style>
	<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=131106" type="text/javascript"></script>
	<script src="../../js/jscolor/jscolor.js?ver=4" type="text/javascript"></script>
	<script type="text/javascript">
		var media;
		var ts_start_time_array = [];
		var ts_stop_time_array = [];
		var sub_time;
		var initial_time = '';
		var initial_time_end = '';
		var current_start_time = '';
		var current_id_arr = [];
		var transArr = Array();
		var glossArr = Array();
		var speakerShowArr = Array();
		var tierShowArr = Array();
		var dataArr = JSON.parse('<?php echo json_encode($tierDataArr); ?>');
		var tierArr = JSON.parse('<?php echo json_encode($tierArr); ?>');
		var timeslotArr = JSON.parse('<?php echo json_encode($time_slot_array); ?>');
		var lineTimeArr = JSON.parse('<?php echo json_encode($lineTimeArr); ?>');

		$(document).ready(function() {
			setColors();
			setData();
			for(pid in tierArr){
				if(tierArr.hasOwnProperty(pid)){
					var showSpeaker = false;
					var tiers = tierArr[pid].tiers;
					for(tid in tiers){
						if(tiers.hasOwnProperty(tid)){
							if(tierShowArr.indexOf(tid) === -1){
								if(dataArr[tid]){
									var display = dataArr[tid].display;
									if(display !== 'nodisplay'){
										showSpeaker = true;
										tierShowArr.push(tid);
									}
								}
							}
						}
					}
					if(speakerShowArr.indexOf(pid) === -1 && showSpeaker){
						var checkboxId = 'check-tier-'+pid;
						if(document.getElementById(checkboxId)){
							speakerShowArr.push(pid);
							document.getElementById(checkboxId).checked = true;
						}
					}
				}
			}
			setTranscriptionBox();
			initial_time = <?php echo $start_at_time; ?>;
			initial_time_end = <?php echo $start_at_time_end; ?>;
			media = document.getElementById("sync_player");
			media.setAttribute("ontimeupdate", "sync(this.currentTime)");
			media.setAttribute("onmousemove", "sync(this.currentTime)");
			media.setAttribute("onclick", "sync(this.currentTime)");
			setPlayer();
			//primePlayer();
			document.getElementById('sync_player').onmousemove();
		});

		function alertMLoadErr(){
			//document.getElementById("innertext").innerHTML = "<h1>Failed to open media file</h1>";
		}

		function setColors(){
			for(pid in tierArr){
				//pid == $speaker
				var tiers = tierArr[pid].tiers;
				for(tid in tiers){
					//tid == $a_tier['TIER_ID']
					if(dataArr[tid] && dataArr[tid].color){
						tierArr[pid].tiers[tid].color = dataArr[tid].color;
					}
				}
			}
		}

		function setData(){
			transArr = [];
			glossArr = [];
			for(pid in tierArr){
				//pid == $speaker
				var tiers = tierArr[pid].tiers;
				for(tid in tiers){
					//tid == $a_tier['TIER_ID']
					var spkr = tiers[tid].code;
					var color = tiers[tid].color;
					if(dataArr[tid]){
						var display = dataArr[tid].display;
						var lines = dataArr[tid].lines;
						for(l in lines){
							var lineref = lines[l].lineref;
							var start = lineTimeArr[lineref].start;
							if(display === 'bottom'){
								if(!transArr[start]) transArr[start] = [];
								if(!transArr[start][tid]) transArr[start][tid] = [];
								transArr[start][tid]['speaker'] = pid;
								transArr[start][tid]['color'] = color;
								transArr[start][tid]['lineref'] = lineref;
								transArr[start][tid]['start'] = lines[l].start;
								transArr[start][tid]['stop'] = lines[l].stop;
								transArr[start][tid]['spkr'] = spkr;
								transArr[start][tid]['id'] = l;
								transArr[start][tid]['value'] = lines[l].value;
							}
							else{
								if(!glossArr[lineref]) glossArr[lineref] = [];
								if(!glossArr[lineref][tid]) glossArr[lineref][tid] = [];
								if(!glossArr[lineref][tid][l]) glossArr[lineref][tid][l] = [];
								glossArr[lineref][tid][l]['speaker'] = pid;
								glossArr[lineref][tid][l]['color'] = color;
								glossArr[lineref][tid][l]['spkr'] = spkr;
								glossArr[lineref][tid][l]['value'] = lines[l].value;
							}
						}
					}
				}
			}
		}

		function setTranscriptionBox(){
			var innerHtml = '<div class="divTable scrollTableCont">';
			for(t in transArr){
				for(s in transArr[t]){
					var speaker = transArr[t][s]['speaker'];
					if((tierShowArr.indexOf(s) !== -1) && (speakerShowArr.indexOf(speaker) !== -1)){
						var color = transArr[t][s]['color'];
						var lineref = transArr[t][s]['lineref'];
						var start = transArr[t][s]['start'];
						var stop = transArr[t][s]['stop'];
						var spkr = transArr[t][s]['spkr'];
						var id = transArr[t][s]['id'];
						var value = transArr[t][s]['value'];
						innerHtml += '<div name="bottom-'+lineref+'" class="divTableRow" style="color:#'+color+'" data-start="'+start+'" data-stop="'+stop+'"><div class="divTableCell spkr">'+spkr+'</div><div class="divTableCell spkn" id="'+id+'">'+value+'</div></div>';
					}
				}
			}
			innerHtml += '</div>';
			document.getElementById("txt_lns").innerHTML = innerHtml;
		}

		function setPlayer(){
			var i = 0;
			for(l in lineTimeArr){
				ts_start_time_array[i] = (timeslotArr[lineTimeArr[l]['start']]/1000);
				ts_stop_time_array[i] = (timeslotArr[lineTimeArr[l]['stop']]/1000);
				if(document.getElementsByName("bottom-"+l)){
					var element = document.getElementsByName("bottom-"+l)[0];
					if(element) element.childNodes[0].setAttribute("onclick","set_time_play_and_pause("+ts_start_time_array[i]+", "+ts_stop_time_array[i]+")");
				}
				i++;
			}
			if (initial_time > 0){
				try{
					set_time_play_and_pause(initial_time, initial_time_end);
				}
				catch(error){
					media.addEventListener("canplay", function(){
						set_time_play_and_pause(initial_time, initial_time_end);
					},true);
				}
			}
		}

		function set_time_play_and_pause(start_time, end_time){
			media.pause();
			clearTimeout(sub_time);
			play_time = Math.ceil((end_time - start_time) * 1000);
			if(play_time > 0){
				media.currentTime = start_time;
				media.play();
				sub_time = setTimeout(function(){
					media.pause();
				}, play_time);
			}
		}

		function sync(current_time){
			var txt_lns_rect = document.getElementById("txt_lns").getBoundingClientRect();
			var mid_point = 460;
			var max_scroll = (document.getElementById("txt_lns").scrollHeight - 470);
			var ref_id;
			var innerHtml = '';
			var i = 0;
			for(l in lineTimeArr){
				if((current_time >= parseFloat(ts_start_time_array[i])) && (current_time <= parseFloat(ts_stop_time_array[i]))){
					if(document.getElementsByName("bottom-"+l)){
						var elements = document.getElementsByName("bottom-"+l);
						for(e in elements){
							if(elements[e] instanceof Element){
								elements[e].setAttribute("id","txt-current_"+i);
								if(elements[e].getBoundingClientRect().bottom > txt_lns_rect.bottom){
									var scroll = (elements[e].getBoundingClientRect().top - txt_lns_rect.top) - 10;
									document.getElementById("txt_lns").scrollTop += scroll;
								}
							}
						}
					}
					if(current_start_time != ts_start_time_array[i]){
						current_start_time = ts_start_time_array[i];
						current_id_arr = [];
						document.getElementById("txt_refs").innerHTML = '';
					}
					if(current_id_arr.indexOf(l) === -1){
						if(glossArr[l]){
							innerHtml = document.getElementById("txt_refs").innerHTML;
							for(s in glossArr[l]){
								if(tierShowArr.indexOf(s) !== -1){
									for(n in glossArr[l][s]){
										var speaker = glossArr[l][s][n]['speaker'];
										if(speakerShowArr.indexOf(speaker) !== -1){
											var spkr = glossArr[l][s][n]['spkr'];
											var value = glossArr[l][s][n]['value'];
											var color = glossArr[l][s][n]['color'];
											innerHtml += "<div class='divTableRow' style='color:#"+color+"'><div class='divTableCell spkr'>"+spkr+"</div><div class='divTableCell tran'>"+value+"</div></div>";
										}
									}
								}
							}
							document.getElementById("txt_refs").innerHTML = innerHtml;
						}
						current_id_arr.push(l);
					}
				}
				else{
					try{
						if(document.getElementsByName("bottom-"+l)){
							var elements = document.getElementsByName("bottom-"+l);
							for(e in elements){
								if(elements[e] instanceof Element){
									elements[e].removeAttribute("id");
								}
							}
						}
					}
					catch (err) { }
				}
				i++;
			}
		}

		function changePresCheck(tierID){
			var eleName = "check-tier-"+tierID;
			var idName = "check-tier-"+tierID;
			var cbarray = document.getElementsByName(eleName);
			var checked = document.getElementById(idName).checked;
			for(i in cbarray){
				var tierName = cbarray[i].value;
				cbarray[i].checked = checked;
				if(tierName) toggleTier(tierName);
			}
		}

		function changeTierCheck(tierID){
			var eleName = "check-tier-"+tierID;
			var idName = "check-tier-"+tierID;
			var cbarray = document.getElementsByName(eleName);
			var tchecked = false;
			for(i in cbarray){
				if((cbarray[i].checked === true) && cbarray[i].value) tchecked = true;
			}
			document.getElementById(idName).checked = tchecked;
		}

		function restartPlayer(){
			media.currentTime = 0;
			document.getElementById("txt_refs").innerHTML = '';
			var innerHtml = '';
			var i = 0;
			for(l in lineTimeArr){
				if((0 >= parseFloat(ts_start_time_array[i])) && (0 <= parseFloat(ts_stop_time_array[i]))){
					current_start_time = ts_start_time_array[i];
					current_id_arr = [];
					document.getElementById("txt_refs").innerHTML = '';
					if(current_id_arr.indexOf(l) === -1){
						if(glossArr[l]){
							innerHtml = document.getElementById("txt_refs").innerHTML;
							for(s in glossArr[l]){
								if(tierShowArr.indexOf(s) !== -1){
									for(n in glossArr[l][s]){
										var speaker = glossArr[l][s][n]['speaker'];
										if(speakerShowArr.indexOf(speaker) !== -1){
											var spkr = glossArr[l][s][n]['spkr'];
											var value = glossArr[l][s][n]['value'];
											var color = glossArr[l][s][n]['color'];
											innerHtml += "<div class='divTableRow' style='color:#"+color+"'><div class='divTableCell spkr'>"+spkr+"</div><div class='divTableCell tran'>"+value+"</div></div>";
										}
									}
								}
							}
							document.getElementById("txt_refs").innerHTML = innerHtml;
						}
						current_id_arr.push(l);
					}
				}
				i++;
			}
		}

		function toggleTier(tierID){
			var chkIdStr = 'checkbox-'+tierID;
			if(document.getElementById(chkIdStr).checked === true){
				tierShowArr.push(tierID);
			}
			else{
				var index = tierShowArr.indexOf(tierID);
				tierShowArr.splice(index,1);
			}
			restartPlayer();
			setTranscriptionBox();
			setPlayer();
		}

		function changeTierColor(color,speaker,tierID){
			var keyID = 'key-'+tierID;
			var selColor = '#'+color;
			document.getElementById(keyID).style.color = selColor;
			tierArr[speaker].tiers[tierID].color = color;
			setData();
			restartPlayer();
			setTranscriptionBox();
			setPlayer();
		}

		function changeTierPlacement(tierID){
			var idName = tierID+'-display';
			var value = document.getElementById(idName).value;
			dataArr[tierID].display = value;
			setData();
			restartPlayer();
			setTranscriptionBox();
			setPlayer();
		}

		function toggleSpeaker(spkr){
			var chkIdStr = 'check-tier-'+spkr;
			if(document.getElementById(chkIdStr).checked === true){
				speakerShowArr.push(spkr);
			}
			else{
				var index = speakerShowArr.indexOf(spkr);
				speakerShowArr.splice(index,1);
			}
			restartPlayer();
			setTranscriptionBox();
			setPlayer();
		}

		function toggleDisplayPanel(){
			var display = document.getElementById('spkr_keys').style.display;
			if(display === 'none'){
				document.getElementById('spkr_keys').style.display = 'block';
				document.getElementById('plusButton').style.display = 'none';
				document.getElementById('minusButton').style.display = 'block';
			}
			if(display === 'block'){
				document.getElementById('spkr_keys').style.display = 'none';
				document.getElementById('plusButton').style.display = 'flex';
				document.getElementById('minusButton').style.display = 'none';
			}
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
if($isEditor && $collid) echo '<a href="../../collections/misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
if($isEditor) echo '<a href="index.php?collid='.$collid.'">Manage EAF Files</a> &gt;&gt; ';
else echo '<a href="index.php?collid='.$collid.'">View EAF Files</a> &gt;&gt; ';
echo '<b>'.$player_title.' EAF Detail</b>';
echo '</div>';
?>
<!-- This is inner text! -->
<div id="innertext" style="padding-left:0px;padding-right:0px;padding-top:0px;">
	<div id="txt_sync_content">
		<?php
		if ($xml){
			if (substr($media_file, -4) === ".mp4"){
				$media_type_tag = "video";
				$media_mime_type = "video/mp4";
				$media_player_height = " height='400'";
			}
			?>
			<div id='player-title-box'><h1><?php echo $player_title; ?></h1></div>
			<div id="spkr_keys_box">
				<div style="float:left;cursor:pointer;" onclick="toggleDisplayPanel();" title="Toggle Display Panel">
					<div id='plusButton' style="display:flex;align-items:center;">
						<img style='border:0px;margin-right:8px;' src='../../images/plus.png' /> (Open to customize display)
					</div>
					<img id='minusButton' style='border:0px;display:none;' src='../../images/minus.png' />
				</div>
				<div id="spkr_keys" style="display:none;margin-left:50px;">
					<?php
					foreach($tierArr as $tierId => $tArr){
						if(($presSettingArr && in_array($tArr['participant'],$presSettingArr)) || !$presSettingArr){
							$subTierArr = $tArr['tiers'];
							echo "<div style='clear:both;display:flex;align-items:center;'><input type='checkbox' class='check-pres' style='margin-right:8px;' id='check-tier-".$tierId."' value='".$tArr['participant']."' onchange='changePresCheck(\"".$tierId."\");toggleSpeaker(\"".$tArr['participant']."\");'> <span class='spkr_name'>".$tArr['participant']."</span></div>";
							foreach($subTierArr as $subtierId => $stArr){
								$display = '';
								$name = $stArr['name'];
								if(($tSettingArr && array_key_exists($name,$tSettingArr)) || !$tSettingArr){
									$color = ((array_key_exists($name,$tSettingArr) && $tSettingArr[$name]['color'])?$tSettingArr[$name]['color']:$stArr['color']);
									$code = $stArr['code'];
									if(!array_key_exists($name,$tSettingArr) && ($name != $tierId)) $display = 'top';
									else $display = $tSettingArr[$name]['display'];
									echo "<div style='clear:both;margin-left:20px;display:flex;align-items:center;'>";
									echo "<input type='checkbox' style='margin-right:8px;' class='check-tier' name='check-tier-".$tierId."' id='checkbox-".$subtierId."' onchange='changeTierCheck(\"".$tierId."\");toggleTier(\"".$subtierId."\");' value='".$name."' ".($display!=='nodisplay'?'checked':'')."> ";
									echo "<input id='color-".$subtierId."' class='color' style='cursor:pointer;border:1px black solid;height:14px;width:14px;font-size:0px;margin-right:8px;' value='".$color."' onchange='changeTierColor(this.value,\"".$tierId."\",\"".$subtierId."\");' /> ";
									echo "<span id='key-".$subtierId."' class='spkr_key' style='color:#".$color.";margin-right:8px;'>".$code."</span><span style='margin-right:8px;'>&middot;</span>";
									echo "<span class='spkr_name' style='margin-right:8px;'>".$name.(($name == $tierId)?' (transcription)':'')."</span>";
									echo '<select id="'.$subtierId.'-display" style="margin-left:10px;" onchange="changeTierPlacement(\''.$subtierId.'\');">';
									echo "<option value='top' ".($display!=='bottom'?'selected':'')." >One-line display</option>";
									echo "<option value='bottom' ".($display==='bottom'?'selected':'')." >Scrolling</option>";
									echo "</select>";
									echo "</div>";
								}
							}
						}
					}
					?>
				</div>
			</div>
			<div id="player_area" style="width:100%;margin-left:auto;margin-right:auto;">
				<<?php echo $media_type_tag.$media_player_height; ?> onclick="sync(this.currentTime)" onmousemove="sync(this.currentTime)" ontimeupdate="sync(this.currentTime)" id="sync_player" controls=true>
					<source src="<?php echo $media_file; ?>" type="<?php echo $media_mime_type; ?>" onerror="alertMLoadErr();">
				</<?php echo $media_type_tag; ?>>
			</div>
			<div id='gloss-box'>
				<div id='txt_refs' class='divTable glossTable'></div>
			</div>
			<div id='txt_lns' class='scrollTable'></div>
			<?php
		}
		else{
			echo "<h1>Failed to open XML file</h1>";
		}
		?>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
