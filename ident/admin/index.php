<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/KeyCharAdmin.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../ident/admin/index.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$langId = array_key_exists('langid',$_REQUEST)?$_REQUEST['langid']:'';

$charManager = new KeyCharAdmin();
$charManager->setLangId($langId);

$charArr = $charManager->getCharacterArr();
$headingArr = $charManager->getHeadingArr();

$isEditor = false;
if($IS_ADMIN || array_key_exists("KeyAdmin",$USER_RIGHTS)){
	$isEditor = true;
}

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title>Character Admin</title>
	<?php

	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/symb/shared.js"></script>
	<script type="text/javascript">
		function validateNewCharForm(f){
			if(f.charname.value == ""){
				alert("Character name must have a value");
				return false;
			}
			if(f.chartype.value == ""){
				alert("A character type must be selected");
				return false;
			}
			if(f.sortsequence.value && !isNumeric(f.sortsequence.value)){
				alert("Sort Sequence must be a numeric value only");
				return false;
			}
			return true;
		}

		function openHeadingAdmin(){
			newWindow = window.open("headingadmin.php","headingWin","scrollbars=1,toolbar=0,resizable=1,width=800,height=600,left=50,top=50");
			if (newWindow.opener == null) newWindow.opener = self;
		}
	</script>
	<style type="text/css">
		input{ autocomplete: off; }
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<b>Character Management</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor){
			?>
			<div id="addeditchar">
				<div style="float:right;margin:10px;">
					<a href="#" onclick="toggle('addchardiv');">
						<img src="../../images/add.png" alt="Create New Character" />
					</a>
				</div>
				<div id="addchardiv" style="display:none;margin-bottom:8px;">
					<form name="newcharform" action="chardetails.php" method="post" onsubmit="return validateNewCharForm(this)">
						<fieldset style="padding:10px;">
							<legend><b>New Character</b></legend>
							<div>
								Character Name:<br />
								<input type="text" name="charname" maxlength="255" style="width:400px;" />
							</div>
							<div style="padding-top:6px;">
								<div style="float:left;">
									Type:<br />
									<select name="chartype" style="width:180px;">
										<option value="UM">Multi-state</option>
									</select>
								</div>
								<div style="margin-left:30px;float:left;">
									Difficulty:<br />
									<select name="difficultyrank" style="width:100px;">
										<option value="">---------------</option>
										<option value="1">Easy</option>
										<option value="2">Intermediate</option>
										<option value="3">Advanced</option>
										<option value="4">Hidden</option>
									</select>
								</div>
								<div style="margin-left:30px;float:left;">
									Grouping:<br />
									<select name="hid" style="max-width:300px;">
										<option value="">Not Assigned</option>
										<option value="">---------------------</option>
										<?php
										$hArr = $headingArr;
										asort($hArr);
										foreach($hArr as $k => $v){
											echo '<option value="'.$k.'">'.$v['name'].'</option>';
										}
										?>
									</select>
									<a href="#" onclick="openHeadingAdmin(); return false;"><img src="../../images/edit.png" /></a>
								</div>
							</div>
							<div style="padding-top:6px;clear:both;">
								<b>Sort Sequence</b><br />
								<input type="text" name="sortsequence" />
							</div>
							<div style="width:100%;padding-top:6px;">
								<button name="formsubmit" type="submit" value="Create">Create</button>
							</div>
						</fieldset>
					</form>
				</div>
				<div id="charlist" style="padding-left:10px;">
					<?php
					if($charArr){
						?>
						<h3>Characters</h3>
						<ul>
							<?php
							foreach($headingArr as $hid => $hArr){
								if(array_key_exists($hid, $charArr)){
									?>
									<li>
										<a href="#" onclick="toggle('char-<?php echo $hid; ?>');return false;"><b><?php echo $hArr['name']; ?></b></a>
										<div id="char-<?php echo $hid; ?>" style="display:block;">
											<ul>
												<?php
												$charList = $charArr[$hid];
												foreach($charList as $cid => $charName){
													echo '<li><a href="chardetails.php?cid=' . htmlspecialchars($cid, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($charName, HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
												}
												?>
											</ul>
										</div>
									</li>
									<?php
								}
							}
							if(array_key_exists(0, $charArr)){
								$noHeaderArr = $charArr[0];
								?>
								<li>
									<a href="#" onclick="toggle('char-0');return false;"><b>No Assigned Grouping</b></a>
									<div id="char-0" style="display:block;">
										<ul>
											<?php
											foreach($noHeaderArr as $cid => $charName){
												echo '<li><a href="chardetails.php?cid=' . htmlspecialchars($cid, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($charName, HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
											}
											?>
										</ul>
									</div>
								</li>
								<?php
							}
							?>
						</ul>
					<?php
					}
					else{
						echo '<div style="font-weight:bold;font-size:120%;">There are no existing characters</div>';
					}
					?>
				</div>
			</div>
			<?php
		}
		else{
			echo '<h2>You are not authorized to add characters</h2>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>