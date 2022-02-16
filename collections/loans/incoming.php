<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/incoming.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

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
		if($formSubmit == 'createLoanIn'){
			$loanId = $loanManager->createNewLoanIn($_POST);
			if(!$loanId) $statusStr = $loanManager->getErrorMessage();
		}
		elseif($formSubmit == 'Save Incoming'){
			$statusStr = $loanManager->editLoanIn($_POST);
		}
	}
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?>: Incoming Loan Management</title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		var tabIndex = <?php echo $tabIndex; ?>;

		function verifyLoanInEditForm(f){
			var submitStatus = true;
			$("#editLoanInForm input[type=date]").each(function() {
				//Need for Safari browser which doesn't support date input types
				if(this.value != ""){
					var validFormat = /^\s*\d{4}-\d{2}-\d{2}\s*$/ //Format: yyyy-mm-dd
					if(!validFormat.test(this.value)){
						alert("Date (e.g. "+this.name+") values must follow format: YYYY-MM-DD");
						submitStatus = false;
					}
				}
			});
			if(f.iidowner.options[f.iidowner.selectedIndex].value == 0){
				alert("Select an institution");
				submitStatus = false;
			}
			if(f.loanidentifierown.value == ""){
				alert("Enter the sender's loan number");
				submitStatus = false;
			}
			return submitStatus;
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
	<div class="navpath">
		<a href='../../index.php'>Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management Menu</a> &gt;&gt;
		<a href="index.php?tabindex=1&collid=<?php echo $collid; ?>">Loan Index</a> &gt;&gt;
		<a href="incoming.php?collid=<?php echo $collid.'&loanid='.$loanId; ?>"><b>Incoming Loan Management</b></a>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor && $collid){
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
			$specList = $loanManager->getSpecimenList($loanId);
			?>
			<div id="tabs" style="margin:0px;">
			    <ul>
					<li><a href="#loandiv"><span>Loan Details</span></a></li>
					<?php
					if($specList){
						?>
						<li><a href="#specdiv"><span>Specimens</span></a></li>
						<?php
					}
					?>
					<li><a href="#inloandeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="loandiv">
					<?php
					$loanArr = $loanManager->getLoanInDetails($loanId);
					?>
					<form id="editLoanInForm" name="editloanform" action="incoming.php" method="post" onsubmit="return verifyLoanInEditForm(this)">
						<fieldset>
							<legend>Loan In Details</legend>
							<div style="padding-top:18px;float:left;">
								<span>
									<b>Loan Number:</b> <input type="text" autocomplete="off" name="loanidentifierborr" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo ($loanArr['loanidentifierborr']?$loanArr['loanidentifierborr']:$loanArr['loanidentifierown']); ?>" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Entered By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="createdbyborr" maxlength="32" style="width:100px;" value="<?php echo ($loanArr['createdbyborr']?$loanArr['createdbyborr']:$PARAMS_ARR['un']); ?>" onchange=" " disabled />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Processed By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="processedbyborr" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyborr']; ?>" onchange=" " />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Received:
								</span><br />
								<span>
									<input type="date" name="datereceivedborr" value="<?php echo $loanArr['datereceivedborr']; ?>" onchange="checkDate(this)" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Due:
								</span><br />
								<span>
									<input type="date" name="datedue" value="<?php echo $loanArr['datedue']; ?>" <?php echo ($loanArr['collidown']?'disabled':''); ?> onchange="checkDate(this)" />
								</span>
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Sent From:
									</span><br />
									<span>
										<select name="iidowner">
											<?php
											$instArr = $loanManager->getInstitutionArr();
											foreach($instArr as $k => $v){
												echo '<option value="'.$k.'" '.($loanArr['iidowner']==$k?'SELECTED':'').'>'.$v.'</option>';
											}
											?>
										</select>
									</span>
								</div>
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;margin-right:40px;">
									<span>
										Sender's Loan Number:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="loanidentifierown" maxlength="255" style="width:160px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $loanArr['loanidentifierown']; ?>" <?php echo ($loanArr['collidown']?'disabled':''); ?> />
									</span>
								</div>
								<div style="float:left;margin-right:40px;">
									<span>
										Requested for:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="forwhom" maxlength="32" style="width:180px;" value="<?php echo $loanArr['forwhom']; ?>" onchange=" " />
									</span>
								</div>
								<div style="float:left;">
									<span>
										<b>Specimen Total:</b><br />
										<input type="text" autocomplete="off" name="numspecimens" maxlength="32" style="width:150px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo ($loanArr['collidown']?count($specList):$loanArr['numspecimens']); ?>" onchange=" " <?php echo ($loanArr['collidown']?'disabled':''); ?> />
									</span>
								</div>
							</div>
							<div style="padding-top:8px;clear:both;">
								<div style="float:left;">
									<span>
										Loan Description:
									</span><br />
									<span>
										<textarea name="description" rows="10" style="width:320px;resize:vertical;" onchange=" " <?php echo ($loanArr['collidown']?'disabled="disabled"':''); ?> ><?php echo $loanArr['description']; ?></textarea>
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										Notes:
									</span><br />
									<span>
										<textarea name="notes" rows="10" style="width:320px;resize:vertical;" onchange=" " <?php echo ($loanArr['collidown']?'disabled="disabled"':''); ?> ><?php echo $loanArr['notes']; ?></textarea>
									</span>
								</div>
							</div>
							<div style="width:100%;padding-top:8px;float:left;">
								<hr />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Date Returned:
									</span><br />
									<span>
										<input type="date" name="datesentreturn" value="<?php echo $loanArr['datesentreturn']; ?>" onchange="checkDate(this)" />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Ret. Processed By:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="processedbyreturnborr" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyreturnborr']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										# of Boxes:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="totalboxesreturned" maxlength="32" style="width:50px;" value="<?php echo $loanArr['totalboxesreturned']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Shipping Service:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="shippingmethodreturn" maxlength="32" style="width:180px;" value="<?php echo $loanArr['shippingmethodreturn']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Date Closed:
									</span><br />
									<span>
										<input type="date" name="dateclosed" value="<?php echo $loanArr['dateclosed']; ?>" <?php echo ($loanArr['collidown']?'disabled':''); ?> onchange="checkDate(this)" />
									</span>
								</div>
							</div>
							<div style="padding-top:8px;float:left;">
								<div>
									Additional Invoice Message:
								</div>
								<div>
									<textarea name="invoicemessageborr" rows="5" style="width:700px;resize:vertical;" onchange=" "><?php echo $loanArr['invoicemessageborr']; ?></textarea>
								</div>
							</div>
							<div style="clear:both;padding-top:8px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="collidborr" type="hidden" value="<?php echo $collid; ?>" />
								<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
								<button name="formsubmit" type="submit" value="Save Incoming">Save</button>
							</div>
						</fieldset>
					</form>
					<?php
					$specimenTotal = count($specList);
					$loanType = 'in';
					$identifier = $loanId;
					include('reportsinclude.php');
					?>
					<div style="margin:20px"><b>&lt;&lt; <a href="index.php?collid=<?php echo $collid; ?>">Return to Loan Index Page</a></b></div>
				</div>
				<?php
				if($specList){
					?>
					<div id="specdiv">
						<table class="styledtable" style="font-family:Arial;font-size:12px;">
							<tr>
								<th style="width:100px;text-align:center;">Catalog Number</th>
								<th style="width:375px;text-align:center;">Details</th>
								<th style="width:75px;text-align:center;">Date Returned</th>
							</tr>
							<?php
							foreach($specList as $occid => $specArr){
								?>
								<tr>
									<td>
										<div style="float:right">
											<a href="#" onclick="openIndPopup(<?php echo $occid; ?>); return false;"><img src="../../images/list.png" style="width:13px" title="Open Specimen Details page" /></a><br/>
											<a href="#" onclick="openEditorPopup(<?php echo $occid; ?>); return false;"><img src="../../images/edit.png" style="width:13px" title="Open Occurrence Editor" /></a>
										</div>
										<?php
										if($specArr['catalognumber']) echo '<div>'.$specArr['catalognumber'].'</div>';
										if(isset($specArr['othercatalognumbers'])) echo '<div>'.implode('; ',$specArr['othercatalognumbers']).'</a></div>';
										?>
									</td>
									<td>
										<?php
										$loc = $specArr['locality'];
										if(strlen($loc) > 500) $loc = substr($loc,400);
										echo '<i>'.$specArr['sciname'].'</i>; ';
										echo  $specArr['collector'].'; '.$loc;
										if($specArr['notes']) echo '<div class="notesDiv"><b>Notes:</b> '.$specArr['notes'],'</div>';
										?>
									</td>
									<td><?php echo $specArr['returndate']; ?></td>
								</tr>
								<?php
							}
							?>
						</table>
					</div>
					<?php
				}
				?>
				<div id="inloandeldiv">
					<form name="delinloanform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this loan?')">
						<fieldset>
							<legend>Delete Incoming Loan</legend>
							<?php
							if($specList){
								?>
								<div style=";margin-bottom:15px;">
									Loan cannot be deleted until all linked specimens are removed (can only be done by lending institution)
								</div>
								<?php
							}
							?>
							<input name="formsubmit" type="submit" value="Delete Loan" <?php if($specList) echo 'DISABLED'; ?> />
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