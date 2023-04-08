<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistVoucherReport.php');
include_once($SERVER_ROOT.'/content/lang/checklists/voucheradmin.'.$LANG_TAG.'.php');

$clid = array_key_exists("clid",$_REQUEST)?$_REQUEST["clid"]:0;
$pid = array_key_exists("pid",$_REQUEST)?$_REQUEST["pid"]:"";
$startPos = (array_key_exists('start',$_REQUEST)?(int)$_REQUEST['start']:0);
$displayMode = (array_key_exists('displaymode',$_REQUEST)?$_REQUEST['displaymode']:0);

$clManager = new ChecklistVoucherReport();
$clManager->setClid($clid);
$clManager->setCollectionVariables();

$isEditor = 0;
if($IS_ADMIN || (array_key_exists("ClAdmin",$USER_RIGHTS) && in_array($clid,$USER_RIGHTS["ClAdmin"]))){
	$isEditor = 1;
}
if($isEditor){
	?>
	<div id="nonVoucheredDiv">
		<div style="margin:10px;">
			<?php
			$nonVoucherCnt = $clManager->getNonVoucheredCnt();
			?>
			<div style="float:right;">
				<form name="displaymodeform" method="post" action="voucheradmin.php">
					<b><?php echo $LANG['DISPLAYMODE'];?>:</b>
					<select name="displaymode" onchange="this.form.submit()">
						<option value="0"><?php echo $LANG['NONVOUCHTAX'];?></option>
						<option value="1" <?php echo ($displayMode==1?'SELECTED':''); ?>><?php echo $LANG['OCCURNONVOUCH'];?></option>
						<option value="2" <?php echo ($displayMode==2?'SELECTED':''); ?>><?php echo $LANG['NEWOCCUR'];?></option>
						<!-- <option value="3" <?php //echo ($displayMode==3?'SELECTED':''); ?>>Non-species level or poorly identified vouchers</option> -->
					</select>
					<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
					<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
					<input name="tabindex" type="hidden" value="0" />
				</form>
			</div>
			<?php
			if(!$displayMode || $displayMode==1 || $displayMode==2){
				?>
				<div style='float:left;margin-top:3px;height:30px;'>
					<b><?php echo $LANG['TAXWITHOUTVOUCH'];?>: <?php echo $nonVoucherCnt; ?></b>
					<?php
					if($clManager->getChildClidArr()){
						echo ' (excludes taxa from children checklists)';
					}
					?>
					<span>
						<a href="voucheradmin.php?clid=<?php echo htmlspecialchars($clid, HTML_SPECIAL_CHARS_FLAGS) . '&pid=' . htmlspecialchars($pid, HTML_SPECIAL_CHARS_FLAGS); ?>"><img src="../images/refresh.png" style="width:14px;vertical-align: middle;" title="<?php echo htmlspecialchars($LANG['REFRESHLIST'], HTML_SPECIAL_CHARS_FLAGS);?>" /></a>
					</span>
				</div>
			<?php
			}
			if($displayMode){
				?>
				<div style="clear:both;">
					<div style="margin:10px;">
						<?php echo $LANG['LISTEDBELOW'];?>
					</div>
					<div>
						<?php
						if($specArr = $clManager->getNewVouchers($startPos,$displayMode)){
							?>
							<form name="batchnonvoucherform" method="post" action="voucheradmin.php" onsubmit="return validateBatchNonVoucherForm(this)">
								<table class="styledtable" style="font-family:Arial;font-size:12px;">
									<tr>
										<th>
											<span title="Select All">
												<input name="occids[]" type="checkbox" onclick="selectAll(this);" value="0-0" />
											</span>
										</th>
										<th><?php echo $LANG['CHECKLISTID'];?></th>
										<th><?php echo $LANG['COLLECTOR'];?></th>
										<th><?php echo $LANG['LOCALITY'];?></th>
									</tr>
									<?php
									foreach($specArr as $cltid => $occArr){
										foreach($occArr as $occid => $oArr){
											echo '<tr>';
											echo '<td><input name="occids[]" type="checkbox" value="'.$occid.'-'.$cltid.'" /></td>';
											echo '<td><a href="../taxa/index.php?taxon=' . htmlspecialchars($oArr['tid'], HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">' . htmlspecialchars($oArr['sciname'], HTML_SPECIAL_CHARS_FLAGS) . '</a></td>';
											echo '<td>';
											echo $oArr['recordedby'].' '.$oArr['recordnumber'].'<br/>';
											if($oArr['eventdate']) echo $oArr['eventdate'].'<br/>';
											echo '<a href="../collections/individual/index.php?occid=' . htmlspecialchars($occid, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">';
											echo $oArr['collcode'];
											echo '</a>';
											echo '</td>';
											echo '<td>'.$oArr['locality'].'</td>';
											echo '</tr>';
										}
									}
									?>
								</table>
								<input name="tabindex" value="0" type="hidden" />
								<input name="clid" value="<?php echo $clid; ?>" type="hidden" />
								<input name="pid" value="<?php echo $pid; ?>" type="hidden" />
								<input name="displaymode" value="<?php echo $displayMode; ?>" type="hidden" />
								<input name="usecurrent" value="1" type="checkbox" checked /><?php echo $LANG['ADDNAMECURRTAX'];?><br/>
								<input name="submitaction" value="Add Vouchers" type="submit" />
							</form>
						<?php
						}
						else{
							echo '<div style="font-weight:bold;font-size:120%;">'.$LANG['NOVOUCHLOCA'].'</div>';
						}
						?>
					</div>
				</div>

			<?php
			}
			else{
				?>
				<div style="clear:both;">
					<div style="margin:10px;">
						<?php echo $LANG['LISTEDBELOWARESPECINSTRUC'];?>
					</div>
					<div style="margin:20px;">
						<?php
						if($nonVoucherArr = $clManager->getNonVoucheredTaxa($startPos)){
							foreach($nonVoucherArr as $family => $tArr){
								echo '<div style="font-weight:bold;">'.strtoupper($family).'</div>';
								echo '<div style="margin:10px;text-decoration:italic;">';
								foreach($tArr as $tid => $sciname){
									?>
									<div>
										<a href="#" onclick="openPopup('../taxa/index.php?taxauthid=1&taxon=<?php echo htmlspecialchars($tid, HTML_SPECIAL_CHARS_FLAGS) . '&clid=' . htmlspecialchars($clid, HTML_SPECIAL_CHARS_FLAGS); ?>','taxawindow');return false;"><?php echo $sciname; ?></a>
										<a href="#" onclick="openPopup('../collections/list.php?db=all&usethes=1&reset=1&mode=voucher&taxa=<?php echo htmlspecialchars($sciname, HTML_SPECIAL_CHARS_FLAGS) . '&targetclid=' . htmlspecialchars($clid, HTML_SPECIAL_CHARS_FLAGS) . '&targettid=' . htmlspecialchars($tid, HTML_SPECIAL_CHARS_FLAGS);?>','editorwindow');return false;">
											<img src="../images/link.png" style="width:13px;" title="<?php echo $LANG['LINKVOUCHSPECIMEN'];?>" />
										</a>
									</div>
								<?php
								}
								echo '</div>';
							}
							$arrCnt = $nonVoucherArr;
							if($startPos || $nonVoucherCnt > 100){
								echo '<div style="text-weight:bold;">';
								if($startPos > 0) echo '<a href="voucheradmin.php?clid=' . htmlspecialchars($clid, HTML_SPECIAL_CHARS_FLAGS) . '&pid=' . htmlspecialchars($pid, HTML_SPECIAL_CHARS_FLAGS) . '&start=' . htmlspecialchars(($startPos-100), HTML_SPECIAL_CHARS_FLAGS) . '">';
								echo '&lt;&lt; '.$LANG['PREVIOUS'].'';
								if($startPos > 0) echo '</a>';
								echo ' || <b>'.$startPos.'-'.($startPos+($arrCnt<100?$arrCnt:100)).''.$LANG['RECORDS'].'</b> || ';
								if(($startPos + 100) <= $nonVoucherCnt) echo '<a href="voucheradmin.php?clid=' . htmlspecialchars($clid, HTML_SPECIAL_CHARS_FLAGS) . '&pid=' . htmlspecialchars($pid, HTML_SPECIAL_CHARS_FLAGS) . '&start=' . htmlspecialchars(($startPos+100), HTML_SPECIAL_CHARS_FLAGS) . '">';
								echo ''.$LANG['NEXT'].' &gt;&gt;';
								if(($startPos + 100) <= $nonVoucherCnt) echo '</a>';
								echo '</div>';
							}
						}
						else{
							echo '<h2>'.$LANG['ALLTAXACONTAINVOUCH'].'</h2>';
						}
						?>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
	<?php
}
?>