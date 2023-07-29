<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSupport.php');
header('Content-Type: text/html; charset='.$CHARSET);

$targetId = filter_var($_REQUEST['targetid'], FILTER_SANITIZE_NUMBER_INT);
$collid = array_key_exists('collid', $_REQUEST) ? filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$action = array_key_exists('action', $_POST) ? $_POST['action'] : '';
$catalogNumber = array_key_exists('catalognumber',$_POST) ? filter_var($_POST['catalognumber'], FILTER_SANITIZE_STRING) : '';
$otherCatalogNumbers = array_key_exists('othercatalognumbers',$_POST) ? filter_var($_POST['othercatalognumbers'], FILTER_SANITIZE_STRING) : '';
$recordedBy = array_key_exists('recordedby',$_POST) ? filter_var($_POST['recordedby'], FILTER_SANITIZE_STRING) : '';
$recordNumber = array_key_exists('recordnumber',$_POST) ? filter_var($_POST['recordnumber'], FILTER_SANITIZE_STRING) : '';

$collEditorArr = array();
if(array_key_exists('CollAdmin',$USER_RIGHTS)) $collEditorArr = $USER_RIGHTS['CollAdmin'];
if(array_key_exists('CollEditor',$USER_RIGHTS)) $collEditorArr = array_unique(array_merge($collEditorArr,$USER_RIGHTS['CollEditor']));

$occManager = new OccurrenceSupport();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Occurrence Search Page</title>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
	    function updateParentForm(occId) {
	        opener.document.getElementById("imgdisplay-<?php echo $targetId;?>").value = occId;
	        opener.document.getElementById("imgoccid-<?php echo $targetId;?>").value = occId;
	        self.close();
	        return false;
	    }

	    function verifyOccurSearchForm(f){
			if(!f.collid.value){
				alert("You must select target collection");
				return false;
			}
			if(!f.catalognumber.value && !f.othercatalognumbers.value && !f.recordedby.value && !f.recordnumber.value){
				alert("You must enter at least one search term");
				return false;
			}
			return true;
	    }

	    function linkToNewOccurrence(f){
		    if(!f.collid.value){
				alert("You must select target collection");
				return false;
		    }
		    else{
				$.ajax({
					type: "POST",
					url: "../editor/rpc/occurAddData.php",
					dataType: "json",
					data: { collid: f.collid.value }
				}).done(function( retObj ) {
					if(retObj.status == "true"){
						updateParentForm(retObj.occid);
					}
					else{
						alert("Unable to create new record due to error ("+retObj.error+"). Contact portal administrator");
					}
				});
		    }
		}

	    function isNumeric(inStr){
	       	var validChars = "0123456789-.";
	       	var isNumber = true;
	       	var charVar;

	       	for(var i = 0; i < inStr.length && isNumber == true; i++){
	       		charVar = inStr.charAt(i);
	    		if(validChars.indexOf(charVar) == -1){
	    			isNumber = false;
	    			break;
	          	}
	       	}
	    	return isNumber;
	    }
	</script>
	<style type="text/css">
		body{ width: 700px; min-width: 400px; }
		innertext{ padding: 15px; }
		fieldset{ margin:15px; padding:15px }
		select{ width: 100%; }
	</style>
</head>
<body>
	<div id="innertext">
		<?php
		if($collEditorArr){
			$collArr = $occManager->getCollectionArr($IS_ADMIN?'':$collEditorArr);
			?>
			<form name="occform" action="occurrencesearch.php" method="post" onsubmit="return verifyOccurSearchForm(this)" >
				<fieldset>
					<legend>Voucher Search Panel</legend>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Target Collection:</div>
						<div style="float:left;">
							<select name="collid">
								<option value="">Select Collection</option>
								<option value="">--------------------------------</option>
								<?php
								foreach($collArr as $id => $collName){
									echo '<option value="'.$id.'" '.($id == $collid?'SELECTED':'').'>'.$collName.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Catalog #:</div>
						<div style="float:left;"><input name="catalognumber" type="text" value="<?php echo $catalogNumber; ?>" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Other Catalog #:</div>
						<div style="float:left;"><input name="othercatalognumbers" type="text" value="<?php echo $otherCatalogNumbers; ?>" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Collector Last Name:</div>
						<div style="float:left;"><input name="recordedby" type="text"  value="<?php echo $recordedBy; ?>" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<div style="float:left;width:130px;">Collector Number:</div>
						<div style="float:left;"><input name="recordnumber" type="text" value="<?php echo $recordNumber; ?>" /></div>
					</div>
					<div style="clear:both;padding:2px;">
						<input name="action" type="submit" value="Search Occurrences" />
						<input type="hidden" name="targetid" value="<?php echo $targetId;?>" />
					</div>
				</fieldset>
			</form>
			<?php
			if($action){
				if($occArr = $occManager->getOccurrenceList($collid, $catalogNumber, $otherCatalogNumbers, $recordedBy, $recordNumber)){
					echo '<div style="margin:30px 10px;">';
					foreach($occArr as $occid => $vArr){
						?>
						<div style="margin:10px;">
							<?php echo '<b>OccId '.$occid.':</b> '.$vArr["recordedby"].' ['.($vArr['recordnumber']?$vArr['recordnumber']:$vArr['eventdate']).']; '.$vArr['locality'];?>
							<div style="margin-left:10px;">
								<a href="#" onclick="updateParentForm('<?php echo $occid;?>');return false;">Select Occurrence Record</a>
							</div>
						</div>
						<hr />
						<?php
					}
					echo '</div>';
				}
				else{
					?>
					<div style="margin:30 10px;">
						<b>No records were returned. Please modify your search and try again.</b>
					</div>
					<?php
				}
			}
			?>
			<fieldset>
				<legend>Link to New Occurrence Record</legend>
				<form name="occform" action="occurrencesearch.php" method="post" onsubmit="return false" >
					<select name="collid">
						<option value="">Select Collection</option>
						<option value="">--------------------------------</option>
						<?php
						foreach($collArr as $id => $collName){
							echo '<option value="'.$id.'" '.($id == $collid?'SELECTED':'').'>'.$collName.'</option>';
						}
						?>
					</select>
					<button type="button" onclick="linkToNewOccurrence(this.form)">Create New Occurrence</button>
				</form>
			</fieldset>
			<?php
		}
		else{
			?>
			<div style="margin:30 10px;">
				<b>You are not authorized to link to any collections</b>
			</div>
			<?php
		}
		?>
	</div>
</body>
</html>