<?php
include_once('config/symbini.php');
if($LANG_TAG == 'en' || !file_exists($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/index.en.php');
else include_once($SERVER_ROOT.'/content/lang/index.'.$LANG_TAG.'.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$SHOULD_USE_HARVESTPARAMS = $SHOULD_USE_HARVESTPARAMS ?? true;
$actionPage = $SHOULD_USE_HARVESTPARAMS ? "harvestparams.php" : "./search/index.php";

?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	include_once($SERVER_ROOT . '/includes/googleanalytics.php');
	?>
</head>
<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath"></div>
	<div id="innertext" class="footer-header-wrapper">
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
				<ul class="usa-card-group" style="margin-left: 0rem; padding-left: 0rem;">
					<li class="usa-card tablet:grid-col-4">
						<div class="usa-card__container">
							<div class="usa-card__header">
								<h2 class="usa-card__heading">U.S. National Arboretum Herbarium (NA)</h2>
							</div>
							<div class="usa-card__media">
								<div class="usa-card__img">
								<img class="card-image"
									src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/NA_tile.png"
									alt="An image of a flower"
								/>
								</div>
							</div>
							<div class="usa-card__body" style="min-height: 8rem;">
								<p>
								700,000 pressed, preserved plant specimens representing USDA research and botanical exploration.
								</p>
							</div>
							<div class="usa-card__footer">
								<a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $NA_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
								<a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $NA_COLLID?>" class="usa-button card-button">Search Collection</a>
							</div>
						</div>
					</li>
					<li class="usa-card tablet:grid-col-4">
						<div class="usa-card__container">
							<div class="usa-card__header">
								<h2 class="usa-card__heading">U.S. National Seed Herbarium (BARC)</h2>
							</div>
							<div class="usa-card__media">
								<div class="usa-card__img">
								<img class="card-image"
									src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/BARC_tile.png"
									alt="An image of a seed"
								/>
								</div>
							</div>
							<div class="usa-card__body" style="min-height: 8rem;">
								<p>
								Over 150,000 preserved seed and fruit samples, primarily of non-native plant species.
								</p>
							</div>
							<div class="usa-card__footer">
								<a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BARC_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
								<a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BARC_COLLID?>" class="usa-button card-button">Search Collection</a>
							</div>
						</div>
					</li>
					<li class="usa-card tablet:grid-col-4">
						<div class="usa-card__container">
							<div class="usa-card__header">
								<h2 class="usa-card__heading">U.S. National Fungus Collections (BPI)</h2>
							</div>
							<div class="usa-card__media">
								<div class="usa-card__img">
								<img class="card-image"
									src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJkAYHVampT3wmtnFbdohmwyCsiIpfFVDuJQ&usqp=CAU"
									alt="An image of a mushroom"
								/>
								</div>
							</div>
							<div class="usa-card__body" style="min-height: 8rem;">
								<p>
								The Western Hemisphere’s largest fungal herbarium, including the John A. Stevenson Mycological Library.
								</p>
							</div>
							<div class="usa-card__footer">
								<a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BPI_SNAPSHOT_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
								<a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BPI_SNAPSHOT_COLLID?>" class="usa-button card-button">Search Collection</a>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
