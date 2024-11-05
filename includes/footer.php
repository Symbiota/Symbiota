<footer>
	<div class="logo-gallery">
		<?php
		//include($SERVER_ROOT . '/accessibility/module.php');
		?>
		<a href="https://www.nsf.gov" target="_blank" aria-label="<?= $LANG['F_VISIT_NSF'] ?>">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/logo_nsf.gif" alt="<?= $LANG['F_NSF_LOGO'] ?>" />
		</a>
		<a href="http://idigbio.org" target="_blank" title="iDigBio" aria-label="<?= $LANG['F_VISIT_IDIGBIO'] ?>">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/logo_idig.png" alt="<?= $LANG['F_IDIGBIO_LOGO'] ?>" />
		</a>
	</div>
	<p>
		<?= "This project made possible by U.S. National Science Foundation Award" ?> <a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=2027654" target="_blank">#2027654</a>.
	</p>
	<p>
		<?= $LANG['F_POWERED_BY'] ?> <a href="https://symbiota.org/" target="_blank">Symbiota</a>.
	</p>
	<!--
		<a href="https://www.psaalgae.org/" target="_blank" title="Phycological Society of America" aria-label="Visit PSA website">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/PSA_Logo.png"  alt="PSA Logo" />
		</a>
	-->
</footer>
