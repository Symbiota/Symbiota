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
	<body id="main-content">
		<div class="overlay show" id="box1">
  			<div class="spinner-border spinner-border-sm _text-primary spinner1 " id="testout" >
			</div>
		</div>
	<div style="display: none;">
		<?php
		include($SERVER_ROOT . '/includes/header.php');
		?>
		<div class="navpath"></div>
		<div id="innertext" class="footer-wrapper" style="max-width: 80%; padding: 0; display: none;">
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
				<div class="content">
					<div class="layout layout--100">
						<div class="layout--bg layout layout--33-34-33">
							<!-- <div class="layout__header">
								<h2 id="section--staff-picks" class="layout__header__title">Collections</h2>
							</div> -->
							<div class="layout__container">
								<div  class="layout__region layout__region--first">
									<div class="block block-layout-builder block-inline-blockcard usa-card clearfix">
										<div class="usa-card__container">
											<header class="usa-card__header">
												<h3 class="usa-card__heading">U.S. National Arboretum Herbarium (NA)</h3>
											</header>
											<div class="usa-card__media">
												<div class="usa-card__img">
													<img style="height:235px;" loading="lazy" src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/NA_tile.png" width="330" height="220" alt="An image of a flower" class="img-responsive" />
												</div>
											</div>
											<div class="usa-card__body">
												<div class="field field--name-card-body field--type-text-long field--label-hidden field--item">
													<p>700,000 pressed, preserved plant specimens representing USDA research and botanical exploration.</p>
												</div>
											</div>
											<div class="usa-card__footer">
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $NA_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
												</div>
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $NA_COLLID?>" class="usa-button card-button">Search Collection</a>
												</div>
											</div>
										</div>
									</div>
								</div>
		
								<div  class="layout__region layout__region--second">
									<div class="block block-layout-builder block-inline-blockcard usa-card clearfix">
										<div class="usa-card__container">
											<header class="usa-card__header">
												<h3 class="usa-card__heading">U.S. National Seed Herbarium (BARC)</h3>
											</header>
											<div class="usa-card__media">
												<div class="usa-card__img">
													<img style="height:235px;" loading="lazy" src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/BARC_tile.png" width="330" height="220" alt="An image of a seed" class="img-responsive" />
												</div>
											</div>
											<div class="usa-card__body">
												<div class="field field--name-card-body field--type-text-long field--label-hidden field--item">
													<p>Over 150,000 preserved seed and fruit samples, primarily of non-native plant species.</p>
												</div>
											</div>
											<div class="usa-card__footer">
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BARC_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
												</div>
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BARC_COLLID?>" class="usa-button card-button">Search Collection</a>
												</div>
											</div>
										</div>
									</div>
								</div>
					
								<div  class="layout__region layout__region--third">
									<div class="block block-layout-builder block-inline-blockcard usa-card clearfix">
										<div class="usa-card__container">
											<header class="usa-card__header">
												<h3 class="usa-card__heading">U.S. National Fungus Collections (BPI)</h3>
											</header>
											<div class="usa-card__media">
												<div class="usa-card__img">
													<img style="height:235px;" loading="lazy" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJkAYHVampT3wmtnFbdohmwyCsiIpfFVDuJQ&usqp=CAU" width="330" height="220" alt="An image of a mushroom" class="img-responsive" />
												</div>
											</div>
											<div class="usa-card__body">
												<div class="field field--name-card-body field--type-text-long field--label-hidden field--item">
													<p>The Western Hemisphere’s largest fungal herbarium, including the John A. Stevenson Mycological Library.</p>
												</div>
											</div>
											<div class="usa-card__footer">
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BPI_SNAPSHOT_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
												</div>
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BPI_SNAPSHOT_COLLID?>" class="usa-button card-button">Search Collection</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT . '/includes/footer.php');
		?>
	</div>
</body>
</html>
