<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCrowdSource.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../../profile/index.php?refurl=../collections/specprocessor/index.php?tabindex=1?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$uid = array_key_exists('uid',$_REQUEST)?$_REQUEST['uid']:0;
$rStatus = array_key_exists('rstatus',$_REQUEST)?$_REQUEST['rstatus']:'5,10';
$start = array_key_exists('start',$_REQUEST)?$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?$_REQUEST['limit']:500;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$csManager = new OccurrenceCrowdSource();
//If collid is null, it will be assumed that current user wants to review their own specimens (they can still edit pending, closed specimen can't be editted)
$csManager->setCollid($collid);

$isEditor = 0;
if(array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
	$isEditor = 1;
}

$statusStr = '';
if($isEditor && $action){
	if($action == 'submitReviews'){
		$statusStr = $csManager->submitReviews($_POST);
	}
	elseif($action == 'resetToNotReviewed'){
		$statusStr = $csManager->resetReviewStatus($_POST,5);
	}
	elseif($action == 'resetToOpen'){
		$statusStr = $csManager->resetReviewStatus($_POST,0);
	}
}

$projArr = $csManager->getProjectDetails();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Crowdsourcing Reviewer</title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../../js/jquery.js" type="text/javascript"></script>
	<script" src="../../../js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
		function selectAll(cbObj){
			var cbStatus = cbObj.checked;
			var f = cbObj.form;
			for(var i = 0; i < f.length; i++) {
				if(f.elements[i].name == "occid[]") f.elements[i].checked = cbStatus;
			}
		}

		function selectCheckbox(occid){
			document.getElementById("o-"+occid).checked = true;
		}

		function expandNotes(textObj){
			textObj.style.width = "300px";
		}

		function collapseNotes(textObj){
			textObj.style.width = "60px";
		}

		function validateReviewForm(f){
			for(var i = 0; i < f.length; i++) {
				if(f.elements[i].name == "occid[]" && f.elements[i].checked) return true;
			}
			alert("No records have been selected");
			return false;
		}

		function showAdditionalActions(){
			$('#addActionsDiv').show();
			$('#showAddDiv').hide();
		}
	</script>
</head>
<body style="margin-left: 0px; margin-right: 0px;background-color:white;">
	<div class='navpath'>
		<a href="../../../index.php">Home</a> &gt;&gt;
		<a href="index.php">Score Board</a> &gt;&gt;
		<?php
		if($collid) echo '<a href="../index.php?tabindex=1&collid='.$collid.'">Control Panel</a> &gt;&gt;';
		?>
		<b>Crowdsourcing Review</b>
	</div>
	<div style="margin:10px;">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:20px;color:<?php echo (substr($statusStr,0,5)=='ERROR'?'red':'green');?>">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($recArr = $csManager->getReviewArr($start,$limit,$uid,$rStatus)){
			$totalCnt = $recArr['totalcnt'];
			unset($recArr['totalcnt']);
			//Set up navigation string
			$pageCnt = count($recArr);
			//echo json_encode($recArr);
			$end = ($start + $pageCnt);
			$urlPrefix = 'review.php?collid='.$collid.'&uid='.$uid.'&rstatus='.$rStatus;
			$navStr = '<b>';
			if($start > 0) $navStr .= '<a href="'.$urlPrefix.'&start=0&limit='.$limit.'">';
			$navStr .= '|&lt; ';
			if($start > 0) $navStr .= '</a>';
			$navStr .= '&nbsp;&nbsp;&nbsp;';
			if($start > 0) $navStr .= '<a href="'.$urlPrefix.'&start='.($start-$limit).'&limit='.$limit.'">';
			$navStr .= '&lt;&lt;';
			if($start > 0) $navStr .= '</a>';
			$navStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;'.($start + 1).' - '.($end).' of '.number_format($totalCnt).'&nbsp;&nbsp;|&nbsp;&nbsp;';
			if($totalCnt > ($start+$limit)) $navStr .= '<a href="'.$urlPrefix.'&start='.($start+$limit).'&limit='.$limit.'">';
			$navStr .= '&gt;&gt;';
			if($totalCnt > ($start+$limit)) $navStr .= '</a>';
			$navStr .= '&nbsp;&nbsp;&nbsp;';
			if(($start+$pageCnt) < $totalCnt) $navStr .= '<a href="'.$urlPrefix.'&start='.(floor($totalCnt/$limit)*$limit).'&limit='.$limit.'">';
			$navStr .= '&gt;|';
			if(($start+$pageCnt) < $totalCnt) $navStr .= '</a> ';
			$navStr .= '</b>';
			?>
			<div style="width:850px;">
				<div style="float:right;">
					<form name="filter" action="review.php" method="get">
						<fieldset style="width:300px;text-align:left;">
							<legend><b>Filter</b></legend>
							<div style="margin:3px;">
								<b>Review Status:</b>
								<select name="rstatus" onchange="this.form.submit()">
									<option value="5,10">All Records</option>
									<option value="5,10">----------------------</option>
									<option value="5" <?php echo ($rStatus=='5'?'SELECTED':''); ?>>Not Reviewed</option>
									<option value="10" <?php echo ($rStatus=='10'?'SELECTED':''); ?>>Reviewed and Approved)</option>
								</select>
							</div>
							<?php
							if($collid){
								?>
								<div style="margin:3px;">
									<b>Editor:</b>
									<select name="uid" onchange="this.form.submit()">
										<option value="">All Editors</option>
										<option value="">----------------------</option>
										<?php
										$editorArr = $csManager->getEditorList();
										foreach($editorArr as $eUid => $eName){
											echo '<option value="'.$eUid.'" '.($eUid==$uid?'SELECTED':'').'>'.$eName.'</option>'."\n";
										}
										?>
									</select>
								</div>
								<?php
							}
							else{
								echo '<input name="uid" type="hidden" value="'.$uid.'" />';
							}
							?>
							<div style="margin:3px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<!-- <input name="action" type="submit" value="Filter Records" /> -->
							</div>
						</fieldset>
					</form>
				</div>
				<div style="font-weight:bold;font-size:130%;">
					<?php echo ($collid?$projArr['name']:$USER_DISPLAY_NAME); ?>
				</div>
				<div style="clear:both;">
					<?php echo $navStr; ?>
				</div>
			</div>
			<div style="clear:both;">
				<?php
				if($totalCnt){
					?>
					<div style="clear:both;">
						<form name="reviewform" method="post" action="review.php" onsubmit="return validateReviewForm(this)">
							<?php
							if($collid){
								echo '<input name="collid" type="hidden" value="'.$collid.'" />';
								echo '<input name="rstatus" type="hidden" value="'.$rStatus.'" />';
								echo '<input name="uid" type="hidden" value="'.$uid.'" />';
							}
							?>
							<table class="styledtable" style="font-family:Arial;font-size:12px;">
								<tr>
									<?php
									if($collid) echo '<th><span title="Select All"><input name="selectall" type="checkbox" onclick="selectAll(this)" /></span></th>';
									?>
									<th>Points</th>
									<th>Comments</th>
									<th>Edit</th>
									<?php
									//Display table header
									$header = $csManager->getHeaderArr();
									foreach($header as $v){
										echo '<th>'.$v.'</th>';
									}
									?>
								</tr>
								<?php
								$cnt = 0;
								//echo json_encode($recArr);
								foreach($recArr as $occid => $rArr){
									?>
									<tr <?php echo ($cnt%2?'class="alt"':'') ?>>
										<?php
										$notes = '';
										if(isset($rArr['notes'])) $notes = $rArr['notes'];
										$points = 2;
										if(isset($rArr['points'])) $points = $rArr['points'];
										if($collid){
											echo '<td><input id="o-'.$occid.'" name="occid[]" type="checkbox" value="'.$occid.'" /></td>';
											if(isset($rArr['points'])){
												echo '<td><input name="p-'.$occid.'" type="text" value="'.$points.'" style="width:40px;" DISABLED /></td>';
												echo '<td><b>Reviewed and Approved</b></td>';
											}
											else{
												echo '<td><select name="p-'.$occid.'" style="width:45px;" onchange="selectCheckbox('.$occid.')">';
												echo '<option value="0" '.($points=='0'?'SELECTED':'').'>0</option>';
												echo '<option value="1" '.($points=='1'?'SELECTED':'').'>1</option>';
												echo '<option value="2" '.($points=='2'?'SELECTED':'').'>2</option>';
												echo '<option value="3" '.($points=='3'?'SELECTED':'').'>3</option>';
												echo '</select></td>';
												echo '<td><input name="c-'.$occid.'" type="text" value="'.$notes.'" style="width:60px;" onfocus="expandNotes(this)" onblur="collapseNotes(this)" onchange="selectCheckbox('.$occid.')" /></td>';
											}
										}
										else{
											echo '<td><input name="p-'.$occid.'" type="text" value="'.$points.'" style="width:15px;" DISABLED /></td>';
											echo '<td>'.$notes.'</td>';
										}
										?>
										<td>
											<?php
											if($isEditor || $rArr['reviewstatus'] == 5){
												echo '<a href="../../editor/occurrenceeditor.php?csmode=1&occid='.$occid.'" target="_blank">';
												echo '<img src="../../../images/edit.png" style="border:solid 1px gray;height:13px;" />';
												echo '</a>';
											}
											else{
												echo '<img src="../../../images/cross-out.png" style="border:solid 1px gray;height:13px;" />';
											}
											?>
										</td>
										<?php
										foreach($header as $v){
											$displayStr = $rArr[$v];
											if(strlen($displayStr) > 40){
												$displayStr = substr($displayStr,0,40).'...';
											}
											echo '<td>'.$displayStr.'</td>'."\n";
										}
										?>
									</tr>
									<?php
									$cnt++;
								}
								?>
							</table>
							<div style="width:850px;">
								<div>
									<?php echo $navStr; ?>
								</div>
								<div style="clear:both;">
									<?php
									if($collid){
										?>
										<div style="margin:10px;clear:both;">
											<button name="action" type="submit" value="submitReviews" >Submit Reviews</button>
											<input name="updateProcessingStatus" type="checkbox" value="1" checked />
											Set Processing Status to reviewed (unchecking will leave Processing Status as set by user for each record)
										</div>
										<div id="showAddDiv" style="margin:10px"><a href="#" onclick="showAdditionalActions();return false;">Show Additional Actions</a></div>
										<div id="addActionsDiv" style="display:none;margin:20px 10px;">
											<div><button name="action" type="submit" value="resetToNotReviewed" onclick="return confirm('Are you sure you want to change review status? All points and review comments will be deleted.')">Remove points and change to Not Reviewed</button></div>
											<div style="margin-top:5px"><button name="action" type="submit" value="resetToOpen" onclick="return confirm('Are you sure you want to reset status? Editor, points, and review comments will be deleted.')">Move back into crowdsourcing queue as Open Records</button></div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						</form>
					</div>
					<?php
				}
				else{
					if($collid && $rStatus == 5){
						?>
						<div style="clear:both;margin:30px 15px;font-weight:bold;">
							<div style="font-size:120%;">
								There are no more records to review for this user
							</div>
							<div style="margin:15px;">
								Return to <a href="../index.php?tabindex=1&collid=<?php echo $collid; ?>">Control Panel</a>
							</div>
							<div style="margin:15px;">
								Return to <a href="index.php">Score Board</a>
							</div>
						</div>
						<?php
					}
					else{
						?>
						<div style="clear:both;font-size:120%;padding-top:30px;font-weight:bold;">
							There are no records matching search criteria
						</div>
						<?php
					}
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
</body>
</html>