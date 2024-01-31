<!DOCTYPE html>
<?php
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;

include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceProtectedSpecies.php');
include_once($SERVER_ROOT.'/content/lang/collections/misc/protectedspecies.' . $LANG_TAG . '.php');
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
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title>Rare, Threatened, Sensitive Species</title>
	<link href="<?php echo htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS); ?>/jquery-ui.css" type="text/css" rel="stylesheet">
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
			<?php echo $LANG['EDIT'] ?> <img style="width:1.5em;border:0px;" src="../../images/edit.png" alt="pencil icon depicting edit capability" />
		</div>
		<?php
	}
	?>
	<h1>Protected Species</h1>
	<div style="float:right;">
		<form name="searchform" action="protectedspecies.php" method="post">
			<fieldset style="margin:0px 15px;padding:10px">
				<legend><b>Filter</b></legend>
				<div style="margin:3px">
					<label for="searchtaxon"><?php echo $LANG['TAXON_SEARCH'] ?>:</label>
					<input id="searchtaxon" name="searchtaxon" type="text" value="<?= htmlspecialchars($searchTaxon, HTML_SPECIAL_CHARS_FLAGS) ?>" />
				</div>
				<div style="margin:3px">
					<input name="submitaction" type="submit" value="Search" />
				</div>
			</fieldset>
		</form>
	</div>
	<div class="bottom-breathing-room">
		Species in the list below have protective status with specific locality details below county withheld (e.g. decimal lat/long).
		Rare, threatened, or sensitive status are the typical causes for protection though species that are cherished by collectors or wild harvesters may also appear on the list.
	</div>
	<div>
		<?php
		$occurCnt = $rsManager->getSpecimenCnt();
		if($occurCnt) echo '<div class="bottom-breathing-room">Occurrences protected: '.number_format($occurCnt).'</div>';
		if($isEditor){
			if($action == 'checkstats'){
				echo '<div>Number of specimens affected: '.$rsManager->protectGlobalSpecies().'</div>';
			}
			else{
				echo "<div><a href=\"protectedspecies.php?submitaction=checkstats\">" . $LANG['VERIFY_PROTECTIONS'] . "</a></div>";
			}
		}
		?>
	</div>
	<div style="clear:both">
		<section class="fieldset-like">
			<h1><span>Global Protections</span></h1>
			<br/>
			<?php
			if($isEditor){
				?>
				<div class="editobj" style="display:none;width:400px;margin-bottom:20px">
					<form name="addspeciesform" action='protectedspecies.php' method='post' >
						<fieldset style='margin:5px'>
							<legend><b>Add Taxon to List</b></legend>
							<div style="margin:3px;">
								Scientific Name:
								<input type="text" id="speciestoadd" name="speciestoadd" style="width:300px" />
								<input type="hidden" id="tidtoadd" name="tidtoadd" value="" />
							</div>
							<div style="margin:3px;">
								<input type="hidden" name="submitaction" value="addspecies" />
								<button value="Add Species" onclick="submitAddSpecies(this.form)" >Add Species</button>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			if($rsArr){
				foreach($rsArr as $family => $speciesArr){
					?>
					<h3>
						<span>
							<?php echo $family; ?>
						</span>
					</h3>
					<div style='margin-left:20px; margin-bottom:20px;'>
						<?php
						foreach($speciesArr as $tid => $nameArr){
							echo '<div id="tid-'.$tid.'">';
							echo '<a href="../../taxa/index.php?taxon=' . htmlspecialchars($tid, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">';
							echo '<i>' . htmlspecialchars($nameArr['sciname'], HTML_SPECIAL_CHARS_FLAGS) . '</i> ';
							echo htmlspecialchars($nameArr['author'], HTML_SPECIAL_CHARS_FLAGS) . '</a> ';
							if($isEditor){
								?>
								<span class="editobj" style="display:none;">
									<a href="protectedspecies.php?submitaction=deletespecies&tidtodel=<?php echo htmlspecialchars($tid, HTML_SPECIAL_CHARS_FLAGS);?>">
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
		</section>
		<section class="fieldset-like">
			<h1><span>State/Province Level Protections</span></h1>
			<?php
			$stateList = $rsManager->getStateList();
			$emptyList = true;
			foreach($stateList as $clid => $stateArr){
				if($isEditor || $stateArr['access'] == 'public'){
					echo '<div>';
					echo '<a href="../../checklists/checklist.php?clid=' . htmlspecialchars($clid, HTML_SPECIAL_CHARS_FLAGS) . '">';
					echo $stateArr['locality'].': '.$stateArr['name'];
					echo '</a>';
					if(strpos($stateArr['access'] ?? '', 'private') !== false) echo ' (private)';
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
		</section>
	</div>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php')
?>
</body>
</html>