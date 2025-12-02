<footer>
	<div class="logo-gallery">
	<?php
		//include($SERVER_ROOT . '/accessibility/module.php');
		?>
		<a href="https://www.uvg.edu.gt/" target="_blank">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/UVG_logo.png" />
		</a>
		<a href="https://www.usac.edu.gt/" target="_blank" title="USAC">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/USACLOGO.png" />
		</a>
		<a href="https://biodiversity.ku.edu/" target="_blank" title="Visit KU BI website" aria-label="Visit KU BI website">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/KU_BI.png"  alt="KU BI Logo" />
		</a>
		<a href="https://biokic.asu.edu" target="_blank" title="<?= $LANG['F_BIOKIC'] ?>" aria-label="Visit BioKIC website">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/logo-asu-biokic.png"  alt="<?= $LANG['F_BIOKIC_LOGO'] ?>" />
		</a>
	</div>
	<p>
		<?= $LANG['F_MORE_INFO'] ?>, <a href="https://symbiota.org/docs" target="_blank" rel="noopener noreferrer"><?= $LANG['F_READ_DOCS'] ?></a> <?= $LANG['F_CONTACT'] ?>
		<a href="https://symbiota.org/contact-the-support-hub/" target="_blank" rel="noopener noreferrer"><?= $LANG['F_SSH'] ?></a>.
	</p>
	<p>
		<?= $LANG['F_POWERED_BY'] ?> <a href="https://symbiota.org/" target="_blank">Symbiota</a>.
	</p>
</footer>
