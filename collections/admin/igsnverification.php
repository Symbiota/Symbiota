<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');
header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/igsnmanagement.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$namespace = array_key_exists('namespace',$_REQUEST)?$_REQUEST['namespace']:'';
$action = array_key_exists('formsubmit',$_REQUEST)?$_REQUEST['formsubmit']:'';

//Variable sanitation
if(!is_numeric($collid)) $collid = 0;
if(preg_match('/[^A-Z]+/', $namespace)) $namespace = '';

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS))){
	$isEditor = 1;
}
$guidManager = new OccurrenceSesar();
$guidManager->setCollid($collid);
$guidManager->setCollArr();
$guidManager->setNamespace($namespace);

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title>IGSN GUID Management</title>
	<?php
	$activateJQuery = false;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript">
		function syncIGSN(occid, catNum, igsn){
			$.ajax({
				method: "POST",
				data: { occid: occid, catnum: catNum, igsn: igsn },
				dataType: "json",
				url: "rpc/syncigsn.php"
			})
			.done(function(jsonRes) {
				if(jsonRes.status == 1){
					$("#syncDiv-"+occid).text('SUCCESS: IGSN added!');
				}
				else{
					$("#syncDiv-"+occid).css('color', 'red');
					if(jsonRes.errCode == 1) $("#syncDiv-"+occid).text('FAILED: occurrenceID GUID already exists: '+jsonRes.guid);
					else if(jsonRes.errCode == 2) $("#syncDiv-"+occid).text('FAILED: catalogNumber does not match: '+jsonRes.catNum);
					else if(jsonRes.errCode == 3) $("#syncDiv-"+occid).text('FAILED: occurrence record not found (#'+occid+')');
					else if(jsonRes.errCode == 8) $("#syncDiv-"+occid).text('FAILED: not authorized to modify occurrence');
					else if(jsonRes.errCode == 9) $("#syncDiv-"+occid).text('FAILED: missing variables');
				}
			})
		}
	</script>
	<style type="text/css">
		fieldset{ margin:10px; padding:15px; }
		fieldset legend{ font-weight:bold; }
		.form-label{  }
		button{ margin:15px; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = 'false';
include($SERVER_ROOT.'/includes/header.php');
?>
<div class='navpath'>
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
	<a href="igsnmapper.php?collid=<?php echo $collid; ?>">IGSN GUID Generator</a> &gt;&gt;
	<a href="igsnmanagement.php?collid=<?php echo $collid; ?>">IGSN GUID Management</a> &gt;&gt;
	<b>IGSN Verification</b>
</div>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($isEditor){
		echo '<h3>IGSN Management: '.$guidManager->getCollectionName().'</h3>';
		if($statusStr){
			?>
			<fieldset>
				<legend>Error Panel</legend>
				<?php echo $statusStr; ?>
			</fieldset>
			<?php
		}
		if(!$guidManager->getProductionMode()){
			echo '<h2 style="color:orange">-- In Development Mode --</h2>';
		}
		if($action == 'verifysesar'){
			echo '<fieldset><legend>Action Panel</legend>';
			echo '<ul>';
			$guidManager->setVerboseMode(2);
			echo '<li>Verifying all IGSNs located within SESAR system against portal database...</li>';
			$sesarArr = $guidManager->verifySesarGuids();
			echo '<li style="margin-left:15px">Results:</li>';
			echo '<li style="margin-left:25px">Checked '.$sesarArr['totalCnt'].' IGSNs</li>';
			if(isset($sesarArr['collid'])){
				echo '<li style="margin-left:25px">Registered IGSNs by Collection:</li>';
				foreach($sesarArr['collid'] as $id => $collArr){
					echo '<li style="margin-left:40px"><a href="../misc/collprofiles.php?collid='.$id.'" target="_blank">'.$collArr['name'].'</a>: '.$collArr['cnt'].' IGSNs</li>';
				}
			}
			$missingCnt = 0;
			if(isset($sesarArr['missing'])) $missingCnt = count($sesarArr['missing']);
			echo '<li style="margin-left:25px">';
			echo '# IGSNs not in database: '.$missingCnt;
			if($missingCnt) echo ' <a href="#" onclick="$(\'#missingGuidList\').show();return false;">(display list)</a>';
			echo '</li>';
			if($missingCnt){
				echo '<div id="missingGuidList" style="margin-left:40px;display:none">';
				foreach($sesarArr['missing'] as $igsn => $missingArr){
					echo '<li><a href="https://app.geosamples.org/sample/igsn/'.$igsn.'" target="_blank" title="Open IGSN in SESAR Systems">'.$igsn.'</a> ';
					if(isset($missingArr['occid'])){
						echo '=> <a href="../individual/index.php?occid='.$missingArr['occid'].'" target="_blank" title="Open occurrence profile page">'.$missingArr['catNum'].'</a> ';
						echo '<a href="#" onclick="syncIGSN('.$missingArr['occid'].',\''.$missingArr['catNum'].'\',\''.$igsn.'\');return false" title="Add IGSN to target occurrence"><img src="../../images/link.png" style="width:13px"/></a>';
						echo '<span id="syncDiv-'.$missingArr['occid'].'" style="margin-left:15px;color:green;"></span>';
					}
					echo '</li>';
				}
				echo '</div>';
			}
			echo '<li style="margin-left:15px">Finished verifying GUIDs!</li>';
			echo '</ul>';

			if($collid){
				echo '<ul style="margin-top:15px">';
				echo '<li>Verifying collection\'s IGSNs against SESAR system...</li>';
				ob_flush();
				flush();
				$localArr = $guidManager->verifyLocalGuids();
				$missingCnt = 0;
				if(isset($localArr)) $missingCnt = count($localArr);
				echo '<li style="margin-left:15px">';
				echo 'IGSNs in portal, but not SESAR: '.$missingCnt;
				if($missingCnt) echo ' <a href="#" onclick="$(\'#unmappedGuidList\').show();return false;">(display list)</a>';
				echo '</li>';
				if($missingCnt){
					echo '<div id="unmappedGuidList" style="margin-left:30px;display:none">';
					foreach($localArr as $occid => $guid){
						echo '<li><a href="../individual/index.php?occid='.$occid.'" target="_blank">'.$guid.'</a></li>';
					}
					echo '</div>';
				}
				echo '<li style="margin-left:15px">Finished verifying local IGSN GUIDs!</li>';
				echo '</ul>';
			}
			echo '</fieldset>';
		}
	}
	else echo '<h2>You are not authorized to access this page or collection identifier has not been set</h2>';
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>