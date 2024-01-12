<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyHarvester.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../taxa/taxonomy/quickload.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$sciname = array_key_exists('sciname',$_REQUEST)?$_REQUEST['sciname']:'';
$author = array_key_exists('author',$_REQUEST)?$_REQUEST['author']:'';
$kingdom = array_key_exists('kingdom',$_REQUEST)?$_REQUEST['kingdom']:'';
$submitAction = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

//Sanitation
$sciname = filter_var($sciname,FILTER_SANITIZE_STRING);
$author = filter_var($author,FILTER_SANITIZE_STRING);
$kingdom = filter_var($kingdom,FILTER_SANITIZE_STRING);

$loadManager = new TaxonomyHarvester();

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy',$USER_RIGHTS)){
	$isEditor = true;
}

$status = '';
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Taxon Loader: </title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.min.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery-3.7.1.min.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.min.js"></script>
	<script type="text/javascript">
		function verifyLoadForm(f){
			if(f.sciname.value == ""){
				alert("Taxon name must have an value");
				return false;
			}
			return true;
		}

	</script>
</head>
<body>
<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="taxonomydisplay.php">Taxonomy Tree Viewer</a> &gt;&gt;
		<b>Taxonomy Loader</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($status){
			echo '<div style="margin:20px;">'.$status.'</div>';
		}
		if($isEditor){
			if($submitAction){
				echo '<fieldset><legend>Action Panel</legend>';
				if($submitAction == 'submitName'){
					$loadManager->setVerboseMode(2);
					$loadManager->setDefaultAuthor($author);
					$kTid = substr($kingdom,0,strpos($kingdom,':'));
					$kName = substr($kingdom,strpos($kingdom,':')+1);
					$loadManager->setKingdomTid($kTid);
					$loadManager->setKingdomName($kName);
					$loadManager->setTaxonomicResources(array('col'));
					$tid = $loadManager->processSciname($sciname);
				}
				echo '</fieldset>';
			}
			?>
			<form id="loaderform" name="loaderform" action="quickload.php" method="post" onsubmit="return verifyLoadForm(this)">
				<fieldset>
					<legend><b>Add New Taxon</b></legend>
					<div>
						<div style="float:left;width:170px;">Taxon Name:</div>
						<input type="text" id="sciname" name="sciname" style="width:300px;border:inset;" value="<?php echo $sciname; ?>" />
					</div>
					<div>
						<div style="float:left;width:170px;">Author:</div>
						<input type='text' id='author' name='author' style='width:300px;border:inset;' value="<?php echo $author; ?>" />
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Kingdom:</div>
						<select id="rankid" name="kingdom" style="border:inset;">
							<option value="">Select Kingdom</option>
							<option value="">--------------------------------</option>
							<?php
							$defaultKingdom = $loadManager->getDefaultKingdom();
							$kindomArr = $loadManager->getKingdomArr();
							foreach($kindomArr as $kingdomTid => $kingdomName){
								$id = $kingdomTid.':'.$kingdomName;
								echo '<option value="'.$id.'" '.($kingdomTid==$defaultKingdom['tid']?' SELECTED':'').'>'.$kingdomName.'</option>';
							}
							?>
						</select>
					</div>
					<div style="clear:both;">
						<button type="submit" name="submitaction" value="submitName">Submit New Name</button>
					</div>
				</fieldset>
			</form>
			<?php
		}
		else{
			?>
			<div style="margin:30px;font-weight:bold;font-size:120%;">
				You do not have permission to access this page. Please contact the portal manager.
			</div>
			<?php
		}
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</div>
</body>
</html>