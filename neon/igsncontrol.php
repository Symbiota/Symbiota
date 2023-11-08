<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/IgsnManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/igsncontrol.php?'.$_SERVER['QUERY_STRING']);

$recTarget = array_key_exists('recTarget',$_POST)?$_POST['recTarget']:'';
$resetSession = array_key_exists('resetSession',$_POST) && $_POST['resetSession'] == 1?1:0;
$startIndex = array_key_exists('startIndex',$_POST)?$_POST['startIndex']:'';
$limit = array_key_exists('limit',$_POST)?$_POST['limit']:1000;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

//Sanitation
$recTarget = filter_var($recTarget, FILTER_SANITIZE_STRING);
$startIndex = filter_var($startIndex, FILTER_SANITIZE_STRING);
if(!is_numeric($limit)) $limit = 1000;

$igsnManager = new IgsnManager();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;

$statusStr = '';
if($isEditor){
	if($action == 'export'){
		$markAsSubmitted = array_key_exists('markAsSubmitted',$_POST) && $_POST['markAsSubmitted'] == 1?true:false;
		if($igsnManager->exportReport($recTarget, $startIndex, $limit, $markAsSubmitted)){
			exit;
		}
		else{
			$statusStr = 'Unable to create export. Are you sure there are unsynchronized records?';
		}
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> IGSN Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function verifySync(f){
			if(f.recTarget.value == 'notsubmitted'){
				alert("Unsubmitted records cannot be synchronized");
				return false;
			}
			return true;
		}

		function checkMarkAsSubmitted(cbElem){
			let f = cbElem.form;
			if(cbElem.checked){
				if(f.recTarget.value != "notsubmitted") alert("Only 'Not Submitted' records can be tagged as Submitted to NEON");
			}
		}
	</script>
	<style type="text/css">
		fieldset{ padding:15px }
		legend{ font-weight:bold; }
		.fieldGroupDiv{ clear:both; margin:10px; }
		.fieldDiv{ float:left; }
		label{ font-weight: bold; }
		button{ width: 250px; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../index.php">Home</a> &gt;&gt;
	<a href="index.php">NEON Biorepository Tools</a> &gt;&gt;
	<a href="igsncontrol.php"><b>NEON IGSN Control Panel</b></a>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		if($statusStr){
			echo '<div style="color:red">'.$statusStr.'</div>';
		}
		if($action != 'syncIGSNs'){
			?>
			<fieldset>
				<legend><b>Collections Needing IGSNs</b></legend>
				<div style="margin-bottom:10px;">IGSN can only be created for NEON samples that have been both Received and Accepted for Analysis</div>
				<div style="">
					<ul>
						<?php
						$taskList = $igsnManager->getIgsnTaskReport();
						if($taskList){
							foreach($taskList as $collid => $collArr){
								echo '<li>'.$collArr['collname'].' ('.$collArr['collcode'].'): ';
								echo '<a href="../collections/admin/igsnmanagement.php?collid='.$collid.'" target="_blank">'.$collArr['cnt'].'</a></li>';
							}
						}
						else{
							echo '<div style="margin:20px"><b>All collections have IGSN assigned</b></div>';
						}
						?>
					</ul>
				</div>
			</fieldset>
			<?php
		}
		if($action == 'syncIGSNs'){
			echo '<fieldset>';
			echo '<legend>Action Panel: IGSN synchronization</legend>';
			echo '<ul>';
			$startIndex = $igsnManager->synchronizeIgsn($recTarget, $startIndex ,$limit, $resetSession);
			echo '<li><a href="igsncontrol.php">Return to IGSN report listing</a></li>';
			echo '</ul>';
			echo '</fieldset>';
		}
		?>
		<fieldset>
			<legend>SESAR Synchronization</legend>
			<div style="margin-bottom:10px;">
				This tool will harvest all NEON IGSNs from the SASAR system, insert them into a local tables, and then run comparisons with the IGSNs stored within the Biorepo to
				locate inconsistancies. In particular, it is looking for multiple ISGNs assigned to a single specimen, IGSNs within SESAR and not the Biorepo, and in Biorepo and not in SESAR.
			</div>
			<form name="" method="post" action="../collections/admin/igsnverification.php">
				<input name="namespace" type="hidden" value="NEO" />
				<button name="formsubmit" type="submit" value="verifysesar">NEON IGSN Verification</button>
			</form>
		</fieldset>
		<fieldset>
			<legend>IGSN Synchronization with NEON</legend>
			<div style="margin-bottom:10px;">
				Displays occurrence counts that have been synchronized with the central NEON System.
				After IGSNs have been integrated into NEON system, run the synchronization tool to adjust the report.
				Previous unsynchronized records are rechecked within an session. The session will have to be reset to preform additional rechecks.<br>
				Export function will produce a report containing unchecked or unsynchronized records limited by the transaction limit.
				Ideally, reports submitted to NEON should not contain more than 5000 records.
			</div>
			<div style="">
				<ul>
					<?php
					$reportArr = $igsnManager->getIgsnSynchronizationReport();
					if($reportArr){
						echo '<div><label>Not submitted: </label>'.(isset($reportArr['x'])?$reportArr['x']:'0').'</div>';
						echo '<div><label>Submitted but unchecked: </label>'.(isset($reportArr['3'])?$reportArr['3']:'0').'</div>';
						echo '<div><label>Unsynchronized: </label>'.(isset($reportArr[0])?$reportArr[0]:'0').'</div>';
						if(isset($reportArr[100])) echo '<div><label>Unsynchronized (current session): </label>'.$reportArr[100].'</div>';
						echo '<div><label>Synchronized: </label>'.(isset($reportArr[1])?$reportArr[1]:'0').'</div>';
						echo '<div><label>Mismatched: </label>'.(isset($reportArr[2])?$reportArr[2]:'0').'</div>';
						echo '<div><label>Data return errors: </label>'.(isset($reportArr[10])?$reportArr[10]:'0').'</div>';
					}
					?>
				</ul>
				<div style="">
					<form name="igsnsyncform" method="post" action="igsncontrol.php">
						<div style="clear:both;">
							<div style="float:left; margin-left:35px; margin-right:5px"><label>Target:</label> </div>
							<div style="float:left;">
								<input name="recTarget" type="radio" value="notsubmitted" <?php echo ($recTarget == 'notsubmitted'?'checked':''); ?> /> Not submitted<br/>
								<input name="recTarget" type="radio" value="unchecked" <?php echo ($recTarget == 'unchecked'?'checked':''); ?> /> Unchecked<br/>
								<input name="recTarget" type="radio" value="unsynchronized" <?php echo (!$recTarget || $recTarget == 'unsynchronized'?'checked':''); ?> /> Unsynchronized records<br/>
							</div>
						</div>
						<div style="clear:both;padding-top:10px;margin-left:35px;">
							<input name="resetSession" type="checkbox" value="1" >
							<label>reset session</label>
						</div>
						<div style="clear:both;padding-top:10px;margin-left:35px;">
							<label>Start at IGSN:</label> <input name="startIndex" type="text" value="<?php echo $startIndex; ?>" />
						</div>
						<div style="clear:both;padding-top:10px;margin-left:35px;">
							<label>Transaction limit:</label> <input name="limit" type="text" value="<?php echo $limit; ?>" required />
						</div>
						<div style="clear:both;padding:20px 35px;">
							<div style="float:left;"><button name="action" type="submit" value="syncIGSNs" onclick="return verifySync(this.form)">Synchronize Records</button></div>
							<div style="float:left;margin-left:20px">
								<button name="action" type="submit" value="export" style="margin-bottom:0px">Export Report</button><br>
								<span style="margin-left:15px"><input name="markAsSubmitted" type="checkbox" value="1" onchange="checkMarkAsSubmitted(this)"> mark as submitted</span>
							</div>
							<div style="float:left;margin-left:20px">
								<button name="action" type="submit" value="refresh" style="margin-bottom:0px">Rerfresh Statistics</button>
							</div>
						</div>
					</form>
					<div style="clear:both;margin-left:40px">
						<a href="http://data.neonscience.org/web/external-lab-ingest" target="_blank">NEON report submission page</a>
					</div>
				</div>
			</div>
		</fieldset>
		<?php
	}
	else{
		?>
		<div style='font-weight:bold;margin:30px;'>
			You do not have permissions to access occurrence harvester
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