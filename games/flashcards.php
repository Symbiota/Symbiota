<?php
//error_reporting(E_ALL);
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/GamesManager.php');

header('Content-Type: text/html; charset='.$CHARSET);

$clid = array_key_exists('clid', $_REQUEST) ? filter_var($_REQUEST['clid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$dynClid = array_key_exists('dynclid', $_REQUEST) ? filter_var($_REQUEST['dynclid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$taxonFilter = array_key_exists('taxonfilter', $_REQUEST) ? $_REQUEST['taxonfilter'] : '';
$showCommon = array_key_exists('showcommon', $_REQUEST) ? filter_var($_REQUEST['showcommon'], FILTER_SANITIZE_NUMBER_INT) : 0;
$lang = array_key_exists('lang', $_REQUEST) ? $_REQUEST['lang'] : $DEFAULT_LANG;

$fcManager = new GamesManager();
$fcManager->setClid($clid);
$fcManager->setDynClid($dynClid);
$fcManager->setTaxonFilter($taxonFilter);
$fcManager->setShowCommon($showCommon);
$fcManager->setLang($lang);

$sciArr = array();
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Flash Cards</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<style>
		.flashcard-img {
			height: 97%;
			max-width: 450px;
		}

        .flashcard-nav {
			justify-content: space-between;
			display: flex;
			width: 450px;
		}
    </style>
	<script type="text/javascript">
		var imageArr = new Array();
		var sciNameArr = new Array();
		var toBeIdentified = new Array();
		var activeIndex = 0;
		var activeImageArr = new Array();
		var activeImageIndex = 0;
		var totalCorrect = 0;
		var totalTried = 0;
		var firstTry = true;

		function init(){
			<?php
				$imagesArr = $fcManager->getFlashcardImages();
				if($imagesArr){
					foreach($imagesArr as $imgArr){
						if(array_key_exists('url',$imgArr)){
							$scinameStr = $imgArr['sciname'];
							if($showCommon && array_key_exists('vern',$imgArr)){
								$scinameStr .= ' ('.$imgArr['vern'].')';
							}
							$sciArr[$imgArr['tid']] = $scinameStr;
							echo 'sciNameArr.push('.$imgArr['tid'].');'."\n";
							echo 'imageArr['.$imgArr['tid'].'] = new Array("'.implode('","',$imgArr['url']).'");'."\n";
						}
					}
				}
			?>
			reset();
		}

		function reset(){
			toBeIdentified = new Array();
			if(sciNameArr.length == 0){
				alert("Sorry, there are no images for the species list you have defined");
			}
			else{
				toBeIdentified = sciNameArr.slice();
				document.getElementById("numtotal").innerHTML = sciNameArr.length;
				document.getElementById("numcomplete").innerHTML = 0;
				document.getElementById("numcorrect").innerHTML = 0;
				insertNewImage();
			}
		}

		function insertNewImage(){
			activeIndex = toBeIdentified.shift();
			activeImageArr = imageArr[activeIndex];
			document.getElementById("activeimage").src = activeImageArr[0];
			document.getElementById("imageanchor").href = activeImageArr[0];
			activeImageIndex = 0;
			document.getElementById("imageindex").innerHTML = 1;
			document.getElementById("imagecount").innerHTML = activeImageArr.length;
		}

		function nextImage(){
			activeImageIndex++;
			if(activeImageIndex >= activeImageArr.length){
				activeImageIndex = 0;
			}
			document.getElementById("activeimage").src = activeImageArr[activeImageIndex];
			document.getElementById("imageanchor").href = activeImageArr[activeImageIndex];
			document.getElementById("imageindex").innerHTML = activeImageIndex + 1;
			document.getElementById("imagecount").innerHTML = activeImageArr.length;
			document.getElementById("scinameselect").options[0].selected = "1";
		}

		function checkId(){
			let idIndexSelected = document.getElementById('scinameselect').value;
			if(idIndexSelected > 0){
				if(idIndexSelected == activeIndex){
					totalTried++;
					alert("Correct! Try another");
					document.getElementById("numcomplete").innerHTML = sciNameArr.length - toBeIdentified.length;
					if(firstTry){
						totalCorrect++;
						document.getElementById("numcorrect").innerHTML = totalCorrect;
					}
					firstTry = true;
					if(toBeIdentified.length > 0){
						insertNewImage();
						document.getElementById("scinameselect").value = "-1";
					}
					else{
						alert("Nothing left to identify. Hit reset to start again.");
					}
				}
				else{
					alert("Sorry, incorrect. Try Again.");
					if(firstTry) totalTried++;
					firstTry = false;
				}
			}
		}

		function tellMe(){
			var wWidth = 900;
			if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
			if(wWidth > 1200) wWidth = 1200;
			newWindow = window.open("../taxa/index.php?taxon="+activeIndex,"activetaxon",'scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
			if (newWindow.opener == null) newWindow.opener = self;
			firstTry = false;
		}
	</script>
</head>

<body onload="init();">
<?php
	$displayLeftMenu = (isset($checklists_flashcardsMenu)?$checklists_flashcardsMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	echo '<div class="navpath">';
	echo '<a href="../index.php">Home</a> &gt;&gt; ';
	if(isset($checklists_flashcardsCrumbs) && $checklists_flashcardsCrumbs){
		echo $checklists_flashcardsCrumbs;
	}
	else{
		echo '<a href="../checklists/checklist.php?clid=' . $clid . '&dynclid=' . $dynClid . '">';
		echo $fcManager->getClName();
		echo '</a> &gt;&gt; ';
	}
	echo ' <b>Flashcard Game</b>';
	echo '</div>';
	?>
	<!-- This is inner text! -->
	<div id='innertext'>
		<h1 class="page-heading screen-reader-only">Flash Cards</h1>
		<div class="games-content">
			<div>
				<a id="imageanchor" href="" target="_blank">
					<img src="#" id="activeimage" class="flashcard-img" alt="Image to guess"/>
				</a>
			</div>
			<div class="games-body">
					<div>
						<div class="flashcard-nav">
							<a href="#" onclick="insertNewImage(); return false;"><img src="../images/skipthisone.png" title="Skip to Next Species" aria-label="Skip to Next Species" ></a>
							<span>Image <span id="imageindex">1</span> of <span id="imagecount">?</span></span>
							<a href="#" onclick="nextImage(); return false;"><img id="rightarrow" src="../images/rightarrow.png" title="Show Next Image" aria-label="Show Next Image" ></a>
						</div>
					</div>
				<div class="games-body">
						<label for="scinameselect">Name of Above Organism:</label>
						<select id="scinameselect">
							<option value="0">-------------------------</option>
							<?php
							asort($sciArr);
							foreach($sciArr as $t => $s){
								echo "<option value='".$t."'>".$s."</option>";
							}
							?>
						</select>
					<div>
						<button type="submit" onclick="checkId()">Check Name</button>
					</div>
				</div>
				<div>
					<div>
						<b><span id="numcomplete">0</span></b> out of <b><span id="numtotal">0</span></b> Species Identified
					</div>
					<div>
						<b><span id="numcorrect">0</span></b> Identified Correctly on First Try
					</div>
				</div>
				<div style="cursor:pointer;color:green;" onclick="tellMe()"><b>Tell Me What It Is!</b></div>
				<div>
					<form id="taxonfilterform" name="taxonfilterform" action="flashcards.php" method="post">
						<fieldset class="games-body">
							<legend>Options</legend>
							<input type="hidden" name="clid" value="<?= $clid ?>" />
							<input type="hidden" name="lang" value="<?= htmlspecialchars($lang, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) ?>" />
							<div>
								<select name="taxonfilter" aria-label="Filter Quiz by Taxonomic Group">
									<option value="0">All Taxa</option>
									<option value="0">---------------------------</option>
									<?php
									$fcManager->echoFlashcardTaxonFilterList();
									?>
								</select>
							</div>
							<div>
								<?php
								//Display Common Names: 0 = false, 1 = true
								if($DISPLAY_COMMON_NAMES){
									echo '<input id="showcommon" name="showcommon" type="checkbox" value="1" '.($showCommon?"checked":"").' /> <label for="showcommon">Display Common Names</label>'."\n";
								}
								?>
							</div>
							<button type="submit" >Apply</button>
						</fieldset>
					</form>
				</div>
				<div style="color:red;font-weight: bold"><a href="#" onclick="reset()">Reset Game</a></div>
			</div>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
