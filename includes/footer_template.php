<footer>
	<div class="logo-gallery">
		<a href="https://www.nsf.gov" target="_blank">
			<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/logo_nsf.gif" />
		</a>
		<a href="http://idigbio.org" target="_blank" title="iDigBio">
			<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/logo_idig.png" />
		</a>
		<a href="https://biokic.asu.edu" target="_blank" title="Biodiversity Knowledge Integration Center">
			<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/logo-asu-biokic.png" />
		</a>
	</div>
	<p>This project made possible by National Science Foundation Awards <a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=" target="_blank">#------</a>.</p>
	<p>For more information about Symbiota, <a href="https://symbiota.org/docs" target="_blank" rel="noopener noreferrer">read the docs</a> or contact the <a href="https://symbiota.org/contact-the-support-hub/" target="_blank" rel="noopener noreferrer">Symbiota Support Hub</a>.</p>
	</p>
	<p>Powered by <a href="https://symbiota.org/" target="_blank">Symbiota</a>.</p>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', ()=>{
			document.getElementById('accessibility-button').disabled=false;
			updateButtonTextBasedOnEnabledStylesheet('<?php echo $LANG['TOGGLE_508_OFF'] ?>', '<?php echo $LANG['TOGGLE_508_ON'] ?>');
		});
	</script>
</footer>