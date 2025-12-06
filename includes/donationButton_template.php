<?php
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/templates/donationButton.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT . '/content/lang/templates/donationButton.en.php');
else include_once($SERVER_ROOT . '/content/lang/templates/donationButton.' . $LANG_TAG . '.php');
?>

<div style="position:fixed; right:2rem; bottom:2rem;">
	<a tabindex="-1" style="text-decoration:none;" href="<?= $GLOBALS['DONATE_LINK']?>" target="_blank">
		<button  style="border-radius:30px; padding:1rem 1.5rem; display:flex; gap:0.5rem; align-items:center; border:none;">
			<div style="width:24px; height:24px; fill:#FFF;">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
			</div>
			<span><?= $LANG['DONATE'] ?></span>
		</button>
	</a>
</div>
