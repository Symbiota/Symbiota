<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDuplicate.php');

$recordedBy = array_key_exists('recordedby',$_REQUEST)?trim(urldecode($_REQUEST['recordedby'])):'';
$recordNumber = array_key_exists('recordnumber',$_REQUEST)?trim($_REQUEST['recordnumber']):'';
$eventDate = array_key_exists('eventdate',$_REQUEST)?trim($_REQUEST['eventdate']):'';
$catNum = array_key_exists('catnum',$_POST)?trim($_POST['catnum']):'';
$queryOccid = array_key_exists('occid',$_POST)?$_POST['occid']:'';
$currentOccid = array_key_exists('curoccid',$_REQUEST)?$_REQUEST['curoccid']:'';
$dupeOccid = array_key_exists('dupeoccid',$_POST)?$_POST['dupeoccid']:'';
$dupeTitle = array_key_exists('dupetitle',$_POST)?$_POST['dupetitle']:'';
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$dupeManager = new OccurrenceDuplicate();
$dupArr = $dupeManager->getDupeList($recordedBy, $recordNumber, $eventDate, $catNum, $queryOccid, $currentOccid);

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Duplicate Linker</title>
	<script>
		<?php 
		if($action == 'Link as Duplicate'){
			$dupeManager->linkDuplicates($currentOccid,$dupeOccid,$dupeTitle);
			echo 'window.opener.document.getElementById("dupeRefreshForm").submit();';
			echo 'self.close();';
		}
		?>
		
		function validateDupeForm(f){


			return true;
		}

		function openIndWindow(occid){
			$url = "../../individual/index.php?occid="+occid;
			indWindow=open($url,"indlist","resizable=1,scrollbars=1,toolbar=0,width=1000,height=800,left=100,top=100");
		}
	</script>
</head>
<body>
	<!-- inner text -->
	<div id="innertext">
		<fieldset style="padding:15px;">
			<legend><b>Link New Specimen</b></legend>
			<form name="adddupform" method="post" action="dupelist.php" onsubmit="return validateDupeForm(this)">
				<section style="margin-bottom: 10px;">
					<label for="recordedby"><b>Last Name:</b></label>
					<input name="recordedby" id="recordedby" type="text" value="<?php echo $recordedBy; ?>" />
				</section>
				<section style="margin-bottom: 10px;">
					<label for="recordnumber"><b>Number:</b></label>
					<input name="recordnumber" id="recordnumber" type="text" value="<?php echo $recordNumber; ?>" />
				</section>
				<section style="margin-bottom: 10px;">
					<label for="eventdate"><b>Date:</b></label>
					<input name="eventdate" id="eventdate" type="date" value="<?php echo $eventDate; ?>" />
				</section>
				<section style="margin-bottom: 10px;">
					<label for="catnum"><b>Catalog Number:</b></label>
					<input name="catnum" id="catnum" type="text" value="" />
				</section>
				<section style="margin-bottom: 10px;">
					<label for="occid"><b>Occurrence ID:</b></label>
					<input name="occid" id="occid" type="text" value="" />
				</section>
				<section style="margin-bottom: 10px;">
					<input name="curoccid" type="hidden" value="<?php echo $currentOccid; ?>" />
					<input name="" type="submit" value="Search for Duplicates" />
 				</section>
			</form>
		</fieldset>
		<fieldset>
			<legend><b>Possible Duplicates</b></legend>
			<?php 
			if($dupArr){
				foreach($dupArr as $dupOccid => $occArr){
					?>
					<div style="margin:30px 10px">
						<div>
							<?php 
							echo $occArr['collname'];
							?>
						</div>
						<div>
							<?php 
							echo $occArr['recordedby'].' '.$occArr['recordnumber'].' <span style="margin-left:15px">'.$occArr['eventdate'];
							if($occArr['verbatimeventdate']) echo ' ('.$occArr['verbatimeventdate'].')';
							echo '</span>';
							echo '<span style="margin-left:50px">'.$occArr['catalognumber'].'</span>';
							?>
						</div>
						<div>
							<?php 
							echo trim($occArr['country'].', '.$occArr['stateprovince'].', '.$occArr['county'].', '.$occArr['locality'],' ,');
							?>
						</div>
						<div>
							<a href="#" onclick="openIndWindow(<?php echo $dupOccid; ?>)">More Details</a>
						</div>
						<div style="margin:5px 0px 20px 15px;">
							<form action="dupelist.php" method="post">
								<input name="curoccid" type="hidden" value="<?php echo $currentOccid; ?>" />
								<input name="dupeoccid" type="hidden" value="<?php echo $dupOccid; ?>" />
								<input name="dupetitle" type="hidden" value="<?php echo $occArr['recordedby'].' '.$occArr['recordnumber'].' '.$occArr['eventdate']; ?>"  />
								<input name="submitaction" type="submit" value="Link as Duplicate" />
							</form>
						</div>
					</div>
					<?php
				}
			}
			else{
				echo '<div style="margin:20px;font-weight:bold">No specimens found matching search criteria</div>';
			}
			?>
		</fieldset>
	</div>
</body>
</html>