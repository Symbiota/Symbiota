<?php
require_once('../config/symbini.php');
require_once($SERVER_ROOT.'/classes/PortalIndex.php');
//include_once($SERVER_ROOT.'/content/lang/admin/portalindex.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../admin/portalindex.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$portalID = array_key_exists('portalid',$_REQUEST)?$_REQUEST['portalid']:0;
$remoteID = array_key_exists('remoteid',$_REQUEST)?$_REQUEST['remoteid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Sanitation
if(!is_numeric($portalID)) $portalID = 0;
if(!is_numeric($remoteID)) $remoteID = 0;

$portalManager = new PortalIndex();

$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Portal Index Control Panel</title>
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
		</script>
		<style type="text/css">
			fieldset{ margin:20px; padding:15px; }
			legend{ font-weight: bold; }
			label{ font-weight: bold; }
			hr{ margin-top: 15px; margin-bottom: 15px; }
		</style>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<b>Portal Index Control Panel</b>
		</div>
		<div id="innertext">
			<?php
			if($isEditor){
				if($formSubmit == 'importProfile'){
					if($collid = $portalManager->importProfile($portalID, $remoteID))
						echo '<div><a href="../collections/misc/collprofiles.php?collid='.$collid.'" target="_blank">New snapshot collection created</a></div>';
						else echo '<div>failed to insert new collections: '.$portalManager->getErrorMessage().'</div>';
				}
				$indexArr = $portalManager->getPortalIndexArr($portalID);
				?>
				<fieldset>
					<legend>Portal Index</legend>
					<?php
					foreach($indexArr as $portalID => $portalArr){
						foreach($portalArr as $fieldName => $fieldValue){
							if($fieldValue){
								echo '<div><label>'.$fieldName.'</label>: ';
								$href = '';
								if($fieldName=='urlRoot') $href = $fieldValue;
								elseif($fieldName=='guid') $href = $portalArr['urlRoot'].'/api/v2/installation/ping';
								if($href) echo '<a href="'.$href.'" target="_blank">';
								echo $fieldValue;
								if($href) echo '</a>';
								echo '</div>';
							}
						}
						echo '<hr/>';
						if($remoteID){
							$collectArr = $portalManager->getCollectionList($portalArr['urlRoot'], $remoteID);
							$collTitle = $collectArr['collectionName'].' ('.$collectArr['institutionCode'].($collectArr['collectionCode']?':'.$collectArr['collectionCode']:'').')';
							echo '<div style="font-weight:bold">#'.$remoteID.': '.$collTitle.'</div>';
							$collectArr['remoteID'] = $collectArr['collID'];
							unset($collectArr['collID']);
							echo '<div style="margin:15px 30px;">';
							foreach($collectArr as $fName => $fValue){
								if($fValue){
									if($fName == 'fullDescription') $fValue = htmlentities($fValue);
									elseif($fName == 'internalCollid') $fValue = '<a href="'.$CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.$fValue.'" target="_blank">Mapped Internally</a>';
									echo '<div><label>'.$fName.'</label>: '.$fValue.'</div>';
								}
							}
							echo '</div>';
							if(!$collectArr['internalCollid']){
								?>
								<div style="margin: 0px 30px">
									<form name="collPubForm" method="post" action="portalindex.php">
										<input name="portalid" type="hidden" value="<?php echo $portalID; ?>" />
										<input name="remoteid" type="hidden" value="<?php echo $remoteID; ?>" />
										<input name="collid" type="hidden" value="<?php echo $collectArr['internalCollid']; ?>" />
										<button name="formsubmit" type="submit" value="importProfile">Import Profile</button>
									</form>
								</div>
								<?php
							}
						}
						elseif($formSubmit == 'listCollections'){
							if($collList = $portalManager->getCollectionList($portalArr['urlRoot'])){
								echo '<div><label>Collection Count</label>: '.count($collList).'</div>';
								echo '<table class="styledtable">';
								echo '<tr><th>ID</th><th>Institution Code</th><th>Collection Code</th><th>Collection Name</th><th>Dataset Type</th><th>Management</th><th>Mapped Internally</th></tr>';
								foreach($collList as $collArr){
									echo '<tr>';
									echo '<td><a href="portalindex.php?portalid='.$portalID.'&remoteid='.$collArr['collID'].'">'.$collArr['collID'].'</a></td>';
									echo '<td>'.$collArr['institutionCode'].'</td>';
									echo '<td>'.$collArr['collectionCode'].'</td>';
									echo '<td>'.$collArr['collectionName'].'</td>';
									echo '<td>'.$collArr['collType'].'</td>';
									echo '<td>'.$collArr['managementType'].'</td>';
									if($collArr['internalCollid']) $internal = '<a href="'.$CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.$collArr['internalCollid'].'" target="_blank">Yes</a>';
									else $internal = 'No';
									echo '<td>'.$internal.'</td>';
									echo '</tr>';
								}
								echo '</table>';
							}
						}
						else{
							?>
							<div>
								<form name="portalActionForm" method="post" action="portalindex.php">
									<input name="portalid" type="hidden" value="<?php echo $portalID; ?>" />
									<button name="formsubmit" type="submit" value="listCollections">List Collections</button>
								</form>
							</div>
							<?php
						}
						echo '<hr/>';
					}
					?>
				</fieldset>
				<?php
			}
			else echo '<h2>ERROR: access denied</h2>';
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>