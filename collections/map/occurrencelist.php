<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/list.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceMapManager.php');
include_once($SERVER_ROOT . '/rpc/crossPortalHeaders.php');

header("Content-Type: text/html; charset=".$CHARSET);

$cntPerPage = array_key_exists("cntperpage",$_REQUEST)?$_REQUEST["cntperpage"]:100;
$pageNumber = array_key_exists("page",$_REQUEST)?$_REQUEST["page"]:1;
$recLimit = (array_key_exists('recordlimit',$_REQUEST)&&is_numeric($_REQUEST['recordlimit'])?$_REQUEST['recordlimit']:15000);

//Sanitation
if(!is_numeric($cntPerPage)) $cntPerPage = 100;
if(!is_numeric($pageNumber)) $pageNumber = 1;

$mapManager = new OccurrenceMapManager();
$searchVar = $mapManager->getQueryTermStr();
$recCnt = $mapManager->getRecordCnt();
$occArr = array();

$host = ($SERVER_HOST === '127.0.0.1' || $SERVER_HOST === 'localhost'? false: $SERVER_HOST . $CLIENT_ROOT);

if(!$recLimit || $recCnt < $recLimit){
	$occArr = $mapManager->getOccurrenceArr($pageNumber,$cntPerPage);
}
?>
<div id="queryrecordsdiv" style="">
	<div style="height:25px;margin-top:-5px;">
		<div>
			<div style="float:left;">
				<form name="downloadForm" action="../download/index.php" method="post" onsubmit="targetPopup(this)" style="float:left">
					<button class="ui-button ui-widget ui-corner-all" style="margin:5px;padding:5px;cursor: pointer" title="<?php echo $LANG['DOWNLOAD_SPECIMEN_DATA']; ?>">
						<img src="../../images/dl2.png" srcset="../../images/download.svg" class="svg-icon" style="width:15px" />
					</button>
					<input name="reclimit" type="hidden" value="<?php echo $recLimit; ?>" />
					<input name="sourcepage" type="hidden" value="map" />
					<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
					<input name="dltype" type="hidden" value="specimen" />
				</form>
				<form name="fullquerykmlform" action="kmlhandler.php" method="post" target="_blank" style="float:left;">
					<input name="reclimit" type="hidden" value="<?php echo $recLimit; ?>" />
					<input name="sourcepage" type="hidden" value="map" />
					<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
					<button name="submitaction" type="submit" class="ui-button ui-widget ui-corner-all" style="margin:5px;padding:5px;cursor: pointer" title="Download KML file">
						<img src="../../images/dl2.png" srcset="../../images/download.svg" class="svg-icon" style="width:15px; padding-right: 5px; vertical-align:top" />KML
					</button>
				</form>
				<button class="ui-button ui-widget ui-corner-all" style="margin:5px;padding:5px;cursor: pointer;" onclick="copyUrl()" title="<?php echo (isset($LANG['COPY_TO_CLIPBOARD'])?$LANG['COPY_TO_CLIPBOARD']:'Copy URL to Clipboard'); ?>">
					<img src="../../images/dl2.png" srcset="../../images/link.svg" class="svg-icon" style="width:15px" /></button>
			</div>
		</div>
	</div>
	<div>
		<?php
		$paginationStr = '<div><div style="clear:both;"><hr/></div><div style="margin:5px;">';
		$lastPage = (int)($recCnt / $cntPerPage) + 1;
		$startPage = ($pageNumber > 5?$pageNumber - 5:1);
		$endPage = ($lastPage > $startPage + 10?$startPage + 10:$lastPage);
		$pageBar = '';
		if($startPage > 1){
			$pageBar .= '<span class="pagination" style="margin-right:5px;"><a href="occurrencelist.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '" >' . htmlspecialchars($LANG['PAGINATION_FIRST'], HTML_SPECIAL_CHARS_FLAGS) . '</a></span>';
			$pageBar .= '<span class="pagination" style="margin-right:5px;"><a href="occurrencelist.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars((($pageNumber - 10) < 1?1:$pageNumber - 10), HTML_SPECIAL_CHARS_FLAGS) . '">&lt;&lt;</a></span>';
		}
		for($x = $startPage; $x <= $endPage; $x++){
			if($pageNumber != $x){
				$pageBar .= '<span class="pagination" style="margin-right:3px;margin-right:3px;"><a href="occurrencelist.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars($x, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($x, HTML_SPECIAL_CHARS_FLAGS) . '</a></span>';
			}
			else{
				$pageBar .= '<span class="pagination" style="margin-right:3px;margin-right:3px;font-weight:bold;">'.$x.'</span>';
			}
		}
		if(($lastPage - $startPage) >= 10){
			$pageBar .= '<span class="pagination" style="margin-left:5px;"><a href="occurrencelist.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars((($pageNumber + 10) > $lastPage?$lastPage:($pageNumber + 10)), HTML_SPECIAL_CHARS_FLAGS) . '">&gt;&gt;</a></span>';
			$pageBar .= '<span class="pagination" style="margin-left:5px;"><a href="occurrencelist.php?' . htmlspecialchars($searchVar, HTML_SPECIAL_CHARS_FLAGS) . '&page=' . htmlspecialchars($lastPage, HTML_SPECIAL_CHARS_FLAGS) . '">Last</a></span>';
		}
		$pageBar .= '</div><div style="margin:5px;">';
		$beginNum = ($pageNumber - 1)*$cntPerPage + 1;
		$endNum = $beginNum + $cntPerPage - 1;
		if($endNum > $recCnt) $endNum = $recCnt;
		$pageBar .= $LANG['PAGINATION_PAGE'].' '.$pageNumber.', '.$LANG['PAGINATION_RECORDS'].' '.$beginNum.'-'.$endNum.' '.$LANG['PAGINATION_OF'].' '.$recCnt;
		$paginationStr .= $pageBar;
		$paginationStr .= '</div><div style="clear:both;"><hr/></div></div>';
		echo $paginationStr;

		if($occArr){
			?>
			<form name="selectform" id="selectform" action="" method="post" onsubmit="" target="_blank">
				<table class="styledtable" style="font-family:Arial;font-size:12px;margin-left:-15px;">
					<tr>
						<!--
						<th style="width:10px;" title="Select/Deselect all Records">
							<input id="selectallcheck" type="checkbox" onclick="selectAll(this);" '.($allSelected==true?"checked":"").' />
						</th>
						 -->
						<th>Catalog #</th>
						<th>Collector</th>
						<th>Date</th>
						<th>Scientific Name</th>
					</tr>
					<?php
					$trCnt = 0;
					foreach($occArr as $occId => $recArr){
						$trCnt++;
						echo '<tr '.($trCnt%2?'class="alt"':'').' id="tr'.$occId.'" onclick="emit_occurrence('.$occId.')">';
						//echo '<td style="width:10px;">';
						//echo '<input type="checkbox" class="occcheck" id="ch'.$occId.'" name="occid[]" value="'.$occId.'" onchange="findSelections(this);" '.(in_array($occId,$selections)?"checked":"").' />';
						//echo '</td>';
						echo '<td id="cat'.$occId.'" >'.$recArr["cat"].'</td>';
						echo '<td id="label'.$occId.'" >';
						echo '<a href="#" onmouseover="openOccidInfoBox(\''.$recArr["c"].'\','.$recArr["lat"].','.$recArr["lon"].');" onmouseout="closeOccidInfoBox();" onclick="openRecord({occid:' . $occId . ($host?', host:\'' . $host. '\'' : '' ). '}); return false;">'.($recArr["c"]?$recArr["c"]:"Not available").'</a>';
						echo '</td>';
						echo '<td id="e'.$occId.'" >'.$recArr["e"].'</td>';
						echo '<td id="s'.$occId.'" >'.$recArr["s"].'</td>';
						echo '</tr>';
					}
					?>
				</table>
			</form>
			<?php
			if($lastPage > $startPage) echo '<div style="">'.$paginationStr.'</div>';
		}
		else{
			if($recCnt > $recLimit){
				?>
				<div style="font-weight:bold;font-size:120%;">Record count exceeds limit</div>
				<?php
			}
			else{
				?>
				<div style="font-weight:bold;font-size:120%;">No records found matching the query</div>
				<?php
			}
		}
		?>
	</div>
</div>
