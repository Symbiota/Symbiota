<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/KeyMatrixEditor.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ident/tools/matrixeditor.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$clid = $_REQUEST['clid'];
$taxonFilter = array_key_exists("tf",$_REQUEST)?$_REQUEST["tf"]:'';
$generaOnly = array_key_exists("generaonly",$_POST)?$_POST["generaonly"]:0;
$cidValue = array_key_exists("cid",$_REQUEST)?$_REQUEST["cid"]:'';
$removeAttrs = array_key_exists("r",$_REQUEST)?$_REQUEST["r"]:"";
$addAttrs = array_key_exists("a",$_REQUEST)?$_REQUEST["a"]:"";
$langValue = array_key_exists("lang",$_REQUEST)?$_REQUEST["lang"]:"";

if(!is_numeric($clid)) $clid = 0;
if(!is_numeric($taxonFilter)) $taxonFilter = 0;
if(!is_numeric($cidValue)) $cidValue = 0;

$muManager = new KeyMatrixEditor();
$muManager->setClid($clid);
if($langValue) $muManager->setLang($langValue);
if($cidValue) $muManager->setCid($cidValue);

$isEditor = false;
if($IS_ADMIN || array_key_exists("KeyEditor",$USER_RIGHTS) || array_key_exists("KeyAdmin",$USER_RIGHTS)){
	$isEditor = true;
}

if($isEditor){
	if($removeAttrs || $addAttrs){
		$muManager->processAttributes($removeAttrs,$addAttrs);
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Character Mass Updater</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script>
		var addAttrArr = [];
		var removeAttrArr = [];
		var dataChanged = false;

		window.onbeforeunload = verifyClose();

		function verifyClose() {
			if(dataChanged == true) {
				return "You will lose any unsaved data if you don't first save your changes!";
			}
		}

		function attrChanged(cbElem,target){
			if(cbElem.checked == true){
				if(removeAttrArr.indexOf(target) > -1) removeAttrArr.splice(removeAttrArr.indexOf(target),1);
				else if(addAttrArr.indexOf(target) == -1) addAttrArr.push(target);
			}
			else{
				if(addAttrArr.indexOf(target) > -1) addAttrArr.splice(addAttrArr.indexOf(target),1);
				else if(removeAttrArr.indexOf(target) == -1) removeAttrArr.push(target);
			}
		}

		function submitAttrs(){
			var sform = document.submitform;
			var a;
			var r;
			var submitForm = false;

			if(addAttrArr.length > 0){
				for(a in addAttrArr){
					var addValue = addAttrArr[a];
					if(addValue.length > 1){
						var newInput = document.createElement("input");
						newInput.setAttribute("type","hidden");
						newInput.setAttribute("name","a[]");
						newInput.setAttribute("value",addValue);
						sform.appendChild(newInput);
					}
				}
				submitForm = true;
			}

			if(removeAttrArr.length > 0){
				for(r in removeAttrArr){
					var removeValue = removeAttrArr[r];
					if(removeValue.length > 1){
						var newInput = document.createElement("input");
						newInput.setAttribute("type","hidden");
						newInput.setAttribute("name","r[]");
						newInput.setAttribute("value",removeValue);
						sform.appendChild(newInput);
					}
				}
				submitForm = true;
			}
			if(submitForm) sform.submit();
			else alert("It doesn't appear that any edits have been made");
		}
	</script>
	<style type="text/css">
		table {
			text-align: left;
			position: relative;
		}
		th {
			position: sticky;
			top: 0;
		}
	</style>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class='navpath'>
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="../../checklists/checklist.php?clid=<?php echo $clid; ?>">Open Checklist</a> &gt;&gt;
	<a href="../key.php?clid=<?php echo $clid; ?>&taxon=All+Species">Open Key</a> &gt;&gt;
	<?php
	if($cidValue){
		?>
		<a href='matrixeditor.php?clid=<?php echo $clid.'&tf='.$taxonFilter.'&lang='.$langValue; ?>'>
			Return to Character List
		</a> &gt;&gt;
		<?php
	}
	?>
	<b>Matrix Editor</b>
</div>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($clid && $isEditor){
		if(!$cidValue){
			?>
			<form id="filterform" action="matrixeditor.php" method="post" onsubmit="return verifyFilterForm(this)">
				<fieldset>
		  			<div style="margin: 10px 0px;">Select character to edit</div>
		  			<div>
						<select name="tf" onchange="this.form.submit()">
				 			<option value="">All Taxa</option>
				 			<option value="">--------------------------</option>
					  		<?php
					  		$selectList = $muManager->getTaxaQueryList();
				  			foreach($selectList as $tid => $scinameValue){
				  				echo '<option value="'.$tid.'" '.($tid==$taxonFilter?"SELECTED":"").'>'.$scinameValue."</option>";
				  			}
					  		?>
						</select>
						<?php
						count($selectList);
						?>
					</div>
					<div style="margin: 10px 0px;">
						<input type="checkbox" name="generaonly" value="1" <?php if($generaOnly) echo "checked"; ?> />
						Exclude Species Rank
					</div>
			 		<?php
	 				$cList = $muManager->getCharList($taxonFilter);
					foreach($cList as $h => $charData){
						echo "<div style='margin-top:1em;font-size:125%;font-weight:bold;'>$h</div>\n";
						ksort($charData);
						foreach($charData as $cidKey => $charValue){
							echo '<div> <input name="cid" type="radio" value="'.$cidKey.'" onclick="this.form.submit()">'.$charValue.'</div>'."\n";
						}
					}
			 		?>
					<input type='hidden' name='clid' value='<?php echo $clid; ?>' />
					<input type="hidden" name="lang" value="<?php echo $langValue; ?>" />
			 	</fieldset>
			</form>
			<?php
		}
		else{
			$inheritStr = "&nbsp;<span title='State has been inherited from parent taxon'><b>(i)</b></span>";
			?>
			<div><?php echo $inheritStr; ?> = character state is inherited as true from a parent taxon (genus, family, etc)</div>
		 	<table class="styledtable" style="font-family:Arial;font-size:12px;">
				<?php
				$muManager->echoTaxaList($taxonFilter,$generaOnly);
				?>
			</table>
			<form name="submitform" action="matrixeditor.php" method="post">
				<input type='hidden' name='tf' value='<?php echo $taxonFilter; ?>' />
				<input type='hidden' name='cid' value='<?php echo $cidValue; ?>' />
				<input type='hidden' name='clid' value='<?php echo $clid; ?>' />
				<input type='hidden' name='lang' value='<?php echo $langValue; ?>' />
				<input type='hidden' name='generaonly' value='<?php echo $generaOnly; ?>' />
			</form>
			<?php
	 	}
	}
	else{
		echo "<h1>You appear not to have necessary premissions to edit character data.</h1>";
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>