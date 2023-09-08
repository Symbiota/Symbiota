<?php
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;

include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceProtectedSpecies.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$searchTaxon = array_key_exists('searchtaxon', $_REQUEST) ? $_REQUEST['searchtaxon'] : '';
$action = array_key_exists('submitaction', $_REQUEST) ? $_REQUEST['submitaction'] : '';

$isEditor = 0;
if($IS_ADMIN || array_key_exists('RareSppAdmin',$USER_RIGHTS)){
	$isEditor = 1;
}

$rsManager = new OccurrenceProtectedSpecies($isEditor?'write':'readonly');

if($isEditor){
	if($action == 'addspecies'){
		$rsManager->addSpecies($_POST['tidtoadd']);
	}
	elseif($action == 'deletespecies'){
		$rsManager->deleteSpecies($_REQUEST['tidtodel']);
	}
}
$rsManager->setTaxonFilter($searchTaxon);
$rsArr = $rsManager->getProtectedSpeciesList();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title>Rare, Threatened, Sensitive Species</title>
	<link href="<?php echo $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script>
		$(document).ready(function() {
			$("#speciestoadd").autocomplete({ source: "rpc/speciessuggest.php" },{ minLength: 3, autoFocus: true });
			$("#searchtaxon").autocomplete({ source: "rpc/speciessuggest.php" },{ minLength: 3 });
		});

		function toggle(target){
		  	var divs = document.getElementsByTagName("div");
		  	for (var i = 0; i < divs.length; i++) {
		  	var divObj = divs[i];
				if(divObj.className == target){
					if(divObj.style.display=="none"){
						divObj.style.display="block";
					}
				 	else {
				 		divObj.style.display="none";
				 	}
				}
			}

		  	var spans = document.getElementsByTagName("span");
		  	for (var h = 0; h < spans.length; h++) {
		  	var spanObj = spans[h];
				if(spanObj.className == target){
					if(spanObj.style.display=="none"){
						spanObj.style.display="inline";
					}
				 	else {
				 		spanObj.style.display="none";
				 	}
				}
			}
		}

		function submitAddSpecies(f){
			var sciName = f.speciestoadd.value;
			if(sciName == ""){
				alert("Enter the scientific name of species you wish to add");
				return false;
			}

			$.ajax({
				type: "POST",
				url: "rpc/gettid.php",
				dataType: "json",
				data: { sciname: sciName }
			}).done(function( data ) {
				f.tidtoadd.value = data;
				f.submit();
			}).fail(function(jqXHR){
				alert("ERROR: Scientific name does not exist in database. Did you spell it correctly? If so, it may have to be added to taxa table.");
			});
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = (isset($collections_misc_rarespeciesMenu)?$collections_misc_rarespeciesMenu:true);
include($SERVER_ROOT.'/includes/header.php');
if(isset($collections_misc_rarespeciesCrumbs)){
	echo "<div class='navpath'>";
	echo "<a href='../index.php'>Home</a> &gt;&gt; ";
	echo $collections_misc_rarespeciesCrumbs." &gt;&gt;";
	echo " <b>Sensitive Species for Masking Locality Details</b>";
	echo "</div>";
}
?>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($isEditor){
		?>
		<div style="float:right;cursor:pointer;" onclick="toggle('editobj');" title="Toggle Editing Functions">
			<img style="border:0px;" src="../../images/edit.png" />
		</div>
		<?php
	}
	?>
	<h1>Protected Species</h1>
	<div style="float:right;">
		<fieldset style="margin:0px 15px;padding:10px">
			<legend><b>Filter</b></legend>
			<form name="searchform" action="protectedspecies.php" method="post">
				<div style="margin:3px">
					Taxon Search:
					<input id="searchtaxon" name="searchtaxon" type="text" value="<?php echo htmlspecialchars($searchTaxon, HTML_SPECIAL_CHARS_FLAGS); ?>" />
				</div>
				<div style="margin:3px">
					<input name="submitaction" type="submit" value="Search" />
				</div>
			</form>
		</fieldset>
	</div>
	<div style='margin:15px;'>
		Species in the list below have protective status with specific locality details below county withheld (e.g. decimal lat/long).
		Rare, threatened, or sensitive status are the typical causes for protection though species that are cherished by collectors or wild harvesters may also appear on the list.
	</div>
	<div>
		<?php
		$occurCnt = $rsManager->getSpecimenCnt();
		if($occurCnt) echo '<div style="margin:0px 40px 0px 20px;float:left">Occurrences protected: '.number_format($occurCnt).'</div>';
		if($isEditor){
			if($action == 'checkstats'){
				echo '<div>Number of specimens affected: '.$rsManager->protectGlobalSpecies().'</div>';
			}
			else{
				echo '<div><a href="protectedspecies.php?submitaction=checkstats"><button style="font-size:70%">Verify protections</button></a></div>';
			}
		}
		?>
	</div>
	<div style="clear:both">
		<fieldset style="padding:15px;margin:15px">
			<legend><b>Global Protections</b></legend>
			<?php
			if($isEditor){
				?>
				<div class="editobj" style="display:none;width:400px;">
					<form name="addspeciesform" action='protectedspecies.php' method='post'>
						<fieldset style='margin:5px;background-color:#FFFFCC;'>
							<legend><b>Add Species to List</b></legend>
							<div style="margin:3px;">
								Scientific Name:
								<input type="text" id="speciestoadd" name="speciestoadd" style="width:300px" />
								<input type="hidden" id="tidtoadd" name="tidtoadd" value="" />
							</div>
							<div style="margin:3px;">
								<input type="hidden" name="submitaction" value="addspecies" />
								<input type="button" value="Add Species" onclick="submitAddSpecies(this.form)" />
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			if($rsArr){
				foreach($rsArr as $family => $speciesArr){
					?>
					<h3><?php echo $family; ?></h3>
					<div style='margin-left:20px;'>
						<?php
						foreach($speciesArr as $tid => $nameArr){
							echo '<div id="tid-'.$tid.'"><a href="../../taxa/index.php?taxon='.$tid.'" target="_blank"><i>'.$nameArr['sciname'].'</i> '.$nameArr['author'].'</a> ';
							if($isEditor){
								?>
								<span class="editobj" style="display:none;">
									<a href="protectedspecies.php?submitaction=deletespecies&tidtodel=<?php echo $tid;?>">
										<img src="../../images/del.png" style="width:13px;border:0px;" title="remove species from list" />
									</a>
								</span>
								<?php
							}
							echo "</div>";
						}
						?>
					</div>
					<?php
				}
			}
			else{
				?>
				<div style="margin:20px;font-weight:bold;font-size:120%;">
					No species were returned marked for global protection.
				</div>
				<?php
			}
			?>
		</fieldset>
		<fieldset style="padding:15px;margin:15px">
			<legend><b>State/Province Level Protections</b></legend>
			<?php
			$stateList = $rsManager->getStateList();
			$emptyList = true;
			foreach($stateList as $clid => $stateArr){
				if($isEditor || $stateArr['access'] == 'public'){
					echo '<div>';
					echo '<a href="../../checklists/checklist.php?clid='.$clid.'">';
					echo $stateArr['locality'].': '.$stateArr['name'];
					echo '</a>';
					if($stateArr['access'] == 'private') echo ' (private)';
					echo '</div>';
					$emptyList = false;
				}
			}
			if($emptyList){
				?>
				<div style="margin:20px;font-weight:bold;font-size:120%;">
					 No checklists returned
				</div>
				<?php
			}
			?>
		</fieldset>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php')
?>
</body>
</html>