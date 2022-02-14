<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/specimennotes.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$occid = $_REQUEST['occid'];
$loanID = $_REQUEST['loanid'];

//Sanitation
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($occid)) $occid = 0;
if(!is_numeric($loanID)) $loanID = 0;

$isEditor = 0;
if($SYMB_UID && $collid){
	if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))
		|| (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor']))){
		$isEditor = 1;
	}
}

$loanManager = new OccurrenceLoans();
$loanManager->setCollId($collid);
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?> Loan Specimen Notes Editor</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		function submitNotesForm(f){
			self.close();
		}
	</script>
	<style>
		body{ width:800px; min-width:400px; max-width:1000px; background-color: #FFFFFF; }
		fieldset{ padding:20px }
		fieldset legend{ font-weight:bold }
	</style>
</head>
<body>
	<!-- This is inner text! -->
	<div id="popup-innertext">
		<?php
		if($isEditor && $collid){
			$noteArr = $loanManager->getSpecimenDetails($loanID, $occid)
			?>
			<fieldset class="notesDiv" >
				<legend>Loan Specimen Editor</legend>
				<form name="noteEditor" action="outgoing.php" method="post" target="parentWin" onsubmit="submitNotesForm()">
					<div>
						<b>Date returned:</b>
						<input name="returndate" type="datetime-local" value="<?php echo $noteArr['returnDate']; ?>" />
					</div>
					<div>
						<b>Specimen notes:</b>
						<input name="notes" type="text" value="<?php echo $noteArr['notes']; ?>" style="width:100%" />
					</div>
					<div>
						<input name="loanid" type="hidden" value="<?php echo $loanID; ?>" />
						<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="tabindex" type="hidden" value="1" />
						<button name="formsubmit" type="submit" value="saveSpecimenDetails">Save Edits</button>
					</div>
				</form>
			</fieldset>
			<?php
		}
		else{
			if(!$isEditor) echo '<h2>You are not authorized to manage loans for this collection</h2>';
			else echo '<h2>ERROR: unknown error, please contact system administrator</h2>';
		}
		?>
	</div>
</body>
</html>