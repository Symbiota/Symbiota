<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/reports/annotationmanager.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST["collid"];
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$datasetManager = new OccurrenceLabel();
$datasetManager->setCollid($collid);

$isEditor = 0;
$annoArr = array();
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
	$isEditor = 1;
}
elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
	$isEditor = 1;
}
if($isEditor){
	$annoArr = $datasetManager->getAnnoQueue();
}

// Add collection customization variables
if($collid && file_exists('../editor/includes/config/occurVarColl'.$collid.'.php')){
	//Specific to particular collection
	include('../editor/includes/config/occurVarColl'.$collid.'.php');
}
elseif(file_exists('../editor/includes/config/occurVarDefault.php')){
	//Specific to Default values for portal
	include('../editor/includes/config/occurVarDefault.php');
}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE; ?> Annotation Label Manager</title>
    <?php
      $activateJQuery = false;
      if(file_exists($SERVER_ROOT.'/includes/head.php')){
        include_once($SERVER_ROOT.'/includes/head.php');
      }
      else{
        echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
      }
    ?>
		<script type="text/javascript">
			function selectAll(cb){
				boxesChecked = true;
				if(!cb.checked){
					boxesChecked = false;
				}
				var dbElements = document.getElementsByName("detid[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					dbElement.checked = boxesChecked;
				}
			}

			function validateSelectForm(f){
				var dbElements = document.getElementsByName("detid[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					if(dbElement.checked) return true;
				}
			   	alert("Please select at least one annotation record!");
			  	return false;
			}

			function openIndPopup(occid){
				openPopup('../individual/index.php?occid=' + occid);
			}

			function openEditorPopup(occid){
				openPopup('../editor/occurrenceeditor.php?occid=' + occid);
			}

			function openPopup(urlStr){
				var wWidth = 900;
				if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
				if(wWidth > 1200) wWidth = 1200;
				newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
				if (newWindow.opener == null) newWindow.opener = self;
				return false;
			}

			function changeAnnoFormTarget(f, target){
				if(target == 'word'){
					f.action = 'defaultannotationsword.php';
					f.target = '_self';
				}
				else{
					//Print in browser
					f.action = 'defaultannotations.php';
					f.target = '_blank';
				}
			}
		</script>
	</head>
	<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<?php
		if(stripos(strtolower($datasetManager->getMetaDataTerm('colltype')), "observation") !== false){
			echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
		}
		else{
			echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
		}
		?>
		<b>Annotation Label Printing</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor){
			$reportsWritable = false;
			if(is_writable($SERVER_ROOT.'/temp/report')) $reportsWritable = true;
			if(!$reportsWritable){
				?>
				<div style="padding:5px;">
					<span style="color:red;">Please contact the site administrator to make temp/report folder writable in order to export to docx files.</span>
				</div>
				<?php
			}
			echo '<h2>'.$datasetManager->getCollName().'</h2>';
			?>
			<div>
				<?php
				if($annoArr){
					?>
					<form name="annoselectform" id="annoselectform" action="defaultannotations.php" method="post" onsubmit="return validateSelectForm(this);">
						<table class="styledtable" style="width:800px;">
							<tr>
								<th title="Select/Deselect all Specimens" style="width:30px;"><input name="" value="" type="checkbox" onclick="selectAll(this);" /></th>
								<th style="width:25px;text-align:center;">#</th>
								<th style="width:125px;text-align:center;"><?php echo (defined('RECORDEDBYLABEL')?RECORDEDBYLABEL:(isset($LANG['COLLECTOR'])?$LANG['COLLECTOR']:'Collector')); ?></th>
								<th style="width:300px;text-align:center;"><?php echo (defined('SCIENTIFICNAMELABEL')?SCIENTIFICNAMELABEL:(isset($LANG['SCINAME'])?$LANG['SCINAME']:'Scientific Name')); ?></th>
								<th style="width:400px;text-align:center;">Determination</th>
							</tr>
							<?php
							$trCnt = 0;
							foreach($annoArr as $detId => $recArr){
								$trCnt++;
								?>
								<tr <?php echo ($trCnt%2?'class="alt"':''); ?>>
									<td>
										<input type="checkbox" name="detid[]" value="<?php echo $detId; ?>" />
									</td>
									<td>
										<input type="text" name="q-<?php echo $detId; ?>" value="1" style="width:20px;border:inset;" />
									</td>
									<td>
										<a href="#" onclick="openIndPopup(<?php echo $recArr['occid']; ?>); return false;">
											<?php echo $recArr['collector']; ?>
										</a>
										<a href="#" onclick="openEditorPopup(<?php echo $recArr['occid']; ?>); return false;">
											<img src="../../images/edit.png" />
										</a>
									</td>
									<td>
										<?php echo $recArr['sciname']; ?>
									</td>
									<td>
										<?php echo $recArr['determination']; ?>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
						<fieldset style="margin-top:15px;">
							<legend><b>Annotation Printing</b></legend>
							<div>
								<div style="margin:4px;">
									<b>Header:</b>
									<input type="text" name="lheading" value="" style="width:450px" />
								</div>
								<div style="margin:4px;">
									<b>Footer:</b>
									<input type="text" name="lfooter" value="<?php echo $datasetManager->getAnnoCollName(); ?>" style="width:450px" />
								</div>
							</div>
							<div style="float:left">
								<div style="margin:4px;">
									<input type="checkbox" name="speciesauthors" value="1" onclick="" />
									<b>Print species authors for infraspecific taxa</b>
								</div>
								<div style="margin:4px;">
									<input type="checkbox" name="printcatnum" value="1" />
									<b>Print <?php echo (defined('CATALOGNUMBERLABEL')?CATALOGNUMBERLABEL:(isset($LANG['CAT_NUM'])?$LANG['CAT_NUM']:'Catalog Number')); ?>s</b>
								</div>
								<div style="margin:4px;">
									<input type="checkbox" name="clearqueue" value="1" onclick="" />
									<b>Remove selected annotations from queue</b>
								</div>
							</div>
							<div style="float:left;margin-left:50px">
								<div style="">
									<b>Border Width:</b>
									<select name="borderwidth">
										<option value="0">0</option>
										<option value="1" selected>1</option>
										<option value="2">2</option>
										<option value="3">3</option>
									</select>
								</div>
								<div style="margin-top:4px;">
									<b>Rows per page:</b>
									<select name="rowcount">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3" selected>3</option>
									</select>
								</div>
								<div style="margin-top:4px;">
									<b>Spacing between labels:</b>
									<input type="text" name="marginsize" value="5" style="width:25px" />
								</div>
							</div>
							<div style="float:left;margin-left:50px">
								<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
								<input type="submit" name="submitaction" onclick="changeAnnoFormTarget(this.form, 'browser');" value="Print in Browser" />
								<?php
								if($reportsWritable){
									?>
									<div style="margin-top:5px"><input type="submit" name="submitaction" onclick="changeAnnoFormTarget(this.form, 'word');" value="Export to DOCX" /></div>
									<?php
								}
								?>
							</div>
						</fieldset>
					</form>
					<?php
				}
				else{
					?>
					<div style="font-weight:bold;margin:20px;font-weight:150%;">
						There are no annotations queued to be printed.
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
		else{
			?>
			<div style="font-weight:bold;margin:20px;font-weight:150%;">
				You do not have permissions to print annotation labels for this collection.
				Please contact the site administrator to obtain the necessary permissions.
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