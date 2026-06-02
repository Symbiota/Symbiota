<?php
include_once($SERVER_ROOT.'/classes/EthnoMediaManager.php');
include_once($SERVER_ROOT.'/classes/EthnoDataManager.php');

$mediaid = array_key_exists('mediaid',$_REQUEST)?$_REQUEST['mediaid']:0;

$ethnoMediaManager = new EthnoMediaManager();
$ethnoDataManager = new EthnoDataManager();

if($action === 'Upload EAF'){
	$ethnoMediaManager->addFile();
	$mediaid = $ethnoMediaManager->getMediaid();
	$ethnoMediaManager->setMediaid($mediaid);
	$eafArr = $ethnoMediaManager->getEAFInfoArr();
	$eaf_file = $eafArr['eaffile'];
	$file_path = $SERVER_ROOT.$eaf_file;
	if(@simplexml_load_file($file_path)){
		header('Location: ../../ethno/eaf/eafedit.php?mediaid='.$mediaid.'&collid='.$collId.'&occid='.$occId.'&occindex='.$occIndex.'&csmode='.$crowdSourceMode);
	}
	else{
		$ethnoMediaManager->deleteEAF($mediaid);
		$mediaid = 0;
		$statusStr = "EAF file could not be parsed.";;
	}
}
elseif($action === 'Delete EAF Record'){
	$ethnoMediaManager->deleteEAF($_POST['mediaid']);
}
elseif($action === 'Remap EAF Record'){
	$ethnoMediaManager->remapEAFOccLinkRecord($_POST['mediaid'],$_POST['occid'],$_POST['targetoccid']);
}

$ethnoDataManager->setCollid($collId);
$ethnoDataManager->setOccId($occId);
$ethnoDataManager->setOccTaxonomy();
$ethnoTid = $ethnoDataManager->getTid();
$ethnoMediaManager->setCollid($collId);
$eafArr = $ethnoMediaManager->getOccEAFArr($occId);
?>
<script type="text/javascript">
	function verifyUploadEAF(f){
		var file = f.eaffile.files[0];
		var mp3url = f.mp3url.value;
		var description = f.description.value;
		if(!file){
			alert("Please select an eaf file to upload.");
			return false;
		}
		else if(file.size > 1000000){
			alert("The eaf file you are trying to upload is too big.");
			return false;
		}
		else if(!(mp3url.substr(mp3url.length - 4) === '.mp3') && !(mp3url.substr(mp3url.length - 4) === '.mp4')){
			alert("Please enter a valid url to the associated mp3 file.");
			return false;
		}
		else if(!description){
			alert("Please enter a description for the eaf file.");
			return false;
		}
		else{
			document.getElementById("performactionup").value = 'Upload EAF';
			document.neweafform.submit();
		}
	}
</script>
<div id="ethnoMediaDiv" style="width:795px;">
	<div style="float:right;cursor:pointer;<?php echo (!$ethnoUseUseDataArr?'display:none;':''); ?>" onclick="toggle('neweafdiv');" title="Upload EAF File">
		<img style="border:0px;width:12px;" src="../../../images/add.png" />
	</div>
	<div id="neweafdiv" style="clear:both;<?php echo (($ethnoUseUseDataArr)?'display:none;':''); ?>">
		<form name="eafnewform" action="occurrenceeditor.php" method="post">
			<fieldset style="padding:15px;">
				<legend><b>Add New EAF File</b></legend>
				<div id="eafuploaddiv" style="margin-top:8px;">
					EAF File:
					<!-- following line sets MAX_FILE_SIZE (must precede the file input field)  -->
					<input type='hidden' name='MAX_FILE_SIZE' value='100000000' />
					<input name='eaffile' type='file' size='70' />
				</div>
				<div style="width:100%;margin-top:8px;">
					MP3/MP4 URL
					<input name="mp3url" type="text" style="width:500px;" />
				</div>
				<div style="width:100%;margin-top:8px;">
					EAF Description
					<input name="description" type="text" style="width:500px;" />
				</div>
				<div style="clear:both;padding-top:8px;float:right;">
					<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
					<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
					<input type="hidden" name="tid" value="<?php echo $ethnoTid; ?>" />
					<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
					<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
					<input type="hidden" name="tabtarget" value="4" />
					<input id="performactionup" name="submitaction" type="hidden" value="" />
					<button type="button" onclick='verifyUploadEAF(this.form);'>Upload</button>
				</div>
			</fieldset>
		</form>
	</div>
	<?php
	if($eafArr){
		?>
		<div style="clear:both;margin:15px;">
			<hr />
			<?php
			foreach($eafArr as $medId => $mArr){
				?>
				<div style="clear:both;margin:15px;display:flex;justify-content:space-between;">
					<div>
						<a href="../../eaf/eafdetail.php?mediaid=<?php echo $medId; ?>&collid=<?php echo $collId; ?>" target="_blank"><?php echo $mArr['desc']; ?></a>
					</div>
					<div style="cursor:pointer;" title="Edit EAF Record">
						<a href="../../eaf/eafedit.php?mediaid=<?php echo $medId; ?>&collid=<?php echo $collId; ?>&occid=<?php echo $occId; ?>&occindex=<?php echo $occIndex; ?>&csmode=<?php echo $crowdSourceMode; ?>">
							<img style="border:0;width:15px;" src="../../../images/edit.png" />
						</a>
					</div>
				</div>
				<div id="media<?php echo $medId; ?>editdiv" style="clear:both;">
					<form name="media<?php echo $medId; ?>remapform" action="occurrenceeditor.php" method="post" onsubmit="">
						<fieldset style="padding:10px;">
							<legend><b>Remap to Another Occurrence Record</b></legend>
							<div style="display:flex;justify-content:space-between;">
								<div>
									<b>Occurrence Record #:</b>
									<input id="mediaoccid-<?php echo $medId; ?>" name="targetoccid" type="text" value="" />
									<span style="cursor:pointer;color:blue;margin-left:5px;"  onclick="openOccurrenceSearch('mediaoccid-<?php echo $medId; ?>')">Open Occurrence Linking Aid</span>
								</div>
								<div style="margin:10px 20px;">
									<input name="occid" type="hidden" value="<?php echo $occId; ?>" />
									<input type="hidden" name="mediaid" value="<?php echo $medId; ?>" />
									<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
									<input type="hidden" name="tid" value="<?php echo $ethnoTid; ?>" />
									<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
									<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
									<input type="hidden" name="tabtarget" value="4" />
									<input type="submit" name="submitaction" value="Remap EAF Record" />
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<hr/>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
</div>
