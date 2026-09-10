<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceEditorDeterminations.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('collections/editor/batchdeterminations');

header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/editor/batchdeterminations.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = Sanitize::int($_REQUEST['collid'] ?? 0);
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

$occManager = new OccurrenceEditorDeterminations();
$occManager->setCollId($collid);
$occManager->getCollMap();

$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}
elseif(array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collid, $USER_RIGHTS['CollEditor'])){
	$isEditor = 1;
}
$statusStr = '';
if($isEditor){
	if($formSubmit == 'addNewDeterminations'){
		$occidArr = $_REQUEST['occid'];
		foreach($occidArr as $k){
			$occManager->setOccId(Sanitize::int($k));
			$occManager->addDetermination($_REQUEST, $isEditor);
		}
		$statusStr = 'SUCCESS: ' . count($occidArr) . ' annotations submitted';
	}
}
?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=<?= $CHARSET;?>">
		<title><?= $DEFAULT_TITLE.' '.$LANG['BATCH_DETERS'] ?></title>
		<link href="<?= $CSS_BASE_PATH ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?= $CLIENT_ROOT ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT ?>/js/symb/taxa.suggest.js?v=1" type="text/javascript"></script>
		<script type="text/javascript">

			$(document).ready(function() {

				const taxonSearchInput = document.querySelector('#nomsciname');
				if(taxonSearchInput){
					taxonSearchInput.addEventListener('focus', (event) => {
						taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
						taxaSuggest.config.includeAuthor = false;
						taxaSuggest.config.includeKingdom = false;
						taxaSuggest.initiate("nomsciname");
					});
				}

				const taxonInput = document.querySelector('#dafsciname');
				if(taxonInput){
					taxonInput.addEventListener('focus', (event) => {
						taxaSuggest.config.clientRoot = "<?= $CLIENT_ROOT ?>";
						taxaSuggest.config.includeAuthor = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_AUTHOR) ? 'false' : 'true') ?>;
						taxaSuggest.config.includeKingdom = <?= (empty($TAXON_AUTOCOMPLETE_INCLUDE_KINGDOM) ? 'false' : 'true') ?>;
						taxaSuggest.initiate("dafsciname", function(result) {
							if (result.valid) {
								document.getElementById("dafsciname").value = result.item.sciname;
								document.getElementById("daftid").value = result.item.id;
								document.getElementById("dafauthor").value = result.item.author;
								document.getElementById("daffamily").value = result.item.family;
							}
							else{
								document.getElementById("daftid").value = "";
								document.getElementById("dafauthor").value = "";
								document.getElementById("daffamily").value = "";
								if(this.value != ""){
									alert("<?= $LANG['WARNING_TAXON_NOT_FOUND'] ?>");
								}
							}
						});
					});
				}

			});

			function submitAccForm(f){
				var workingObj = document.getElementById("workingcircle");
				workingObj.style.display = "inline"
				var allCatNum = 0;
				if(f.allcatnum.checked) allCatNum = 1;

				$.ajax({
					type: "POST",
					url: "rpc/getnewdetitem.php",
					dataType: "json",
					data: {
						catalognumber: f.catalognumber.value,
						allcatnum: allCatNum,
						sciname: f.sciname.value,
						collid: f.collid.value
					}
				}).done(function( retStr ) {
					if(retStr != ""){
						for (var occid in retStr) {
							var occObj = retStr[occid];
							if(f.catalognumber.value && checkCatalogNumber(occid, occObj["cn"])){
								alert("<?= $LANG['RECORD_EXISTS'] ?>");
							}
							else{
								var trNode = createNewTableRow(occid, occObj);
								var tableBody = document.getElementById("catrecordstbody");
								tableBody.insertBefore(trNode, tableBody.firstElementChild);
							}
						}
						document.getElementById("accrecordlistdviv").style.display = "block";
					}
					else{
						alert("<?= $LANG['NO_RECORDS'] ?>");
					}
				});

				if(f.catalognumber.value != ""){
					f.catalognumber.value = '';
					f.catalognumber.focus();
				}
				workingObj.style.display = "none";
				return false;
			}

			function checkCatalogNumber(catNum){
				var dbElements = document.getElementsByName("occid[]");
				for(i = 0; i < dbElements.length; i++){
					if(dbElements[i].value == catNum) return true;
				}
				return false;
			}

			function createNewTableRow(occid, occObj){
				var trNode = document.createElement("tr");
				var inputNode = document.createElement("input");
				inputNode.setAttribute("type", "checkbox");
				inputNode.setAttribute("name", "occid[]");
				inputNode.setAttribute("value", occid);
				inputNode.setAttribute("checked", "checked");
				var tdNode1 = document.createElement("td");
				tdNode1.appendChild(inputNode);
				trNode.appendChild(tdNode1);
				var tdNode2 = document.createElement("td");
				var anchor1 = document.createElement("a");
				anchor1.setAttribute("href","#");
				anchor1.setAttribute("onclick","openIndPopup("+occid+"); return false;");
				if(occObj["cn"]) anchor1.innerHTML = occObj["cn"];
				else anchor1.innerHTML = "[no catalog number]";
				tdNode2.appendChild(anchor1);
				var anchor2 = document.createElement("a");
				anchor2.setAttribute("href","#");

				tdNode2.appendChild(anchor2);
				trNode.appendChild(tdNode2);
				var tdNode3 = document.createElement("td");
				tdNode3.appendChild(document.createTextNode(occObj["sn"]));
				trNode.appendChild(tdNode3);
				var tdNode4 = document.createElement("td");
				tdNode4.appendChild(document.createTextNode(occObj["coll"]+'; '+occObj["loc"]));
				trNode.appendChild(tdNode4);
				return trNode;
			}

			function clearAccForm(f){
				if(confirm("<?= $LANG['CLEAR_FORM_RESETS'] ?>") == true){
					document.getElementById("accrecordlistdviv").style.display = "none";
					document.getElementById("catrecordstbody").innerHTML = '';
					f.catalognumber.value = '';
					f.sciname.value = '';
				}
			}

			function validateSelectForm(f){
				var specNotSelected = true;
				var dbElements = document.getElementsByName("occid[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					if(dbElement.checked){
						specNotSelected = false;
						break;
					}
				}
				if(specNotSelected){
					alert("<?= $LANG['SELECT_ONE'] ?>");
					return false;
				}

				if(f.sciname.value != "" && f.tidtoadd.value == ""){
					alert("<?= $LANG['WARNING_TAXON_NOT_FOUND'] ?>");
					return false;
				}
				return true;
			}

			function selectAll(cb){
				boxesChecked = true;
				if(!cb.checked){
					boxesChecked = false;
				}
				var dbElements = document.getElementsByName("occid[]");
				for(i = 0; i < dbElements.length; i++){
					var dbElement = dbElements[i];
					dbElement.checked = boxesChecked;
				}
			}

			function annotationTypeChanged(selectElem){
				var f = selectElem.form;
				if(selectElem.value == "na"){
					f.identificationqualifier.value = "";
					$("#idQualifierDiv").hide();
					f.confidenceranking.value = "";
					$("#codDiv").hide();
					f.identifiedby.value = "Nomenclatural Adjustment";
					f.identifiedby.readonly = true;
					f.makecurrent.checked = true;

					var today = new Date();
					var month = (today.getMonth() + 1);
					var day = today.getDate();
					var year = today.getFullYear();
					if(month < 10) month = '0' + month;
					if(day < 10) day = '0' + day;
					f.dateidentified.value = [year, month, day].join('-');
				}
				else{
					$("#idQualifierDiv").show();
					f.confidenceranking.value = 5;
					$("#codDiv").show();
					f.identifiedby.value = "";
					f.identifiedby.readonly = true;
					f.dateidentified.value = "";
					f.makecurrent.checked = false;
				}
			}

			function openIndPopup(occid){
				openPopup('../individual/index.php?occid=' + occid);
			}

			function openEditorPopup(occid){
				openPopup('occurrenceeditor.php?occid=' + occid);
			}

			function openPopup(urlStr){
				var wWidth = 900;
				if(document.body.offsetWidth) wWidth = document.body.offsetWidth*0.9;
				if(wWidth > 1200) wWidth = 1200;
				newWindow = window.open(urlStr,'popup','scrollbars=1,toolbar=0,resizable=1,width='+(wWidth)+',height=600,left=20,top=20');
				if (newWindow.opener == null) newWindow.opener = self;
				return false;
			}
		</script>
		<style>
			.top-breathing-room-sm-px {
				margin-top: 5px;
			}
			.left-breathing-room-rel-lg {
				margin-left: 2em;
			}
		</style>
	</head>
	<body>
	<?php
	$displayLeftMenu = (isset($collections_batchdeterminationsMenu) ? $collections_batchdeterminationsMenu : false);
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'><?= $LANG['HOME'] ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?= $collid ?>&emode=1"><?= $LANG['COLL_MANAGE'] ?></a> &gt;&gt;
		<b><?= $LANG['BATCH_DETERS'] ?></b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['BATCH_DETERS'] ?></h1>
		<?php
		if($isEditor){
			echo '<h2>'.$occManager->getCollName().'</h2>';
			?>
			<div>
				<section class="fieldset-like">
					<h2> <span> <?= $LANG['DEFINE_RECORDSET'] ?> </span> </h2>
					<div>
						<?= $LANG['RECORDSET_EXPLAIN'] ?>
					</div>
					<div style="margin-top:15px;">
						<form name="accqueryform" action="batchdeterminations.php" method="post" onsubmit="return submitAccForm(this);">
							<section class="flex-form" style="align-items: center; gap:0.5rem; margin-bottom: 1rem">
								<div style="margin: 0; display:flex; align-items: center; gap:0.25rem">
									<label for="catalognumber"><?= $LANG['CATNUM'] ?>:</label>
									<input style="margin: 0" name="catalognumber" id="catalognumber" type="text" style="border-color:green;width:200px;" />
								</div>
								<div style="margin: 0">
									<input name="allcatnum" id="allcatnum" type="checkbox" checked /> <label for="allcatnum"><?= $LANG['TARGET_ALL'] ?></label>
								</div>
							</section>
							<div style="margin-bottom: 1rem; display:flex; align-items: center; gap:0.25rem">
								<label for="nomsciname"><?= $LANG['TAXON'] ?>:</label>
								<input type="text" id="nomsciname" name="sciname"  style="margin:0; width:260px;">
							</div>
							<section class="flex-form">
								<div style="margin: 0">
									<button name="addrecord" type="submit"><?= $LANG['ADD_RECORDS'] ?></button>
									<img id="workingcircle" src="../../images/workingcircle.gif" style="display:none;" alt="progress is being made" />
								</div>
								<div style="margin: 0">
									<button name="clearaccform" type="button" onclick='clearAccForm(this.form)'><?= $LANG['CLEAR_LIST'] ?></button>
									<input name="collid" type="hidden" value="<?= $collid ?>" />
								</div>
							</section>
						</form>
					</div>
					<div style="margin-top: 1rem">
						* <?= $LANG['LIST_LIMIT'] ?><br/>
					</div>
					<?php
					if($statusStr){
						echo '<div style="margin:30px 20px;">';
						echo '<div style="color:orange;font-weight:bold;">'.$statusStr.'</div>';
						echo '<div style="margin-top:10px;"><a href="../reports/annotationmanager.php?collid=' . $collid . '" target="_blank">' . $LANG['DISPLAY_QUEUE'] . '</a></div>';
						echo '</div>';
					}
					?>
				</section>
				<div id="accrecordlistdviv" style="display:none;">
					<form name="accselectform" id="accselectform" action="batchdeterminations.php" method="post" onsubmit="return validateSelectForm(this);">
						<div style="margin-top: 15px; margin-left: 10px;">
							<input name="accselectall" value="" type="checkbox" onclick="selectAll(this);" checked />
							<?= $LANG['SELECT_DESELECT'] ?>
						</div>
						<table class="styledtable">
							<thead>
								<tr>
									<th style="width:25px;text-align:center;">&nbsp;</th>
									<th style="width:125px;text-align:center;"><?= $LANG['CATNUM'] ?></th>
									<th style="width:300px;text-align:center;"><?= $LANG['SCINAME'] ?></th>
									<th style="text-align:center;"><?= $LANG['COLLECTOR_LOCALITY'] ?></th>
								</tr>
							</thead>
							<tbody id="catrecordstbody"></tbody>
						</table>
						<div id="newdetdiv" style="">
							<fieldset style="margin: 15px 15px 0px 15px;padding:15px;">
								<legend><b><?= $LANG['NEW_DET_DETAILS'] ?></b></legend>
								<div style='margin:3px;position:relative;height:35px'>
									<div style="float:left;">
										<b><?= $LANG['ANNOTATION_TYPE'] ?>: </b>
									</div>
									<div style="float:left;">
										<input name="annotype" type="radio" value="id" onchange="annotationTypeChanged(this)" checked /> <?= $LANG['ID_ADJUST'] ?><br/>
										<input name="annotype" type="radio" value="na" onchange="annotationTypeChanged(this)" /> <?= $LANG['NOM_ADJUST'] ?>
									</div>
								</div>
								<div style="clear:both;margin:15px 0px"><hr /></div>
								<div id="idQualifierDiv" style='margin:3px;clear:both'>
									<b><?= $LANG['ID_QUALIFIER'] ?>:</b>
									<input type="text" name="identificationqualifier" title="e.g. cf, aff, etc" />
								</div>
								<div style='margin:3px;'>
									<label for="dafsciname"><b><?= $LANG['SCINAME'] ?></b></label>:
									<input type="text" id="dafsciname" name="sciname" style="width:350px;" required >
									<input type="hidden" id="daftid" name="tidtoadd" value="" />
									<input type="hidden" id="daffamily" name="family" value="" />
								</div>
								<div style='margin:3px;'>
									<b><?= $LANG['AUTHOR'] ?>:</b>
									<input type="text" id="dafauthor" name="scientificnameauthorship" style="width:200px;" />
								</div>
								<div id="codDiv" style='margin:3px;'>
									<b><?= $LANG['CONFIDENCE'] ?>:</b>
									<select name="confidenceranking">
										<option value="8"><?= $LANG['HIGH'] ?></option>
										<option value="5" selected><?= $LANG['MEDIUM'] ?></option>
										<option value="2"><?= $LANG['LOW'] ?></option>
									</select>
								</div>
								<div id="identifiedByDiv" style='margin:3px;'>
									<label for="identifiedby"><b><?= $LANG['DETERMINER'] ?></b></label>:
									<input type="text" name="identifiedby" id="identifiedby" style="width:200px;" required />
								</div>
								<div id="dateIdentifiedDiv" style='margin:3px;'>
									<label for="dateidentified"><b><?= $LANG['DATE'] ?></b></label>:
									<input type="text" name="dateidentified" id="dateidentified" onchange="detDateChanged(this.form);" required />
								</div>
								<div style='margin:3px;'>
									<b><?= $LANG['REFERENCE'] ?>:</b>
									<input type="text" name="identificationreferences" style="width:350px;" />
								</div>
								<div style='margin:3px;'>
									<b><?= $LANG['NOTES'] ?>:</b>
									<input type="text" name="identificationremarks" style="width:350px;" />
								</div>
								<div id="makeCurrentDiv" style='margin:3px;'>
									<input type="checkbox" name="makecurrent" value="1" checked /> <?= $LANG['MAKE_CURRENT'] ?>
								</div>
								<div style='margin:3px;'>
									<input type="checkbox" name="printqueue" value="1" checked /> <?= $LANG['ADD_PRINT_QUEUE'] ?>
									<a href="../reports/annotationmanager.php?collid=<?= $collid ?>" target="_blank"><img src="../../images/list.png" style="width:1.2em" title="<?= $LANG['DISPLAY_QUEUE'] ?>" /></a>
								</div>
								<div style='margin:15px;'>
									<div>
										<input name="collid" type="hidden" value="<?= $collid ?>" />
										<input name="tabtarget" type="hidden" value="0" />
										<button type="submit" name="formsubmit" value="addNewDeterminations"><?= $LANG['ADD_DETERS'] ?></button>
									</div>
									<p><?php include('includes/requiredFieldInstruction.php')?></p>
								</div>
							</fieldset>
						</div>
					</form>
				</div>
			</div>
			<?php
		}
		else{
			?>
			<div style="font-weight:bold;margin:20px;font-weight:150%;">
				<?= $LANG['NO_PERMISSIONS'] ?>
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
