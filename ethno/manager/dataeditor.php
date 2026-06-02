<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');
include_once($SERVER_ROOT.'/classes/EthnoProjectManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$ethnoDataManager = new EthnoDataManager();
$ethnoProjectManager = new EthnoProjectManager();

if(!$SYMB_UID) {
	header('Location: ../../profile/index.php?refurl=../ethno/manager/dataeditor.php?collid=' . $collId . '&eventid=' . $eventId . '&occid=' . $occId . '&occindex=' . $occIndex . '&csmode=' . $csMode . '&dataid=' . $dataId);
}

$collId = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$eventId = array_key_exists('eventid',$_REQUEST)?$_REQUEST['eventid']:0;
$dataId = array_key_exists('dataid',$_REQUEST)?$_REQUEST['dataid']:0;
$occId = array_key_exists('occid',$_REQUEST)?$_REQUEST['occid']:0;
$occIndex = array_key_exists('occindex',$_REQUEST)?$_REQUEST['occindex']:0;
$csMode = array_key_exists('csmode',$_REQUEST)?$_REQUEST['csmode']:0;
$tabIndex = array_key_exists("tabtarget",$_REQUEST)?$_REQUEST["tabtarget"]:0;
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

$genusMatch = array_key_exists("genusMatch",$_REQUEST);
$scinameMatch = array_key_exists("scinameMatch",$_REQUEST);
$semanticMatch = array_key_exists("semanticMatch",$_REQUEST);
$levenshteinMatch = array_key_exists("levenshteinMatch",$_REQUEST);
$levenshteinValue = array_key_exists('levenshteinValue',$_REQUEST)?$_REQUEST['levenshteinValue']:0;
$vernacularDiffLang = array_key_exists("vernacularDiffLang",$_REQUEST);
$vernacularStringMatch = array_key_exists("vernacularStringMatch",$_REQUEST);
$vernacularStringMatchValue = array_key_exists('vernacularStringMatchValue',$_REQUEST)?$_REQUEST['vernacularStringMatchValue']:'';
$vernacularRegexMatch = array_key_exists("vernacularRegexMatch",$_REQUEST);
$vernacularRegexMatchValue = array_key_exists('vernacularRegexMatchValue',$_REQUEST)?$_REQUEST['vernacularRegexMatchValue']:'';
$verbatimParseMatch = array_key_exists("verbatimParseMatch",$_REQUEST);
$verbatimParseValue = array_key_exists('verbatimParseValue',$_REQUEST)?$_REQUEST['verbatimParseValue']:'';
$verbatimParseRegexMatch = array_key_exists("verbatimParseRegexMatch",$_REQUEST);
$verbatimParseRegexMatchValue = array_key_exists('verbatimParseRegexMatchValue',$_REQUEST)?$_REQUEST['verbatimParseRegexMatchValue']:'';
$verbatimGlossMatch = array_key_exists("verbatimGlossMatch",$_REQUEST);
$verbatimGlossValue = array_key_exists('verbatimGlossValue',$_REQUEST)?$_REQUEST['verbatimGlossValue']:'';
$verbatimGlossRegexMatch = array_key_exists("verbatimGlossRegexMatch",$_REQUEST);
$verbatimGlossRegexMatchValue = array_key_exists('verbatimGlossRegexMatchValue',$_REQUEST)?$_REQUEST['verbatimGlossRegexMatchValue']:'';

//Sanitation
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) {
	$action = '';
}
if(!is_numeric($collId)) {
	$collId = 0;
}
if(!is_numeric($tabIndex)) {
	$tabIndex = 0;
}

$linkageSearchReturnArr = array();
$occidStr = '';

if($action === 'Edit Data Record'){
	$ethnoDataManager->saveDataRecordChanges($_POST);
}
elseif($action === 'Add Linkage'){
	$ethnoDataManager->createDataLinkage($_POST);
}
elseif($action === 'Edit Linkage'){
	$ethnoDataManager->saveDataLinkageChanges($_POST);
}
elseif($action === 'Delete Linkage'){
	$ethnoDataManager->deleteDataLinkage($_POST);
}
elseif($action === 'Find records'){
	$ethnoDataManager->prepareLinkageSqlWhere($_POST);
	$linkageSearchReturnArr = $ethnoDataManager->getLinkageSearchReturn($_POST);;
}

$ethnoDataManager->setCollid($collId);
$ethnoPersonnelArr = $ethnoDataManager->getPersonnelArr();
$ethnoReferenceArr = $ethnoDataManager->getReferenceArr();
$langArr = $ethnoProjectManager->getLangNameDropDownList($collId);
$ethnoNameSemanticTagArr = $ethnoDataManager->getNameSemanticTagArr();
$partsUsedTidArr = $ethnoDataManager->getPartsUsedTidArr();
$useTidArr = $ethnoDataManager->getUseTidArr();
$ethnoUsePartsUsedTagArr = $ethnoDataManager->getPartsUsedTagArrFull();
$ethnoUseUseTagArr = $ethnoDataManager->getUseTagArrFull();
$dataArr = $ethnoDataManager->getDataEditArr($dataId);
$linkageArr = $ethnoDataManager->getLinkageArr($dataId);
$personelArr = $dataArr["personnelArr"];
$nameSemanticsArr = $dataArr["semanticTags"];
$useUseArr = $dataArr["useTags"];
$usePartsArr = $dataArr["partsTags"];
if($dataArr["occid"]){
	$ethnoDataManager->setOccId($dataArr["occid"]);
	$occidStr = $ethnoDataManager->getOccTextStr(false);
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Edit Vernacular Data</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<style type="text/css"></style>
	<script src="../../js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=131106" type="text/javascript"></script>
	<script type="text/javascript">
		var partsUsedTidArr = JSON.parse('<?php echo json_encode($partsUsedTidArr); ?>');
		var useTidArr = JSON.parse('<?php echo json_encode($useTidArr); ?>');

		$(document).ready(function() {
			processKingdomSelection(4);

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

			$( "#ethnoSciName" )
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
						focus: function() {
							return false;
						},
						autoFocus: true,
						select: function( event, ui ) {
							var terms = this.value.split( /,\s*/ );
							terms.pop();
							terms.push( ui.item.value );
							document.getElementById('ethnoTaxaId').value = ui.item.id;
							getTaxonKingdom('new',ui.item.id);
							this.value = terms;
							return false;
						},
						change: function (event, ui) {
							if(!ui.item && this.value !== "") {
								document.getElementById('ethnoTaxaId').value = '';
								processKingdomSelection(4);
								alert("You must select a name from the list.");
							}
						}
					},
			{});

			$( "#ethnoLinkageName" )
				.bind( "keydown", function( event ) {
					if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
						event.preventDefault();
					}
				})
				.autocomplete({
					source: function( request, response ) {
						$.getJSON( "rpc/autofillvernacularname.php", {
							name: request.term
						}, response );
					},
					search: function() {
						var term = this.value;
						if ( term.length < 1 ) {
							return false;
						}
					},
					focus: function() {
						return false;
					},
					select: function( event, ui ) {
						var terms = this.value.split( /,\s*/ );
						terms.pop();
						terms.push( ui.item.value );
						document.getElementById('ethnoLinkageNameId').value = ui.item.id;
						this.value = terms;
						return false;
					},
					change: function (event, ui) {
						if (!ui.item) {
							this.value = '';
							document.getElementById('ethnoLinkageNameId').value = '';
							alert("Vernacular name must be selected from the list.");
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

		function verifyEthnoDataForm(f){
			var sourceValue = document.getElementById('editDataSource').value;
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

		function verifyNameLinkageForm(f){
			var linkVerified = false;
			var linkTypeVerified = false;
			for(var h=0;h<f.length;h++){
				if(f.elements[h].name === "linkdataid[]" && f.elements[h].checked){
					linkVerified = true;
				}
				if(f.elements[h].name === "linktype" && f.elements[h].checked){
					linkTypeVerified = true;
				}
			}
			if(!linkVerified){
				alert("Please select a record to link.");
				return false;
			}
			if(!linkTypeVerified){
				alert("Please select a link type.");
				return false;
			}
			else{
				return true;
			}
		}

		function getPerTargetLanguage(perid,checkid,divid){
			if(document.getElementById(checkid).checked === true){
				var http = new XMLHttpRequest();
				var url = "../includes/rpc/gettargetlanguage.php";
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

		function processOccAssociate(){
			var occid = document.getElementById('associateoccid').value;
			if(occid){
				document.getElementById('editSciNameDiv').style.display = 'none';
				document.getElementById('ethnoSciName').value = '';
				document.getElementById('ethnoTaxaId').value = '';
				getAssociatedOccKingdom('new',occid);
			}
			else{
				document.getElementById('editUseSciNameDiv').style.display = 'flex';
			}
		}

		function processRemoveOccurrenceLink(){
			var occid = document.getElementById('associateoccid').value;
			if(occid){
				getOccTaxaData(occid);
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
			document.getElementById('ethnoSciName').value = sciname;
			document.getElementById('ethnoTaxaId').value = tid;
			document.getElementById('editSciNameDiv').style.display = 'flex';
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
					processKingdomSelection(http.responseText);
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
					processKingdomSelection(http.responseText);
				}
			};
			http.send(params);
		}

		function processKingdomSelection(id){
			var zeroDisplay;
			var partZeroId = "part-0";
			var useZeroId = "use-0";
			if(id === 0) zeroDisplay = "block";
			else zeroDisplay = "none";
			document.getElementById(partZeroId).style.display = zeroDisplay;
			document.getElementById(useZeroId).style.display = zeroDisplay;
			for(i in partsUsedTidArr){
				var partId = "part-"+partsUsedTidArr[i];
				if(partsUsedTidArr[i] == id) document.getElementById(partId).style.display = 'block';
				else document.getElementById(partId).style.display = 'none';
			}
			for(i in useTidArr){
				var useId = "use-"+useTidArr[i];
				if(useTidArr[i] == id) document.getElementById(useId).style.display = 'block';
				else document.getElementById(useId).style.display = 'none';
			}
		}

		function openOccurrenceSearch(target) {
			occWindow=open("../../collections/misc/occurrencesearch.php?targetid="+target+"&collid=<?php echo $collId; ?>","occsearch","resizable=1,scrollbars=1,toolbar=1,width=750,height=600,left=20,top=20");
			occWindow.focus();
			if (occWindow.opener == null) occWindow.opener = self;
		}

		function verifyLinkageSearchForm(){
			var genusMatch = document.getElementById('genusMatch').checked;
			var scinameMatch = document.getElementById('scinameMatch').checked;
			var semanticMatch = document.getElementById('semanticMatch').checked;
			var levenshteinMatch = document.getElementById('levenshteinMatch').checked;
			var levenshteinValue = document.getElementById('levenshteinValue').value;
			var vernacularDiffLang = document.getElementById('vernacularDiffLang').checked;
			var vernacularStringMatch = document.getElementById('vernacularStringMatch').checked;
			var vernacularStringMatchValue = document.getElementById('vernacularStringMatchValue').value;
			var vernacularRegexMatch = document.getElementById('vernacularRegexMatch').checked;
			var vernacularRegexMatchValue = document.getElementById('vernacularRegexMatchValue').value;
			var verbatimParseMatch = document.getElementById('verbatimParseMatch').checked;
			var verbatimParseValue = document.getElementById('verbatimParseValue').value;
			var verbatimParseRegexMatch = document.getElementById('verbatimParseRegexMatch').checked;
			var verbatimParseRegexMatchValue = document.getElementById('verbatimParseRegexMatchValue').value;
			var verbatimGlossMatch = document.getElementById('verbatimGlossMatch').checked;
			var verbatimGlossValue = document.getElementById('verbatimGlossValue').value;
			var verbatimGlossRegexMatch = document.getElementById('verbatimGlossRegexMatch').checked;
			var verbatimGlossRegexMatchValue = document.getElementById('verbatimGlossRegexMatchValue').value;

			if(!genusMatch && !scinameMatch && !semanticMatch && !levenshteinMatch && !vernacularDiffLang && !vernacularStringMatch && !vernacularRegexMatch && !verbatimParseMatch && !verbatimParseRegexMatch && !verbatimGlossMatch && !verbatimGlossRegexMatch){
				alert('Please select at least one criteria to search for linkages.');
				return false;
			}

			if(genusMatch && scinameMatch){
				alert('Matching genus and matching scientific name cannot both be selected.');
				return false;
			}

			if(levenshteinMatch && (!levenshteinValue || levenshteinValue == "")){
				alert('Please enter a minimum Levenshtein Distance value.');
				return false;
			}

			if(levenshteinMatch && isNaN(levenshteinValue)){
				alert('The minimum Levenshtein Distance must be a number.');
				return false;
			}

			if(vernacularStringMatch && (!vernacularStringMatchValue || vernacularStringMatchValue == "")){
				alert('Please enter a string to search for within vernacular names.');
				return false;
			}

			if(vernacularRegexMatch && (!vernacularRegexMatchValue || vernacularRegexMatchValue == "")){
				alert('Please enter the regex criteria for vernacular names.');
				return false;
			}

			if(verbatimParseMatch && (!verbatimParseValue || verbatimParseValue == "")){
				alert('Please enter a verbatim parse value.');
				return false;
			}

			if(verbatimParseRegexMatch && (!verbatimParseRegexMatchValue || verbatimParseRegexMatchValue == "")){
				alert('Please enter the regex criteria for the verbatim parse value.');
				return false;
			}

			if(verbatimGlossMatch && (!verbatimGlossValue || verbatimGlossValue == "")){
				alert('Please enter a verbatim gloss value.');
				return false;
			}

			if(verbatimGlossRegexMatch && (!verbatimGlossRegexMatchValue || verbatimGlossRegexMatchValue == "")){
				alert('Please enter the regex criteria for the verbatim gloss value.');
				return false;
			}

			return true;
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');

echo '<div class="navpath">';
echo '<a href="../../index.php">Home</a> &gt;&gt; ';
if($collId) echo '<a href="../../collections/misc/collprofiles.php?collid='.$collId.'&emode=1">Collection Control Panel</a> &gt;&gt; ';
echo '<a href="dataeventlist.php?collid='.$collId.'">Manage Data Collection Events</a> &gt;&gt; ';
echo '<b>Edit Vernacular Data</b>';
echo '</div>';

if($occId){
	echo '<div style="margin:10px;">';
	echo '<a href="../../collections/editor/occurrenceeditor.php?collid='.$collId.'&occid='.$occId.'&occindex='.$occIndex.'&csmode='.$csMode.'&tabtarget=3">Return to Occurrence Editor</a>';
	echo '</div>';
}
if($eventId){
	echo '<div style="margin:10px;">';
	echo '<a href="dataeventeditor.php?eventid='.$eventId.'&collid='.$collId.'&tabtarget=1">Return to Data Collection Event Editor</a>';
	echo '</div>';
}
if($action === 'Find records' && !$linkageSearchReturnArr){
	echo '<div style="margin:10px;font-weight:bold;color:red;">';
	echo 'There were no records matching your criteria.';
	echo '</div>';
}
?>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($dataArr["occid"]){
		echo '<div style="margin-left:15px;margin-bottom:10px;color:blue;font-weight:bold;">';
		echo 'Collection record: '.$occidStr;
		echo '</div>';
	}
	?>
	<div id="tabs" style="margin:0;">
		<ul>
			<li><a href="#detaildiv">Details</a></li>
			<li><a href="#linkagediv">Linkages</a></li>
			<li><a href="#admindiv">Admin</a></li>
		</ul>
		<div id="detaildiv" style="">
			<form name="nameeditform" action="dataeditor.php" method="post" onsubmit="return verifyEthnoDataForm(this);">
				<fieldset style="padding:15px">
					<div id="editSciNameDiv" style="<?php echo ($dataArr["occid"]?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Scientific name:</b></span>
						<input id="ethnoSciName" name="verbatimSciName" type="text" style="width:500px;" value="<?php echo $dataArr["SciName"]; ?>" />
					</div>
					<div style="<?php echo ($dataArr['datasource']!=='reference'?'display:flex;':'display:none;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Associate occurrence:</b></span>
						<span>
						<span style="font-size:12px;cursor:pointer;color:blue;margin-right:5px;"  onclick="openOccurrenceSearch('associateoccid')">Open Occurrence Linking Aid</span> <input id="associateoccid" name="associateoccid" type="text" onchange="processOccAssociate();" value="<?php echo $dataArr["occid"]; ?>" readonly/>
						<span style="font-size:12px;cursor:pointer;color:blue;margin-right:5px;" onclick="processRemoveOccurrenceLink();">Remove Link</span>
					</span>
					</div>
					<div id="editReferenceDiv" style="<?php echo ($dataArr['datasource']==='reference'?'display:flex;':'display:none;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Reference pages:</b></span>
						<input name="refpages" type="text" style="width:500px;" value="<?php echo $dataArr["refpages"]; ?>" />
					</div>
					<div id="editPersonnelDiv" style="<?php echo ($dataArr['datasource']==='reference'?'display:none;':'display:block;'); ?>clear:both;margin-top:10px;">
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
								$onChangeLine = "'consultantNew".$id."','ethnoNewNameLanguage'";
								echo '<input id="consultantNew'.$dataId.$id.'" name="consultant[]" value="'.$id.'" type="checkbox" onchange="getPerTargetLanguage('.$id.','.$onChangeLine.');" '.(in_array($id,$personelArr)?'checked ':'').'/> '.$name.'<br />';
							}
							?>
						</div>
					</div>
				</fieldset>
				<fieldset style="margin-top:10px;padding:15px">
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Verbatim vernacular name:</b></span>
						<input name="verbatimVernacularName" type="text" style="width:500px;" value="<?php echo $dataArr["verbatimVernacularName"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Verbatim parse:</b></span>
						<input name="verbatimParse" type="text" style="width:500px;" value="<?php echo $dataArr["verbatimParse"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Verbatim gloss:</b></span>
						<input name="verbatimGloss" type="text" style="width:500px;" value="<?php echo $dataArr["verbatimGloss"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Other verbatim vernacular name:</b></span>
						<input name="otherVerbatimVernacularName" type="text" style="width:500px;" value="<?php echo $dataArr["otherVerbatimVernacularName"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Annotated vernacular name:</b></span>
						<input name="annotatedVernacularName" type="text" style="width:500px;" value="<?php echo $dataArr["annotatedVernacularName"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Annotated parse:</b></span>
						<input name="annotatedParse" type="text" style="width:500px;" value="<?php echo $dataArr["annotatedParse"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Annotated gloss:</b></span>
						<input name="annotatedGloss" type="text" style="width:500px;" value="<?php echo $dataArr["annotatedGloss"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Verbatim language:</b></span>
						<input name="verbatimLanguage" type="text" style="width:500px;" value="<?php echo $dataArr["verbatimLanguage"]; ?>" />
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Glottolog language:</b></span>
						<select id="ethnoNewNameLanguage" name="languageid" style="width:500px;">
							<option value="">----Select Language----</option>
							<?php
							foreach($langArr as $k => $v){
								echo '<option value="'.$v['id'].'" '.(($dataArr["langId"]==$v['id'])?'selected':'').'>'.$v['name'].'</option>';
							}
							?>
						</select>
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Free translation:</b></span>
						<input name="freetranslation" type="text" style="width:500px;" value="<?php echo $dataArr["translation"]; ?>" />
					</div>
					<div style="<?php echo ($dataArr['datasource']==='reference'?'display:flex;':'display:none;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Taxonomic description:</b></span>
						<textarea name="taxonomicDescription" style="width:500px;height:50px;resize:vertical;"><?php echo $dataArr["taxonomicDescription"]; ?></textarea>
					</div>
					<div id="editNameDiscussionDiv" style="<?php echo ($dataArr['datasource']==='reference'?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Consultant comments on name:</b></span>
						<textarea name="nameDiscussion" style="width:500px;height:50px;resize:vertical;"><?php echo $dataArr["nameDiscussion"]; ?></textarea>
					</div>
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
								echo '<input name="semantics[]" id="sempar-'.$id.'-'.$dataId.'" value="'.$id.'" type="checkbox" '.(in_array($id,$nameSemanticsArr)?'checked ':'').'/> '.$pTagLine.'<br />';
								unset($stArr['ptag']);
								unset($stArr['pdesc']);
								if($stArr){
									echo '<div style="padding-left:15px;clear:both;">';
									foreach($stArr as $cid => $cArr){
										$cTag = $cArr['ctag'];
										$cDesc = $cArr['cdesc'];
										$cTagLine = $cTag.' '.$cDesc;
										echo '<input name="semantics[]" value="'.$cid.'" type="checkbox" onchange="checkSemanticParent('.$checkStr.');" '.(in_array($cid,$nameSemanticsArr)?'checked ':'').'/> '.$cTagLine.'<br />';
									}
									echo '</div>';
								}
							}
							?>
						</div>
					</div>
					<div style="clear:both;margin-top:10px;">
						<span style="font-size:13px;"><b>Typology:</b></span>
						<div style="clear:both;margin-left:20px;">
							<input type="radio" name="typology" value="opaque" <?php echo ($dataArr["typology"]==='opaque'?'checked':''); ?>> Opaque<br />
							<input type="radio" name="typology" value="transparent" <?php echo ($dataArr["typology"]==='transparent'?'checked':''); ?>> Transparent<br />
							<input type="radio" name="typology" value="modifiedopaque" <?php echo ($dataArr["typology"]==='modifiedopaque'?'checked':''); ?>> Modified opaque<br />
							<input type="radio" name="typology" value="modifiedtransparent" <?php echo ($dataArr["typology"]==='modifiedtransparent'?'checked':''); ?>> Modified transparent
						</div>
					</div>
				</fieldset>
				<fieldset style="margin-top:10px;padding:15px">
					<div style="clear:both;margin-top:10px;">
						<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('PartsUsed<?php echo $dataId; ?>');">
							<div id='plusButtonPartsUsed<?php echo $dataId; ?>' style="display:none;align-items:center;">
								Parts used: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
							</div>
							<div id='minusButtonPartsUsed<?php echo $dataId; ?>' style="display:flex;align-items:center;">
								Parts used: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
							</div>
						</div>
						<div id="contentPartsUsed<?php echo $dataId; ?>" style="display:block;padding-left:15px;clear:both;">
							<?php
							echo '<div id="part-0" style="display:none;">Please enter a scientific name to display parts used options.</div>';
							foreach($ethnoUsePartsUsedTagArr as $tid => $tidPArr){
								echo '<div id="part-'.$tid.'" style="display:'.($dataArr["kingdomId"]==$tid?'block;':'none;').'">';
								foreach($tidPArr as $id => $text){
									echo '<input name="parts[]" value="'.$id.'" type="checkbox" '.(in_array($id,$usePartsArr)?'checked ':'').'/> '.$text.'<br />';
								}
								echo '</div>';
							}
							?>
						</div>
					</div>
					<div style="clear:both;margin-top:10px;">
						<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('Uses<?php echo $dataId; ?>');">
							<div id='plusButtonUses<?php echo $dataId; ?>' style="display:flex;align-items:center;">
								Uses: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
							</div>
							<div id='minusButtonUses<?php echo $dataId; ?>' style="display:none;align-items:center;">
								Uses: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
							</div>
						</div>
						<div id="contentUses<?php echo $dataId; ?>" style="display:none;padding-left:15px;clear:both;">
							<?php
							echo '<div id="use-0" style="display:none;">Please enter a scientific name to display use options.</div>';
							foreach($ethnoUseUseTagArr as $tid => $tidUArr){
								echo '<div id="use-'.$tid.'" style="display:'.($dataArr["kingdomId"]==$tid?'block;':'none;').'">';
								foreach($tidUArr as $id => $uArr){
									$header = $uArr['header'];
									unset($uArr['header']);
									if($header){
										$headerStr = str_replace(' ','',$header);
										echo '<div style="clear:both;margin-top:10px;">';
										?>
										<div style="cursor:pointer;font-size:13px;font-weight:bold;" onclick="toggleEthnoDiv('<?php echo $headerStr.$dataId; ?>');">
											<div id='plusButton<?php echo $headerStr.$dataId; ?>' style="display:flex;align-items:center;">
												<?php echo $header; ?>: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/plus.png' />
											</div>
											<div id='minusButton<?php echo $headerStr.$dataId; ?>' style="display:none;align-items:center;">
												<?php echo $header; ?>: <img style='border:0px;margin-left:8px;width:13px;' src='../../images/minus.png' />
											</div>
										</div>
										<?php
										echo '<div id="content'.$headerStr.$dataId.'" style="display:none;padding-left:15px;clear:both;">';
										foreach($uArr as $uid => $text){
											echo '<input name="uses[]" value="'.$uid.'" type="checkbox" '.(in_array($uid,$useUseArr)?'checked ':'').'/> '.$text.'<br />';
										}
										echo '</div>';
										echo '</div>';
									}
									else{
										foreach($uArr as $uid => $text){
											echo '<input name="uses[]" value="'.$uid.'" type="checkbox" '.(in_array($uid,$useUseArr)?'checked ':'').'/> '.$text.'<br />';
										}
									}
								}
								echo '</div>';
							}
							?>
						</div>
					</div>
					<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
						<span style="font-size:13px;"><b>Consultant comments:</b></span>
						<input name="consultantComments" type="text" style="width:500px;" value="<?php echo $dataArr["consultantComments"]; ?>" />
					</div>
					<div id="editUseDiscussionDiv" style="<?php echo ($dataArr['datasource']==='reference'?'display:none;':'display:flex;'); ?>clear:both;margin-top:10px;justify-content:space-between;">
						<span style="font-size:13px;"><b>Consultant comments on use:</b></span>
						<textarea name="useDiscussion" style="width:500px;height:50px;resize:vertical;"><?php echo $dataArr["useDiscussion"]; ?></textarea>
					</div>
				</fieldset>
				<div style="clear:both;float:right;margin-top:10px;">
					<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
					<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
					<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
					<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
					<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
					<input name="dataid" type="hidden" value="<?php echo $dataId; ?>" />
					<input type="hidden" name="tabtarget" value="0" />
					<input type="hidden" id="editDataSource" name="datasource" value="<?php echo $dataArr['datasource']; ?>" />
					<input type="hidden" id="ethnoTaxaId" name="tid" value="<?php echo $dataArr["tid"]; ?>" />
					<input type="submit" name="submitaction" value="Edit Data Record" />
				</div>
			</form>
		</div>

		<div id="linkagediv" style="">
			<div style="float:right;margin-bottom:10px;cursor:pointer;<?php echo (!$linkageArr?'display:none;':''); ?>" onclick="toggle('addlinkagediv');" title="Add a New Linkage">
				<img style="border:0px;width:12px;" src="../../images/add.png" />
			</div>
			<div id="addlinkagediv" style="clear:both;<?php echo (($linkageArr && $action !== 'Find records')?'display:none;':''); ?>">
				<fieldset style="padding:15px;">
					<legend><b>Set criteria for linkage search</b></legend>
					<form name="searchcriteriaform" method="post" action="dataeditor.php" onsubmit="return verifyLinkageSearchForm();">
						<div style="margin:5px;">
							<input name="genusMatch" id="genusMatch" value="1" type="checkbox" <?php echo ($genusMatch?'checked':''); ?> /> Matching genus
						</div>
						<div style="margin:5px;">
							<input name="scinameMatch" id="scinameMatch" value="1" type="checkbox" <?php echo ($scinameMatch?'checked':''); ?> /> Matching scientific name
						</div>
						<div style="margin:5px;">
							<input name="semanticMatch" id="semanticMatch" value="1" type="checkbox" <?php echo ($semanticMatch?'checked':''); ?> /> Matching semantic tags
						</div>
						<div style="margin:5px;">
							<input name="levenshteinMatch" id="levenshteinMatch" value="1" type="checkbox" <?php echo ($levenshteinMatch?'checked':''); ?> /> With a Levenshtein distance of
							<input name="levenshteinValue" id="levenshteinValue" style="width:50px;" type="text" value="<?php echo $levenshteinValue; ?>" /> or less
						</div>
						<div style="margin:5px;">
							<input name="vernacularDiffLang" id="vernacularDiffLang" value="1" type="checkbox" <?php echo ($vernacularDiffLang?'checked':''); ?> /> With a vernacular name in a different language
						</div>
						<div style="margin:5px;">
							<input name="vernacularStringMatch" id="vernacularStringMatch" value="1" type="checkbox" <?php echo ($vernacularStringMatch?'checked':''); ?> /> With a vernacular name containing the following string
							<input name="vernacularStringMatchValue" id="vernacularStringMatchValue" type="text" value="<?php echo $vernacularStringMatchValue; ?>" />
						</div>
						<div style="margin:5px;">
							<input name="vernacularRegexMatch" id="vernacularRegexMatch" value="1" type="checkbox" <?php echo ($vernacularRegexMatch?'checked':''); ?> /> With a vernacular name that meets the following regex criteria
							<input name="vernacularRegexMatchValue" id="vernacularRegexMatchValue" type="text" value="<?php echo $vernacularRegexMatchValue; ?>" />
						</div>
						<div style="margin:5px;">
							<input name="verbatimParseMatch" id="verbatimParseMatch" value="1" type="checkbox" <?php echo ($verbatimParseMatch?'checked':''); ?> /> With a verbatim parse of
							<input name="verbatimParseValue" id="verbatimParseValue" type="text" value="<?php echo ($verbatimParseMatch?$verbatimParseValue:$dataArr["verbatimParse"]); ?>" />
						</div>
						<div style="margin:5px;">
							<input name="verbatimParseRegexMatch" id="verbatimParseRegexMatch" value="1" type="checkbox" <?php echo ($verbatimParseRegexMatch?'checked':''); ?> /> With a verbatim parse that meets the following regex criteria
							<input name="verbatimParseRegexMatchValue" id="verbatimParseRegexMatchValue" type="text" value="<?php echo $verbatimParseRegexMatchValue; ?>" />
						</div>
						<div style="margin:5px;">
							<input name="verbatimGlossMatch" id="verbatimGlossMatch" value="1" type="checkbox" <?php echo ($verbatimGlossMatch?'checked':''); ?> /> With a verbatim gloss of
							<input name="verbatimGlossValue" id="verbatimGlossValue" type="text" value="<?php echo ($verbatimGlossMatch?$verbatimGlossValue:$dataArr["verbatimGloss"]); ?>" />
						</div>
						<div style="margin:5px;">
							<input name="verbatimGlossRegexMatch" id="verbatimGlossRegexMatch" value="1" type="checkbox" <?php echo ($verbatimGlossRegexMatch?'checked':''); ?> /> With a verbatim gloss that meets the following regex criteria
							<input name="verbatimGlossRegexMatchValue" id="verbatimGlossRegexMatchValue" type="text" value="<?php echo $verbatimGlossRegexMatchValue; ?>" />
						</div>
						<div style="margin:20px;">
							<input name="linkageVerbatimName" type="hidden" value="<?php echo $dataArr["verbatimVernacularName"]; ?>" />
							<input name="linkageLangId" type="hidden" value="<?php echo $dataArr["langId"]; ?>" />
							<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
							<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
							<input name="dataid" type="hidden" value="<?php echo $dataId; ?>" />
							<input name="occid" type="hidden" value="<?php echo $occId; ?>" />
							<input name="occindex" type="hidden" value="<?php echo $occIndex; ?>" />
							<input name="csmode" type="hidden" value="<?php echo $csMode; ?>" />
							<input name="tabtarget" type="hidden" value="1" />
							<input name="submitaction" type="submit" value="Find records" />
						</div>
					</form>
				</fieldset>

				<?php
				if($linkageSearchReturnArr){
					?>
					<fieldset style="padding:15px">
						<legend><b>Add a New Linkages</b></legend>
						<form name="linkagenewform" action="dataeditor.php" method="post" onsubmit="return verifyNameLinkageForm(this);">
							<fieldset>
								<legend><b>Records matching criteria</b></legend>
								<table class="styledtable" style="width:770px;font-family:Arial;font-size:12px;margin-left:auto;margin-right:auto;">
									<tr>
										<th style="width:20px;"></th>
										<th style="width:300px;">Project Name</th>
										<th style="width:200px;">Verbatim Varnacular Name</th>
										<th style="width:100px;">Language</th>
										<th style="width:150px;">Verbatim Parse</th>
										<th style="width:150px;">Verbatim Gloss</th>
									</tr>
									<?php
									foreach($linkageSearchReturnArr as $linkDataId => $pArr){
										echo '<tr>';
										echo '<td style="width:20px;"><input name="linkdataid[]" type="checkbox" value="'.$linkDataId.'" /></td>'."\n";
										echo '<td style="width:300px;">'.$pArr['CollectionName'].'</td>'."\n";
										echo '<td style="width:200px;">'.$pArr['verbatimVernacularName'].'</td>'."\n";
										echo '<td style="width:100px;">'.$pArr['languageName'].'</td>'."\n";
										echo '<td style="width:150px;">'.$pArr['verbatimParse'].'</td>'."\n";
										echo '<td style="width:150px;">'.$pArr['verbatimGloss'].'</td>'."\n";
										echo '</tr>';
									}
									?>
								</table>
							</fieldset>
							<div style="clear:both;margin-top:10px;">
								<span style="font-size:13px;"><b>Linkage type:</b></span>
								<div style="clear:both;margin-left:20px;">
									<input type="radio" name="linktype" value="cognate" > Cognate<br />
									<input type="radio" name="linktype" value="loan" > Loan<br />
									<input type="radio" name="linktype" value="calque" > Calque
								</div>
							</div>
							<div style="display:flex;clear:both;margin-top:10px;justify-content:space-between;">
								<span style="font-size:13px;"><b>Source reference:</b></span>
								<?php
								if($ethnoReferenceArr){
									?>
									<select id="ethnoNewLinkageReference" name="refid" style="width:500px;">
										<option value="">----Select reference----</option>
										<?php
										foreach($ethnoReferenceArr as $k => $v){
											echo '<option value="'.$v['refid'].'">'.$v['title'].'</option>';
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
							<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
								<span style="font-size:13px;"><b>Reference pages:</b></span>
								<input name="refpages" type="text" style="width:500px;" value="" />
							</div>
							<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
								<span style="font-size:13px;"><b>Discussion:</b></span>
								<textarea name="discussion" style="width:500px;height:50px;resize:vertical;"></textarea>
							</div>
							<div style="clear:both;float:right;margin-top:10px;">
								<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
								<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
								<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
								<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
								<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
								<input name="dataid" type="hidden" value="<?php echo $dataId; ?>" />
								<input type="hidden" id="ethnoLinkageNameId" name="linknameid" value="" />
								<input type="hidden" name="tabtarget" value="1" />
								<input type="submit" name="submitaction" value="Add Linkage" />
							</div>
						</form>
					</fieldset>
					<?php
				}
				?>
			</div>
			<?php
			if($linkageArr){
				?>
				<hr style="clear:both;margin:30px 0px;" />
				<div style="clear:both;margin:15px;">
					<?php
					foreach($linkageArr as $linkId => $linkArr){
						$linkageTypeStr = '';
						if($linkArr["linktype"]==='cognate') {
							$linkageTypeStr = 'Cognate';
						}
						elseif($linkArr["linktype"]==='loan') {
							$linkageTypeStr = 'Loan';
						}
						elseif($linkArr["linktype"]==='calque') {
							$linkageTypeStr = 'Calque';
						}
						?>
						<div style="float:right;cursor:pointer;" onclick="toggle('linkage<?php echo $linkId; ?>editdiv');" title="Edit Linkage Record">
							<img style="border:0px;width:15px;" src="../../images/edit.png" />
						</div>
						<div style="margin-top:30px">
							<?php
							if($linkageTypeStr){
								?>
								<div style="font-size:13px;">
									<b>Link type:</b>
									<?php echo $linkageTypeStr; ?>
								</div>
								<?php
							}
							if($linkArr["verbatimVernacularName"]){
								?>
								<div style="font-size:13px;">
									<b>Linked verbatim vernacular name:</b>
									<?php echo $linkArr["verbatimVernacularName"]; ?>
								</div>
								<?php
							}
							if($linkArr["langName"]){
								?>
								<div style="font-size:13px;">
									<b>Linked Glottolog language:</b>
									<?php echo $linkArr["langName"]; ?>
								</div>
								<?php
							}
							if($linkArr["sciname"]){
								?>
								<div style="font-size:13px;">
									<b>Linked scientific name:</b>
									<?php echo $linkArr["sciname"]; ?>
								</div>
								<?php
							}
							if($linkArr["refSource"]){
								?>
								<div style="font-size:13px;">
									<b>Linkage reference source:</b>
									<?php echo $linkArr["refSource"]; ?>
								</div>
								<?php
							}
							if($linkArr["refpages"]){
								?>
								<div style="font-size:13px;">
									<b>Linkage reference pages:</b>
									<?php echo $linkArr["refpages"]; ?>
								</div>
								<?php
							}
							if($linkArr["discussion"]){
								?>
								<div style="font-size:13px;">
									<b>Linkage discussion:</b>
									<?php echo $linkArr["discussion"]; ?>
								</div>
								<?php
							}
							?>
						</div>
						<div id="linkage<?php echo $linkId; ?>editdiv" style="display:none;clear:both;">
							<form name="linkage<?php echo $linkId; ?>editform" action="dataeditor.php" method="post" onsubmit="return verifyNameLinkageForm(this);">
								<fieldset style="padding:15px">
									<legend><b>Edit Name Linkage</b></legend>
									<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
										<span style="font-size:13px;"><b>Verbatim vernacular name to link:</b></span>
										<input id="ethnoNameLinkageName" name="linkageVerbatimName" type="text" style="width:500px;" value="<?php echo $linkArr["verbatimVernacularName"]; ?>" />
									</div>
									<div style="clear:both;margin-top:10px;">
										<span style="font-size:13px;"><b>Linkage type:</b></span>
										<div style="clear:both;margin-left:20px;">
											<input type="radio" name="linktype" value="cognate" <?php echo ($linkArr["linktype"]==='cognate'?'checked ':''); ?>> Cognate<br />
											<input type="radio" name="linktype" value="loan" <?php echo ($linkArr["linktype"]==='loan'?'checked ':''); ?>> Loan<br />
											<input type="radio" name="linktype" value="calque" <?php echo ($linkArr["linktype"]==='calque'?'checked ':''); ?>> Calque
										</div>
									</div>
									<div style="display:flex;clear:both;margin-top:10px;justify-content:space-between;">
										<span style="font-size:13px;"><b>Source reference:</b></span>
										<?php
										if($ethnoReferenceArr){
											?>
											<select id="ethnoNewLinkageReference" name="refid" style="width:500px;">
												<option value="">----Select reference----</option>
												<?php
												foreach($ethnoReferenceArr as $k => $v){
													echo '<option value="'.$v['refid'].'" '.($linkArr['refid']==$v['refid']?'selected':'').'>'.$v['title'].'</option>';
												}
												?>
											</select>
											<?php
										}
										else{
											echo '<div>';
											echo 'There are no references entered in the database.<br />';
											echo 'Please go to the <a href="../../references/index.php" target="_blank">Reference Management page</a> to add references.';
											echo '</div>';
										}
										?>
									</div>
									<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
										<span style="font-size:13px;"><b>Reference pages:</b></span>
										<input name="refpages" type="text" style="width:500px;" value="<?php echo $linkArr["refpages"]; ?>" />
									</div>
									<div style="clear:both;margin-top:10px;display:flex;justify-content:space-between;">
										<span style="font-size:13px;"><b>Discussion:</b></span>
										<textarea name="discussion" style="width:500px;height:50px;resize:vertical;"><?php echo $linkArr["discussion"]; ?></textarea>
									</div>
									<div style="margin-top:10px;">
										<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
										<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
										<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
										<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
										<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
										<input name="dataid" type="hidden" value="<?php echo $dataId; ?>" />
										<input name="linkid" type="hidden" value="<?php echo $linkId; ?>" />
										<input type="hidden" id="ethnoLinkageNameId<?php echo $linkId; ?>" name="linknameid" value="<?php echo $linkArr["linkedNameId"]; ?>" />
										<input type="hidden" name="tabtarget" value="1" />
										<input type="submit" name="submitaction" value="Edit Linkage" />
									</div>
								</fieldset>
							</form>
							<form name="linkage<?php echo $linkId; ?>delform" action="dataeditor.php" method="post" onsubmit="">
								<fieldset style="padding:15px">
									<legend><b>Delete Linkage</b></legend>
									<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
									<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
									<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
									<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
									<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
									<input name="dataid" type="hidden" value="<?php echo $dataId; ?>" />
									<input name="linkid" type="hidden" value="<?php echo $linkId; ?>" />
									<input type="hidden" name="tabtarget" value="1" />
									<div style="margin:10px 20px;">
										<input type="submit" name="submitaction" value="Delete Linkage" />
									</div>
								</fieldset>
							</form>
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

		<div id="admindiv" style="">
			<form name="deldataform" action="<?php echo ($occId?'../../collections/editor/occurrenceeditor.php':'dataeventeditor.php'); ?>" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this data record?')">
				<fieldset style="width:350px;margin:20px;padding:20px;">
					<legend><b>Delete Data Record</b></legend>
					<input name="submitaction" type="submit" value="Delete Data Record" />
					<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
					<input name="eventid" type="hidden" value="<?php echo $eventId; ?>" />
					<input name="dataid" type="hidden" value="<?php echo $dataId; ?>" />
					<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
					<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
					<input type="hidden" name="csmode" value="<?php echo $csMode; ?>" />
					<input type="hidden" name="tabtarget" value="<?php echo ($occId?3:1); ?>" />
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
