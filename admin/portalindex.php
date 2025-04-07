<?php
require_once('../config/symbini.php');
require_once($SERVER_ROOT.'/classes/PortalIndex.php');
//include_once($SERVER_ROOT.'/content/lang/admin/portalindex.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../admin/portalindex.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$portalID = array_key_exists('portalid', $_REQUEST) ? filter_var($_REQUEST['portalid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$remoteCollID = array_key_exists('remoteid', $_REQUEST) ? filter_var($_REQUEST['remoteid'], FILTER_SANITIZE_NUMBER_INT) : 0;
$remotePath = array_key_exists('remotePath', $_POST) ? filter_var($_POST['remotePath'], FILTER_SANITIZE_URL) : '';
$formSubmit = array_key_exists('formsubmit', $_REQUEST) ? $_REQUEST['formsubmit'] : '';

$portalManager = new PortalIndex();
$indexArr = $portalManager->getPortalIndexArr($portalID);

$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;
?>
<html lang="en">
	<head>
		<title><?= $DEFAULT_TITLE; ?> Portal Index Control Panel</title>
		<link href="<?= $CSS_BASE_PATH; ?>/jquery-ui.css" type="text/css" rel="stylesheet">
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-3.7.1.min.js" type="text/javascript"></script>
		<script src="<?= $CLIENT_ROOT; ?>/js/jquery-ui.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			let portalObj = {};
			let dataTime = "";

			<?php
			foreach($indexArr as $pid => $portalObj){
				echo 'portalObj['.$pid.'] = {"name": "'.$portalObj['portalName'].'","url": "'.$portalObj['urlRoot'].'"};'."\n";
			}

			if($portalID) echo 'displayPortalDetails('.$portalID.');';
			?>

			function validateHandshakeForm(f){
				if(f.remotePath.value == ""){
					alert("Enter URL to remote portal base index page");
					return false;
				}
				return true;
			}

			function searchPortals(f){
				$("[id^=occur-div-]").text("");
				let searchObj = {};
				if(f.sciname.value != "") searchObj.sciname = f.sciname.value;
				if(f.country.value != "") searchObj.country = f.country.value;
				if(f.stateProvince.value != "") searchObj.stateProvince = f.stateProvince.value;
				if(f.county.value != "") searchObj.county = f.county.value;
				Object.keys(portalObj).forEach(function(key, index) {
					portalQuery(key, this[key].name, this[key].url, searchObj);
				}, portalObj);
			}

			function portalQuery(portalID, portalName, portalUrl, searchObj){
				$("#occur-div-" + portalID).append('Searching... ');
				let urlFrag = "";
				Object.keys(searchObj).forEach(function(key, index) {
					let fieldName = key;
					if(fieldName == "sciname") fieldName = "taxa";
					urlFrag = urlFrag + "&" + fieldName + "=" + searchObj[key];
				});
				searchObj.limit = 1;
				searchObj.offset = 0;
				$.ajax({
					method: "GET",
					data: searchObj,
					dataType: "json",
					url: portalUrl + "/api/v2/occurrence/search"
				})
				.done(function(jsonRes) {
					$("#occur-div-"+portalID).append(jsonRes.count.toLocaleString() + " occurrences");
					if(jsonRes.count > 0){
						addLink(portalID, portalUrl + "/collections/list.php?usethes=1&taxontype=2" + urlFrag, "Query Results");
						addLink(portalID, portalUrl + "/collections/map/index.php?usethes=1&taxontype=2" + urlFrag, "Simple Map");
						addLink(portalID, portalUrl + "/collections/map/index.php?gridSizeSetting=60" + urlFrag, "Dynamic Map");
						let downloadUrl = portalUrl + "/collections/download/index.php?searchvar=" + urlFrag.replace("=", "%3D");
						$("#occur-div-"+portalID).append('<div class="occur-sub-div"><a href="#" onclick="openPopup(\''+downloadUrl+'\');return false;">Download Results</a></div>');
					}
				})
				.fail(function( jqXHR, textStatus ) {
					$("#occur-div-"+portalID).append(" ERROR ("+textStatus+")");
				});
			}

			function addLink(portalID, url, text){
				$("#occur-div-"+portalID).append('<div class="occur-sub-div"><a href="'+url+'" target="_blank">'+text+'</a></div>');
			}

			function openPopup(url){
				let popupWidth = 1100;
				if(document.body.offsetWidth < popupWidth) popupWidth = document.body.offsetWidth*0.9;
				let newWindow = window.open(url,'downloadPane','scrollbars=1,toolbar=0,resizable=1,width='+(popupWidth)+',height=700,left=20,top=20');
				if (newWindow.opener == null) newWindow.opener = self;
			}

			function setAllPortalDetails(){
				Object.keys(portalObj).forEach(function(key, index) {
					displayPortalDetails(key);
				}, portalObj);
			}

			function displayPortalDetails(pid){
				if(dataTime == ""){
					let today = new Date();
					let dd = String(today.getDate()).padStart(2, '0');
					let mm = String(today.getMonth() + 1).padStart(2, '0');
					let yyyy = today.getFullYear();
					//dataTime = yyyy + "-" + mm + "-" + dd + " " + today.toTimeString();
					dataTime = yyyy + "-" + mm + "-" + dd + " " + today.toLocaleTimeString();
				}
				$('#portal-div-'+pid).show();
				setPortalDetails(pid);
			}

			function setPortalDetails(pid){
				$.ajax({
					method: "GET",
					dataType: "json",
					url: portalObj[pid].url + "/api/v2/installation/ping"
				})
				.done(function(jsonRes) {
					$("#status-div-"+pid+" span").text("Success, online!");
					$("#status-div-"+pid+" span").css("color", "green");
					$("#guid-div-"+pid+" span").text(jsonRes.guid);
					$("#manager-div-"+pid+" span").text(jsonRes.managerEmail);
					$("#version-div-"+pid+" span").text(jsonRes.symbiotaVersion);
					$("#lastupdate-div-"+pid+" span").text(dataTime);
				})
				.fail(function( jqXHR, textStatus ) {
					$("#status-div-"+pid+" span").text("Failed!");
					$("#status-div-"+pid+" span").css("color", "red");
				});
			}

			function validateSearchForm(f){
				formIsValid = false;
				if(f.sciname.value == ""){
					formIsValid = true;
				}
				else if(f.country.value == ""){
					formIsValid = true;
				}
				else if(f.stateProvince.value == ""){
					formIsValid = true;
				}
				else if(f.county.value == ""){
					formIsValid = true;
				}
				if(!formIsValid){
					alert("Enter a term into at least one field");
					return false;
				}
				return formIsValid;
			}
		</script>
		<style type="text/css">
			fieldset{ margin:20px; padding:15px; }
			legend{ font-weight: bold; }
			label{  }
			button{ margin: 20px; }
			hr{ margin-top: 15px; margin-bottom: 15px; }
			.from-section{  }
			.form-section span{ right-margin: 10px; }
			form label{ font-weight: bold }
			.occur-sub-div{ margin-left: 15px; }
			.portalList-div{ margin: 10px 20px; }
			.portalName-div{ margin-top: 10px; font-weight: bold; }
			.portalName-div img{ width: 12px; }
			#table-div{ margin: 15px; }
		</style>
	</head>
	<body>
		<?php
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<b><a href="portalindex.php">Portal Index Control Panel</a></b>
		</div>
		<div role="main" id="innertext">
			<?php
			if(!isset($GLOBALS['ACTIVATE_PORTAL_INDEX'])){
				echo 'This feature has not yet been activated within this portal';
			}
			elseif($isEditor){
				if($formSubmit != 'listCollections'){
					if($formSubmit){
						echo '<fieldset>';
						echo '<legend>Action Panel</legend>';
						if($formSubmit == 'importProfile'){
							if($collid = $portalManager->importProfile($portalID, $remoteCollID)) echo '<div><a href="../collections/misc/collprofiles.php?collid='.$collid.'" target="_blank">New snapshot collection created</a></div>';
							else echo '<div>failed to insert new collections: '.$portalManager->getErrorMessage().'</div>';
						}
						elseif($formSubmit == 'initiateHandshake'){
							if($resArr = $portalManager->initiateHandshake($remotePath)){
								if($resArr['status']) echo '<div>Success - handshake successful: '.$resArr['message'].'</div>';
								else echo '<div>ERROR - handshake failed: '.$resArr['message'].'</div>';
								//print_r($resArr);
							}
							else echo '<div>ERROR initiating handshake: '.$portalManager->getErrorMessage().'</div>';
						}
						echo '</fieldset>';
					}
					if($IS_ADMIN){
						$selfArr = $portalManager->getSelfDetails();
						?>
						<fieldset id="admin-container">
							<legend>Current Portal Details</legend>
							<div class="from-section"><label>Portal title:</label> <?= $selfArr['portalName']; ?></div>
							<div class="from-section"><label>Endpoint:</label> <?= $selfArr['urlRoot']; ?></div>
							<div class="from-section"><label>Global Unique Identifier:</label> <?= $selfArr['guid']; ?></div>
							<div class="from-section"><label>Manager email:</label> <?= $selfArr['managerEmail']; ?></div>
							<div class="from-section"><label>Software version:</label> <?= $selfArr['symbiotaVersion']; ?></div>
							<hr />
							<div class="handshake-div"><a href="#" onclick="$('.handshake-div').toggle(); return false;">Initiate Handshake with External Portal</a></div>
							<div class="handshake-div" style="display:none">
								<form action="portalindex.php" method="post" onsubmit="return validateHandshakeForm(this)">
									<div class="from-section"><label>Path to Remote Portal:</label> <input name="remotePath" type="text" value="<?= $remotePath; ?>" style="width: 500px" /></div>
									<div class="from-section"><button name="formsubmit" type="submit" value="initiateHandshake">Initiate Handshake</button></div>
								</form>
							</div>
						</fieldset>
						<?php
					}
				}
				?>
				<fieldset>
					<legend>Portal Index</legend>
					<?php
					if(!$portalID){
						?>
						<div>
							<div>
								<form id="searchPanelForm" name="searchPanelForm" onSubmit="return validateSearchForm(this)" >
									<fieldset>
										<legend>Portal Search</legend>
										<div class="form-section">
											<label for="sciname">Scientific Name:</label>
											<input id="sciname" name="sciname" type="text" style="width: 250px" >
										</div>
										<div class="form-section">
											<span>
												<label for="country">Country:</label>
												<input id="country" name="country" type="text" >
											</span>
											<span>
												<label for="stateProvince">State/Province:</label>
												<input id="stateProvince" name="stateProvince" type="text" >
											</span>
											<span>
												<label for="county">County:</label>
												<input id="county" name="county" type="text" >
											</span>
										</div>
										<div class="form-section">
											<button id="taxonSearchButton" name="taxonSearch" type="button" onclick="searchPortals(this.form)">Search Portals</button>
										</div>
									</fieldset>
								</form>
							</div>
							<div>
								<button name="displayAllDetails" type="button" onclick="setAllPortalDetails()">Display Details for All Portals</button>
							</div>
						</div>
						<?php
					}
					?>
					<div class="portalList-div" style="clear: both;">
						<?php
						foreach($indexArr as $pid => $portalArr){
							?>
							<div class="portalName-div"><?= $portalArr['portalName'] ?>
								<a href="#" onclick="displayPortalDetails(<?= $pid ?>);return false;"><img src="../images/list.png"></a>
							</div>
							<div id="portal-div-<?= $pid ?>" style="display:<?= ($portalID ? '' : 'none') ?>;margin-left:15px">
								<div>
									<label>URL</label>: <a href="<?= $portalArr['urlRoot'] ?>" target="_blank"><?= $portalArr['urlRoot'] ?></a>
								</div>
								<div id="status-div-<?= $pid ?>">
									<label>Status</label>:
									<span>grabbing details <img class="icon-img" src="../images/workingcircle.gif" ></span>
								</div>
								<div id="guid-div-<?= $pid ?>">
									<label>GUID</label>:
									<span><?= $portalArr['guid'] ?></span>
									</div>
								<div id="manager-div-<?= $pid ?>">
									<label>Manager</label>:
									<span><?= $portalArr['managerEmail'] ?></span>
								</div>
								<div id="version-div-<?= $pid ?>">
									<label>Software version</label>:
									<span><?= $portalArr['symbiotaVersion'] ?></span>
								</div>
								<div id="lastupdate-div-<?= $pid ?>">
									<label>Last update</label>:
									<span><?= $portalArr['initialTimestamp'] ?></span>
								</div>
								<div id="initial-ts-div-<?= $pid ?>">
									<label>Date added to index</label>:
									<span><?= $portalArr['initialTimestamp'] ?></span>
								</div>
								<?php
								if($formSubmit != 'listCollections'){
									if($portalID){
										echo '<div><a href="portalindex.php?portalid='.$pid.'&formsubmit=listCollections">List Collections</a></div>';
									}
									else{
										echo '<div><a href="portalindex.php?portalid='.$pid.'">Display Full Details</a></div>';
									}
								}
								?>
							</div>
							<div id="occur-div-<?= $pid ?>" style="margin-left:15px"></div>
							<?php
							if($remoteCollID){
								$collectArr = $portalManager->getCollectionList($portalArr['urlRoot'], $remoteCollID);
								?>
								<fieldset>
									<legend>Remote Collection #<?=$remoteCollID ?></legend>
									<?php
									$remoteCollid = $collectArr['collID'];
									unset($collectArr['collID']);
									unset($collectArr['iid']);
									$internalArr = $collectArr['internal'];
									unset($collectArr['internal']);
									foreach($collectArr as $fName => $fValue){
										if($fValue){
											if($fName == 'fullDescription') $fValue = htmlentities($fValue);
											echo '<div><label>'.$fName.'</label>: '.$fValue.'</div>';
										}
									}
									$remoteUrl = $portalArr['urlRoot'].'/collections/misc/collprofiles.php?collid='.$remoteCollid;
									echo '<div><label>Remote collection</label>: <a href="'.$remoteUrl.'" target="_blank">'.$remoteUrl.'</a></div>';
									if($internalArr){
										?>
										<fieldset>
											<legend>Internally Mapped Snapshot Collection</legend>
											<?php
											foreach($internalArr as $collid => $intArr){
												$internalUrl = $CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.$collid;
												?>
												<div><label>Management Type</label>: <?= $intArr['managementType'] ?></div>
												<div><label>Specimen count</label>: <?= number_format($intArr['recordCnt']) ?></div>
												<div><label>Refresh date</label>: <?= $intArr['uploadDate'] ?></div>
												<div><label>Internal collection</label>: <a href="<?= $internalUrl ?>" target="_blank"><?= $internalUrl ?></a></div>
												<?php
												if($importProfile = $portalManager->getDataImportProfile($collid)){
													foreach($importProfile as $uspid => $profileArr){
														?>
														<hr/>
														<div style="margin:10px 5px">
															<div>
																<label>Title</label>: <?= $profileArr['title'] ?>
															</div>
															<div>
																<label>Path</label>: <?= $profileArr['path'] ?>
															</div>
															<div>
																<label>Query string</label>: <?= $profileArr['queryStr'] ?>
															</div>
															<div>
																<label>Stored procedure (cleaning)</label>: <?= $profileArr['cleanUpSp'] ?>
															</div>
															<div>
																Display all <a href="../collections/admin/specuploadmanagement.php?collid=<?= $collid ?>" target="_blank">Import Profiles</a>
															</div>
															<div>
																Initiate <a href="../collections/admin/specuploadmap.php?uploadtype=13&uspid=<?= $uspid ?>&collid=<?= $collid ?>" target="_blank">Data Import</a>
															</div>
														</div>
														<?php
													}
												}
											}
											?>
										</fieldset>
										<?php
									}
									else{
										?>
										<div style="margin: 0px 30px">
											<form name="collPubForm" method="post" action="portalindex.php">
												<input name="portalid" type="hidden" value="<?= $pid; ?>" />
												<input name="remoteid" type="hidden" value="<?= $remoteCollID; ?>" />
												<button name="formsubmit" type="submit" value="importProfile">Create Internal Snapshot Profile</button>
											</form>
										</div>
										<?php
									}
									?>
								</fieldset>
								<?php
							}
							elseif($formSubmit == 'listCollections'){
								if($collList = $portalManager->getCollectionList($portalArr['urlRoot'])){
									echo '<div id="table-div">';
									echo '<div><label>Collection Count</label>: '.count($collList).'</div>';
									echo '<table class="styledtable">';
									echo '<tr><th>ID</th><th>Institution Code</th><th>Collection Code</th><th>Collection Name</th><th>Dataset Type</th><th>Management</th><th>Mapped Internally</th></tr>';
									foreach($collList as $collArr){
										echo '<tr>';
										echo '<td><a href="portalindex.php?portalid='.$pid.'&remoteid='.$collArr['collID'].'">'.$collArr['collID'].'</a></td>';
										echo '<td>'.$collArr['institutionCode'].'</td>';
										echo '<td>'.$collArr['collectionCode'].'</td>';
										echo '<td>'.$collArr['collectionName'].'</td>';
										echo '<td>'.$collArr['collType'].'</td>';
										echo '<td>'.$collArr['managementType'].'</td>';
										if(isset($collArr['internal']) && $collArr['internal'])
											$internal = '<a href="'.$CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.key($collArr['internal']).'" target="_blank">Yes</a>';
										else $internal = 'No';
										echo '<td>'.$internal.'</td>';
										echo '</tr>';
									}
									echo '</table>';
									echo '</div>';
								}
							}
						}
						if(!$indexArr) echo '<div>Portal Index empty. No portals have yet been registered.</div>';
						?>
					</div>
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
