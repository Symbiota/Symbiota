<SCRIPT LANGUAGE=JAVASCRIPT>
<!--
if (top.frames.length!=0)
  top.location=self.document.location;
// -->
</SCRIPT>
<table id="maintable" cellspacing="0">
	<tr>
		<td class="header" colspan="3">
			<!-- <div style="height:110px;background-image:url(<?php echo $CLIENT_ROOT; ?>/images/layout/header.jpg);background-repeat:no-repeat;position:relative;"> -->
			<div style="clear:both;">
				<div style="clear:both;">
					<img style="" src="<?php echo $CLIENT_ROOT; ?>/images/layout/header.jpg" />
				</div>
			</div>
		</td>
	</tr>
    <tr>
	<?php 
	if($displayLeftMenu){
		?> 
		<td class='middleleft' background="" style="background-repeat:repeat-y;"> 
			<div style="">
				<?php include($SERVER_ROOT.'/includes/leftmenu.php'); ?>
			</div>
		</td>
		<?php 
	}
	else{
		?>
        	<td class="middleleftnomenu" background="">
        		<div style='width:20px;'></div>
        	</td>
        <?php 
	}
	?>
	<td class='middlecenter'>

