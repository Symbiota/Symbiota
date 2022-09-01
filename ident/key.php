<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/KeyDataManager.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/ident/key.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/ident/key.en.php');
else include_once($SERVER_ROOT.'/content/lang/ident/key.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset='.$CHARSET);

$isEditor = false;
if($IS_ADMIN || array_key_exists('KeyEditor',$USER_RIGHTS)){
	$isEditor = true;
}

$attrsValues = Array();

$clValue = array_key_exists('cl',$_REQUEST)?$_REQUEST['cl']:'';
if(!$clValue && array_key_exists('clid',$_REQUEST)) $clValue = $_REQUEST['clid'];
$dynClid = array_key_exists('dynclid',$_REQUEST)?$_REQUEST['dynclid']:0;
$taxonValue = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']:'';
$rv = array_key_exists('rv',$_REQUEST)?$_REQUEST['rv']:'';
$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']:'';
$langValue = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']:'';
$sortBy = array_key_exists('sortby',$_REQUEST)?$_REQUEST['sortby']:0;
$displayCommon = array_key_exists('displaycommon',$_REQUEST)?$_REQUEST['displaycommon']:0;
$displayImages = array_key_exists('displayimages',$_REQUEST)?$_REQUEST['displayimages']:0;
$action = array_key_exists('submitbutton',$_REQUEST)?$_REQUEST['submitbutton']:'';
if(!$action && array_key_exists('attr',$_REQUEST) && is_array($_REQUEST['attr'])){
	$attrsValues = $_REQUEST['attr'];	//Array of: cid + '-' + cs (ie: 2-3)
}

//Sanitation
if(!is_numeric($dynClid)) $dynClid = 0;
$taxonValue = filter_var($taxonValue,FILTER_SANITIZE_STRING);
if(!is_numeric($rv)) $rv = '';
if(!is_numeric($pid)) $pid = 0;
$langValue = 'English';
if(!is_numeric($sortBy)) $sortBy = 0;
if(!is_numeric($displayCommon)) $displayCommon = 0;
if(!is_numeric($displayImages)) $displayImages = 0;

$dataManager = new KeyDataManager();

//if(!$langValue) $langValue = $defaultLang;
if($sortBy) $dataManager->setSortBy($sortBy);
if($displayCommon) $dataManager->setDisplayCommon(1);
if($displayImages) $dataManager->setDisplayImages(true);
$dataManager->setLanguage($langValue);
if($pid) $dataManager->setProject($pid);
if($dynClid) $dataManager->setDynClid($dynClid);
$clid = $dataManager->setClValue($clValue);
if($taxonValue) $dataManager->setTaxonFilter($taxonValue);
if($attrsValues) $dataManager->setAttrs($attrsValues);
if($rv) $dataManager->setRelevanceValue($rv);

$taxa = $dataManager->getTaxaArr();
$chars = $dataManager->getCharArr();

//Harevest and remove language list from $chars
$languages = Array();
if($chars){
	$languages = $chars['Languages'];
	unset($chars['Languages']);
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.$LANG['WEBKEY'].preg_replace('/\<[^\>]+\>/','',$dataManager->getClName()); ?></title>
	<?php
	$activateJQuery = true;
	if(file_exists($SERVER_ROOT.'/includes/head.php')){
		include_once($SERVER_ROOT.'/includes/head.php');
	}
	else{
		echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="../css/alerts.css" type="text/css" rel="stylesheet" />
	<script src="../js/jquery.js" type="text/javascript"></script>
	<script src="../js/jquery-ui.js" type="text/javascript"></script>
	<script src="../js/symb/ident.key.js" type="text/javascript"></script>
	<script type="text/javascript">
		$( function() {
			$( "#key-chars" ).resizable();
		} );

		function setLang(list){
			var langName = list.options[list.selectedIndex].value;
			var objs = document.getElementsByTagName("span");
			for (i = 0; i < objs.length; i++) {
				var obj = objs[i];
				if(obj.lang == langName) obj.style.display="";
				else if(obj.lang != "") obj.style.display="none";
			}
		}

		function resetForm(f) {
			var inputs = f.getElementsByTagName('input');
			for (var i = 0; i<inputs.length; i++) {
				switch (inputs[i].type) {
					case 'text':
						inputs[i].value = '';
						break;
					case 'radio':
					case 'checkbox':
						inputs[i].checked = false;
				}
			}

			var selects = f.getElementsByTagName('select');
			for (var i = 0; i<selects.length; i++)
				selects[i].selectedIndex = 0;
			f.submit();
			return false;
		}

		function openEditorPopup(tid){
			var url = 'tools/editor.php?tid='+tid;
			window.open(url,'keyeditor','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,width=1100,height=600,left=20,top=20');
		}

		function openGlossaryPopup(glossid){
			var urlStr = "../glossary/individual.php?glossid="+glossid;
			glossWindow = window.open(urlStr,'glosspopup','toolbar=0,status=1,scrollbars=1,width=900,height=450,left=20,top=20');
			if(glossWindow.opener == null) glossWindow.opener = self;
			return false;
		}
	</script>
	<style type="text/css">
		#title-div { font-weight: bold; font-size: 120% }
		#char-div {  }
		#key-chars { display: inline-block; float: right; max-width: 35%; overflow: hidden; }
		.infoAnchor img{ width: 12px; border: 0px; }
		fieldset { padding: 5px 10px; }
		legend { font-weight:bold }
		.editimg { width: 13px }
		#key-div {  }
		.char-heading { font-weight: bold; margin-top:1em; font-size:125%; }
		#key-taxa { vertical-align: top; }
		.charHeading {}
		.headingname { font-weight: bold; margin-top: 1em; font-size: 125%; }
		.cs-div { display: flex; }
		.cs-div input { margin-right: 5px }
		.characterStateName {}
		.dynam {}
		.dynamlang{ margin-top: 0.5em; font-weight: bold; }
		.dynamopt{}
		.editimg{ margin-left:10px; }
		.family-div{ font-weight: bold; margin-top: 10px; font-size: 1.3em; }
		.vern-span{  }
		<?php
		if($displayImages){
			?>
			.taxon-div{ display: inline-block; flex-flow: row wrap; }
			.img-div{ display: inline-block; position: relative; margin: 3px; width: 160px; height: 160px; border: 1px solid gray; overflow: hidden; }
			.img-div img{ position: absolute; max-height: 165px; max-width: 165px; top: -9999px; bottom: -9999px; left: -9999px; right: -9999px; margin: auto; }
			.img-div div{ text-align: center; margin-top: 25%; }
			.sciname-div{ text-align: center }
			<?php
		}
		else{
			?>
			.taxon-div{ flex-flow: row wrap; }
			.img-div{}
			.sciname-div{ margin-left: 10px; }
			<?php
		}
		?>
	</style>
</head>
<body>
<?php
$displayLeftMenu = (isset($ident_keyMenu)?$ident_keyMenu:true);
include($SERVER_ROOT.'/includes/header.php');
echo '<div class="navpath">';
echo '<a href="../index.php">'.$LANG['HOME'].'</a> &gt;&gt; ';
if($dynClid){
	if($dataManager->getClType() == 'Specimen Checklist'){
		echo '<a href="'.$CLIENT_ROOT.'/collections/list.php?tabindex=0">';
		echo 'Occurrence Checklist';
		echo '</a> &gt;&gt; ';
	}
}
elseif($clid){
	echo '<a href="'.$CLIENT_ROOT.'/checklists/checklist.php?clid='.$clid.'&pid='.$pid.'">';
	echo 'Checklist: '.$dataManager->getClName();
	echo '</a> &gt;&gt; ';
}
elseif($pid){
	echo '<a href="'.$CLIENT_ROOT.'/projects/index.php?pid='.$pid.'">';
	echo 'Project Checklists';
	echo '</a> &gt;&gt; ';
}
echo '<a href="key-v1.php?clid='.$clid.'&pid='.$pid.'&taxon=All+Species" alt="Traditional key">Previous version of Key</a> &gt;&gt; ';
echo '<b>Identification Key (new version): '.$dataManager->getClName().'</b>';
echo '</div>';
?>
<div id="innertext">
	<?php
	if($clid || $dynClid){
		?>
		<div id="char-div">
			<fieldset id="key-chars">
				<legend>Filter/Display Options</legend>
				<form name="keyform" id="keyform" action="key.php" method="post">
					<div>
						<div style="float:right"><button type="button" onclick="resetForm(this.form)">Reset</button></div>
						<div><span><?php echo (isset($LANG['TAXON_SEARCH'])?$LANG['TAXON_SEARCH']:'Family/Genus Filter');?>:</span></div>
						<select name="taxon" onchange="this.form.submit();">
							<?php
							echo '<option value="All Species">'.$LANG['SELECTTAX'].'</option>';
							$selectList = $dataManager->getTaxaFilterList();
							foreach($selectList as $value){
								$selectStr = ($value==$taxonValue?'SELECTED':'');
								echo '<option '.$selectStr.'>'.$value.'</option>';
							}
							?>
						</select>
					</div>
					<hr size="2" />
					<?php
					//echo "<div style=''>Relevance value: <input name='rv' type='text' size='3' title='Only characters with > ".($rv*100)."% relevance to the active spp. list will be displayed.' value='".$dataManager->getRelevanceValue()."'></div>";
					//List char Data with selected states checked
					if(count($languages) > 1){
						echo '<div id="langlist" style="margin:0.5em;">Languages: <select name="lang" onchange="setLang(this);">';
						foreach($languages as $l){
							echo '<option value="'.$l.'" '.($defaultLang == $l?'SELECTED':'').'>'.$l.'</option>';
						}
						echo '</select></div>';
					}
					?>
					<div style="margin:5px">
						<?php echo (isset($LANG['SORT'])?$LANG['SORT']:'Sort by').': '; ?>
						<select name="sortby" onchange="this.form.submit();">
							<?php
							echo '<option value="0">'.(isset($LANG['SORT_SCINAME_FAMILY'])?$LANG['SORT_SCINAME_FAMILY']:'Family/Scientific Name').'</option>';
							echo '<option value="1" '.($sortBy?'SELECTED':'').'>'.(isset($LANG['SORT_SCINAME'])?$LANG['SORT_SCINAME']:'Scientific Name').'</option>';
							?>
						</select>
					</div>
					<?php
					if(!isset($DISPLAY_COMMON_NAMES) || $DISPLAY_COMMON_NAMES){
						?>
						<div style="margin:5px">
							<input name="displaycommon" type="checkbox" value="1" onchange="this.form.submit();" <?php if($displayCommon) echo 'checked'; ?> />
							<?php echo (isset($LANG['DISPLAY_COMMON'])?$LANG['DISPLAY_COMMON']:'Display Common Names'); ?>
						</div>
						<?php
					}
					?>
					<div style="margin:5px">
						<input name="displayimages" type="checkbox" value="1" onchange="this.form.submit();" <?php if($displayImages) echo 'checked'; ?> />
						<?php echo (isset($LANG['DISPLAY_IMAGES'])?$LANG['DISPLAY_IMAGES']:'Display images').': '; ?>
					</div>
					<?php
					if($chars){
						//echo "<div id='showall' class='dynamControl' style='display:none'><a href='#' onclick='toggleAll();'>Show All Characters</a></div>\n";
						//echo "<div class='dynamControl' style='display:block'><a href='#' onclick='toggleAll();'>Hide Advanced Characters</a></div>\n";
						foreach($chars as $key => $htmlStrings){
							echo $htmlStrings."\n";
						}
					}
					?>
					<div>
						<input type="hidden" id="cl" name="clid" value="<?php echo $clid; ?>" />
						<input type="hidden" id="dynclid" name="dynclid" value="<?php echo $dynClid; ?>" />
						<input type="hidden" id="pid" name="pid" value="<?php echo $pid; ?>" />
						<input type="hidden" id="rv" name="rv" value="<?php echo $dataManager->getRelevanceValue(); ?>" />
					</div>
				</form>
			</fieldset>
		</div>
		<?php
		if($clid && $isEditor){
			?>
			<div style="float:right;margin:15px;" title="Edit Character Matrix">
				<a href="tools/matrixeditor.php?clid=<?php echo $clid; ?>"><img class="editimg" src="../images/edit.png" /><span style="font-size:70%;">CM</span></a>
			</div>
			<?php
		}
		?>
		<div id="title-div">
			<?php
			if($FLORA_MOD_IS_ACTIVE) echo '<a href="../checklists/checklist.php?clid='.$clid.'&dynclid='.$dynClid.'&pid='.$pid.'">';
			echo $dataManager->getClName().' ';
			if($FLORA_MOD_IS_ACTIVE) echo '</a>';
			?>
		</div>
		<div id="key-taxa">
			<?php
			if(!$dynClid && $dataManager->getClAuthors()) echo '<div>'.$dataManager->getClAuthors().'</div>';
			$count = $dataManager->getTaxaCount();
			if($count > 0) echo '<div style="margin-bottom:15px;">'.$LANG['SPECCOUNT'].': '.$count.'</div>';
			else echo '<div>'.$LANG['NOMATCH'].'</div>';
			$clType =$dataManager->getClType();
			ksort($taxa);
			foreach($taxa as $family => $taxaArr){
				if($family) echo '<div class="family-div">'.$family.'</div>';
				//natcasesort($taxaArr);
				foreach($taxaArr as $tid => $taxonArr){
					echo '<div class="taxon-div">';
					if($displayImages){
						echo '<div class="img-div">';
						echo '<a href="../taxa/index.php?taxon='.$tid."&clid=".($clType=="static"?$clid:"").'" target="_blank">';
						if(isset($taxonArr['i'])) echo '<img src="'.$taxonArr['i'].'" />';
						else echo '<div>Image<br/>Not<br/>Available</div>';
						echo '</a>';
						echo '</div>';
					}
					echo '<div class="sciname-div">';
					echo '<a href="../taxa/index.php?taxon='.$tid."&clid=".($clType=="static"?$clid:"").'" target="_blank"><i>'.$taxonArr['s'].'</i></a>';
					if($displayCommon) echo ($displayImages?'<br/>':(isset($taxonArr['v'])?' - ':'')).'<span class="vern-span">'.(isset($taxonArr['v'])?$taxonArr['v']:'&nbsp;').'</span>';
					if($isEditor && !$displayImages){
						echo '<a href="#" onclick="openEditorPopup('.$tid.')">';
						echo '<img class="editimg" src="../images/edit.png" title="'.$LANG['EDITMORP'].'" />';
						echo '</a>';
					}
					echo '</div>';
					echo '</div>';
				}
			}
			?>
		</div>
		<?php
	}
	else echo '<div style="margin: 40px 20px; font-weight:bold">Error: checklist identifier is NULL</div>';
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
<script src="../js/alerts.js" type="text/javascript"></script>
<script>
	let alerts = [{'alertMsg': 'Looking for the previous version of Key? You can still access it above via the breadcrumb links located just below top menu bar.'}];
	handleAlerts(alerts,"keyAlert",true);
</script>
</html>