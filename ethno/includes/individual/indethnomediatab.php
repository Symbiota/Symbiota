<div id="ethnomediatab">
	<?php
	foreach($eafArr as $medId => $mArr){
		?>
		<div style="clear:both;margin:15px;display:flex;justify-content:space-between;">
			<div>
				<a href="<?php echo $CLIENT_ROOT; ?>/ethno/eaf/eafdetail.php?mediaid=<?php echo $medId; ?>&collid=<?php echo $collid; ?>" target="_blank"><?php echo $mArr['desc']; ?></a>
			</div>
			<?php
			if($isEditor){
				?>
				<div style="cursor:pointer;" title="Edit EAF Record">
					<a href="<?php echo $CLIENT_ROOT; ?>/ethno/eaf/eafedit.php?mediaid=<?php echo $medId; ?>&collid=<?php echo $collid; ?>&occid=<?php echo $occid; ?>">
						<img style="border:0;width:15px;" src="../../../images/edit.png" />
					</a>
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
