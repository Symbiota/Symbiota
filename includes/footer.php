<footer>
	<!--
	<dialog id="accessibility-modal" class="accessibility-dialog" aria-label="<?= $LANG['F_ACCESSIBILITY_OPTIONS']; ?>">
		<h1><?= $LANG['F_ACCESSIBILITY_OPTIONS']; ?></h1>
		<p class="bottom-breathing-room-rel"><?= $LANG['F_ACCESSIBILITY_OPTIONS_DESCRIPTION']; ?></p>
		<button type="button" class="btn btn-primary bottom-breathing-room-rel" onclick="toggleAccessibilityStyles('<?php echo $CLIENT_ROOT . '/includes' . '/' ?>', '<?php echo $CSS_BASE_PATH ?>', '<?php echo $LANG['F_TOGGLE_508_OFF'] ?>', '<?php echo $LANG['F_TOGGLE_508_ON'] ?>')" id="accessibility-button" data-accessibility="accessibility-button">
			<?= $LANG['F_TOGGLE_508_ON'] ?>
		</button>
		<form method="dialog">
			<button type="submit" class="btn btn-primary"><?= $LANG['F_CLOSE']; ?></button>
		</form>
	</dialog>
	-->
	<div class="logo-gallery">
		<!--
		<button id="accessibility-options-button" type="button" class="btn btn-primary  accessibility-option-button">
			<span class="button__item-container">
				<?= $LANG['F_ACCESSIBILITY_OPTIONS']; ?>
				<span>
					<img alt="accessibility icon of a person" src="<?= $CLIENT_ROOT ?>/images/accessibility_FILL0_wght400_GRAD0_opsz24.svg" />
				</span>
	        </span>
		</button>
-->
		<a href="https://www.uvg.edu.gt/" target="_blank">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/UVG_logo.png" />
		</a>
		<a href="https://www.usac.edu.gt/" target="_blank" title="iDigBio">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/USACLOGO.png" />
		</a>
		<a href="https://biokic.asu.edu" target="_blank" title="Biodiversity Knowledge Integration Center" aria-label="Visit BioKIC website">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/logo-asu-biokic.png"  alt="Logo for the Biodiversity Knowledge Integration Center" />
		</a>
	</div>
	<p>Para más información acerca de Symbiota <a href="https://biokic.github.io/symbiota-docs/es/" target="_blank" rel="noopener noreferrer">puede dirigirse a la documentación</a> o contactar al <a href="https://symbiota.org/ayuda/" target="_blank" rel="noopener noreferrer">Symbiota Support Hub</a>.
	</p>
	<p>
		For more information about Symbiota, <a href="https://symbiota.org/docs" target="_blank" rel="noopener noreferrer">read the docs</a> or contact the <a href="https://symbiota.org/contact-the-support-hub/" target="_blank" rel="noopener noreferrer">Symbiota Support Hub</a>.
	</p>
	<p>
		Powered by <a href="https://symbiota.org/" target="_blank">Symbiota</a>.
	</p>
	<!--
	<script>
		let toggleOff508 = "<?= $LANG['TOGGLE_508_OFF'] ?>";
		let toggleOn508 = "<?= $LANG['TOGGLE_508_ON'] ?>";
	</script>
	<script src="<?= $CLIENT_ROOT; ?>/js/symb/accessibility.footer.js?ver=1" type="text/javascript"></script>
	-->
</footer>
