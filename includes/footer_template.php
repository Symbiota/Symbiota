<footer>
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
	<div class="logo-gallery">
		<button id="accessibility-options-button" type="button" class="btn btn-primary  accessibility-option-button">
			<span class="button__item-container">
				<?= $LANG['F_ACCESSIBILITY_OPTIONS']; ?>
				<span>
					<img alt="<?= $LANG['F_ACCESSIBILITY_ICON'] ?>" src="<?= $CLIENT_ROOT ?>/images/accessibility_FILL0_wght400_GRAD0_opsz24.svg" />
				</span>
	        </span>
		</button>
		<a href="https://www.nsf.gov" target="_blank" aria-label="<?= $LANG['F_VISIT_NSF'] ?>">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/logo_nsf.gif" alt="<?= $LANG['F_NSF_LOGO'] ?>" />
		</a>
		<a href="http://idigbio.org" target="_blank" title="iDigBio" aria-label="<?= $LANG['F_VISIT_IDIGBIO'] ?>">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/logo_idig.png" alt="<?= $LANG['F_IDIGBIO_LOGO'] ?>" />
		</a>
		<a href="https://biokic.asu.edu" target="_blank" title="<?= $LANG['F_BIOKIC'] ?>" aria-label="Visit BioKIC website">
			<img src="<?= $CLIENT_ROOT; ?>/images/layout/logo-asu-biokic.png"  alt="<?= $LANG['F_BIOKIC_LOGO'] ?>" />
		</a>
	</div>
	<p>
		<?= $LANG['F_NSF_AWARDS'] ?> <a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=" target="_blank">#------</a>.
	</p>
	<p>
		<?= $LANG['F_MORE_INFO'] ?>, <a href="https://symbiota.org/docs" target="_blank" rel="noopener noreferrer"><?= $LANG['F_READ_DOCS'] ?></a> <?= $LANG['F_CONTACT'] ?>
		<a href="https://symbiota.org/contact-the-support-hub/" target="_blank" rel="noopener noreferrer"><?= $LANG['F_SSH'] ?></a>.
	</p>
	<p>
		<?= $LANG['F_POWERED_BY'] ?> <a href="https://symbiota.org/" target="_blank">Symbiota</a>.
	</p>
	<script>
		let toggleOff508 = "<?= $LANG['TOGGLE_508_OFF'] ?>";
		let toggleOn508 = "<?= $LANG['TOGGLE_508_ON'] ?>";
	</script>
	<script src="<?= $CLIENT_ROOT; ?>/js/symb/accessibility.footer.js?ver=1" type="text/javascript"></script>
</footer>
