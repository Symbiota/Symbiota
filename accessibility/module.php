<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/accessibility.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/templates/accessibility.en.php');
else include_once($SERVER_ROOT . '/content/lang/templates/accessibility.' . $LANG_TAG . '.php');
?>
<dialog id="accessibility-modal" class="accessibility-dialog" aria-label="<?= $LANG['A_ACCESSIBILITY_OPTIONS']; ?>">
	<h1><?= $LANG['A_ACCESSIBILITY_OPTIONS']; ?></h1>
	<p class="bottom-breathing-room-rel"><?= $LANG['A_ACCESSIBILITY_OPTIONS_DESCRIPTION']; ?></p>
	<button type="button" class="btn btn-primary bottom-breathing-room-rel" onclick="toggleAccessibilityStyles()" id="accessibility-button" data-accessibility="accessibility-button">
		<?= $LANG['A_TOGGLE_508_ON'] ?>
	</button>
	<form method="dialog">
		<button type="submit" class="btn btn-primary"><?= $LANG['A_CLOSE']; ?></button>
	</form>
</dialog>
<button id="accessibility-options-button" type="button" class="btn btn-primary  accessibility-option-button">
	<span class="button__item-container">
		<?= $LANG['A_ACCESSIBILITY_OPTIONS']; ?>
		<span>
			<img alt="<?= $LANG['A_ACCESSIBILITY_ICON'] ?>" src="<?= $CLIENT_ROOT ?>/images/accessibility_FILL0_wght400_GRAD0_opsz24.svg" />
		</span>
	</span>
</button>
<script type="text/javascript">
	const toggleOff508Text = "<?= $LANG['A_TOGGLE_508_OFF'] ?>";
	const toggleOn508Text = "<?= $LANG['A_TOGGLE_508_ON'] ?>";
	const clientRootPath = "<?= $CLIENT_ROOT ?>";
</script>
<link href="<?= $CSS_BASE_PATH ?>/symbiota/accessibility-controls.css?ver=<?= $CSS_VERSION ?>" type="text/css" rel="stylesheet">
<script src="<?= $CLIENT_ROOT ?>/js/symb/accessibilityUtils.js?ver=1b" type="text/javascript"></script>
