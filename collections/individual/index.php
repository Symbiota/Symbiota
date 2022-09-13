<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceIndividual.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverCore.php');
include_once($SERVER_ROOT.'/classes/RdfUtility.php');
include_once($SERVER_ROOT.'/content/lang/collections/individual/index.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/content/lang/fieldterms/materialSampleVars.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occid = array_key_exists('occid',$_REQUEST)?trim($_REQUEST['occid']):0;
$collid = array_key_exists('collid',$_REQUEST)?trim($_REQUEST['collid']):0;
$pk = array_key_exists('pk',$_REQUEST)?trim($_REQUEST['pk']):'';
$guid = array_key_exists('guid',$_REQUEST)?trim($_REQUEST['guid']):'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$clid = array_key_exists('clid',$_REQUEST)?trim($_REQUEST['clid']):0;
$format = isset($_GET['format'])?$_GET['format']:'';
$submit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Sanitize input variables
if(!is_numeric($occid)) $occid = 0;
if(!is_numeric($collid)) $collid = 0;
if($guid) $guid = filter_var($guid,FILTER_SANITIZE_STRING);
if(!is_numeric($tabIndex)) $tabIndex = 0;
if(!is_numeric($clid)) $clid = 0;
if($pk && !preg_match('/^[a-zA-Z0-9\s_]+$/',$pk)) $pk = '';

$indManager = new OccurrenceIndividual($submit?'write':'readonly');
if($occid) $indManager->setOccid($occid);
elseif($guid) $occid = $indManager->setGuid($guid);
elseif($collid && $pk){
	$indManager->setCollid($collid);
	$indManager->setDbpk($pk);
}

$indManager->setDisplayFormat($format);
$indManager->setOccurData();
if(!$occid) $occid = $indManager->getOccid();
if(!$collid) $collid = $indManager->getCollid();

$isSecuredReader = false;
$isEditor = false;
if($SYMB_UID){
	//Check editing status
	if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))){
		$isEditor = true;
	}
	elseif((array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor']))){
		$isEditor = true;
	}
	elseif(isset($occArr['observeruid']) && $occArr['observeruid'] == $SYMB_UID){
		$isEditor = true;
	}
	elseif($indManager->isTaxonomicEditor()){
		$isEditor = true;
	}
	//Check locality security
	if($isEditor || array_key_exists('RareSppAdmin',$USER_RIGHTS) || array_key_exists('RareSppReadAll',$USER_RIGHTS)){
		$isSecuredReader = true;
	}
	elseif(isset($USER_RIGHTS['RareSppReader']) && in_array($collid,$USER_RIGHTS['RareSppReader'])){
		$isSecuredReader = true;
	}
	elseif(isset($USER_RIGHTS['CollAdmin'])){
		$isSecuredReader = true;
	}
	elseif(isset($USER_RIGHTS['CollEditor']) && in_array($collid,$USER_RIGHTS['CollEditor'])){
		$isSecuredReader = true;
	}
}
$indManager->applyProtections($isSecuredReader);
$occArr = $indManager->getOccData();
$collMetadata = $indManager->getMetadata();
$genticArr = $indManager->getGeneticArr();

$statusStr = '';
//  If other than HTML was requested, return just that content.
if(isset($_SERVER['HTTP_ACCEPT'])){
	$accept = RdfUtility::parseHTTPAcceptHeader($_SERVER['HTTP_ACCEPT']);
	foreach($accept as $key => $mediarange){
		if($mediarange=='text/turtle' || $format == 'turtle') {
			Header("Content-Type: text/turtle; charset=".$CHARSET);
			$dwcManager = new DwcArchiverCore();
			$dwcManager->setCustomWhereSql(" o.occid = $occid ");
			echo $dwcManager->getAsTurtle();
			die;
		}
		elseif($mediarange=='application/rdf+xml' || $format == 'rdf') {
			Header("Content-Type: application/rdf+xml; charset=".$CHARSET);
			$dwcManager = new DwcArchiverCore();
			$dwcManager->setCustomWhereSql(" o.occid = $occid ");
			echo $dwcManager->getAsRdfXml();
			die;
		}
		elseif($mediarange=='application/json' || $format == 'json') {
			Header("Content-Type: application/json; charset=".$CHARSET);
			$dwcManager = new DwcArchiverCore();
			$dwcManager->setCustomWhereSql(" o.occid = $occid ");
			echo $dwcManager->getAsJson();
			die;
		}
	}
}

if($SYMB_UID){
	//Form action submitted
	if(array_key_exists('delvouch',$_GET) && $occid){
		if(!$indManager->deleteVoucher($occid,$_GET['delvouch'])){
			$statusStr = $indManager->getErrorMessage();
		}
	}
	if(array_key_exists('commentstr',$_POST)){
		if(!$indManager->addComment($_POST['commentstr'])){
			$statusStr = $indManager->getErrorMessage();
		}
	}
	elseif($submit == 'deleteComment'){
		if(!$indManager->deleteComment($_POST['comid'])){
			$statusStr = $indManager->getErrorMessage();
		}
	}
	elseif(array_key_exists('repcomid',$_GET)){
		if($indManager->reportComment($_GET['repcomid'])){
			$statusStr = (isset($LANG['FLAGGEDCOMMENT'])?$LANG['FLAGGEDCOMMENT']:'Comment reported as inappropriate. Comment will remain unavailable to public until reviewed by an administrator.');
		}
		else{
			$statusStr = $indManager->getErrorMessage();
		}
	}
	elseif(array_key_exists('publiccomid',$_GET)){
		if(!$indManager->makeCommentPublic($_GET['publiccomid'])){
			$statusStr = $indManager->getErrorMessage();
		}
	}
	elseif($submit == 'Add Voucher'){
		if(!$indManager->linkVoucher($_POST)){
			$statusStr = $indManager->getErrorMessage();
		}
	}
	if($isEditor){
		if($submit == 'restoreRecord'){
			if($indManager->restoreRecord($occid)){
				$occArr = $indManager->getOccData();
				$collMetadata = $indManager->getMetadata();
			}
			else $statusStr = $indManager->getErrorMessage();
		}
	}
}

$displayMap = false;
if($occArr && is_numeric($occArr['decimallatitude']) && is_numeric($occArr['decimallongitude'])) $displayMap = true;
$dupClusterArr = $indManager->getDuplicateArr();
$commentArr = $indManager->getCommentArr($isEditor);
$traitArr = $indManager->getTraitArr();
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.(isset($LANG['DETAILEDCOLREC'])?$LANG['DETAILEDCOLREC']:'Detailed Collection Record Information'); ?></title>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<meta name="description" content="<?php echo 'Occurrence author: '.($occArr?$occArr['recordedby'].','.$occArr['recordnumber']:''); ?>" />
	<meta name="keywords" content="<?php echo (!empty($occArr['occurrenceid'])?$occArr['occurrenceid']:'').', '.(!empty($occArr['recordid'])?$occArr['recordid']:''); ?>" />
	<?php
	$cssPath = '/css/symb/custom/collindividualindex.css';
	if(!file_exists($SERVER_ROOT.$cssPath)) $cssPath = '/css/symb/collindividualindex.css';
	echo '<link href="'.$CLIENT_ROOT.$cssPath.'?ver='.$CSS_VERSION_LOCAL.'" type="text/css" rel="stylesheet" />';
	$activateJQuery = false;
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<link href="../../css/symb/popup.css" type="text/css" rel="stylesheet" />
	<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script src="//maps.googleapis.com/maps/api/js?<?php echo (isset($GOOGLE_MAP_KEY) && $GOOGLE_MAP_KEY?'key='.$GOOGLE_MAP_KEY:''); ?>"></script>
	<script type="text/javascript">
		var tabIndex = <?php echo $tabIndex; ?>;
		var map;
		var mapInit = false;

		$(document).ready(function() {
			$('#tabs').tabs({
				beforeActivate: function(event, ui) {
					if(document.getElementById("map_canvas") && ui.newTab.index() == 1 && !mapInit){
						mapInit = true;
						initializeMap();
					}
					return true;
				},
				active: tabIndex
			});

			$("#tabs").tabs().css({
				'min-height': '400px',
				'overflow': 'auto'
			});
		});

		function refreshRecord(occid){
			$.ajax({
				method: "GET",
				url: "<?php echo $CLIENT_ROOT; ?>/api/v2/occurrence/"+occid+"/reharvest"
			})
			.done(function( response ) {
				if(response.status == 200){
					$("#dataStatus").val(response.dataStatus);
					$("#fieldsModified").val(JSON.stringify(response.fieldsModified));
					$("#sourceDateLastModified").val(response.sourceDateLastModified);
					alert("Record reharvested. Page will reload to refresh contents...");
					$("#refreshForm").submit();
				}
				else{
					alert("ERROR updating record: "+response.error);
				}
			});
		}

		function displayAllMaterialSamples(){
			$(".mat-sample-div").show();
			$("#mat-sample-more-div").hide();
		}

		function toggle(target){
			var objDiv = document.getElementById(target);
			if(objDiv){
				if(objDiv.style.display=="none") objDiv.style.display = "block";
				else objDiv.style.display = "none";
			}
			else{
				var divObjs = document.getElementsByTagName("div");
				for (i = 0; i < divObjs.length; i++) {
					var obj = divObjs[i];
					if(obj.getAttribute("class") == target || obj.getAttribute("className") == target){
						if(obj.style.display=="none") obj.style.display="inline";
						else obj.style.display="none";
					}
				}
			}
		}

		function verifyVoucherForm(f){
			var clTarget = f.elements["clid"].value;
			if(clTarget == "0"){
				window.alert("Please select a checklist");
				return false;
			}
			return true;
		}

		function verifyCommentForm(f){
			if(f.commentstr.value.replace(/^\s+|\s+$/g,"")) return true;
			alert("Please enter a comment");
			return false;
		}

		function openIndividual(target) {
			occWindow=open("index.php?occid="+target,"occdisplay","resizable=1,scrollbars=1,toolbar=0,width=900,height=600,left=20,top=20");
			if (occWindow.opener == null) occWindow.opener = self;
		}

		<?php
		if($displayMap){
			?>
			function initializeMap(){
				var mLatLng = new google.maps.LatLng(<?php echo $occArr['decimallatitude'].",".$occArr['decimallongitude']; ?>);
				var dmOptions = {
					zoom: 8,
					center: mLatLng,
					marker: mLatLng,
					mapTypeId: google.maps.MapTypeId.TERRAIN,
					scaleControl: true
				};
				map = new google.maps.Map(document.getElementById("map_canvas"), dmOptions);
				//Add marker
				var marker = new google.maps.Marker({
					position: mLatLng,
					map: map
				});
			}
			<?php
		}
		?>
	</script>
	<style>
		fieldset{ margin:10px; padding:15px; width:90% }
		legend{ font-weight:bold; }
		.title{ font-weight:bold; font-size:120%; }
		.label{ font-weight:bold; }
		.imgDiv{ max-width:200; float:left; text-align:center; padding:5px }
		.occur-ref{ margin: 10px 0px }
		.traitDiv{ margin:20px; }
		.traitName{ font-weight:bold; }
	</style>
</head>
<body>
	<div id="fb-root"></div>
	<script>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));

		window.twttr=(function(d,s,id){
			var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};
			if(d.getElementById(id))return;js=d.createElement(s);
			js.id=id;js.src="https://platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js,fjs);t._e=[];
			t.ready=function(f){t._e.push(f);};
			return t;
		}(document,"script","twitter-wjs"));
	</script>
	<!-- This is inner text! -->
	<div id="popup-innertext">
		<?php
		if($statusStr){
			?>
			<hr />
			<div style="padding:15px;">
				<span style="color:red;"><?php echo $statusStr; ?></span>
			</div>
			<hr />
			<?php
		}
		if($occArr){
			?>
			<div id="tabs" style="margin:10px;clear:both;">
				<ul>
					<li><a href="#occurtab"><span><?php echo (isset($LANG['DETAILS'])?$LANG['DETAILS']:'Details'); ?></span></a></li>
					<?php
					if($displayMap) echo '<li><a href="#maptab"><span>'.(isset($LANG['MAP'])?$LANG['MAP']:'Map').'</span></a></li>';
					if($genticArr) echo '<li><a href="#genetictab"><span>'.(isset($LANG['GENETIC'])?$LANG['GENETIC']:'Genetic').'</span></a></li>';
					if($dupClusterArr) echo '<li><a href="#dupestab"><span>'.(isset($LANG['DUPLICATES'])?$LANG['DUPLICATES']:'Duplicates').'</span></a></li>';
					?>
					<li><a href="#commenttab"><span><?php echo ($commentArr?count($commentArr).' ':''); echo (isset($LANG['COMMENTS'])?$LANG['COMMENTS']:'Comments'); ?></span></a></li>
					<li><a href="linkedresources.php?occid=<?php echo $occid.'&tid='.$occArr['tidinterpreted'].'&clid='.$clid.'&collid='.$collid; ?>"><span><?php echo (isset($LANG['LINKEDRES'])?$LANG['LINKEDRES']:'Linked Resources'); ?></span></a></li>
					<?php
					if($traitArr) echo '<li><a href="#traittab"><span>'.(isset($LANG['TRAITS'])?$LANG['TRAITS']:'Traits').'</span></a></li>';
					if($isEditor) echo '<li><a href="#edittab"><span>'.(isset($LANG['EDITHIST'])?$LANG['EDITHIST']:'Edit History').'</span></a></li>';
					?>
				</ul>
				<div id="occurtab">
					<div style="float:right;">
						<div style="float:right;">
							<a class="twitter-share-button" href="https://twitter.com/share" data-url="<?php echo $_SERVER['HTTP_HOST'].$CLIENT_ROOT.'/collections/individual/index.php?occid='.$occid.'&clid='.$clid; ?>"><?php echo (isset($LANG['TWEET'])?$LANG['TWEET']:'Tweet'); ?></a>
						</div>
						<div style="float:right;margin-right:10px;">
							<div class="fb-share-button" data-href="" data-layout="button_count"></div>
						</div>
					</div>
					<?php
					$iconUrl = (substr($collMetadata["icon"],0,6)=='images'?'../../':'').$collMetadata['icon'];
					if($iconUrl){
						$iconUrl = '<img border="1" height="50" width="50" src="'.$iconUrl.'" />';
						echo '<div style="float:left;margin:15px 0px;text-align:center;font-weight:bold;width:120px;">';
						echo $iconUrl;
						echo '</div>';
					}
					$instCode = $collMetadata['institutioncode'];
					if($collMetadata['collectioncode']) $instCode .= ':'.$collMetadata['collectioncode'];
					?>
					<div style="padding:25px;font-size:18px;font-weight:bold;">
						<div style="float:left;margin-right:5px">
							<?php echo $collMetadata['collectionname']; ?>
						</div>
						<div style="float:left;">
							<?php echo '('.$instCode.')'; ?>
						</div>
					</div>
					<div style="clear:both;margin-left:60px;">
						<?php
						if(array_key_exists('loan',$occArr)){
							?>
							<div style="float:right;color:red;font-weight:bold;" title="<?php echo 'Loan #'.$occArr['loan']['identifier']; ?>">
								<?php echo (isset($LANG['ONLOANTO'])?$LANG['ONLOANTO']:'On Loan To'); ?>
								<?php echo $occArr['loan']['code']; ?>
							</div>
							<?php
						}
						if(array_key_exists('relation',$occArr)){
							?>
							<fieldset style="float:right; width:45%">
								<legend><?php echo (isset($LANG['RELATEDOCC'])?$LANG['RELATEDOCC']:'Related Occurrences'); ?></legend>
								<?php
								$displayLimit = 5;
								$cnt = 0;
								foreach($occArr['relation'] as $id => $assocArr){
									if($cnt == $displayLimit){
										echo '<div class="relation-hidden"><a href="#" onclick="$(\'.relation-hidden\').toggle();return false;">show all records</a></div>';
										echo '<div class="relation-hidden" style="display:none">';
									}
									echo '<div>';
									echo $assocArr['relationship'];
									if($assocArr['subtype']) echo ' ('.$assocArr['subtype'].')';
									echo ': ';
									$relID = $assocArr['identifier'];
									$relUrl = $assocArr['resourceurl'];
									if(!$relUrl && $assocArr['occidassoc']) $relUrl = $GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$assocArr['occidassoc'];
									if($relUrl) $relID = '<a href="'.$relUrl.'">'.$relID.'</a>';
									if($relID) echo $relID;
									elseif($assocArr['sciname']) echo $assocArr['sciname'];
									echo '</div>';
									$cnt++;
								}
								if(count($occArr['relation']) > $displayLimit) echo '</div>';
								?>
							</fieldset>
							<?php
						}
						if($occArr['catalognumber']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['CATALOGNUM'])?$LANG['CATALOGNUM']:'Catalog #').': </label>';
								echo $occArr['catalognumber'];
								?>
							</div>
							<?php
						}
						if($occArr['occurrenceid']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['OCCID'])?$LANG['OCCID']:'Occurrence ID').': </label>';
								$resolvableGuid = false;
								if(substr($occArr['occurrenceid'],0,4) == 'http') $resolvableGuid = true;
								if($resolvableGuid) echo '<a href="'.$occArr['occurrenceid'].'" target="_blank">';
								echo $occArr['occurrenceid'];
								if($resolvableGuid) echo '</a>';
								?>
							</div>
							<?php
						}
						if($occArr['othercatalognumbers']){
							if(substr($occArr['othercatalognumbers'],0,1)=='{'){
								$otherCatArr = json_decode($occArr['othercatalognumbers'],true);
								foreach($otherCatArr as $catTag => $catValueArr){
									if(!$catTag) $catTag = 'Secondary Catalog #';
									echo '<div><label>'.$catTag.':</label> '.implode('; ', $catValueArr).'</div>';
								}
							}
							else{
								?>
								<div>
									<?php
									echo '<label>'.(isset($LANG['SECONDARYCATNUM'])?$LANG['SECONDARYCATNUM']:'Secondary Catalog #').': </label>';
									echo $occArr['othercatalognumbers'];
									?>
								</div>
								<?php
							}
						}
						if($occArr['sciname']){
							echo '<label>'.(isset($LANG['TAXON'])?$LANG['TAXON']:'Taxon').':</label> ';
							echo '<i>'.$occArr['sciname'].'</i> '.$occArr['scientificnameauthorship'];
							if(isset($occArr['taxonsecure'])){
								echo '<span style="margin-left:10px;color:orange">'.(isset($LANG['IDPROTECTED'])?$LANG['IDPROTECTED']:'identification protected').'</span>';
							}
							if($occArr['tidinterpreted']){
								//echo ' <a href="../../taxa/index.php?taxon='.$occArr['tidinterpreted'].'" title="Open Species Profile Page"><img src="" /></a>';
							}
							?>
							<br/>
							<?php
							if($occArr['identificationqualifier']) echo '<label>'.(isset($LANG['IDQUALIFIER'])?$LANG['IDQUALIFIER']:'Identification Qualifier').':</label> '.$occArr['identificationqualifier'].'<br/>';
						}
						if($occArr['family']) echo '<label>'.(isset($LANG['FAMILY'])?$LANG['FAMILY']:'Family').':</label> '.$occArr['family'];
						if($occArr['identifiedby']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['DETERMINER'])?$LANG['DETERMINER']:'Determiner').': </label>'.$indManager->activateOrcidID($occArr['identifiedby']);
								if($occArr['dateidentified']) echo ' ('.$occArr['dateidentified'].')';
								?>
							</div>
							<?php
						}
						if($occArr['taxonremarks']){
							?>
							<div style="margin-left:10px;">
								<?php
								echo '<label>'.(isset($LANG['TAXONREMARKS'])?$LANG['TAXONREMARKS']:'Taxon Remarks').': </label>';
								echo $occArr['taxonremarks'];
								?>
							</div>
							<?php
						}
						if($occArr['identificationremarks']){
							?>
							<div style="margin-left:10px;">
								<?php
								echo '<label>'.(isset($LANG['IDREMARKS'])?$LANG['IDREMARKS']:'ID Remarks').': </label>';
								echo $occArr['identificationremarks'];
								?>
							</div>
							<?php
						}
						if($occArr['identificationreferences']){ ?>
							<div style="margin-left:10px;">
								<?php
								echo '<label>'.(isset($LANG['IDREFERENCES'])?$LANG['IDREFERENCES']:'ID References').': </label>';
								echo $occArr['identificationreferences'];
								?>
							</div>
							<?php
						}
						if(array_key_exists('dets',$occArr) && (count($occArr['dets']) > 1 || $occArr['dets'][key($occArr['dets'])]['iscurrent'] == 0)){
							?>
							<div class="detdiv" style="margin-left:10px;cursor:pointer;" onclick="toggle('detdiv');">
								<img src="../../images/plus_sm.png" style="border:0px;" />
								<?php echo (isset($LANG['SHOWDETHISTORY'])?$LANG['SHOWDETHISTORY']:'Show Determination History'); ?>
							</div>
							<div class="detdiv" style="display:none;">
								<div style="margin-left:10px;cursor:pointer;" onclick="toggle('detdiv');">
									<img src="../../images/minus_sm.png" style="border:0px;" />
									<?php echo (isset($LANG['HIDEDETHISTORY'])?$LANG['HIDEDETHISTORY']:'Hide Determination History'); ?>
								</div>
								<fieldset>
									<legend><?php echo (isset($LANG['DETHISTORY'])?$LANG['DETHISTORY']:'Determination History'); ?></legend>
									<?php
									$firstIsOut = false;
									$dArr = $occArr['dets'];
									foreach($dArr as $detArr){
										if($firstIsOut) echo '<hr />';
											$firstIsOut = true;
										?>
										 <div style="margin:10px;">
											<?php
											if($detArr['qualifier']) echo $detArr['qualifier'];
											echo ' <label><i>'.$detArr['sciname'].'</i></label> '.$detArr['author'];
											?>
											<div style="">
												<?php
												echo '<label>'.(isset($LANG['DETERMINER'])?$LANG['DETERMINER']:'Determiner').': </label>';
												echo $detArr['identifiedby'];
												?>
											</div>
											<div style="">
												<?php
												echo '<label>'.(isset($LANG['DATE'])?$LANG['DATE']:'Date').': </label>';
												echo $detArr['date'];
												?>
											</div>
											<?php
											if($detArr['ref']){ ?>
												<div style="">
													<?php
													echo '<label>'.(isset($LANG['IDREFERENCES'])?$LANG['IDREFERENCES']:'ID References').': </label>';
													echo $detArr['ref'];
													?>
												</div>
												<?php
											}
											if($detArr['notes']){
												?>
												<div style="">
													<?php
													echo '<label>'.(isset($LANG['IDREMARKS'])?$LANG['IDREMARKS']:'ID Remarks').': </label>';
													echo $detArr['notes'];
													?>
												</div>
												<?php
											}
											?>
										 </div>
										<?php
									}
									?>
								</fieldset>
							</div>
							<?php
						}
						if($occArr['typestatus']){ ?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['TYPESTATUS'])?$LANG['TYPESTATUS']:'Type Status').': </label>';
								echo $occArr['typestatus'];
								?>
							</div>
							<?php
						}
						if($occArr['eventid']){
							?>
							<div>
								<span class="label"><?php echo (isset($LANG['EVENTID'])?$LANG['EVENTID']:'Event ID'); ?>: </span>
								<?php
								echo $occArr['eventid'];
								?>
							</div>
							<?php
						}
						if($occArr['recordedby']){
							$recByLabel = (isset($LANG['OBSERVER'])?$LANG['OBSERVER']:'Observer');
							if($collMetadata['colltype'] == 'Preserved Specimens') $recByLabel = (isset($LANG['COLLECTOR'])?$LANG['COLLECTOR']:'Collector');
							?>
							<div>
								<span class="label"><?php echo $recByLabel; ?>: </span>
								<?php
								$recByStr = $indManager->activateOrcidID($occArr['recordedby']);
								echo $recByStr;
								?>
							</div>
							<?php
							if($occArr['recordnumber']){
								?>
								<div style="margin-left:10px;">
									<span class="label"><?php echo (isset($LANG['NUMBER'])?$LANG['NUMBER']:'Number'); ?>: </span>
									<?php echo $occArr['recordnumber']; ?>
								</div>
								<?php
							}
						}
						if($occArr['eventdate']){
							echo '<div>';
							echo '<span class="label">'.(isset($LANG['EVENTDATE'])?$LANG['EVENTDATE']:'Date').':</span> '.$occArr['eventdate'];
							if($occArr['eventdateend'] && $occArr['eventdateend'] != $occArr['eventdate']){
								echo ' - '.$occArr['eventdateend'];
							}
							echo '</div>';
						}
						if($occArr['verbatimeventdate']){
							echo '<div><span class="label">'.(isset($LANG['VERBATIMDATE'])?$LANG['VERBATIMDATE']:'Verbatim Date').':</span> '.$occArr['verbatimeventdate'].'</div>';
						}
						?>
						<div>
							<?php
							if($occArr['associatedcollectors']){
								?>
								<div>
									<?php
									echo '<label>'.(isset($LANG['ADDITIONALCOLLECTORS'])?$LANG['ADDITIONALCOLLECTORS']:'Additional Collectors').': </label>';
									echo $occArr['associatedcollectors'];
									?>
								</div>
								<?php
							}
							?>
						</div>
						<?php
						$localityStr1 = '';
						if($occArr['country']) $localityStr1 .= $occArr['country'].', ';
						if($occArr['stateprovince']) $localityStr1 .= $occArr['stateprovince'].', ';
						if($occArr['county']) $localityStr1 .= $occArr['county'].', ';
						if($occArr['municipality']) $localityStr1 .= $occArr['municipality'].', ';
						?>
						<div>
							<?php
							echo '<label>'.(isset($LANG['LOCALITY'])?$LANG['LOCALITY']:'Locality').':</label> ';
							if(!isset($occArr['localsecure'])){
								$localityStr1 .= $occArr['locality'];
								if($occArr['locationid']) $localityStr1 .= ' [locationID: '.$occArr['locationid'].']';
							}
							echo trim($localityStr1,',; ');
							if($occArr['localitysecurity'] == 1){
								echo '<div style="margin-left:10px"><span style="color:orange;">'.(isset($LANG['LOCDETAILSPROTECTED'])?$LANG['LOCDETAILSPROTECTED']:'Locality details protected').':<span> ';
								if($occArr['localitysecurityreason'] && substr($occArr['localitysecurityreason'],0,1) != '<') echo $occArr['localitysecurityreason'];
								else echo (isset($LANG['LOCPROTECTEXPLANATION'])?$LANG['LOCPROTECTEXPLANATION']:'protection typically due to rare or threatened status');
								if(!isset($occArr['localsecure'])) echo '<br/>'.(isset($LANG['ACCESS_GRANTED'])?$LANG['ACCESS_GRANTED']:'Current user has been granted access');
								echo '</div>';
							}
							?>
						</div>
						<?php
						if($occArr['decimallatitude']){
							?>
							<div style="margin-left:10px;">
								<?php
								echo $occArr['decimallatitude'].'&nbsp;&nbsp;'.$occArr['decimallongitude'];
								if($occArr['coordinateuncertaintyinmeters']) echo ' +-'.$occArr['coordinateuncertaintyinmeters'].'m.';
								if($occArr['geodeticdatum']) echo '&nbsp;&nbsp;'.$occArr['geodeticdatum'];
								?>
							</div>
							<?php
						}
						if($occArr['verbatimcoordinates']){
							?>
							<div style="margin-left:10px;">
								<?php
								echo '<label>'.(isset($LANG['VERBATIMCOORDINATES'])?$LANG['VERBATIMCOORDINATES']:'Verbatim Coordinates').': </label>';
								echo $occArr['verbatimcoordinates'];
								?>
							</div>
							<?php
						}
						if($occArr['locationremarks']){
							?>
							<div style="margin-left:10px;">
								<?php
								echo '<label>'.(isset($LANG['LOCATIONREMARKS'])?$LANG['LOCATIONREMARKS']:'Location Remarks').': </label>';
								echo $occArr['locationremarks'];
								?>
							</div>
							<?php
						}
						if($occArr['georeferenceremarks']){
							?>
							<div style="margin-left:10px;clear:both;">
								<?php
								echo '<label>'.(isset($LANG['GEOREFREMARKS'])?$LANG['GEOREFREMARKS']:'Georeference Remarks').': </label>';
								echo $occArr['georeferenceremarks'];
								?>
							</div>
							<?php
						}
						if($occArr['minimumelevationinmeters'] || $occArr['verbatimelevation']){
							?>
							<div style="margin-left:10px;">
								<?php
								echo '<label>'.(isset($LANG['ELEVATION'])?$LANG['ELEVATION']:'Elevation').': </label>';
								echo $occArr['minimumelevationinmeters'];
								if($occArr['maximumelevationinmeters']){
									echo '-'.$occArr['maximumelevationinmeters'];
								}
								echo ' '.(isset($LANG['METERS'])?$LANG['METERS']:'meters');
								if($occArr['verbatimelevation']){
									?>
									<span style="margin-left:20px">
										<label><?php echo (isset($LANG['VERBATELEVATION'])?$LANG['VERBATELEVATION']:'Verbatim Elevation'); ?>: </label>
										<?php echo $occArr['verbatimelevation']; ?>
									</span>
									<?php
								}
								else{
									echo ' ('.round($occArr['minimumelevationinmeters']*3.28).($occArr['maximumelevationinmeters']?'-'.round($occArr['maximumelevationinmeters']*3.28):'');
									echo (isset($LANG['FT'])?$LANG['FT']:'ft').')';
								}
								?>
							</div>
							<?php
						}
						if($occArr['minimumdepthinmeters'] || $occArr['verbatimdepth']){
							?>
							<div style="margin-left:10px;">
								<?php
								echo '<label>'.(isset($LANG['DEPTH'])?$LANG['DEPTH']:'Depth').': </label>';
								echo $occArr['minimumdepthinmeters'];
								if($occArr['maximumdepthinmeters']) echo '-'.$occArr['maximumdepthinmeters'];
								echo ' '.(isset($LANG['METERS'])?$LANG['METERS']:'meters');
								if($occArr['verbatimdepth']){
									?>
									<span style="margin-left:20px">
										<?php
										echo '<label>'.(isset($LANG['VERBATDEPTH'])?$LANG['VERBATDEPTH']:'Verbatim Depth').': </label>';
										echo $occArr['verbatimdepth'];
										?>
									</span>
									<?php
								}
								?>
							</div>
							<?php
						}
						if($occArr['informationwithheld']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['INFO_WITHHELD'])?$LANG['INFO_WITHHELD']:'Information withheld').': </label>';
								echo $occArr['informationwithheld'];
								?>
							</div>
							<?php
						}
						if($occArr['habitat']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['HABITAT'])?$LANG['HABITAT']:'Habitat').': </label>';
								echo $occArr['habitat'];
								?>
							</div>
							<?php
						}
						if($occArr['substrate']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['SUBSTRATE'])?$LANG['SUBSTRATE']:'Substrate').': </label>';
								echo $occArr['substrate'];
								?>
							</div>
							<?php
						}
						if($occArr['associatedtaxa']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['ASSOCTAXA'])?$LANG['ASSOCTAXA']:'Associated Taxa').': </label>';
								echo $occArr['associatedtaxa'];
								?>
							</div>
							<?php
						}
						if($occArr['verbatimattributes']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['DESCRIPTION'])?$LANG['DESCRIPTION']:'Description').': </label>';
								echo $occArr['verbatimattributes'];
								?>
							</div>
							<?php
						}
						if($occArr['dynamicproperties']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['DYNAMICPROPERTIES'])?$LANG['DYNAMICPROPERTIES']:'Dynamic Properties').': </label>';
								echo $occArr['dynamicproperties'];
								?>
							</div>
							<?php
						}
						if($occArr['reproductivecondition']){
							?>
							<div>
								<label><?php echo (isset($LANG['REPROCONDITION'])?$LANG['REPROCONDITION']:'Reproductive Condition'); ?>:</label>
								<?php echo $occArr['reproductivecondition']; ?>
							</div>
							<?php
						}
						if($occArr['lifestage']){
							?>
							<div>
								<?php
								echo '<label>'.(isset($LANG['LIFESTAGE'])?$LANG['LIFESTAGE']:'Life Stage').': </label>';
								echo $occArr['lifestage'];
								?>
							</div>
							<?php
						}
						if($occArr['sex']){
							?>
							<div>
								<label><?php echo (isset($LANG['SEX'])?$LANG['SEX']:'Sex'); ?>:</label>
								<?php echo $occArr['sex']; ?>
							</div>
							<?php
						}
						if($occArr['individualcount']){
							?>
							<div>
								<label><?php echo (isset($LANG['INDCOUNT'])?$LANG['INDCOUNT']:'Individual Count'); ?>:</label>
								<?php echo $occArr['individualcount']; ?>
							</div>
							<?php
						}
						if($occArr['samplingprotocol']){
							?>
							<div>
								<label><?php echo (isset($LANG['SAMPPROTOCOL'])?$LANG['SAMPPROTOCOL']:'Sampling Protocol'); ?>:</label>
								<?php echo $occArr['samplingprotocol']; ?>
							</div>
							<?php
						}
						if($occArr['preparations']){
							?>
							<div>
								<label><?php echo (isset($LANG['PREPARATIONS'])?$LANG['PREPARATIONS']:'Preparations'); ?>:</label>
								<?php echo $occArr['preparations']; ?>
							</div>
							<?php
						}
						$noteStr = '';
						if($occArr['occurrenceremarks']) $noteStr .= "; ".$occArr['occurrenceremarks'];
						if($occArr['establishmentmeans']) $noteStr .= "; ".$occArr['establishmentmeans'];
						if($occArr['cultivationstatus']) $noteStr .= "; Cultivated or Captive";
						if($noteStr){
							?>
							<div>
								<label><?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?>:</label>
								<?php echo substr($noteStr,2); ?>
							</div>
							<?php
						}
						if($occArr['disposition']){
							?>
							<div>
								<label><?php echo (isset($LANG['DISPOSITION'])?$LANG['DISPOSITION']:'Disposition'); ?>: </label>
								<?php echo $occArr['disposition']; ?>
							</div>
							<?php
						}
						if(isset($occArr['paleoid'])){
							?>
							<div>
								<label><?php echo (isset($LANG['PALEOTERMS'])?$LANG['PALEOTERMS']:'Paleontology Terms'); ?>: </label>
								<?php
								$paleoStr1 = '';
								if($occArr['eon']) $paleoStr1 .= '; '.$occArr['eon'];
								if($occArr['era']) $paleoStr1 .= '; '.$occArr['era'];
								if($occArr['period']) $paleoStr1 .= '; '.$occArr['period'];
								if($occArr['epoch']) $paleoStr1 .= '; '.$occArr['epoch'];
								if($occArr['stage']) $paleoStr1 .= '; '.$occArr['stage'];
								if($occArr['earlyinterval']) $paleoStr1 .= '; '.$occArr['earlyinterval'];
								if($occArr['lateinterval']) $paleoStr1 .= ' to '.$occArr['lateinterval'];
								if($paleoStr1) echo trim($paleoStr1,'; ');
								?>
								<div style="margin-left:10px">
									<?php
									if($occArr['absoluteage']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['ABSOLUTEAGE'])?$LANG['ABSOLUTEAGE']:'Absolute Age').':</label> '.$occArr['absoluteage'].'</div>';
									if($occArr['storageage']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['STORAGEAGE'])?$LANG['STORAGEAGE']:'Storage Age').':</label> '.$occArr['storageage'].'</div>';
									if($occArr['localstage']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['LOCALSTAGE'])?$LANG['LOCALSTAGE']:'Local Stage').':</label> '.$occArr['localstage'].'</div>';
									if($occArr['biota']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['BIOTA'])?$LANG['BIOTA']:'Biota').':</label> '.$occArr['biota'].'</div>';
									if($occArr['biostratigraphy']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['BIOSTRAT'])?$LANG['BIOSTRAT']:'Biostratigraphy').':</label> '.$occArr['biostratigraphy'].'</div>';
									if($occArr['lithogroup']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['GROUP'])?$LANG['GROUP']:'Group').':</label> '.$occArr['lithogroup'].'</div>';
									if($occArr['formation']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['FORMATION'])?$LANG['FORMATION']:'Formation').':</label> '.$occArr['formation'].'</div>';
									if($occArr['taxonenvironment']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['TAXENVIR'])?$LANG['TAXENVIR']:'Taxon Environment').':</label> '.$occArr['taxonenvironment'].'</div>';
									if($occArr['member']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['MEMBER'])?$LANG['MEMBER']:'Member').':</label> '.$occArr['member'].'</div>';
									if($occArr['bed']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['BED'])?$LANG['BED']:'Bed').':</label> '.$occArr['bed'].'</div>';
									if($occArr['lithology']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['LITHOLOGY'])?$LANG['LITHOLOGY']:'Lithology').':</label> '.$occArr['lithology'].'</div>';
									if($occArr['stratremarks']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['STRATREMARKS'])?$LANG['STRATREMARKS']:'Remarks').':</label> '.$occArr['stratremarks'].'</div>';
									if($occArr['element']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['ELEMENT'])?$LANG['ELEMENT']:'Element').':</label> '.$occArr['element'].'</div>';
									if($occArr['slideproperties']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['SLIDEPROPS'])?$LANG['SLIDEPROPS']:'Slide Properties').':</label> '.$occArr['slideproperties'].'</div>';
									if($occArr['geologicalcontextid']) echo '<div style="float:left;margin-right:25px"><label>'.(isset($LANG['CONTEXTID'])?$LANG['CONTEXTID']:'Context ID').':</label> '.$occArr['geologicalcontextid'].'</div>';
									?>
								</div>
							</div>
							<?php
						}
						if(isset($occArr['exs'])){
							?>
							<div>
								<label><?php echo (isset($LANG['EXSERIES'])?$LANG['EXSERIES']:'Exsiccati series'); ?>:</label>
								<?php
								echo '<a href="../exsiccati/index.php?omenid='.$occArr['exs']['omenid'].'" target="_blank">';
								echo $occArr['exs']['title'].'&nbsp;#'.$occArr['exs']['exsnumber'];
								echo '</a>';
								?>
							</div>
							<?php
						}
						?>
					</div>
					<div style="clear:both;margin-left:60px;">
						<?php
						if(array_key_exists('matSample',$occArr)){
							$matSampleArr = $occArr['matSample'];
							$msCnt = 0;
							$msKey = 0;
							echo '<fieldset><legend>'.(isset($LANG['MATERIAL_SAMPLES'])?$LANG['MATERIAL_SAMPLES']:'Material Samples').'</legend>';
							do{
								if($msKey = key($matSampleArr)){
									echo '<div class="mat-sample-div" style="'.($msCnt?'display:none':'').'">';
									foreach($matSampleArr[$msKey] as $msLabelKey => $msValue){
										if($msValue && isset($MS_LABEL_ARR[$msLabelKey])) echo '<div><label>'.$MS_LABEL_ARR[$msLabelKey].'</label>: '.$msValue.'</div>';
									}
									echo '<hr>';
									echo '</div>';
									if(!$msCnt && count($matSampleArr) > 1){
										echo '<div id="mat-sample-more-div" >';
										echo '<a href="#" onclick="displayAllMaterialSamples();return false;">';
										echo (isset($LANG['DISPLAY_ALL_MATERIAL_SAMPLES'])?$LANG['DISPLAY_ALL_MATERIAL_SAMPLES']:'Display all Material Sample units');
										echo '</a></div>';
									}
								}
								$msCnt++;
							}while(next($matSampleArr));
							echo '</fieldset>';
						}
						if(array_key_exists('imgs',$occArr)){
							$iArr = $occArr['imgs'];
							?>
							<fieldset>
								<legend><?php echo (isset($LANG['SPECIMAGES'])?$LANG['SPECIMAGES']:'Specimen Images'); ?></legend>
								<?php
								foreach($iArr as $imgArr){
									$thumbUrl = $imgArr['tnurl'];
									if(!$thumbUrl || substr($thumbUrl,0,7)=='process'){
										if($image = exif_thumbnail($imgArr['lgurl'])){
											$thumbUrl = 'data:image/jpeg;base64,'.base64_encode($image);
										}
										elseif($imgArr['url'] && substr($imgArr['url'],0,7)!='process') $thumbUrl = $imgArr['url'];
										else $thumbUrl = $imgArr['lgurl'];
									}
									?>
									<div class="imgDiv">
										<a href='<?php echo $imgArr['url']; ?>' target="_blank">
											<img border="1" src="<?php echo $thumbUrl; ?>" title="<?php echo $imgArr['caption']; ?>" style="max-width:170;" />
										</a>
										<?php
										if($imgArr['photographer']) echo '<div>'.(isset($LANG['AUTHOR'])?$LANG['AUTHOR']:'Author').': '.$imgArr['photographer'].'</div>';
										if($imgArr['url'] && substr($thumbUrl,0,7)!='process' && $imgArr['url'] != $imgArr['lgurl']) echo '<div><a href="'.$imgArr['url'].'" target="_blank">'.(isset($LANG['OPENMED'])?$LANG['OPENMED']:'Open Medium Image').'</a></div>';
										if($imgArr['lgurl']) echo '<div><a href="'.$imgArr['lgurl'].'" target="_blank">'.(isset($LANG['OPENLARGE'])?$LANG['OPENLARGE']:'Open Large Image').'</a></div>';
										if($imgArr['sourceurl']) echo '<div><a href="'.$imgArr['sourceurl'].'" target="_blank">'.(isset($LANG['OPENSRC'])?$LANG['OPENSRC']:'Open Source Image').'</a></div>';
										?>
									</div>
									<?php
								}
								?>
							</fieldset>
							<?php
						}
						//Rights
						$rightsStr = $collMetadata['rights'];
						if($collMetadata['rights']){
							$rightsHeading = '';
							if(isset($RIGHTS_TERMS)) $rightsHeading = array_search($rightsStr,$RIGHTS_TERMS);
							if(substr($collMetadata['rights'],0,4) == 'http'){
								$rightsStr = '<a href="'.$rightsStr.'" target="_blank">'.($rightsHeading?$rightsHeading:$rightsStr).'</a>';
							}
							$rightsStr = '<div style="margin-top:2px;"><label>'.(isset($LANG['USAGERIGHTS'])?$LANG['USAGERIGHTS']:'Usage Rights').':</label> '.$rightsStr.'</div>';
						}
						if($collMetadata['rightsholder']){
							$rightsStr .= '<div style="margin-top:2px;"><label>'.(isset($LANG['RIGHTSHOLDER'])?$LANG['RIGHTSHOLDER']:'Rights Holder').':</label> '.$collMetadata['rightsholder'].'</div>';
						}
						if($collMetadata['accessrights']){
							$rightsStr .= '<div style="margin-top:2px;"><label>'.(isset($LANG['ACCESSRIGHTS'])?$LANG['ACCESSRIGHTS']:'Access Rights').':</label> '.$collMetadata['accessrights'].'</div>';
						}
						?>
						<div style="margin:5px 0px 5px 0px;">
							<?php
							if($rightsStr) echo $rightsStr;
							else echo '<a href="../../includes/usagepolicy.php">'.(isset($LANG['USAGEPOLICY'])?$LANG['USAGEPOLICY']:'General Data Usage Policy').'</a>';
							?>
						</div>
						<div style="margin:3px 0px;"><?php echo '<label>'.(isset($LANG['RECORDID'])?$LANG['RECORDID']:'Record ID').': </label>'.$occArr['recordid']; ?></div>
						<?php
						if(isset($occArr['source'])){
							$recordType = (isset($occArr['source']['type'])?$occArr['source']['type']:'');
							$displayTitle = $LANG['SOURCE_RECORD'];
							if(isset($occArr['source']['title'])) $displayTitle = $occArr['source']['title'];
							$displayStr = $occArr['source']['url'];
							if(isset($occArr['source']['displayStr'])) $displayStr = $occArr['source']['displayStr'];
							elseif(isset($occArr['source']['sourceID'])) $displayStr = '#'.$occArr['source']['sourceID'];
							if($recordType == 'symbiota') echo '<fieldset><legend>Externally Managed Snapshot Record</legend>';
							echo '<div><label>'.$displayTitle.':</label> <a href="'.$occArr['source']['url'].'" target="_blank">'.$displayStr.'</a></div>';
							echo '<div style="float:left;">';
							if(isset($occArr['source']['sourceName'])){
								echo '<div>'.(isset($LANG['DATA_SOURCE'])?$LANG['DATA_SOURCE']:'Data source').': '.$occArr['source']['sourceName'].'</div>';
								if($recordType == 'symbiota') echo '<div><label>Source management: </label>Live managed record within a Symbiota portal</div>';
							}
							if(array_key_exists('fieldsModified',$_POST)){
								echo '<div>'.(isset($LANG['REFRESH_DATE'])?$LANG['REFRESH_DATE']:'Last refresh date').': '.(isset($occArr['source']['refreshTimestamp'])?$occArr['source']['refreshTimestamp']:'').'</div>';
								//Input from refersh event
								$dataStatus = filter_var($_POST['dataStatus'], FILTER_SANITIZE_STRING);
								$fieldsModified = filter_var($_POST['fieldsModified'], FILTER_SANITIZE_STRING);
								$sourceDateLastModified = filter_var($_POST['sourceDateLastModified'], FILTER_SANITIZE_STRING);
								echo '<div>'.(isset($LANG['UPDATE_STATUS'])?$LANG['UPDATE_STATUS']:'Update status').': '.$dataStatus.'</div>';
								echo '<div>'.(isset($LANG['FIELDS_MODIFIED'])?$LANG['FIELDS_MODIFIED']:'Fields modified').': '.$fieldsModified.'</div>';
								echo '<div>'.(isset($LANG['SOURCE_DATE_LAST_MODIFIED'])?$LANG['SOURCE_DATE_LAST_MODIFIED']:'Source date last modified').': '.$sourceDateLastModified.'</div>';
							}
							echo '</div>';
							if($SYMB_UID && $recordType == 'symbiota'){
								?>
								<div style="float:left;margin-left:30px;">
									<button name="formsubmit" type="submit" onclick="refreshRecord(<?php echo $occid; ?>)">Refresh Record</button>
								</div>
								<form id="refreshForm" action="index.php" method="post">
									<input id="dataStatus" name="dataStatus" type="hidden" value="" >
									<input id="fieldsModified" name="fieldsModified" type="hidden" value="" >
									<input id="sourceDateLastModified" name="sourceDateLastModified" type="hidden" value="" >
									<input name="occid" type="hidden" value="<?php echo $occid; ?>" >
									<input name="clid" type="hidden" value="<?php echo $clid; ?>" >
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" >
								</form>
								<?php
							}
							if($recordType == 'symbiota') echo '</fieldset>';
						}
						?>
						<div style="margin-top:10px;clear:both;">
							<?php
							if($collMetadata['contact']){
								echo (isset($LANG['ADDITIONALINFO'])?$LANG['ADDITIONALINFO']:'For additional information about this specimen, please contact').': '.$collMetadata['contact'];
								if($collMetadata['email']){
									$emailSubject = $DEFAULT_TITLE.' occurrence: '.$occArr['catalognumber'].' ('.$occArr['othercatalognumbers'].')';
									$refPath = $indManager->getDomain().$CLIENT_ROOT.'/collections/individual/index.php?occid='.$occArr['occid'];
									$emailBody = (isset($LANG['SPECREFERENCED'])?$LANG['SPECREFERENCED']:'Specimen being referenced').': '.$refPath;
									$emailRef = 'subject='.$emailSubject.'&cc='.$ADMIN_EMAIL.'&body='.$emailBody;
									echo ' (<a href="mailto:'.$collMetadata['email'].'?'.$emailRef.'">'.$collMetadata['email'].'</a>)';
								}
							}
							?>
						</div>
						<?php
						if($isEditor || ($collMetadata['publicedits'])){
							?>
							<div style="margin-bottom:10px;">
								<?php
								if($SYMB_UID){
									echo (isset($LANG['SEEERROR'])?$LANG['SEEERROR']:'Do you see an error? If so, errors can be fixed using the').' ';
									?>
									<a href="../editor/occurrenceeditor.php?occid=<?php echo $occArr['occid'];?>">
										<?php echo (isset($LANG['OCCEDITOR'])?$LANG['OCCEDITOR']:'Occurrence Editor'); ?>.
									</a>
									<?php
								}
								else{
									echo (isset($LANG['SEEANERROR'])?$LANG['SEEANERROR']:'See an error?'); ?> <a href="../../profile/index.php?refurl=../collections/individual/index.php?occid=<?php echo $occid; ?>"><?php echo (isset($LANG['LOGIN'])?$LANG['LOGIN']:'Log In'); ?></a> <?php echo (isset($LANG['TOEDITDATA'])?$LANG['TOEDITDATA']:'to edit data');
								}
								?>
							</div>
							<?php
						}
						if(array_key_exists('ref',$occArr)){
							?>
							<fieldset>
								<legend><?php echo (isset($LANG['ASSOCREFS'])?$LANG['ASSOCREFS']:'Associated References'); ?></legend>
								<?php
								foreach($occArr['ref'] as $refid => $refArr){
									echo '<div class="occur-ref">';
									if($refArr['url']) echo '<a href="'.$refArr['url'].'" target="_blank">';
									echo $refArr['display'];
									if($refArr['url']) echo '</a>';
									echo '</div>';
								}
								?>
							</fieldset>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				if($displayMap){
					?>
					<div id="maptab">
						<div id='map_canvas' style='width:100%;height:600px;'></div>
					</div>
					<?php
				}
				if($genticArr){
					?>
					<div id="genetictab">
						<?php
						foreach($genticArr as $gArr){
							?>
							<div style="margin:15px;">
								<div style="font-weight:bold;margin-bottom:5px;"><?php echo $gArr['name']; ?></div>
								<div style="margin-left:15px;"><label><?php echo (isset($LANG['IDENTIFIER'])?$LANG['IDENTIFIER']:'Identifier'); ?>:</label> <?php echo $gArr['id']; ?></div>
								<div style="margin-left:15px;"><label><?php echo (isset($LANG['LOCUS'])?$LANG['LOCUS']:'Locus'); ?>:</label> <?php echo $gArr['locus']; ?></div>
								<div style="margin-left:15px;">
									<label>URL:</label>
									<a href="<?php echo $gArr['resourceurl']; ?>" target="_blank"><?php echo $gArr['resourceurl']; ?></a>
								</div>
								<div style="margin-left:15px;"><label><?php echo (isset($LANG['NOTES'])?$LANG['NOTES']:'Notes'); ?>:</label> <?php echo $gArr['notes']; ?></div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				if($dupClusterArr){
					?>
					<div id="dupestab">
						<div style="margin:15px;">
							<div class="title" style="margin-bottom:10px;text-decoration:underline"><?php echo (isset($LANG['CURRENTRECORD'])?$LANG['CURRENTRECORD']:'Current Record'); ?></div>
							<?php
							echo '<div class="title">'.$collMetadata['collectionname'].' ('.$collMetadata['institutioncode'].($collMetadata['collectioncode']?':'.$collMetadata['collectioncode']:'').')</div>';
							echo '<div style="margin:5px 15px">';
							if($occArr['recordedby']) echo '<div>'.$occArr['recordedby'].' '.$occArr['recordnumber'].'<span style="margin-left:40px;">'.$occArr['eventdate'].'</span></div>';
							if($occArr['catalognumber']) echo '<div><span class="label">'.(isset($LANG['CATALOGNUMBER'])?$LANG['CATALOGNUMBER']:'Catalog Number').':</span> '.$occArr['catalognumber'].'</div>';
							if($occArr['occurrenceid']) echo '<div><span class="label">'.(isset($LANG['GUID'])?$LANG['GUID']:'GUID').':</span> '.$occArr['occurrenceid'].'</div>';
							echo '<div><span class="label">'.(isset($LANG['LATESTID'])?$LANG['LATESTID']:'Latest Identification').':</span> ';
							if(!isset($occArr['taxonsecure'])) echo '<i>'.$occArr['sciname'].'</i> '.$occArr['scientificnameauthorship'];
							else echo (isset($LANG['SPECIDPROTECTED'])?$LANG['SPECIDPROTECTED']:'Species identification protected');
							echo '</div>';
							if($occArr['identifiedby']) echo '<div><span class="label">'.(isset($LANG['IDENTIFIEDBY'])?$LANG['IDENTIFIEDBY']:'Identified by').':</span> '.$occArr['identifiedby'].'<span stlye="margin-left:30px;">'.$occArr['dateidentified'].'</span></div>';
							echo '</div>';
							//Grab other records
							foreach($dupClusterArr as $dupeType => $dupeArr){
								echo '<fieldset style="padding:10px">';
								echo '<legend>';
								if($dupeType=='dupe') echo (isset($LANG['DUPES'])?$LANG['DUPES']:'Specimen Duplicates');
								else echo (isset($LANG['EXSICCATAE'])?$LANG['EXSICCATAE']:'Associated Exsiccatae');
								echo '</legend>';
								foreach($dupeArr as $dupOccid => $dupArr){
									if($dupOccid != $occid){
										echo '<div style="clear:both;margin:15px;">';
										echo '<div style="float:left;margin:5px 15px">';
										echo '<div class="title">'.$dupArr['collname'].' ('.$dupArr['instcode'].($dupArr['collcode']?':'.$dupArr['collcode']:'').')</div>';
										if($dupArr['recordedby']) echo '<div>'.$dupArr['recordedby'].' '.$dupArr['recordnumber'].'<span style="margin-left:40px;">'.$dupArr['eventdate'].'</span></div>';
										if($dupArr['catalognumber']) echo '<div><span class="label">'.(isset($LANG['CATALOGNUMBER'])?$LANG['CATALOGNUMBER']:'Catalog Number').':</span> '.$dupArr['catalognumber'].'</div>';
										if($dupArr['occurrenceid']) echo '<div><span class="label">'.(isset($LANG['GUID'])?$LANG['GUID']:'GUID').':</span> '.$dupArr['occurrenceid'].'</div>';
										echo '<div><span class="label">'.(isset($LANG['LATESTID'])?$LANG['LATESTID']:'Latest Identification').':</span> ';
										if(!isset($occArr['taxonsecure'])) echo '<i>'.$dupArr['sciname'].'</i> '.$dupArr['author'];
										else echo (isset($LANG['SPECIDPROTECTED'])?$LANG['SPECIDPROTECTED']:'Species identification protected');
										echo '</div>';
										if($dupArr['identifiedby']) echo '<div><span class="label">'.(isset($LANG['IDENTIFIEDBY'])?$LANG['IDENTIFIEDBY']:'Identified by').':</span> '.$dupArr['identifiedby'].'<span stlye="margin-left:30px;">'.$dupArr['dateidentified'].'</span></div>';
										echo '<div><a href="#" onclick="openIndividual('.$dupOccid.');return false;">'.(isset($LANG['SHOWFULLDETAILS'])?$LANG['SHOWFULLDETAILS']:'Show Full Details').'</a></div>';
										echo '</div>';
										if(!isset($occArr['taxonsecure']) && !isset($occArr['localsecure'])){
											if($dupArr['url']){
												$url = $dupArr['url'];
												if($IMAGE_DOMAIN) if(substr($url,0,1) == '/') $url = $IMAGE_DOMAIN.$url;
												echo '<div style="float:right;margin:10px;"><img src="'.$url.'" style="width:70px;border:1px solid grey" /></div>';
											}
										}
										echo '<div style="margin:10px 0px;clear:both"><hr/></div>';
										echo '</div>';
									}
								}
								echo '</fieldset>';
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
				<div id="commenttab">
					<?php
					$commentTabIndex = 1;
					if($displayMap) $commentTabIndex++;
					if($genticArr) $commentTabIndex++;
					if($dupClusterArr) $commentTabIndex++;
					if($commentArr){
						echo '<div><label>'.count($commentArr).' '.(isset($LANG['COMMENTS'])?$LANG['COMMENTS']:'Comments').'</label></div>';
						echo '<hr style="color:gray;"/>';
						foreach($commentArr as $comId => $comArr){
							?>
							<div style="margin:15px;">
								<?php
								echo '<div>';
								echo '<b>'.$comArr['username'].'</b> <span style="color:gray;">posted '.$comArr['initialtimestamp'].'</span>';
								echo '</div>';
								if($comArr['reviewstatus'] == 0 || $comArr['reviewstatus'] == 2)
									echo '<div style="color:red;">'.(isset($LANG['COMMENTNOTPUBLIC'])?$LANG['COMMENTNOTPUBLIC']:'Comment not public due to pending abuse report (viewable to administrators only)').'</div>';
								echo '<div style="margin:10px;">'.$comArr['comment'].'</div>';
								if($comArr['reviewstatus']){
									if($SYMB_UID){
									    echo '<div><a href="index.php?repcomid='.$comId.'&occid='.$occid.'&tabindex='.$commentTabIndex.'">';
										echo (isset($LANG['REPORT'])?$LANG['REPORT']:'Report as inappropriate or abusive');
										echo '</a></div>';
									}
								}
								else{
								    echo '<div><a href="index.php?publiccomid='.$comId.'&occid='.$occid.'&tabindex='.$commentTabIndex.'">';
									echo (isset($LANG['MAKECOMPUB'])?$LANG['MAKECOMPUB']:'Make comment public');
									echo '</a></div>';
								}
								if($isEditor || ($SYMB_UID && $comArr['username'] == $PARAMS_ARR['un'])){
									?>
									<div style="margin:20px;">
										<form name="delcommentform" action="index.php" method="post" onsubmit="return confirm('<?php echo (isset($LANG['CONFIRMDELETE'])?$LANG['CONFIRMDELETE']:'Are you sure you want to delete comment'); ?>?')">
											<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
											<input name="comid" type="hidden" value="<?php echo $comId; ?>" />
											<input name="tabindex" type="hidden" value="<?php echo $commentTabIndex; ?>" />
											<button name="formsubmit" type="submit" value="deleteComment"><?php echo (isset($LANG['DELETECOMMENT'])?$LANG['DELETECOMMENT']:'Delete Comment'); ?></button>
										</form>
									</div>
									<?php
								}
								?>
							</div>
							<hr style="color:gray;"/>
							<?php
						}
					}
					else echo '<div class="title" style="margin:20px;">'.(isset($LANG['NOCOMMENTS'])?$LANG['NOCOMMENTS']:'No comments have been submitted').'</div>';
					?>
					<fieldset>
						<legend><?php echo (isset($LANG['NEWCOMMENT'])?$LANG['NEWCOMMENT']:'New Comment'); ?></legend>
						<?php
						if($SYMB_UID){
							?>
							<form name="commentform" action="index.php" method="post" onsubmit="return verifyCommentForm(this);">
								<textarea name="commentstr" rows="8" style="width:98%;"></textarea>
								<div style="margin:15px;">
									<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
									<input name="tabindex" type="hidden" value="<?php echo $commentTabIndex; ?>" />
									<button type="submit" name="formsubmit" value="submitComment"><?php echo (isset($LANG['SUBMITCOMMENT'])?$LANG['SUBMITCOMMENT']:'Submit Comment'); ?></button>
								</div>
								<div>
									<?php echo (isset($LANG['MESSAGEWARNING'])?$LANG['MESSAGEWARNING']:'Messages over 500 words long may be automatically truncated. All comments are moderated.'); ?>
								</div>
							</form>
							<?php
						}
						else{
							echo '<div style="margin:10px;">';
							echo '<a href="../../profile/index.php?refurl=../collections/individual/index.php?tabindex=2&occid='.$occid.'">';
							echo (isset($LANG['LOGIN'])?$LANG['LOGIN']:'Log In');
							echo '</a> ';
							echo (isset($LANG['TOLEAVECOMMENT'])?$LANG['TOLEAVECOMMENT']:'to leave a comment.');
							echo '</div>';
						}
						?>
					</fieldset>
				</div>
				<?php
				if($traitArr){
				    ?>
					<div id="traittab">
						<?php
						foreach($traitArr as $traitID => $tArr){
							if(!$tArr['depStateID']){
								echo '<div class="traitDiv">';
								$indManager->echoTraitUnit($traitArr[$traitID]);
								$indManager->echoTraitDiv($traitArr,$traitID);
								echo '</div>';
							}
						}
						?>
					</div>
					<?php
				}
				if($isEditor){
					?>
					<div id="edittab">
						<div style="padding:15px;">
							<?php
							/*
							 if($USER_RIGHTS && array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
								?>
								<div style="float:right;" title="Manage Edits">
									<a href="../editor/editreviewer.php?collid=<?php echo $collid.'&occid='.$occid; ?>"><img src="../../images/edit.png" style="border:0px;width:14px;" /></a>
								</div>
								<?php
							}
							*/
							echo '<div style="margin:20px 0px 30px 0px;">';
							echo '<label>'.(isset($LANG['ENTEREDBY'])?$LANG['ENTEREDBY']:'Entered By').'</label> '.($occArr['recordenteredby']?$occArr['recordenteredby']:'not recorded').'<br/>';
							echo '<label>'.(isset($LANG['DATEENTERED'])?$LANG['DATEENTERED']:'Date entered').':</label> '.($occArr['dateentered']?$occArr['dateentered']:'not recorded').'<br/>';
							echo '<label>'.(isset($LANG['DATEMODIFIED'])?$LANG['DATEMODIFIED']:'Date modified').':</label> '.($occArr['datelastmodified']?$occArr['datelastmodified']:'not recorded').'<br/>';
							if($occArr['modified'] && $occArr['modified'] != $occArr['datelastmodified']) echo '<label>'.(isset($LANG['SRCDATEMODIFIED'])?$LANG['SRCDATEMODIFIED']:'Source date modified').':</label> '.$occArr['modified'];
							echo '</div>';
							//Display edits
							$editArr = $indManager->getEditArr();
							$externalEdits = $indManager->getExternalEditArr();
							if($editArr || $externalEdits){
								if($editArr){
									?>
									<fieldset>
										<legend><?php echo (isset($LANG['INTERNALEDITS'])?$LANG['INTERNALEDITS']:'Internal Edits'); ?></legend>
										<?php
										foreach($editArr as $k => $eArr){
											$reviewStr = 'OPEN';
											if($eArr['reviewstatus'] == 2) $reviewStr = 'PENDING';
											elseif($eArr['reviewstatus'] == 3) $reviewStr = 'CLOSED';
											?>
											<div>
												<label><?php echo (isset($LANG['EDITOR'])?$LANG['EDITOR']:'Editor'); ?>:</label> <?php echo $eArr['editor']; ?>
												<span style="margin-left:30px;"><label>Date:</label> <?php echo $eArr['ts']; ?></span>
											</div>
											<div>
												<span><label><?php echo (isset($LANG['APPLIEDSTATUS'])?$LANG['APPLIEDSTATUS']:'Applied Status'); ?>:</label> <?php echo ($eArr['appliedstatus']?(isset($LANG['APPLIED'])?$LANG['APPLIED']:'applied'):(isset($LANG['NOTAPPLIED'])?$LANG['NOTAPPLIED']:'not applied')); ?></span>
												<span style="margin-left:30px;"><label><?php echo (isset($LANG['REVIEWSTATUS'])?$LANG['REVIEWSTATUS']:'Review Status'); ?>:</label> <?php echo $reviewStr; ?></span>
											</div>
											<?php
											$edArr = $eArr['edits'];
											foreach($edArr as $vArr){
												echo '<div style="margin:15px;">';
												echo '<label>'.(isset($LANG['FIELD'])?$LANG['FIELD']:'Field').':</label> '.$vArr['fieldname'].'<br/>';
												echo '<label>'.(isset($LANG['OLDVALUE'])?$LANG['OLDVALUE']:'Old Value').':</label> '.$vArr['old'].'<br/>';
												echo '<label>'.(isset($LANG['NEWVALUE'])?$LANG['NEWVALUE']:'New Value').':</label> '.$vArr['new'].'<br/>';
												echo '</div>';
											}
											echo '<div style="margin:15px 0px;"><hr/></div>';
										}
										?>
									</fieldset>
									<?php
								}
								if($externalEdits){
									?>
									<fieldset>
										<legend><?php echo (isset($LANG['EXTERNALEDITS'])?$LANG['EXTERNALEDITS']:'External Edits').':'; ?></legend>
										<?php
										foreach($externalEdits as $orid => $eArr){
											foreach($eArr as $appliedStatus => $eArr2){
												$reviewStr = 'OPEN';
												if($eArr2['reviewstatus'] == 2) $reviewStr = 'PENDING';
												elseif($eArr2['reviewstatus'] == 3) $reviewStr = 'CLOSED';
												?>
												<div>
													<label><?php echo (isset($LANG['EDITOR'])?$LANG['EDITOR']:'Editor').':'; ?></label> <?php echo $eArr2['editor']; ?>
													<span style="margin-left:30px;"><label><?php echo (isset($LANG['DATE'])?$LANG['DATE']:'Date'); ?>:</label> <?php echo $eArr2['ts']; ?></span>
													<span style="margin-left:30px;"><label><?php echo (isset($LANG['SOURCE'])?$LANG['SOURCE']:'Source'); ?>:</label> <?php echo $eArr2['source']; ?></span>
												</div>
												<div>
													<span><label><?php echo (isset($LANG['APPLIEDSTATUS'])?$LANG['APPLIEDSTATUS']:'Applied Status'); ?>:</label> <?php echo ($appliedStatus?'applied':'not applied'); ?></span>
													<span style="margin-left:30px;"><label><?php echo (isset($LANG['REVIEWSTATUS'])?$LANG['REVIEWSTATUS']:'Review Status'); ?>:</label> <?php echo $reviewStr; ?></span>
												</div>
												<?php
												$edArr = $eArr2['edits'];
												foreach($edArr as $fieldName => $vArr){
													echo '<div style="margin:15px;">';
													echo '<label>'.(isset($LANG['FIELD'])?$LANG['FIELD']:'Field').':</label> '.$fieldName.'<br/>';
													echo '<label>'.(isset($LANG['OLDVALUE'])?$LANG['OLDVALUE']:'Old Value').':</label> '.$vArr['old'].'<br/>';
													echo '<label>'.(isset($LANG['NEWVALUE'])?$LANG['NEWVALUE']:'New Value').':</label> '.$vArr['new'].'<br/>';
													echo '</div>';
												}
												echo '<div style="margin:15px 0px;"><hr/></div>';
											}
										}
										?>
									</fieldset>
									<?php
								}
							}
							else{
								echo '<div style="margin:25px 0px;font-weight:bold">'.(isset($LANG['NOTEDITED'])?$LANG['NOTEDITED']:'Record has not been edited since being entered').'</div>';
							}
							echo '<div style="margin:15px">'.(isset($LANG['EDITNOTE'])?$LANG['EDITNOTE']:'Note: Edits are only viewable by collection administrators and editors').'</div>';
							//Display Access Stats
							$accessStats = $indManager->getAccessStats();
							if($accessStats){
								echo '<div style="margin-top:30px"><b>Access Stats</b></div>';
								echo '<table class="styledtable" style="font-size:100%;width:300px;">';
								echo '<tr><th>'.(isset($LANG['YEAR'])?$LANG['YEAR']:'Year').'</th><th>'.(isset($LANG['ACCESSTYPE'])?$LANG['ACCESSTYPE']:'Access Type').'</th><th>'.(isset($LANG['COUNT'])?$LANG['COUNT']:'Count').'</th></tr>';
								foreach($accessStats as $accessDate => $arr1){
									foreach($arr1 as $accessType => $accessCnt){
										echo '<tr><td>'.$accessDate.'</td><td>'.$accessType.'</td><td>'.$accessCnt.'</td></tr>';
									}
								}
								echo '</table>';
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
		else{
			?>
			<h2><?php echo (isset($LANG['UNABLETOLOCATE'])?$LANG['UNABLETOLOCATE']:'Unable to locate occurrence record'); ?></h2>
			<div style="margin:20px">
				<div><?php echo (isset($LANG['CHECKING'])?$LANG['CHECKING']:'Checking archive'); ?>...</div>
				<div style="margin:10px">
					<?php
					ob_flush();
					flush();
					$rawArchArr = $indManager->checkArchive();
					if($rawArchArr && $rawArchArr['obj']){
						$archArr = $rawArchArr['obj'];
						if($isEditor){
							?>
							<div style="float:right">
								<form name="restoreForm" action="index.php" method="post">
									<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<button name="formsubmit" type="submit" value="restoreRecord">Restore Record</button>
								</form>
							</div>
							<?php
						}
						if(isset($archArr['dateDeleted'])) echo '<div style="margin-bottom:10px"><label>'.(isset($LANG['RECORDDELETED'])?$LANG['RECORDDELETED']:'Record deleted').':</label> '.$archArr['dateDeleted'].'</div>';
						if($rawArchArr['notes']) echo '<div style="margin-left:15px"><label>'.(isset($LANG['NOTES'])?$LANG['NOTES']:'Notes').': </label>'.$rawArchArr['notes'].'</div>';
						echo '<table class="styledtable"><tr><th>'.(isset($LANG['FIELD'])?$LANG['FIELD']:'Field').'</th><th>'.(isset($LANG['VALUE'])?$LANG['VALUE']:'Value').'</th></tr>';
						foreach($archArr as $f => $v){
							if(!is_array($v)){
								echo '<tr><td style="width:175px;">'.$f.'</td><td>';
								if(is_array($v)) echo implode(', ',$v);
								else echo $v;
								echo '</td></tr>';
							}
						}
						$extArr = array('dets'=>'identifications','imgs'=>'Images','assoc'=>'Occurrence<br/>Associations','exsiccati'=>'Exsiccati','paleo'=>'Paleontological<br/>Terms','matSample'=>'Material<br/>Sample');
						foreach($extArr as $extName => $extDisplay){
							if(isset($archArr[$extName]) && $archArr[$extName]){
								echo '<tr><td>'.$extDisplay.'</td><td>';
								foreach($archArr[$extName] as $extKey => $extValue){
									if(is_array($extValue)){
										echo '<label>'.(isset($LANG['RECORDID'])?$LANG['RECORDID']:'Record ID').': '.$extKey.'</label><br/>';
										foreach($extValue as $f => $v){
											echo $f.': '.$v.'<br/>';
										}
										echo '<br/>';
									}
									else echo $extKey.': '.$extValue.'<br/>';
								}
								echo '</td></tr>';
							}
						}
						echo '</table>';
					}
					else echo (isset($LANG['UNABLETOLOCATE'])?$LANG['UNABLETOLOCATE']:'Unable to locate record');
					?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</body>
</html>