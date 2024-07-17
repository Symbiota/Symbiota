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
		<title>ARS Biocollections Portal</title>
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
		<main id="innertext" class="footer-wrapper" style="padding-top:0;">
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
							<div class="layout__container">
								<div class="layout__region layout__region--content">
									<section class="block block-title-block block-nal-page-title-block clearfix path-frontpage">
										<h1 class="page-header page-heading">Biocollections of the USDA Agricultural Research Service</h1>
									</section>
								</div>
								<div  class="layout__region layout__region--first">
									<div class="block block-layout-builder block-inline-blockcard usa-card clearfix">
										<div class="usa-card__container">
											<header class="usa-card__header">
												<h3 class="usa-card__heading">U.S. National Arboretum Herbarium (NA)</h3>
											</header>
											<div class="usa-card__media">
												<div class="usa-card__img">
													<img style="height:235px;" loading="lazy" src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/NA_tile.png" width="330" height="220" alt="Plant with several leaves on the bottom right hand corner and a cluster of purple flowers in the upper left" class="img-responsive" />
												</div>
											</div>
											<div class="usa-card__body">
												<div class="field field--name-card-body field--type-text-long field--label-hidden field--item">
													<p>Pressed, dried plant specimens documenting USDA research and botanical exploration.</p>
												</div>
											</div>
											<div class="usa-card__footer">
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="About NA Collection" href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $NA_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
												</div>
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="Search NA Collection" href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $NA_COLLID?>" class="usa-button card-button">Search Collection</a>
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
													<img style="height:235px;" loading="lazy" src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/BARC_tile.png" width="330" height="220" alt="Five brown almond-shaped seeds about 5 mm long and about 3 mm wide with a 5mm scale provided" class="img-responsive" />
												</div>
											</div>
											<div class="usa-card__body">
												<div class="field field--name-card-body field--type-text-long field--label-hidden field--item">
													<p>Extensive reference collection of preserved seed and fruit samples from around the globe.</p>
												</div>
											</div>
											<div class="usa-card__footer">
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="About BARC Collection" href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BARC_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
												</div>
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="Search BARC Collection" href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BARC_COLLID?>" class="usa-button card-button">Search Collection</a>
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
													<img style="height:235px;" loading="lazy" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJkAYHVampT3wmtnFbdohmwyCsiIpfFVDuJQ&usqp=CAU" width="330" height="220" alt="A brigth orange cluster of mushrooms on top of bark with moss on it" class="img-responsive" />
												</div>
											</div>
											<div class="usa-card__body">
												<div class="field field--name-card-body field--type-text-long field--label-hidden field--item">
													<p>The Western Hemisphere's largest fungarium, including the John A. Stevenson Mycological Library.</p>
												</div>
											</div>
											<div class="usa-card__footer">
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="About BPI Collection" href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $BPI_SNAPSHOT_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
												</div>
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="Search BPI Collection" href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $BPI_SNAPSHOT_COLLID?>" class="usa-button card-button">Search Collection</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div  class="layout__region layout__region--first">
									<div class="block block-layout-builder block-inline-blockcard usa-card clearfix">
										<div class="usa-card__container top-breathing-room-rel">
											<header class="usa-card__header">
												<h3 class="usa-card__heading">Entomopathogenic Fungal Cultures Collection</h3>
											</header>
											<div class="usa-card__media">
												<div class="usa-card__img">
													<img style="height:235px;" loading="lazy" src="<?php echo $CLIENT_ROOT ?>/assets/uswds/img/ARSEF_tilephoto.jpg" width="330" height="220" alt="Irridescent pink and green beetle elytra with white fungal fruiting bodies exploding out of all corners" class="img-responsive" />
												</div>
											</div>
											<div class="usa-card__body">
												<div class="field field--name-card-body field--type-text-long field--label-hidden field--item">
													<p>The largest, most comprehensive repository of insect pathogenic fungal strains and germplasm.</p>
												</div>
											</div>
											<div class="usa-card__footer">
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="About ARSEF Collection" href="<?php echo $CLIENT_ROOT?>/collections/misc/collprofiles.php?collid=<?php echo  $ARSEF_SNAPSHOT_COLLID?>" class="usa-button card-button" style="margin-bottom: 1rem;">About Collection</a>
												</div>
												<div class="field field--name-card-link field--type-link field--label-hidden field--item">
													<a aria-label="Search ARSEF Collection" href="<?php echo $CLIENT_ROOT?>/collections/<?php echo $actionPage ?>?db=<?php echo  $ARSEF_SNAPSHOT_COLLID?>" class="usa-button card-button">Search Collection</a>
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
		</main>
		<?php
		include($SERVER_ROOT . '/includes/footer.php');
		?>
	</body>
</html>
