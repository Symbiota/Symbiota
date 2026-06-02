<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoMediaManager.php');
header('Content-Type: text/html; charset='.$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ethno/eaf/eafedit.php?collid='.$collid.'&mediaid='.$mediaid.'&occid='.$occId.'&occindex='.$occIndex.'&csmode='.$csMode);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$mediaid = array_key_exists('mediaid',$_REQUEST)?$_REQUEST['mediaid']:0;
$occId = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?$_REQUEST['occindex']:0;
$csMode = array_key_exists('csmode',$_REQUEST)?$_REQUEST['csmode']:0;
$tabIndex = array_key_exists("tabindex",$_REQUEST)?$_REQUEST["tabindex"]:0;
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($mediaid)) $mediaid = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;

$eafManager = new EthnoMediaManager();
$eafManager->setCollid($collid);

$eafArr = array();
$xml = '';
$tierArr = array();
$presenterArr = array();
$tieridArr = array();
$presSettingArr = array();
$tierSettingArr = array();
$tSettingArr = array();

if($mediaid){
	$eafManager->setMediaid($mediaid);
	if($action){
		if($action === 'Add Taxa'){
			if(!$eafManager->addTaxonLink($_POST['tid'])){
				$statusStr = $eafManager->getErrorStr();
			}
		}
		elseif($action === 'Delete Taxa'){
			if(!$eafManager->deleteTaxonLink($_POST['linkid'])){
				$statusStr = $eafManager->getErrorStr();
			}
		}
		elseif($action === 'Add Occurrence'){
			if(!$eafManager->addMediaOccLink($mediaid,$_POST['targetoccid'])){
				$statusStr = $eafManager->getErrorStr();
			}
		}
		elseif($action === 'Delete Occurrence'){
			if(!$eafManager->deleteOccLink($_POST['linkid'])){
				$statusStr = $eafManager->getErrorStr();
			}
		}
	}
	$eafArr = $eafManager->getEAFInfoArr();
	$settingsArr = $eafArr['displaySettings'];
	$taxaArr = $eafManager->getTaxaArr();
	$occArr = $eafManager->getOccArr();
}

if($eafArr){
	$media_file = $eafArr['url'];
	$eaf_file = $eafArr['eaffile'];
	$player_title = $eafArr['description'];
	$start_at_time = 0;
	$start_at_time_end = 0;
	$specific_start_line_id = "x0";
	$file_path = $SERVER_ROOT.$eaf_file;
	if(file_exists($file_path)){
		$xml = simplexml_load_file($file_path);
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
	$tierArr = $eafManager->createTierArr($xml);
	foreach($tierArr as $tierId => $tArr){
		$presenterArr[] = $tierId;
		$subTierArr = $tArr['tiers'];
		foreach($subTierArr as $subtierId => $stArr){
			$tieridArr[] = $subtierId;
		}
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - EAF Display Editor</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<style type="text/css"></style>
	<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=131106" type="text/javascript"></script>
	<script src="../../js/jscolor/jscolor.js?ver=4" type="text/javascript"></script>
	<script type="text/javascript">
		var presenterArr = JSON.parse('<?php echo json_encode($presenterArr); ?>');
		var tieridArr = JSON.parse('<?php echo json_encode($tieridArr); ?>');

		$(document).ready(function() {
			$('#tabs').tabs({
				beforeLoad: function( event, ui ) {
					$(ui.panel).html("<p>Loading...</p>");
				}
			});

			$( "#addtaxon" )
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "ui-autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						var reqTerm = request.term;
						reqTerm = reqTerm.split( /,\s*/ );
						reqTerm = reqTerm.pop();
						$.getJSON( "<?php echo $CLIENT_ROOT; ?>/rpc/taxasuggest.php", { term: reqTerm }, response );
					},
					autoFocus: true,
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					select: function( event, ui ) {
						var terms = this.value.split( /,\s*/ );
						// remove the current input
						terms.pop();
						// add the selected item
						terms.push( ui.item.value );
						document.getElementById('tid').value = ui.item.id;
						this.value = terms;
						return false;
					},
					change: function (event, ui) {
						if(!ui.item && this.value != "") {
							document.getElementById('tid').value = '';
							alert("You must select a name from the list.");
						}
						else if (document.getElementById(ui.item.value)) {
							this.value = '';
							document.getElementById('tid').value = '';
							alert("Taxon has already been associated.");
						}
					}
				},{});
		});

		function changePresCheck(tierID){
			var eleName = "check-tier-"+tierID;
			var idName = "check-tier-"+tierID;
			var cbarray = document.getElementsByName(eleName);
			var checked = document.getElementById(idName).checked;
			for(i in cbarray){
				cbarray[i].checked = checked;
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

		function verifyEAFEditForm(){
			var valid = false;
			var presArr = [];
			var tierArr = [];
			var dispArr = {};
			var mp3urlVal = document.getElementById("mp3url").value;
			var descVal = document.getElementById("description").value;
			for(p in presenterArr){
				var pidLabel = 'check-tier-'+presenterArr[p];
				if(document.getElementById(pidLabel).checked === true){
					var pVal = document.getElementById(pidLabel).value;
					presArr.push(pVal);
				}
			}
			for(t in tieridArr){
				var stidLabel = 'sub-check-tier-'+tieridArr[t];
				if(document.getElementById(stidLabel).checked === true){
					var tidVal = document.getElementById(stidLabel).value;
					var dispIdName = tidVal+"-display";
					var colorIdName = tidVal+"-color";
					var tdispVal = document.getElementById(dispIdName).value;
					var tcolorVal = document.getElementById(colorIdName).value;
					var tObj = {
						id: tidVal,
						display: tdispVal,
						color: tcolorVal
					};
					tierArr.push(tObj);
				}
			}
			if(tierArr.length > 0 && mp3urlVal && descVal) valid = true;
			if(valid){
				dispArr = {
					presenters: presArr,
					tiers: tierArr
				};
				document.getElementById("displaySettingStr").value = JSON.stringify(dispArr);
				document.editeafform.submit();
			}
			else{
				alert('Please enter an MP3 URL, EAF description, and select at least one tier to display.');
			}
		}

		function openOccurrenceSearch(target) {
			occWindow=open("../../collections/misc/occurrencesearch.php?targetid="+target+"&collid=<?php echo $collid; ?>","occsearch","resizable=1,scrollbars=1,toolbar=1,width=750,height=600,left=20,top=20");
			occWindow.focus();
			if (occWindow.opener == null) occWindow.opener = self;
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
if($collid) echo '<a href="../../collections/misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
echo '<a href="index.php?collid='.$collid.'">Manage EAF Files</a> &gt;&gt; ';
echo '<b>Edit EAF Record</b>';
echo '</div>';

if($occId){
	echo '<div style="margin:10px;">';
	echo '<a href="../../collections/editor/occurrenceeditor.php?collid='.$collid.'&occid='.$occId.'&occindex='.$occIndex.'&csmode='.$csMode.'&tabtarget=4">Return to Occurrence Editor</a>';
	echo '</div>';
}
?>
<!-- This is inner text! -->
<div id="innertext">
	<div id="tabs" style="margin:0px;">
		<ul>
			<li><a href="#eafdetaildiv">Details</a></li>
			<li><a href="#eafadmindiv">Admin</a></li>
		</ul>
		<div id="eafdetaildiv" style="">
			<fieldset>
				<legend><b>Edit EAF Record</b></legend>
				<form name="editeafform" id="editeafform" action="index.php" method="post">
					<div style="width:100%;margin-top:8px;">
						MP3/MP4 URL
						<input name="mp3url" id="mp3url" type="text" style="width:800px;" value="<?php echo $eafArr['url']; ?>" />
					</div>
					<div style="width:100%;margin-top:8px;">
						EAF Description
						<input name="description" id="description" type="text" style="width:800px;" value="<?php echo $eafArr['description']; ?>" />
					</div>
					<div style="width:100%;margin-top:8px;">
						Set default display settings
						<div style="margin-left:50px;">
							<?php
							foreach($tierArr as $tierId => $tArr){
								$subTierArr = $tArr['tiers'];
								echo "<div style='clear:both;'><input type='checkbox' class='check-pres' id='check-tier-".$tierId."' value='".$tArr['participant']."' onchange='changePresCheck(\"".$tierId."\");' ".((($presSettingArr && in_array($tArr['participant'],$presSettingArr)) || !$presSettingArr)?'checked':'')."> ".$tArr['participant']."</div>";
								foreach($subTierArr as $subtierId => $stArr){
									$display = '';
									$name = $stArr['name'];
									$color = ((array_key_exists($name,$tSettingArr) && $tSettingArr[$name]['color'])?$tSettingArr[$name]['color']:$stArr['color']);
									if($tSettingArr && !array_key_exists($name,$tSettingArr)) $color = '000000';
									if(!array_key_exists($name,$tSettingArr) && ($name != $tierId)) $display = 'top';
									elseif(array_key_exists($name,$tSettingArr)) $display = $tSettingArr[$name]['display'];
									echo "<div style='clear:both;margin-left:20px;display:flex;align-items:center;'>";
									echo "<input type='checkbox' class='check-tier' style='margin-right:8px;' name='check-tier-".$tierId."' id='sub-check-tier-".$subtierId."' onchange='changeTierCheck(\"".$tierId."\");' value='".$name."' ".((($tSettingArr && array_key_exists($name,$tSettingArr)) || !$tSettingArr)?'checked':'')."> ";
									echo "<input id='".$name."-color' class='color' style='cursor:pointer;border:1px black solid;height:14px;width:14px;font-size:0px;margin-right:8px;' value='".$color."' /> ";
									echo $name.(($name == $tierId)?' (transcription)':'');
									echo '<select id="'.$name.'-display" style="margin-left:10px;">';
									echo "<option value='top' ".($display==='top'?'selected':'')." >One-line display</option>";
									echo "<option value='bottom' ".($display==='bottom'?'selected':'')." >Scrolling</option>";
									echo "<option value='nodisplay' ".($display==='nodisplay'?'selected':'')." >Do not display</option>";
									echo "</select>";
									echo "</div>";
								}
							}
							?>
						</div>
					</div>
					<div style="clear:both;padding-top:8px;float:right;">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
						<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
						<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
						<input name="mediaid" type="hidden" value="<?php echo $mediaid; ?>" />
						<input id="displaySettingStr" name="displaySettings" type="hidden" value="" />
						<input id="performactionup" name="submitaction" type="hidden" value="Save EAF Edits" />
						<button type="button" onclick='verifyEAFEditForm();'><b>Save</b></button>
					</div>
				</form>
			</fieldset>
			<fieldset style='clear:both;padding:8px;margin-bottom:10px;'>
				<legend><b>Associated Taxa</b></legend>
				<div style="clear:both;" title="Associated Taxa">
					<ul>
						<?php
						foreach($taxaArr as $linkId => $sciname){
							echo '<li><form name="taxadelform" id="'.$sciname.'" action="eafedit.php" style="margin-top:0px;margin-bottom:0px;" method="post">';
							echo $sciname;
							echo '<input style="margin-left:15px;" type="image" src="../../images/del.png" title="Delete Taxon">';
							echo '<input name="collid" type="hidden" value="'.$collid.'" />';
							echo '<input name="mediaid" type="hidden" value="'.$mediaid.'" />';
							echo '<input name="occid" type="hidden" value="'.$occId.'" />';
							echo '<input name="occindex" type="hidden" value="'.$occIndex.'" />';
							echo '<input name="csmode" type="hidden" value="'.$csMode.'" />';
							echo '<input name="linkid" type="hidden" value="'.$linkId.'" />';
							echo '<input name="submitaction" type="hidden" value="Delete Taxa" />';
							echo '</form></li>';
						}
						?>
					</ul>
				</div>
				<div style="clear:both;margin:10px">
					<form name="taxaaddform" id="taxaaddform" action="eafedit.php" method="post" onsubmit="">
						<div style="float:left;">
							<b>Associate Taxon: </b>
						</div>
						<div style="float:left;margin-left:10px;">
							<input type="text" name="addtaxon" id="addtaxon" maxlength="45" style="width:250px;" value="" />
							<input name="tid" id="tid" type="hidden" value="" />
						</div>
						<div style="float:left;margin-left:10px;">
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="mediaid" type="hidden" value="<?php echo $mediaid; ?>" />
							<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
							<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
							<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
							<button name="submitaction" type="submit" value="Add Taxa">Add Taxon</button>
						</div>
					</form>
				</div>
			</fieldset>
			<fieldset style='clear:both;padding:8px;margin-bottom:10px;'>
				<legend><b>Associated Occurrences</b></legend>
				<div style="clear:both;" title="Associated Occurrences">
					<ul>
						<?php
						foreach($occArr as $linkId => $occText){
							echo '<li><form name="occdelform" id="'.$occText.'" action="eafedit.php" style="margin-top:0px;margin-bottom:0px;" method="post">';
							echo $occText;
							echo '<input style="margin-left:15px;" type="image" src="../../images/del.png" title="Delete Occurrence">';
							echo '<input name="collid" type="hidden" value="'.$collid.'" />';
							echo '<input name="mediaid" type="hidden" value="'.$mediaid.'" />';
							echo '<input name="occid" type="hidden" value="'.$occId.'" />';
							echo '<input name="occindex" type="hidden" value="'.$occIndex.'" />';
							echo '<input name="csmode" type="hidden" value="'.$csMode.'" />';
							echo '<input name="linkid" type="hidden" value="'.$linkId.'" />';
							echo '<input name="submitaction" type="hidden" value="Delete Occurrence" />';
							echo '</form></li>';
						}
						?>
					</ul>
				</div>
				<div style="clear:both;margin:10px">
					<form name="occaddform" id="occaddform" action="eafedit.php" method="post" onsubmit="">
						<div style="float:left;">
							<b>Associate Occurrence: </b>
						</div>
						<div style="float:left;margin-left:10px;">
							<input id="associateoccid" name="targetoccid" type="text" value="" readonly/>
							<span style="cursor:pointer;color:blue;"  onclick="openOccurrenceSearch('associateoccid')">Open Occurrence Linking Aid</span>
						</div>
						<div style="float:left;margin-left:10px;">
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="mediaid" type="hidden" value="<?php echo $mediaid; ?>" />
							<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
							<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
							<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
							<button name="submitaction" type="submit" value="Add Occurrence">Add Occurrence</button>
						</div>
					</form>
				</div>
			</fieldset>
		</div>
		<div id="eafadmindiv" style="">
			<form name="deltermform" action="<?php echo ($occId?'../../collections/editor/occurrenceeditor.php':'index.php'); ?>" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this EAF record?')">
				<fieldset style="width:350px;margin:20px;padding:20px;">
					<legend><b>Delete EAF Record</b></legend>
					<input name="submitaction" type="submit" value="Delete EAF Record" />
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="mediaid" type="hidden" value="<?php echo $mediaid; ?>" />
					<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
					<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
					<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
				</fieldset>
			</form>
		</div>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
