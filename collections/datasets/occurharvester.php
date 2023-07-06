<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSupport.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:'';
$action = array_key_exists('formsubmit',$_REQUEST)?$_REQUEST['formsubmit']:'';

$harvManager = new OccurrenceSupport();

$isEditor = 0;
$collList = array();
if($IS_ADMIN){
	$isEditor = 1;
	$collList[] = 'all';
}
else{
	if(array_key_exists("CollEditor",$USER_RIGHTS)){
		if(in_array($collid,$USER_RIGHTS["CollEditor"])){
			$isEditor = 1;
		}
		$collList = $USER_RIGHTS["CollEditor"];
	}
	if(array_key_exists("CollAdmin",$USER_RIGHTS)){
		if(in_array($collid,$USER_RIGHTS["CollAdmin"])){
			$isEditor = 1;
		}
		$collList = array_merge($collList,$USER_RIGHTS["CollAdmin"]);
	}
}

if($isEditor){
	if($action == 'Download Records'){
		$harvManager->exportCsvFile($_POST);
		exit;
	}
	else{

	}
}
?>
<!DOCTYPE HTML>
<html>
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE; ?> - Occurrence Harvester</title>
		<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
			function validateDownloadForm(f){
				return true;
			}

			function loadOccurRecord(fieldObj){
				var occid = fieldObj.value;
				fieldObj.value = "";
				if(!occid) return false;
				if(document.getElementById("occid-"+occid)) return false;

				var newAnchor = document.createElement('a');
				newAnchor.setAttribute("id", "a-"+occid);
				newAnchor.setAttribute("href", "#");
				newAnchor.setAttribute("onclick", "openIndPopup("+occid+");return false;");
				var newText = document.createTextNode(occid);
				newAnchor.appendChild(newText);

				var newDiv = document.createElement('div');
				newDiv.setAttribute("id", "occid-"+occid);
				newDiv.appendChild(newAnchor);

				var newInput = document.createElement('input');
				newInput.setAttribute("type", "hidden");
				newInput.setAttribute("name", "occid[]");
				newInput.setAttribute("value", occid);

				var listElem = document.getElementById("occidlist");
				//listElem.appendChild(newDiv);
				listElem.insertBefore(newDiv,listElem.childNodes[0]);
				listElem.appendChild(newInput);

				document.getElementById("emptylistdiv").style.display = "none";

				setOccurData(occid);
				fieldObj.focus();
			}

			function setOccurData(occidInVal){
				$.ajax({
					type: "POST",
					url: "rpc/getoccurrence.php",
					dataType: "json",
					data: { occid: occidInVal }
				}).done(function( data ) {
					var aElem = document.getElementById("a-"+occidInVal);
					var newText;
					if(data != ""){
						newText = document.createTextNode(" - "+data.recordedby+" #"+data.recordnumber+" ("+data.eventdate+")");
					}
					else{
						newText = document.createTextNode(" - unable to locate occurrence record");
					}
					aElem.appendChild(newText);
				});
			}

			function openIndPopup(occid){
				var urlStr = '../individual/index.php?occid=' + occid;
				var wWidth = 900;
				if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
				if(wWidth > 1200) wWidth = 1200;
				newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
				if (newWindow.opener == null) newWindow.opener = self;
				return false;
			}
		</script>
	</head>
	<body>
	<?php
	$displayLeftMenu = (isset($collections_datasets_indexMenu)?$collections_datasets_indexMenu:true);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<?php
		if(isset($collections_datasets_occurharvesterCrumbs)){
			echo $collections_datasets_occurharvesterCrumbs;
		}
		?>
		<b>Occurrence Harvester</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<div style="margin:15px">
			Scan or type barcode number into field below and then hit enter or tab to add the specimen to the list.
			Once list is complete, you can enter your catalog number in the text field and then transfer to your collection
			or file export to a file that can be imported into your local database.
		</div>
		<div style="margin:20px 0px">
			<hr/>
		</div>
		<div style="margin-bottom:10px;">
			<label for="occidsubmit">Occurrence ID:</label>
			<input type="text" name="occidsubmit" id="occidsubmit" onchange="loadOccurRecord(this)" />
		</div>
		<div style="width:450px;">
			<form name="dlform" method="post" action="occurharvester.php" target="_blank">
				<fieldset>
					<legend><b>Specimen Queue</b></legend>
					<div id="emptylistdiv" style="margin:20px;">
						<b>List Empty: </b>enter barcode in Occurrence ID field
					</div>
					<div id="occidlist" style="margin:10px;">
					</div>
					<?php
					if($collid){
						?>
						<div style="margin:30px">
							<input name="formsubmit" type="submit" value="Transfer Records" />
						</div>
						<?php
					}
					?>
					<div style="margin:30px">
						<input name="formsubmit" type="submit" value="Download Records" />
					</div>
				</fieldset>
			</form>
		</div>

	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
	</body>
</html>