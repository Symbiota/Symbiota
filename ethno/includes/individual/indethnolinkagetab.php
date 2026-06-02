<div id="ethnolinkagetab">
	<?php
	foreach($linkageArr as $linkId => $linkArr){
		$linkageTypeStr = '';
		if($linkArr["linktype"]==='cognate') $linkageTypeStr = 'Cognate';
		elseif($linkArr["linktype"]==='loan') $linkageTypeStr = 'Loan';
		elseif($linkArr["linktype"]==='calque') $linkageTypeStr = 'Calque';
		?>
		<div style="">
			<?php
			if($linkageTypeStr){
				?>
				<div style="font-size:13px;">
					<b>Link type:</b>
					<?php echo $linkageTypeStr; ?>
				</div>
				<?php
			}
			if($linkArr["verbatimVernacularName"]){
				?>
				<div style="font-size:13px;">
					<b>Linked verbatim vernacular name:</b>
					<?php echo $linkArr["verbatimVernacularName"]; ?>
				</div>
				<?php
			}
			if($linkArr["langName"]){
				?>
				<div style="font-size:13px;">
					<b>Linked Glottolog language:</b>
					<?php echo $linkArr["langName"]; ?>
				</div>
				<?php
			}
			if($linkArr["sciname"]){
				?>
				<div style="font-size:13px;">
					<b>Linked scientific name:</b>
					<?php echo $linkArr["sciname"]; ?>
				</div>
				<?php
			}
			if($linkArr["refSource"]){
				?>
				<div style="font-size:13px;">
					<b>Linkage reference source:</b>
					<?php echo $linkArr["refSource"]; ?>
				</div>
				<?php
			}
			if($linkArr["refpages"]){
				?>
				<div style="font-size:13px;">
					<b>Linkage reference pages:</b>
					<?php echo $linkArr["refpages"]; ?>
				</div>
				<?php
			}
			if($linkArr["discussion"]){
				?>
				<div style="font-size:13px;">
					<b>Linkage discussion:</b>
					<?php echo $linkArr["discussion"]; ?>
				</div>
				<?php
			}
			?>
		</div>
		<hr/>
		<?php
	}
	?>
</div>
