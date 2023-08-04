<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/exchange.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$exchangeId = array_key_exists('exchangeid',$_REQUEST)?$_REQUEST['exchangeid']:0;
$identifier = array_key_exists('identifier',$_REQUEST)?$_REQUEST['identifier']:0;
$formSubmit = array_key_exists('formsubmit',$_REQUEST)?$_REQUEST['formsubmit']:'';
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
		if($formSubmit == 'createExchange'){
			$exchangeId = $loanManager->createNewExchange($_POST);
			if(!$exchangeId) $statusStr = $loanManager->getErrorMessage();
		}
		elseif($formSubmit == 'Save Exchange'){
			$statusStr = $loanManager->editExchange($_POST);
		}
		elseif ($formSubmit == "delAttachment") {
			// Delete correspondence attachment
			if (array_key_exists('attachid',$_REQUEST) && is_numeric($_REQUEST['attachid'])) $loanManager->deleteAttachment($_REQUEST['attachid']);
			$statusStr = $loanManager->getErrorMessage();
		}
		elseif ($formSubmit == "saveAttachment") {
			// Save correspondence attachment
			if (array_key_exists('uploadfile',$_FILES)) $loanManager->uploadAttachment($collid, 'exch', $exchangeId, $identifier, $_POST['uploadtitle'], $_FILES['uploadfile']);
			$statusStr = $loanManager->getErrorMessage();
		}
	}
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?>: Exchange Management</title>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		var tabIndex = <?php echo $tabIndex; ?>;
	</script>
	<script type="text/javascript" src="../../js/symb/collections.loans.js?ver=2"></script>
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
		<a href="../misc/collprofiles.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>&emode=1">Collection Management Menu</a> &gt;&gt;
		<a href="index.php?tabindex=2&collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>">Loan Index</a> &gt;&gt;
		<a href="exchange.php?exchangeid=<?php echo htmlspecialchars($exchangeId, HTML_SPECIAL_CHARS_FLAGS); ?>&collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>"><b>Exchange Management</b></a>
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
			?>
			<div id="tabs" style="margin:0px;">
			    <ul>
					<li><a href="#exchangedetaildiv"><span>Exchange Details</span></a></li>
					<li><a href="#exchangedeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="exchangedetaildiv" style="">
					<?php
					$exchangeArr = $loanManager->getExchangeDetails($exchangeId);
					?>
					<form name="editexchangegiftform" action="exchange.php" method="post">
						<fieldset>
							<?php
							if($exchangeArr['transactiontype']=='Adjustment'){ ?>
								<legend>Edit Adjustment</legend>
								<div style="padding-top:4px;float:left;">
									<div style="padding-top:12px;float:left;">
										<span>
											<b>Transaction Number:</b> <input type="text" autocomplete="off" name="identifier" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $exchangeArr['identifier']; ?>" disabled />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Transaction Type:
										</span><br />
										<span>
											<select name="transactiontype" style="width:100px;">
												<?php if($exchangeArr['transactiontype']=='Shipment'){ ?>
													<option value="Shipment" <?php echo ($exchangeArr['transactiontype']=='Shipment'?'SELECTED':'');?>>Shipment</option>
												<?php }
												if($exchangeArr['transactiontype']=='Adjustment'){ ?>
													<option value="Adjustment" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'SELECTED':'');?>>Adjustment</option>
												<?php } ?>
											</select>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Entered By:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="createdby" maxlength="32" style="width:100px;" value="<?php echo $exchangeArr['createdby']; ?>" disabled />
										</span>
									</div>
								</div>
								<div style="padding-top:8px;float:left;">
									<div style="float:left;">
										<span>
											Institution:
										</span>
										<span>
											<select name="iid" style="width:400px;" >
												<?php
												$instArr = $loanManager->getInstitutionArr();
												foreach($instArr as $k => $v){
													echo '<option value="'.$k.'" '.($k==$exchangeArr['iid']?'SELECTED':'').'>'.$v.'</option>';
												}
												?>
											</select>
										</span>
									</div>
									<div style="float:left;">
										<span style="margin-left:40px;">
											<b>Adjustment Amount:</b> <input type="text" autocomplete="off" name="adjustment" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['adjustment']; ?>" />
										</span>
									</div>
								</div>
								<?php
							}
							else{
								?>
								<legend>Edit Gift/Exchange</legend>
								<div style="padding-top:4px;float:left;">
									<div style="padding-top:12px;float:left;">
										<span>
											<b>Transaction Number:</b> <input type="text" autocomplete="off" name="identifier" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $exchangeArr['identifier']; ?>" disabled />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Entered By:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="createdby" maxlength="32" style="width:100px;" value="<?php echo $exchangeArr['createdby']; ?>" disabled />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Date Shipped:
										</span><br />
										<span>
											<input type="date" name="datesent" value="<?php echo $exchangeArr['datesent']; ?>" />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Date Received:
										</span><br />
										<span>
											<input type="date" name="datereceived" value="<?php echo $exchangeArr['datereceived']; ?>" />
										</span>
									</div>
								</div>
								<div style="padding-top:8px;padding-bottom:8px;float:left;">
									<div style="float:left;">
										<span>
											Institution:
										</span><br />
										<span>
											<select name="iid" style="width:400px;" >
												<?php
												$instArr = $loanManager->getInstitutionArr();
												foreach($instArr as $k => $v){
													echo '<option value="'.$k.'" '.($k==$exchangeArr['iid']?'SELECTED':'').'>'.$v.'</option>';
												}
												?>
											</select>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Transaction Type:
										</span><br />
										<span>
											<select name="transactiontype" style="width:100px;">
												<?php if($exchangeArr['transactiontype']=='Shipment'){ ?>
													<option value="Shipment" <?php echo ($exchangeArr['transactiontype']=='Shipment'?'SELECTED':'');?>>Shipment</option>
												<?php }
												if($exchangeArr['transactiontype']=='Adjustment'){ ?>
													<option value="Adjustment" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'SELECTED':'');?>>Adjustment</option>
												<?php } ?>
											</select>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											In/Out:
										</span><br />
										<span>
											<select name="in_out" style="width:100px;">
												<?php if($exchangeArr['transactiontype']=='Adjustment'){ ?>
													<option value="" <?php echo (!$exchangeArr['in_out']?'SELECTED':'');?>>   </option>
												<?php }
												if($exchangeArr['transactiontype']=='Shipment'){ ?>
													<option value="Out" <?php echo ('Out'==$exchangeArr['in_out']?'SELECTED':'');?>>Out</option>
													<option value="In" <?php echo ('In'==$exchangeArr['in_out']?'SELECTED':'');?>>In</option>
												<?php } ?>
											</select>
										</span>
									</div>
								</div>
								<div style="padding-top:8px;padding-bottom:8px;">
									<table class="styledtable" style="font-family:Arial;font-size:12px;">
										<tr>
											<th style="width:220px;text-align:center;">Gift Specimens</th>
											<th style="width:220px;text-align:center;">Exchange Specimens</th>
											<th style="width:220px;text-align:center;">Transaction Totals</th>
										</tr>
										<tr style="text-align:right;">
											<td><b>Total Gifts:</b> <input type="text" autocomplete="off" name="totalgift"  maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalgift']; ?>" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Total Unmounted:</b> <input type="text" autocomplete="off" name="totalexunmounted" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalexunmounted']; ?>" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Exchange Value:</b> <input type="text" name="exchangevalue" maxlength="32" style="width:80px;border:1px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $loanManager->getExchangeValue($exchangeId); ?>" disabled="disabled" /></td>
										</tr>
										<tr style="text-align:right;">
											<td><b>Total Gifts For Det:</b> <input type="text" autocomplete="off" name="totalgiftdet" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalgiftdet']; ?>" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Total Mounted:</b> <input type="text" autocomplete="off" name="totalexmounted" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalexmounted']; ?>" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Total Specimens:</b> <input type="text" name="totalspecimens" maxlength="32" style="width:80px;border:1px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $loanManager->getExchangeTotal($exchangeId); ?>" disabled="disabled" /></td>
										</tr>
									</table>
								</div>
								<div style="padding-top:8px;float:left;">
									<div style="padding-top:15px;float:left;">
										<span style="margin-left:25px;">
											<b>Current Balance:</b> <input type="text" name="invoicebalance" maxlength="32" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $exchangeArr['invoicebalance']; ?>" disabled />
										</span>
									</div>
									<div style="margin-left:100px;float:left;">
										<span>
											# of Boxes:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="totalboxes" maxlength="32" style="width:50px;" value="<?php echo $exchangeArr['totalboxes']; ?>" />
										</span>
									</div>
									<div style="margin-left:60px;float:left;">
										<span>
											Shipping Service:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="shippingmethod" maxlength="32" style="width:180px;" value="<?php echo $exchangeArr['shippingmethod']; ?>" />
										</span>
									</div>
								</div>
								<div style="padding-top:8px;float:left;">
									<div style="float:left;">
										<span>
											Description:
										</span><br />
										<span>
											<textarea name="description" rows="10" style="width:320px;resize:vertical;"><?php echo $exchangeArr['description']; ?></textarea>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Notes:
										</span><br />
										<span>
											<textarea name="notes" rows="10" style="width:320px;resize:vertical;"><?php echo $exchangeArr['notes']; ?></textarea>
										</span>
									</div>
								</div>
								<div style="width:100%;padding-top:8px;float:left;">
									<hr />
								</div>
								<div style="padding-top:8px;float:left;">
									<span>
										Additional Message:
									</span><br />
									<span>
										<textarea name="invoicemessage" rows="5" style="width:700px;resize:vertical;"><?php echo $exchangeArr['invoicemessage']; ?></textarea>
									</span>
								</div>
								<?php
							}
							?>
							<div style="clear:both;padding-top:8px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="exchangeid" type="hidden" value="<?php echo $exchangeId; ?>" />
								<input name="tabindex" type="hidden" value="2" />
								<button name="formsubmit" type="submit" value="Save Exchange">Save</button>
							</div>
						</fieldset>
					</form>
					<?php
					if($exchangeArr['transactiontype']=='Shipment'){
						//Following variables are used within reportsinclude.php, with different values when used on different pages
						$loanType = 'exchange';
						$identifier = $exchangeId;
						include('reportsinclude.php');
					}
					$attachments = $loanManager->getAttachments('exch', $exchangeId);
					if($attachments !== false){
						?>
						<div>
							<form id="attachmentform" name="attachmentform" action="exchange.php" method="post" enctype="multipart/form-data" onsubmit="return verifyFileUploadForm(this)">
								<fieldset>
									<legend>Correspondence Attachments</legend>
									<?php
									// Add any correspondence attachments
									if ($attachments) {
										echo '<ul>';
										foreach($attachments as $attachId => $attachArr){
											echo '<li><div style="float: left;">' . $attachArr['timestamp'] . ' -</div>';
											echo '<div style="float: left; margin-left: 5px;"><a href="../../' .
												$attachArr['path'] . $attachArr['filename']  .'" target="_blank">' .
												($attachArr['title'] != "" ? $attachArr['title'] : $attachArr['filename']) . '</a></div>';
											echo '<a href="exchange.php?collid=' . htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS) . '&exchangeid=' . htmlspecialchars($exchangeId, HTML_SPECIAL_CHARS_FLAGS) . '&attachid='. htmlspecialchars($attachId, HTML_SPECIAL_CHARS_FLAGS) . '&formsubmit=delAttachment"><img src="../../images/del.png" style="width: 15px; margin-left: 5px;"></a></li>';
										}
										echo '</ul>';
									}
									?>
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input name="exchangeid" type="hidden" value="<?php echo $exchangeId; ?>" />
									<input name="identifier" type="hidden" value="<?php echo $exchangeArr['identifier']; ?>" />
									<label style="font-weight: bold;">Add Correspondence Attachment:<sup>*</sup> </label><br/>
									<label>Attachment Title: </label>
									<input name="uploadtitle" type="text" placeholder=" optional, replaces filename" maxlength="80" size="30" />
									<input id="uploadfile" name="uploadfile" type="file" size="30" onchange="verifyFileSize(this)">
									<button name="formsubmit" type="submit" value="saveAttachment">Save Attachment</button>
									<div style="margin-left: 10px"><br/>
									<sup>*</sup>Supported file types include PDF, Word, Excel, images (.jpg/.jpeg or png), and text files (.txt). </br>
									PDFs, images, and text files are preferred, since they will display in the browser.
									</div>
								</fieldset>
							</form>
						</div>
						<?php
					}
					?>
					<div style="margin:20px"><b>&lt;&lt; <a href="index.php?collid=<?php echo htmlspecialchars($collid, HTML_SPECIAL_CHARS_FLAGS); ?>">Return to Loan Index Page</a></b></div>
				</div>
				<div id="exchangedeldiv">
					<form name="delexchangeform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this exchange?')">
						<fieldset>
							<legend>Delete Exchange</legend>
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="tabindex" type="hidden" value="2" />
							<input name="exchangeid" type="hidden" value="<?php echo $exchangeId; ?>" />
							<input name="formsubmit" type="submit" value="Delete Exchange" />
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