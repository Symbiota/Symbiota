<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/outgoing.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$loanId = array_key_exists('loanid',$_REQUEST)?$_REQUEST['loanid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;

$isEditor = 0;
if($SYMB_UID && $collid){
	if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))
		|| (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"]))){
		$isEditor = 1;
	}
}

$loanManager = new OccurrenceLoans();
if($collid) $loanManager->setCollId($collid);

$statusStr = '';
if($isEditor){
	if($formSubmit){
		if($formSubmit == 'createLoanOut'){
			$loanId = $loanManager->createNewLoanOut($_POST);
			if(!$loanId) $statusStr = $loanManager->getErrorMessage();
		}
		elseif($formSubmit == 'Save Outgoing'){
			$statusStr = $loanManager->editLoanOut($_POST);
		}
		elseif($formSubmit == 'performSpecimenAction'){
			if(!$loanManager->editSpecimen($_REQUEST)){
				$statusStr = $loanManager->getErrorMessage();
			}
		}
		elseif($formSubmit == 'Add New Determinations'){
			include_once($SERVER_ROOT.'/classes/OccurrenceEditorDeterminations.php');
			$occManager = new OccurrenceEditorDeterminations();
			$occidArr = $_REQUEST['occid'];
			foreach($occidArr as $k){
				$occManager->setOccId($k);
				$occManager->addDetermination($_REQUEST,$isEditor);
			}
		}
		elseif($formSubmit == 'batchLinkSpecimens'){
			$cnt = $loanManager->batchLinkSpecimens($_POST);
			$statusStr = '<ul>';
			$statusStr .= '<li><b>'.$cnt.'</b> specimens linked successfully</li>';
			if($warnArr = $loanManager->getWarningArr()){
				if(isset($warnArr['missing'])){
					$statusStr .= '<li style="color:red;"><b>Unable to locate following catalog numbers</b></li>';
					foreach($warnArr['missing'] as $errStr){
						$statusStr .= '<li style="margin-left:10px;color:black;">'.$errStr.'</li>';
					}
				}
				if(isset($warnArr['multiple'])){
					$statusStr .= '<li style="color:orange;"><b>Catalog numbers with multiple matches</b></li>';
					foreach($warnArr['multiple'] as $errStr){
						$statusStr .= '<li style="margin-left:10px;color:black;">'.$errStr.'</li>';
					}
				}
				if(isset($warnArr['dupe'])){
					$statusStr .= '<li style="color:orange"><b>Specimens already linked to loan</b></li>';
					foreach($warnArr['dupe'] as $errStr){
						$statusStr .= '<li style="margin-left:10px;color:black;">'.$errStr.'</li>';
					}
				}
				if(isset($warnArr['error'])){
					$statusStr .= '<li style="color:red;"><b>Misc errors</b></li>';
					foreach($warnArr['error'] as $errStr){
						$statusStr .= '<li style="margin-left:10px;color:black;">'.$errStr.'</li>';
					}
				}
				$statusStr .= '</ul>';
			}
			$tabIndex = 1;
		}
		elseif($formSubmit == 'saveSpecimenDetails'){
			if($loanManager->editSpecimenDetails($loanId,$_POST['occid'],$_POST['returndate'],$_POST['notes'])) $statusStr = true;
			echo $statusStr = $loanManager->getErrorMessage();
		}
		elseif($formSubmit == "exportSpecimenList"){
			$loanManager->exportSpecimenList($loanId);
			exit;
		}
	}
}
$specimenTotal = $loanManager->getSpecimenTotal($loanId);
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?>: Outgoing Loan Management</title>
	<?php
	$activateJQuery = true;
	if(file_exists($SERVER_ROOT.'/includes/head.php')){
		include_once($SERVER_ROOT.'/includes/head.php');
	}
	else{
		echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		var tabIndex = <?php echo $tabIndex; ?>;
		var skipFormVerification = false;

		function verifyLoanOutEditForm(){
			var submitStatus = true;
			$("#editLoanOutForm input[type=date]").each(function() {
				//Need for Safari browser which doesn't support date input types
				if(this.value != ""){
					var validFormat = /^\s*\d{4}-\d{2}-\d{2}\s*$/ //Format: yyyy-mm-dd
					if(!validFormat.test(this.value)){
						alert("Date (e.g. "+this.name+") values must follow format: YYYY-MM-DD");
						submitStatus = false;
					}
				}
			});
			return submitStatus;
		}

		function addSpecimen(f,splist){
			if(!f.catalognumber.value){
				alert("Please enter a catalog number!");
				return false;
			}
			else{
				//alert("rpc/insertLoanSpecimens.php?loanid="+f.loanid.value+"&catalognumber="+f.catalognumber.value+"&collid="+f.collid.value);
				$.ajax({
					method: "POST",
					data: { loanid: f.loanid.value, catalognumber: f.catalognumber.value, collid: f.collid.value },
					dataType: "text",
					url: "rpc/insertLoanSpecimens.php"
				})
				.done(function(retStr) {
					if(retStr == "0"){
						document.getElementById("addspecsuccess").style.display = "none";
						document.getElementById("addspecerr1").style.display = "block";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecerr1").style.display = "none";
						}, 4000);
						//alert("ERROR: Specimen record not found in database.");
					}
					else if(retStr == "1"){
						f.catalognumber.value = '';
						document.getElementById("addspecsuccess").style.display = "block";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecsuccess").style.display = "none";
						}, 4000);
						//alert("SUCCESS: Specimen record added to loan.");
						if(splist == 0){
							document.getElementById("speclistdiv").style.display = "block";
							document.getElementById("nospecdiv").style.display = "none";
						}
					}
					else if(retStr == "2"){
						document.getElementById("addspecsuccess").style.display = "none";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "block";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecerr2").style.display = "none";
						}, 4000);
						//alert("ERROR: More than one specimen with that catalog number.");
					}
					else if(retStr == "3"){
						document.getElementById("addspecsuccess").style.display = "none";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "block";
						setTimeout(function () {
							document.getElementById("addspecerr3").style.display = "none";
						}, 4000);
						//alert("ERROR: More than one specimen with that catalog number.");
					}
					else{
						f.catalognumber.value = "";
						document.refreshspeclist.submit();
						/*
						document.getElementById("addspecsuccess").style.display = "block";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecsuccess").style.display = "none";
							}, 5000);
						alert("SUCCESS: Specimen added to loan.");
						*/
					}
				})
				.fail(function() {
					alert("Generation of new ID failed");
				});
			}
			return false;
		}

		function verifySpecEditForm(f){
			if(skipFormVerification) return true;
			skipFormVerification = false;
			//Make sure at least on specimen checkbox is checked
			var cbChecked = false;
			var dbElements = document.getElementsByName("occid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(dbElement.checked){
					cbChecked = true;
					break;
				}
			}
			if(!cbChecked){
				alert("Please select specimens to which you wish to apply the action");
				return false;
			}

			//If task equals delete, confirm action
			var applyTaskObj = f.applytask;
			var l = applyTaskObj.length;
			var applyTaskValue = "";
			for(var i = 0; i < l; i++) {
				if(applyTaskObj[i].checked) {
					applyTaskValue = applyTaskObj[i].value;
				}
			}
			if(applyTaskValue == "delete"){
				return confirm("Are you sure you want to remove selected specimens from this loan?");
			}
			return true;
		}

		function verifyLoanDet(){
			if(document.getElementById('dafsciname').value == ""){
				alert("Scientific Name field must have a value");
				return false;
			}
			if(document.getElementById('identifiedby').value == ""){
				alert("Determiner field must have a value (enter 'unknown' if not defined)");
				return false;
			}
			if(document.getElementById('dateidentified').value == ""){
				alert("Determination Date field must have a value (enter 's.d.' if not defined)");
				return false;
			}
			return true;
		}

		function initLoanDetAutocomplete(f){
			$( f.sciname ).autocomplete({
				source: "../editor/rpc/getspeciessuggest.php",
				minLength: 3,
				change: function(event, ui) {
					if(f.sciname.value){
						pauseSubmit = true;
						verifyLoanDetSciName(f);
					}
					else{
						f.scientificnameauthorship.value = "";
						f.family.value = "";
						f.tidtoadd.value = "";
					}
				}
			});
		}

		function verifyLoanDetSciName(f){
			$.ajax({
				type: "POST",
				url: "../editor/rpc/verifysciname.php",
				dataType: "json",
				data: { term: f.sciname.value }
			}).done(function( data ) {
				if(data){
					f.scientificnameauthorship.value = data.author;
					f.family.value = data.family;
					f.tidtoadd.value = data.tid;
				}
				else{
		            alert("WARNING: Taxon not found. It may be misspelled or needs to be added to taxonomic thesaurus by a taxonomic editor.");
					f.scientificnameauthorship.value = "";
					f.family.value = "";
					f.tidtoadd.value = "";
				}
			});
		}

		function selectAll(cb){
			boxesChecked = true;
			if(!cb.checked){
				boxesChecked = false;
			}
			var dbElements = document.getElementsByName("occid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				dbElement.checked = boxesChecked;
			}
		}

	</script>
	<script type="text/javascript" src="../../js/symb/collections.loans.js?ver=1"></script>
	<style>
		fieldset{ padding:15px; margin:15px }
		fieldset legend{ font-weight:bold }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management Menu</a> &gt;&gt;
		<a href="index.php?collid=<?php echo $collid; ?>">Loan Index</a> &gt;&gt;
		<a href="outgoing.php?collid=<?php echo $collid.'&loanid='.$loanId; ?>"><b>Outgoing Loan Management</b></a>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor && $collid){
			//Collection is defined and User is logged-in and have permissions
			if($statusStr){
				$colorStr = 'red';
				if(stripos($statusStr,'SUCCESS') !== false) $colorStr = 'green';
				?>
				<hr/>
				<div style="margin:15px;color:<?php echo $colorStr; ?>;">
					<?php echo $statusStr; ?>
				</div>
				<hr/>
				<?php
			}
			?>
			<div id="tabs" style="margin:0px;">
			    <ul>
					<li><a href="#outloandetaildiv"><span>Loan Details</span></a></li>
					<li><a href="specimentab.php?collid=<?php echo $collid.'&loanid='.$loanId; ?>"><span>Specimens</span></a></li>
					<li><a href="#outloandeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="outloandetaildiv">
					<?php
					$loanArr = $loanManager->getLoanOutDetails($loanId);
					?>
					<form id="editLoanOutForm" name="editLoanOutForm" action="outgoing.php" method="post" onsubmit="return verifyLoanOutEditForm(this)">
						<fieldset>
							<legend>Loan Out Details</legend>
							<div style="padding-top:18px;float:left;">
								<span>
									<b>Loan Number:</b> <input type="text" autocomplete="off" name="loanidentifierown" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $loanArr['loanidentifierown']; ?>" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Entered By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="createdbyown" maxlength="32" style="width:100px;" value="<?php echo $loanArr['createdbyown']; ?>" disabled />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Processed By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="processedbyown" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyown']; ?>" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Sent:
								</span><br />
								<span>
									<input type="date" name="datesent" value="<?php echo $loanArr['datesent']; ?>" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Due:
								</span><br />
								<span>
									<input type="date" name="datedue" value="<?php echo $loanArr['datedue']; ?>" />
								</span>
							</div>
							<div style="padding-top:8px;float:left;">
								<span>
									Sent To:
								</span><br />
								<span>
									<select name="iidborrower">
										<?php
										$instArr = $loanManager->getInstitutionArr();
										foreach($instArr as $k => $v){
											echo '<option value="'.$k.'" '.($loanArr['iidborrower']==$k?'SELECTED':'').'>'.$v.'</option>';
										}
										?>
									</select>
								</span>
								<?php
								if($IS_ADMIN){
									?>
									<span>
										<a href="../misc/institutioneditor.php?iid=<?php echo $loanArr['iidborrower']; ?>" target="_blank" title="Edit institution details (option available only to Super Admin)">
											<img src="../../images/edit.png" style="width:15px;" />
										</a>
									</span>
									<?php
								}
								?>
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Requested for:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="forwhom" maxlength="32" style="width:180px;" value="<?php echo $loanArr['forwhom']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										<b>Specimen Total:</b><br />
										<input type="text" name="totalspecimens" maxlength="32" style="width:150px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $specimenTotal; ?>" onchange=" " disabled />
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										# of Boxes:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="totalboxes" maxlength="32" style="width:50px;" value="<?php echo $loanArr['totalboxes']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										Shipping Service:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="shippingmethod" maxlength="32" style="width:180px;" value="<?php echo $loanArr['shippingmethod']; ?>" onchange=" " />
									</span>
								</div>
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Loan Description:
									</span><br />
									<span>
										<textarea name="description" rows="10" style="width:320px;resize:vertical;" onchange=" "><?php echo $loanArr['description']; ?></textarea>
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										Notes:
									</span><br />
									<span>
										<textarea name="notes" rows="10" style="width:320px;resize:vertical;" onchange=" "><?php echo $loanArr['notes']; ?></textarea>
									</span>
								</div>
							</div>
							<div style="width:100%;padding-top:8px;float:left;">
								<hr />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Date Received:
									</span><br />
									<span>
										<input type="date" name="datereceivedown" value="<?php echo $loanArr['datereceivedown']; ?>" />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Ret. Processed By:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="processedbyreturnown" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyreturnown']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Date Closed:
									</span><br />
									<span>
										<input type="date" name="dateclosed" value="<?php echo $loanArr['dateclosed']; ?>" />
									</span>
								</div>
							</div>
							<div style="clear:left;padding-top:8px;float:left;">
								<span>
									Additional Invoice Message:
								</span><br />
								<span>
									<textarea name="invoicemessageown" rows="5" style="width:700px;resize:vertical;"><?php echo $loanArr['invoicemessageown']; ?></textarea>
								</span>
							</div>
							<div style="clear:both;padding:10px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
								<button name="formsubmit" type="submit" value="Save Outgoing">Save</button>
							</div>
						</fieldset>
					</form>
					<?php
					$loanType = 'out';
					$identifier = $loanId;
					include('reportsinclude.php');
					?>
					<div style="margin:20px"><b>&lt;&lt; <a href="index.php?collid=<?php echo $collid; ?>">Return to Loan Index Page</a></b></div>
				</div>
				<div id="outloandeldiv">
					<form name="deloutloanform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this loan?')">
						<fieldset>
							<legend>Delete Outgoing Loan</legend>
							<?php
							if($specimenTotal){
								?>
								<div style=";margin-bottom:15px;">
									Loan cannot be deleted until all linked specimens are removed
								</div>
								<?php
							}
							?>
							<input name="formsubmit" type="submit" value="Delete Loan" <?php if($specimenTotal) echo 'DISABLED'; ?> />
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
						</fieldset>
					</form>
				</div>
			</div>
			<?php
		}
		else{
			if(!$isEditor) echo '<h2>You are not authorized to add occurrence records</h2>';
			else echo '<h2>ERROR: unknown error, please contact system administrator</h2>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>