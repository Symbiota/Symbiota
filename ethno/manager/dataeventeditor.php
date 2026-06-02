<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$ethnoDataManager = new EthnoDataManager();
$ethnoProjectManager = new EthnoProjectManager();

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ethno/manager/dataeventeditor.php?collid='.$collId.'&eventid='.$eventId.'&occid='.$occId.'&occindex='.$occIndex.'&csmode='.$csMode);

$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$eventId = array_key_exists('eventid',$_REQUEST)?$_REQUEST['eventid']:0;
$occId = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?$_REQUEST['occindex']:0;
$csMode = array_key_exists('csmode',$_REQUEST)?$_REQUEST['csmode']:0;
$tabIndex = array_key_exists("tabtarget",$_REQUEST)?$_REQUEST["tabtarget"]:0;
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($collId)) $collId = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;

$carryOverArr = array();
$ethnoUsePartsUsedTagArr = array();
$ethnoUseUseTagArr = array();
$ethnoDataArr = array();
$occidStr = '';

if($action === 'Create New Data Collection Event'){
	$ethnoDataManager->createDataCollectionEventRecord($_POST);
	$eventId = $ethnoDataManager->getEventId();
}
elseif($action === 'Save Data Collection Event Edits'){
	$ethnoDataManager->saveDataEventRecordChanges($_POST);
}
elseif($action === 'Submit New Data Record' || $action === 'Submit New Data Record and Carry Over'){
	$ethnoDataManager->createDataRecord($_POST);
}
elseif($action === 'Delete Data Record'){
	$ethnoDataManager->deleteDataRecord($_POST);
}

if($action === 'Submit New Data Record and Carry Over'){
	$carryOverArr["verbatimSciNameNew"] = $_POST['verbatimSciNameNew'];
	$carryOverArr["semanticTags"] = $_POST['semantics'];
	$carryOverArr["verbatimVernacularName"] = $_POST['verbatimVernacularName'];
	$carryOverArr["annotatedVernacularName"] = $_POST['annotatedVernacularName'];
	$carryOverArr["verbatimLanguage"] = $_POST['verbatimLanguage'];
	$carryOverArr["languageid"] = $_POST['languageid'];
	$carryOverArr["otherVerbatimVernacularName"] = $_POST['otherVerbatimVernacularName'];
	$carryOverArr["otherLangId"] = $_POST['otherLangId'];
	$carryOverArr["verbatimParse"] = $_POST['verbatimParse'];
	$carryOverArr["annotatedParse"] = $_POST['annotatedParse'];
	$carryOverArr["verbatimGloss"] = $_POST['verbatimGloss'];
	$carryOverArr["annotatedGloss"] = $_POST['annotatedGloss'];
	$carryOverArr["freetranslation"] = $_POST['freetranslation'];
	$carryOverArr["taxonomicDescription"] = $_POST['taxonomicDescription'];
	$carryOverArr["refpages"] = $_POST['refpages'];
	$carryOverArr["typology"] = $_POST['typology'];
	$carryOverArr["tid"] = $_POST['tid'];
	$carryOverArr["useTags"] = $_POST['uses'];
	$carryOverArr["partsTags"] = $_POST['parts'];
	$carryOverArr["consultantComments"] = $_POST['consultantComments'];
}

$ethnoDataManager->setCollid($collId);
$ethnoDataManager->setEventid($eventId);
$ethnoPersonnelArr = $ethnoDataManager->getPersonnelArr();
$ethnoCommunityArr = $ethnoDataManager->getCommunityArr();
$ethnoReferenceArr = $ethnoDataManager->getReferenceArr();
$ethnoDataEventArr = $ethnoDataManager->getDataEventArr();
$langArr = $ethnoProjectManager->getLangNameDropDownList($collId);
$ethnoNameSemanticTagArr = $ethnoDataManager->getNameSemanticTagArr();
$partsUsedTidArr = $ethnoDataManager->getPartsUsedTidArr();
$useTidArr = $ethnoDataManager->getUseTidArr();
$dataEventDetailArr = $ethnoDataManager->getDataEventDetailArr();
$dataEventPersonelArr = $dataEventDetailArr["personnelArr"];
$ethnoUsePartsUsedTagArr = $ethnoDataManager->getPartsUsedTagArrFull();
$ethnoUseUseTagArr = $ethnoDataManager->getUseTagArrFull();
if($occId){
	$ethnoDataManager->setOccId($occId);
	$occidStr = $ethnoDataManager->getOccTextStr();
	$ethnoDataArr = $ethnoDataManager->getOccDataArr();
}
else{
	$ethnoDataArr = $ethnoDataManager->getDataArr();
}
if(!$occId && $dataEventDetailArr['occid']){
	$ethnoDataManager->setOccId($dataEventDetailArr['occid']);
	$occidStr = $ethnoDataManager->getOccTextStr(false);
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Edit Data Collection Event</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=131106" type="text/javascript"></script>
	<script>
		var partsUsedTidArr = JSON.parse('<?php echo json_encode($partsUsedTidArr); ?>');
		var useTidArr = JSON.parse('<?php echo json_encode($useTidArr); ?>');

		$(document).ready(function() {
			processKingdomSelection('new',4);

			$('#tabs').tabs({
				select: function(event, ui) {
					return true;
				},
				active: <?php echo $tabIndex; ?>,
				beforeLoad: function( event, ui ) {
					$(ui.panel).html("<p>Loading...</p>");
				}
			});

			function split( val ) {
				return val.split( /,\s*/ );
			}

			$( "#ethnoNewSciName" )
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
							return false;
						},
						select: function( event, ui ) {
							var terms = this.value.split( /,\s*/ );
							terms.pop();
							terms.push( ui.item.value );
							document.getElementById('ethnoNewTaxaId').value = ui.item.id;
							getTaxonKingdom('new',ui.item.id);
							this.value = terms;
							return false;
						},
						change: function (event, ui) {
							if(!ui.item && this.value !== "") {
								document.getElementById('ethnoNewTaxaId').value = '';
								processKingdomSelection('new',0);
								alert("You must select a name from the list.");
							}
						}
					},
			{});
		});

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

		function verifyNewDataEventForm(f){
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

		function verifyEthnoDataForm(f){
			var sourceValue = document.getElementById('newEthnoDataEventSourceSelector').value;
			var colsultantVerified = false;
			for(var h=0;h<f.length;h++){
				if(f.elements[h].name === "consultant[]" && f.elements[h].checked){
					colsultantVerified = true;
				}
			}
			if(sourceValue === 'elicitation' && !colsultantVerified){
				alert("Please select at least one consultant.");
				return false;
			}
			else{
				return true;
			}
		}

		function processEthnoDataEventSourceSelectorChange(){
			var selValue = document.getElementById('newEthnoDataEventSourceSelector').value;
			if(selValue === 'elicitation'){
				document.getElementById('newEthnoDataEventReference').style.display = 'none';
				document.getElementById('newEthnoDataEventCommunity').style.display = 'block';
				document.getElementById('newEthnoDataEventPersonnel').style.display = 'block';
				document.getElementById('newEthnoDataEventDate').style.display = 'flex';
				document.getElementById('newEthnoDataEventLocation').style.display = 'flex';
				document.getElementById('newReferenceDiv').style.display = 'none';
				document.getElementById('newPersonnelDiv').style.display = 'block';
				document.getElementById('newNameTaxDescDiv').style.display = 'none';
				document.getElementById('newNameDiscussionDiv').style.display = 'flex';
				document.getElementById('newUseDiscussionDiv').style.display = 'flex';
				document.getElementById('newDataSource').value = 'elicitation';
				<?php
					if(!$occId){
						echo "document.getElementById('newOccLinkDiv').style.display = 'flex';";
					}
				?>
			}
			else if(selValue === 'reference'){
				document.getElementById('newEthnoDataEventReference').style.display = 'flex';
				document.getElementById('newEthnoDataEventCommunity').style.display = 'none';
				document.getElementById('newEthnoDataEventPersonnel').style.display = 'none';
				document.getElementById('newEthnoDataEventDate').style.display = 'none';
				document.getElementById('newEthnoDataEventLocation').style.display = 'none';
				document.getElementById('newReferenceDiv').style.display = 'flex';
				document.getElementById('newPersonnelDiv').style.display = 'none';
				document.getElementById('newNameTaxDescDiv').style.display = 'flex';
				document.getElementById('newNameDiscussionDiv').style.display = 'none';
				document.getElementById('newUseDiscussionDiv').style.display = 'none';
				document.getElementById('newDataSource').value = 'reference';
				<?php
				if(!$occId){
					echo "document.getElementById('newOccLinkDiv').style.display = 'none';";
				}
				?>
			}
		}

		function getPerTargetLanguage(perid,checkid,divid){
			if(document.getElementById(checkid).checked === true){
				var http = new XMLHttpRequest();
				var url = "rpc/gettargetlanguage.php";
				var params = 'perid='+perid+'&collid=<?php echo $collId; ?>';
				//console.log(url+'?'+params);
				http.open("POST", url, true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.onreadystatechange = function() {
					if(http.readyState == 4 && http.status == 200) {
						document.getElementById(divid).value = http.responseText;
					}
				};
				http.send(params);
			}
		}

		function checkSemanticParent(divid){
			document.getElementById(divid).checked = true;
		}

		function processRemoveOccurrenceLink(){
			var occid = document.getElementById('associateoccid').value;
			if(occid){
				getOccTaxaData(occid);
			}
		}

		function processOccAssociate(){
			var occid = document.getElementById('associateoccid').value;
			if(occid){
				document.getElementById('addSciNameDiv').style.display = 'none';
				document.getElementById('ethnoNewSciName').value = '';
				document.getElementById('ethnoNewTaxaId').value = '';
				getAssociatedOccKingdom('new',occid);
			}
			else{
				document.getElementById('addSciNameDiv').style.display = 'flex';
			}
		}

		function getOccTaxaData(occid){
			var http = new XMLHttpRequest();
			var url = "rpc/getocctaxadata.php";
			var params = 'occid='+occid;
			//console.log(url+'?'+params);
			http.open("POST", url, true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function() {
				if(http.readyState == 4 && http.status == 200) {
					var data = JSON.parse(http.responseText);
					removeOccurrenceLink(data);
				}
			};
			http.send(params);
		}

		function removeOccurrenceLink(data){
			var tid = (data.tid?data.tid:'');
			var sciname = (data.sciname?data.sciname:'');
			document.getElementById('associateoccid').value = '';
			document.getElementById('ethnoNewSciName').value = sciname;
			document.getElementById('ethnoNewTaxaId').value = tid;
			document.getElementById('addSciNameDiv').style.display = 'flex';
		}

		function getAssociatedOccKingdom(divid,occid){
			var http = new XMLHttpRequest();
			var url = "rpc/getocckingdom.php";
			var params = 'occid='+occid;
			//console.log(url+'?'+params);
			http.open("POST", url, true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function() {
				if(http.readyState == 4 && http.status == 200) {
					processKingdomSelection(divid,http.responseText);
				}
			};
			http.send(params);
		}

		function getTaxonKingdom(divid,tid){
			var http = new XMLHttpRequest();
			var url = "rpc/gettaxonkingdom.php";
			var params = 'tid='+tid;
			//console.log(url+'?'+params);
			http.open("POST", url, true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.onreadystatechange = function() {
				if(http.readyState == 4 && http.status == 200) {
					processKingdomSelection(divid,http.responseText);
				}
			};
			http.send(params);
		}

		function processKingdomSelection(divid,id){
			var zeroDisplay;
			var partZeroId = "part-"+divid+"-0";
			var useZeroId = "use-"+divid+"-0";
			if(id === 0) zeroDisplay = "block";
			else zeroDisplay = "none";
			document.getElementById(partZeroId).style.display = zeroDisplay;
			document.getElementById(useZeroId).style.display = zeroDisplay;
			for(i in partsUsedTidArr){
				var partId = "part-"+divid+"-"+partsUsedTidArr[i];
				if(partsUsedTidArr[i] == id) document.getElementById(partId).style.display = 'block';
				else document.getElementById(partId).style.display = 'none';
			}
			for(i in useTidArr){
				var useId = "use-"+divid+"-"+useTidArr[i];
				if(useTidArr[i] == id) document.getElementById(useId).style.display = 'block';
				else document.getElementById(useId).style.display = 'none';
			}
		}

		function openOccurrenceSearch(target) {
			occWindow=open("../../collections/misc/occurrencesearch.php?targetid="+target+"&collid=<?php echo $collId; ?>","occsearch","resizable=1,scrollbars=1,toolbar=1,width=750,height=600,left=20,top=20");
			occWindow.focus();
			if (occWindow.opener == null) occWindow.opener = self;
		}
	</script>
</head>
<body>
<?php
include($SERVER_ROOT.'/includes/header.php');

echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
echo '<a href="../../collections/misc/collprofiles.php?collid='.$collId.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
echo '<a href="dataeventlist.php?collid='.$collId.'">Manage Data Collection Events</a> &gt;&gt; ';
echo '<b>Edit Data Collection Event</b>';
echo '</div>';

if($occId){
	echo '<div style="margin:10px;color:red;">';
	echo 'All data entered will be associated with occurrence '.$occidStr;
	echo '</div>';
	echo '<div style="margin:10px;">';
	echo '<a href="dataeventeditor.php?collid='.$collId.'&eventid='.$eventId.'">Click here to remove associated occurrence record</a>';
	echo '</div>';
	echo '<div style="margin:10px;">';
	echo '<a href="../../collections/editor/occurrenceeditor.php?collid='.$collId.'&occid='.$occId.'&occindex='.$occIndex.'&csmode='.$csMode.'&tabtarget=3">Return to Occurrence Editor</a>';
	echo '</div>';
}
?>
<!-- This is inner text! -->
<div id="innertext">
	<h2><?php echo $ethnoProjectManager->getCollectionName(); ?></h2>
	<?php
	if($eventId){
		if($dataEventDetailArr['occid']){
			echo '<div style="margin-left:15px;margin-bottom:10px;color:blue;font-weight:bold;">';
			echo 'Collection record: '.$occidStr;
			echo '</div>';
		}
		?>
		<div id="tabs" style="margin:0px;">
			<ul>
				<li><a href="#dataEventDetailsDiv">Event Details</a></li>
				<li><a href="#dataEventDataDiv">Data</a></li>
				<li><a href="#dataEventAdminDiv">Admin</a></li>
			</ul>

			<div id="dataEventDetailsDiv" style="">
				<?php
				if($dataEventDetailArr['occid']){
					echo '<div style="margin-bottom:15px;">';
					echo '<a style="color:red;" href="../../collections/editor/occurrenceeditor.php?collid='.$collId.'&occid='.$dataEventDetailArr['occid'].'" target="_blank">Open associated Occurrence record</a>';
					echo '</div>';
				}
				?>
				<form name="dataeventeditform" action="dataeventeditor.php" method="post" onsubmit="return verifyNewDataEventForm(this);">
					<div id="newEthnoDataEventSource" style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Data source:</b></span>
						<select id="newEthnoDataEventSourceSelector" name="datasource" onchange="processEthnoDataEventSourceSelectorChange();">
							<option value="elicitation" <?php echo ($dataEventDetailArr['datasource']==='elicitation'?'selected':''); ?>>Elicitation</option>
							<option value="reference" <?php echo ($dataEventDetailArr['datasource']==='reference'?'selected':''); ?>>Reference</option>
						</select>
					</div>
					<div id="newEthnoDataEventReference" style="<?php echo ($dataEventDetailArr['datasource']!=='reference'?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Reference:</b></span>
						<?php
						if($ethnoReferenceArr){
							?>
							<select id="ethnoNewDataEventReference" name="refid" style="width:500px;">
								<option value="">----Select reference----</option>
								<?php
								foreach($ethnoReferenceArr as $k => $v){
									echo '<option value="'.$v['refid'].'" '.($dataEventDetailArr['refid']==$v['refid']?'selected':'').'>'.$v['title'].'</option>';
								}
								?>
							</select>
							<?php
						}
						else{
							echo '<div>';
							echo 'There are no references associated with this project.<br />';
							echo 'Please go to the <a href="../../ethno/manager/index.php?collid='.$collId.'&tabindex=3" target="_blank">Reference Management tab</a> to add references.';
							echo '</div>';
						}
						?>
					</div>
					<div id="newEthnoDataEventCommunity" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:none;':''); ?>clear:both;<?php echo ($ethnoDataEventArr?'margin-top:10px;':''); ?>">
						<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('EthnoCommunity');">
							<div id='plusButtonEthnoCommunity' style="display:none;align-items:center;">
								Community: <img style='border:0;margin-left:8px;width:13px;' src='../../images/plus.png' />
							</div>
							<div id='minusButtonEthnoCommunity' style="display:flex;align-items:center;">
								Community: <img style='border:0;margin-left:8px;width:13px;' src='../../images/minus.png' />
							</div>
						</div>
						<div id="contentEthnoCommunity" style="display:block;padding-left:15px;clear:both;">
							<?php
							foreach($ethnoCommunityArr as $id => $pArr){
								echo '<input type="radio" name="ethComID" value="'.$id.'" '.($dataEventDetailArr['ethComID']==$id?'checked ':'').'> '.$pArr['communityname'].'<br />';
							}
							?>
						</div>
					</div>
					<div id="newEthnoDataEventPersonnel" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:none;':''); ?>clear:both;margin-top:10px;">
						<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('EthnoConsultants');">
							<div id='plusButtonEthnoConsultants' style="display:none;align-items:center;">
								Consultants: <img style='border:0;margin-left:8px;width:13px;' src='../../images/plus.png' />
							</div>
							<div id='minusButtonEthnoConsultants' style="display:flex;align-items:center;">
								Consultants: <img style='border:0;margin-left:8px;width:13px;' src='../../images/minus.png' />
							</div>
						</div>
						<div id="contentEthnoConsultants" style="display:block;padding-left:15px;clear:both;">
							<?php
							foreach($ethnoPersonnelArr as $id => $pArr){
								$name = $pArr['title'].' '.$pArr['firstname'].' '.$pArr['lastname'];
								$onChangeLine = "'consultantNew".$id."','ethnoNewNameLanguage'";
								echo '<input name="consultant[]" value="'.$id.'" type="checkbox" onchange="getPerTargetLanguage('.$id.','.$onChangeLine.');" '.(in_array($id,$dataEventPersonelArr)?'checked ':'').'/> '.$name.'<br />';
							}
							?>
						</div>
					</div>
					<div id="newEthnoDataEventDate" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Date:</b></span>
						<input name="eventdate" type="text" style="width:500px;" value="<?php echo $dataEventDetailArr['eventdate']; ?>" />
					</div>
					<div id="newEthnoDataEventLocation" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Location:</b></span>
						<textarea name="eventlocation" style="width:500px;height:50px;resize:vertical;"><?php echo $dataEventDetailArr['eventlocation']; ?></textarea>
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Synoptic discussion on name data:</b></span>
						<textarea name="namedatadiscussion" style="width:500px;height:50px;resize:vertical;"><?php echo $dataEventDetailArr['namedatadiscussion']; ?></textarea>
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Synoptic discussion on use data:</b></span>
						<textarea name="usedatadiscussion" style="width:500px;height:50px;resize:vertical;"><?php echo $dataEventDetailArr['usedatadiscussion']; ?></textarea>
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Synoptic discussion on consultants:</b></span>
						<textarea name="consultantdiscussion" style="width:500px;height:50px;resize:vertical;"><?php echo $dataEventDetailArr['consultantdiscussion']; ?></textarea>
					</div>
					<div style="clear:both;float:right;margin-top:10px;">
						<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
						<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
						<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
						<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
						<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
						<input type="submit" name="submitaction" value="Save Data Collection Event Edits" />
					</div>
				</form>
			</div>

			<div id="dataEventDataDiv" style="">
				<div style="float:right;margin-bottom:10px;cursor:pointer;<?php echo (!$ethnoDataArr?'display:none;':''); ?>" onclick="toggle('adddatadiv');" title="Add a New Data Record">
					<img style="border:0;width:12px;" src="../../images/add.png" />
				</div>
				<div id="adddatadiv" style="clear:both;<?php echo (($ethnoDataArr&&!$carryOverArr)?'display:none;':''); ?>">
					<form name="datanewform" action="dataeventeditor.php" method="post" onsubmit="return verifyEthnoDataForm(this);">
						<fieldset style="padding:15px">
							<legend><b>Add a New Data Record</b></legend>
							<fieldset style="padding:15px">
								<div id="addSciNameDiv" style="<?php echo ($occId?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
									<span style="font-size:13px;"><b>Scientific name:</b></span>
									<input id="ethnoNewSciName" name="verbatimSciNameNew" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["verbatimSciNameNew"]:''); ?>" />
								</div>
								<div id="newOccLinkDiv" style="<?php echo (($occId||$dataEventDetailArr['datasource']==='reference')?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
									<span style="font-size:13px;"><b>Associate occurrence:</b></span>
									<span>
									<span style="font-size:12px;cursor:pointer;color:blue;margin-right:5px;"  onclick="openOccurrenceSearch('associateoccid')">Open Occurrence Linking Aid</span> <input id="associateoccid" name="associateoccid" type="text" onchange="processOccAssociate();" value="<?php echo ($occId?$occId:''); ?>" readonly/>
									<span style="font-size:12px;cursor:pointer;color:blue;margin-right:5px;" onclick="processRemoveOccurrenceLink();">Remove Link</span>
								</span>
								</div>
								<div id="newReferenceDiv" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:flex;':'display:none;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
									<span style="font-size:13px;"><b>Reference pages:</b></span>
									<input name="refpages" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["refpages"]:''); ?>" />
								</div>
								<div id="newPersonnelDiv" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:none;':'display:block;'); ?>clear:both;margin-top:10px;">
									<?php
									if($ethnoPersonnelArr){
										?>
										<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('EthnoConsultants');">
											<div id='plusButtonEthnoConsultants' style="display:none;align-items:center;">
												Consultants: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
											</div>
											<div id='minusButtonEthnoConsultants' style="display:flex;align-items:center;">
												Consultants: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
											</div>
										</div>
										<div id="contentEthnoConsultants" style="display:block;padding-left:15px;clear:both;">
											<?php
											foreach($ethnoPersonnelArr as $id => $pArr){
												$name = $pArr['title'].' '.$pArr['firstname'].' '.$pArr['lastname'];
												$onChangeLine = "'consultantNew".$id."','ethnoNewLanguage'";
												echo '<input id="consultantNew'.$id.'" name="consultant[]" value="'.$id.'" type="checkbox" onchange="getPerTargetLanguage('.$id.','.$onChangeLine.');"/> '.$name.'<br />';
											}
											?>
										</div>
										<?php
									}
									else{
										echo '<div style="clear:both;">';
										echo 'There are no consultants associated with this project.<br />';
										echo 'Please go to the <a href="../../ethno/manager/editpersonnel.php?collid='.$collId.'" target="_blank">Personnel Management page</a> to add consultants.';
										echo '</div>';
									}
									?>
								</div>
							</fieldset>
							<fieldset style="margin-top:10px;padding:15px">
								<div style="clear:both;margin-top:10px;">
									<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('NameSemantic');">
										<div id='plusButtonNameSemantic' style="display:none;align-items:center;">
											Semantic Tags: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
										</div>
										<div id='minusButtonNameSemantic' style="display:flex;align-items:center;">
											Semantic Tags: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
										</div>
									</div>
									<div id="contentNameSemantic" style="display:block;padding-left:15px;clear:both;">
										<?php
										foreach($ethnoNameSemanticTagArr as $id => $stArr){
											$pTag = $stArr['ptag'];
											$pDesc = $stArr['pdesc'];
											$pTagLine = $pTag.' '.$pDesc;
											$checkStr = "'sempar-".$id."'";
											echo '<input name="semantics[]" id="sempar-'.$id.'" value="'.$id.'" type="checkbox" '.(($carryOverArr&&in_array($id,$carryOverArr["semanticTags"]))?'checked ':'').'/> '.$pTagLine.'<br />';
											unset($stArr['ptag']);
											unset($stArr['pdesc']);
											if($stArr){
												echo '<div style="padding-left:15px;clear:both;">';
												foreach($stArr as $cid => $cArr){
													$cTag = $cArr['ctag'];
													$cDesc = $cArr['cdesc'];
													$cTagLine = $cTag.' '.$cDesc;
													echo '<input name="semantics[]" value="'.$cid.'" type="checkbox" onchange="checkSemanticParent('.$checkStr.');" '.(($carryOverArr&&in_array($cid,$carryOverArr["semanticTags"]))?'checked ':'').'/> '.$cTagLine.'<br />';
												}
												echo '</div>';
											}
										}
										?>
									</div>
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Verbatim vernacular name:</b></span>
									<input name="verbatimVernacularName" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["verbatimVernacularName"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Annotated vernacular name:</b></span>
									<input name="annotatedVernacularName" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["annotatedVernacularName"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Verbatim language:</b></span>
									<input name="verbatimLanguage" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["verbatimLanguage"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Glottolog language:</b></span>
									<select id="ethnoNewLanguage" name="languageid" style="width:500px;">
										<option value="">----Select Language----</option>
										<?php
										foreach($langArr as $k => $v){
											echo '<option value="'.$v['id'].'" '.(($carryOverArr&&$carryOverArr["languageid"]==$v['id'])?'selected':'').'>'.$v['name'].'</option>';
										}
										?>
									</select>
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Other verbatim vernacular name:</b></span>
									<input name="otherVerbatimVernacularName" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["otherVerbatimVernacularName"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Glottolog language:</b></span>
									<select name="otherLangId" style="width:500px;">
										<option value="">----Select Language----</option>
										<?php
										foreach($langArr as $k => $v){
											echo '<option value="'.$v['id'].'" '.(($carryOverArr&&$carryOverArr["otherLangId"]==$v['id'])?'selected':'').'>'.$v['name'].'</option>';
										}
										?>
									</select>
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Verbatim parse:</b></span>
									<input name="verbatimParse" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["verbatimParse"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Annotated parse:</b></span>
									<input name="annotatedParse" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["annotatedParse"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Verbatim gloss:</b></span>
									<input name="verbatimGloss" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["verbatimGloss"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Annotated gloss:</b></span>
									<input name="annotatedGloss" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["annotatedGloss"]:''); ?>" />
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Free translation:</b></span>
									<input name="freetranslation" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["freetranslation"]:''); ?>" />
								</div>
								<div id="newNameTaxDescDiv" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:flex;':'display:none;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
									<span style="font-size:13px;"><b>Taxonomic description:</b></span>
									<textarea name="taxonomicDescription" style="width:500px;height:50px;resize:vertical;"><?php echo ($carryOverArr?$carryOverArr["taxonomicDescription"]:''); ?></textarea>
								</div>
								<div id="newNameDiscussionDiv" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
									<span style="font-size:13px;"><b>Consultant comments on name:</b></span>
									<textarea name="nameDiscussion" style="width:500px;height:50px;resize:vertical;"></textarea>
								</div>
								<div style="clear:both;margin-top:10px;">
									<span style="font-size:13px;"><b>Typology:</b></span>
									<div style="clear:both;margin-left:20px;">
										<input type="radio" name="typology" value="opaque" <?php echo (($carryOverArr&&$carryOverArr["typology"]==='opaque')?'checked':''); ?>> Opaque<br />
										<input type="radio" name="typology" value="transparent" <?php echo (($carryOverArr&&$carryOverArr["typology"]==='transparent')?'checked':''); ?>> Transparent<br />
										<input type="radio" name="typology" value="modifiedopaque" <?php echo (($carryOverArr&&$carryOverArr["typology"]==='modifiedopaque')?'checked':''); ?>> Modified opaque<br />
										<input type="radio" name="typology" value="modifiedtransparent" <?php echo (($carryOverArr&&$carryOverArr["typology"]==='modifiedtransparent')?'checked':''); ?>> Modified transparent
									</div>
								</div>
							</fieldset>
							<fieldset style="margin-top:10px;padding:15px">
								<div style="clear:both;margin-top:10px;">
									<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('UsePartsUsed');">
										<div id='plusButtonUsePartsUsed' style="display:none;align-items:center;">
											Parts used: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
										</div>
										<div id='minusButtonUsePartsUsed' style="display:flex;align-items:center;">
											Parts used: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
										</div>
									</div>
									<div id="contentUsePartsUsed" style="display:block;padding-left:15px;clear:both;">
										<?php
										echo '<div id="part-new-0" style="display:block;">Please enter a scientific name to display parts used options.</div>';
										foreach($ethnoUsePartsUsedTagArr as $tid => $tidPArr){
											echo '<div id="part-new-'.$tid.'" style="display:none;">';
											foreach($tidPArr as $id => $text){
												echo '<input name="parts[]" value="'.$id.'" type="checkbox" '.(($carryOverArr&&in_array($id,$carryOverArr["partsTags"]))?'checked ':'').'/> '.$text.'<br />';
											}
											echo '</div>';
										}
										?>
									</div>
								</div>
								<div style="clear:both;margin-top:10px;">
									<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('UseUses');">
										<div id='plusButtonUseUses' style="display:flex;align-items:center;">
											Uses: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
										</div>
										<div id='minusButtonUseUses' style="display:none;align-items:center;">
											Uses: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
										</div>
									</div>
									<div id="contentUseUses" style="display:none;padding-left:15px;clear:both;">
										<?php
										echo '<div id="use-new-0" style="display:block;">Please enter a scientific name to display use options.</div>';
										foreach($ethnoUseUseTagArr as $tid => $tidUArr){
											echo '<div id="use-new-'.$tid.'" style="display:none;">';
											foreach($tidUArr as $id => $uArr){
												$header = $uArr['header'];
												unset($uArr['header']);
												if($header){
													$headerStr = str_replace(' ','',$header);
													echo '<div style="clear:both;margin-top:10px;">';
													?>
													<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('<?php echo $headerStr; ?>');">
														<div id='plusButton<?php echo $headerStr; ?>' style="display:flex;align-items:center;">
															<?php echo $header; ?>: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
														</div>
														<div id='minusButton<?php echo $headerStr; ?>' style="display:none;align-items:center;">
															<?php echo $header; ?>: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
														</div>
													</div>
													<?php
													echo '<div id="content'.$headerStr.'" style="display:none;padding-left:15px;clear:both;">';
													foreach($uArr as $uid => $text){
														echo '<input name="uses[]" value="'.$uid.'" type="checkbox" '.(($carryOverArr&&in_array($uid,$carryOverArr["useTags"]))?'checked ':'').'/> '.$text.'<br />';
													}
													echo '</div>';
													echo '</div>';
												}
												else{
													foreach($uArr as $uid => $text){
														echo '<input name="uses[]" value="'.$uid.'" type="checkbox" '.(($carryOverArr&&in_array($uid,$carryOverArr["useTags"]))?'checked ':'').'/> '.$text.'<br />';
													}
												}
											}
											echo '</div>';
										}
										?>
									</div>
								</div>
								<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
									<span style="font-size:13px;"><b>Other uses:</b></span>
									<input name="consultantComments" type="text" style="width:500px;" value="<?php echo ($carryOverArr?$carryOverArr["consultantComments"]:''); ?>" />
								</div>
								<div id="newUseDiscussionDiv" style="<?php echo ($dataEventDetailArr['datasource']==='reference'?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
									<span style="font-size:13px;"><b>Consultant comments on use:</b></span>
									<textarea name="useDiscussion" style="width:500px;height:50px;resize:vertical;"></textarea>
								</div>
							</fieldset>
							<div style="clear:both;float:right;margin-top:10px;">
								<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
								<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
								<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
								<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
								<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
								<input type="hidden" id="newDataSource" name="datasource" value="<?php echo $dataEventDetailArr['datasource']; ?>" />
								<input type="hidden" id="ethnoNewTaxaId" name="tid" value="<?php echo ($carryOverArr?$carryOverArr["tid"]:''); ?>" />
								<input type="hidden" name="tabtarget" value="1" />
								<input type="submit" name="submitaction" value="Submit New Data Record" />
								<input type="submit" name="submitaction" value="Submit New Data Record and Carry Over" />
							</div>
						</fieldset>
					</form>
				</div>
				<?php
				if($ethnoDataArr){
					?>
					<hr style="clear:both;margin:30px 0px;" />
					<div style="clear:both;margin:15px;">
						<?php
						foreach($ethnoDataArr as $dataId => $dataArr){
							$dataPersonelStr = '';
							$dataSemanticsStr = '';
							$dataTypologyStr = '';
							$dataUseStr = '';
							$dataPartsStr = '';
							$kingdomid = ($dataArr["kingdomId"]?$dataArr["kingdomId"]:4);
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
							foreach($ethnoUsePartsUsedTagArr[$kingdomid] as $id => $text){
								if(in_array($id,$dataPartsArr)){
									$dataPartsStr .= $text.'; ';
								}
							}
							if($dataPartsStr) $dataPartsStr = substr($dataPartsStr,0,-2);
							foreach($ethnoUseUseTagArr[$kingdomid] as $id => $uArr){
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
								<a href="dataeditor.php?dataid=<?php echo $dataId; ?>&collid=<?php echo $collId; ?>&eventid=<?php echo $eventId; ?>">
									<img style="border:0;width:15px;" src="../../images/edit.png" />
								</a>
							</div>
							<div style="margin-top:10px">
								<?php
								if($dataArr["sciname"]){
									?>
									<div style="font-size:13px;">
										<b>Scientific name:</b>
										<?php echo $dataArr["sciname"]; ?>
									</div>
									<?php
								}
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
					</div>
					<?php
				}
				?>
			</div>

			<div id="dataEventAdminDiv" style="">
				<form name="deldataeventform" action="<?php echo ($occId?'../../collections/editor/occurrenceeditor.php':'dataeventlist.php'); ?>" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this Data Collection Event record?')">
					<fieldset style="width:350px;margin:20px;padding:20px;">
						<legend><b>Delete Data Event Record</b></legend>
						<?php
						if($ethnoDataArr){
							echo '<div style="font-weight:bold;margin-bottom:15px;">';
							echo 'Data collection event record cannot be deleted until all associated data is deleted.';
							echo '</div>';
						}
						?>
						<input name="submitaction" type="submit" value="Delete Data Event Record" <?php if($ethnoDataArr) echo 'DISABLED'; ?> />
						<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
						<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
						<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
						<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
						<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
					</fieldset>
				</form>
			</div>
		</div>
		<?php
	}
	else{
		?>
		<div style='font-weight:bold;'>
			Data collection event has not been identified
		</div>
		<?php
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
