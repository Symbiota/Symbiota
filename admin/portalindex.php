<?php
require_once('../config/symbini.php');
require_once($SERVER_ROOT.'/classes/PortalIndex.php');
//include_once($SERVER_ROOT.'/content/lang/admin/portalindex.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../admin/portalindex.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$portalID = array_key_exists('portalid',$_REQUEST)?$_REQUEST['portalid']:0;
$collID = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Sanitation
if(!is_numeric($portalID)) $portalID = 0;
if(!is_numeric($collID)) $collID = 0;

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
				if($formSubmit == ''){
					//$portalManager->;
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
						if($collID){
							$url = $portalArr['urlRoot'].'/api/v2/collection/'.$collID;
							$collectArr = $portalManager->getAPIResponce($url);
							$collTitle = $collectArr['collectionName'].' ('.$collectArr['institutionCode'].($collectArr['collectionCode']?':'.$collectArr['collectionCode']:'').')';
							echo '<div style="font-weight:bold">#'.$collID.': '.$collTitle.'</div>';
							unset($collectArr['collID']);
							unset($collectArr['collectionName']);
							unset($collectArr['institutionCode']);
							unset($collectArr['collectionCode']);
							echo '<div style="margin:15px 30px;">';
							foreach($collectArr as $fName => $fValue){
								if($fValue) echo '<div><label>'.$fName.'</label>: '.$fValue.'</div>';
							}
							echo '</div>';
							?>
							<form name="collPubForm" method="post" action="portalindex.php">
								<input name="portalid" type="hidden" value="<?php echo $portalID; ?>" />
								<input name="collid" type="hidden" value="<?php echo $collID; ?>" />
								<button ></button>
							</form>
							<?php
						}
						elseif($formSubmit == 'listCollections'){
							$url = $portalArr['urlRoot'].'/api/v2/collection/';
							$collList = $portalManager->getAPIResponce($url);
							if(isset($collList['count'])){
								echo '<div><label>Collection Count</label>: '.$collList['count'].'</div>';
								if($collList['count']){
									echo '<table class="styledtable">';
									echo '<tr><th>ID</th><th>Institution Code</th><th>Collection Code</th><th>Collection Name</th><th>Dataset Type</th><th>Management</th></tr>';
									foreach($collList['results'] as $collArr){
										echo '<tr>';
										echo '<td><a href="portalindex.php?portalid='.$portalID.'&collid='.$collArr['collID'].'">'.$collArr['collID'].'</a></td>';
										echo '<td>'.$collArr['institutionCode'].'</td>';
										echo '<td>'.$collArr['collectionCode'].'</td>';
										echo '<td>'.$collArr['collectionName'].'</td>';
										echo '<td>'.$collArr['collType'].'</td>';
										echo '<td>'.$collArr['managementType'].'</td>';
										echo '</tr>';
									}
									echo '</table>';
								}
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