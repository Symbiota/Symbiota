<?php
include_once('config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

Language::load('templates/index');

header('Content-Type: text/html; charset=' . $CHARSET);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> <?php echo $LANG['HOME']; ?></title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/googleanalytics.php');
	?>
</head>
<style>
	.ask {
		position: fixed;
		bottom: 10px;
		right: 10px;
		left: 50%;
		background-color: #fafafa;
		border-color: #e60000;
		padding: 15px 20px;
		border-radius: 8px;
		border-style: solid;
		border-width: 6px;
		z-index: 9999;
		animation: slideUp 0.6s ease-out forwards;
	}
	@keyframes slideUp {
		0% { opacity: 0; transform: translateY(70%); }
		100% { opacity: 1; transform: translateY(0); }
	}
</style>

<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath"></div>
	<main id="innertext">
		<h1 class="page-heading"><?php echo $DEFAULT_TITLE; ?> <?php echo $LANG['HOME']; ?></h1>
		<?php
		if($LANG_TAG == 'es'){
			?>
			<div>
				<h1 class="headline">Bienvenidos</h1>
				<p>Este portal de datos se ha establecido para promover la colaboración... Reemplazar con texto introductorio en inglés</p>
			</div>
			<?php
		}
		elseif($LANG_TAG == 'fr'){
			?>
			<div>
				<h1 class="headline">Bienvenue</h1>
				<p>Ce portail de données a été créé pour promouvoir la collaboration... Remplacer par le texte d'introduction en anglais</p>
			</div>
			<?php
		}
		else{
			//Default Language
			?>
			<div>
				<h1>Welcome</h1>
				<p>
					This data portal has been established to promote collaborative... Replace
					with introductory text in English. If the portal is not meant to be
					multilingual, remove the unneeded language sections
				</p>
			</div>
			<?php
		}
		?>
	</main>
	<div id="ask" class="ask" style="<?php echo isset($_COOKIE['hide_donate']) ? 'display: none;' : ''; ?>">
		<div style="position:absolute; top:12px; right:10px; display:flex; gap:10px;">
			<a href="https://tinyurl.com/supportsymbiota" target="_blank" class="button" style="background-color:#b9d432; text-decoration:none;" onclick="hideDonation(30*30*24*31);">
				<?= $LANG['DONATE'] ?>
			</a>
		</div>
		<p>Hello Portal User!
		<br><br>
		Do you use and love the CCH Portal and its collections?
		<br><br>
		This portal, and others like it, relies on a small, dedicated group of people, the Symbiota Support Hub (SSH) for website support.  
		<br><br>
		Federal funding for the SSH has ended, and this small team is now maintaining 52+ portals and 90 million occurrence records of life on earth… and still growing!
		<br><br>
		Please support this portal through a donation to the SSH.  Doing so helps each collection that shares data here.
		<br><br>
		Thank you very much.
		<br>
		-The CCH, & Nico, Ed, Jenn, Katie, Greg
		<div style="position:absolute; bottom:12px; right:10px; display:flex; gap:10px;">
			<button class="button" onclick="hideDonation(30*30*24*7);">Close</button>
		</div>
		</p>
	</div>

	<script>
	 function hideDonation(time){
		document.cookie = "hide_donate=true; max-age=" + time + "; path=/; Secure; SameSite=Strict";
		document.getElementById('ask').style.display = 'none';
	};
	</script>

	<?php if(!empty($GLOBALS['DONATE_LINK']) && file_exists($SERVER_ROOT . '/includes/donationButton.php')): ?>
		<?php include($SERVER_ROOT . '/includes/donationButton.php') ?>
	<?php endif ?>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</div>
</body>
</html>
