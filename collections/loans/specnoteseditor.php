<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceLoans.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT . '/content/lang/collections/loans/loan_langs.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/collections/loans/loan_langs.' . $LANG_TAG . '.php');
	else include_once($SERVER_ROOT . '/content/lang/collections/loans/loan_langs.en.php');
	header("Content-Type: text/html; charset=".$CHARSET);
	if(!$SYMB_UID) header('Location: ' . $CLIENT_ROOT . '/profile/index.php?refurl=../collections/loans/specimennotes.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

	$collid = filter_var($_REQUEST['collid'], FILTER_SANITIZE_NUMBER_INT);
	$occid = filter_var($_REQUEST['occid'], FILTER_SANITIZE_NUMBER_INT);;
	$loanID = filter_var($_REQUEST['loanid'], FILTER_SANITIZE_NUMBER_INT);;

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
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
	<title><?= $DEFAULT_TITLE . ' ' . $LANG['LOAN_NOTES_EDITOR']; ?></title>
	<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
	<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function resetCheckin(f){
			f.returndate.value = '';
			f.notes.value = '';
			f.formsubmit.click()
		}

		function submitNotesForm(f){
			self.close();
		}
	</script>
	<style>
		body{ width:800px; min-width:400px; max-width:1000px; background-color: #FFFFFF; }
		fieldset{ padding:20px }
		fieldset legend{ font-weight:bold }
		button{ margin: 10px; display: inline; }
	</style>
</head>
<body>
	<!-- This is inner text! -->
	<div id="popup-innertext" class="left-breathing-room-rel">
		<h1 class="page-heading screen-reader-only"><?= $LANG['LOAN_NOTES_EDITOR']; ?></h1>
		<?php
		if($isEditor && $collid){
			$noteArr = $loanManager->getSpecimenDetails($loanID, $occid);
			?>
			<form name="noteEditor" action="outgoing.php" method="post" target="parentWin" onsubmit="submitNotesForm()">
				<fieldset>
					<legend><?= $LANG['SPECIMEN_CHECKIN'] ?></legend>
					<div>
						<b><?= $LANG['DATE_RETURNED']; ?>:</b>
						<input name="returndate" type="date" value="<?= $noteArr['returnDate'] ?>" />
					</div>
					<div>
						<b><?= $LANG['SPEC_NOTES']; ?>:</b>
						<input name="notes" type="text" value="<?= $noteArr['notes'] ?>" style="width:100%" />
					</div>
					<div>
						<input name="loanid" type="hidden" value="<?= $loanID ?>" />
						<input name="occid" type="hidden" value="<?= $occid ?>" />
						<input name="collid" type="hidden" value="<?= $collid ?>" />
						<input name="tabindex" type="hidden" value="1" />
						<button name="formsubmit" type="submit" value="saveSpecimenDetails"><?= $LANG['SAVE_EDITS']; ?></button>
						<?php
						if($noteArr['returnDate']){
							?>
							<button name="formreset" type="button" value="saveSpecimenDetails" onclick="resetCheckin(this.form)"><?= $LANG['RESET_CHECKIN']; ?></button>
							<?php
						}
						?>
					</div>
				</fieldset>
			</form>
			<?php
		}
		else{
			if(!$isEditor) echo '<h2>' . $LANG['NOT_AUTH_LOANS'] . '</h2>';
			else echo '<h2>' . $LANG['UNKNOWN_ERROR'] . '</h2>';
		}
		?>
	</div>
</body>
</html>