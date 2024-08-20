<?php
include_once('config/symbini.php');
include_once($SERVER_ROOT . '/classes/SiteMapManager.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT . '/content/lang/sitemap.' . $LANG_TAG . '.php'))
	include_once($SERVER_ROOT.'/content/lang/sitemap.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT.'/content/lang/sitemap.en.php');
header('Content-Type: text/html; charset=' . $CHARSET);

$smManager = new SiteMapManager();
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE . ' ' . $LANG['SITEMAP']; ?></title>
	<?php

	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');

	//detect custom css file
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$CSS_BASE_PATH.'/symbiota/sitemap.css')){
		echo '<link href="' . htmlspecialchars($CSS_BASE_PATH, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '/symbiota/sitemap.css" type="text/css" rel="stylesheet">' . "\r\n";
	}
	?>
	<script type="text/javascript">
		function submitTaxaNoImgForm(f){
			if(f.clid.value != ""){
				f.submit();
			}
			return false;
		}
	</script>
	<script type="text/javascript" src="js/symb/shared.js"></script>
	<style>
		.nested-li {
			margin-left: 1.5em;
		}
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($sitemapMenu)?$sitemapMenu:"true");
	include($SERVER_ROOT.'/includes/header.php');
	echo '<div class="navpath">';
	echo '<a href="index.php">' . $LANG['HOME'] . '</a> &gt; ';
	echo ' <b>' . $LANG['SITEMAP'] . '</b>';
	echo '</div>';
	$SHOULD_USE_HARVESTPARAMS = $SHOULD_USE_HARVESTPARAMS ?? false;
	$actionPage = $SHOULD_USE_HARVESTPARAMS ? "collections/index.php" : "collections/search/index.php";
	?>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG['SITEMAP']; ?></h1>
		<div id="sitemap">
			<h2><?php echo $LANG['COLLECTIONS']; ?></h2>
			<ul>
				<li><a href="<?php echo $actionPage ?>"><?= $LANG['SEARCHENGINE'] ?></a> - <?= $LANG['SEARCH_COLL'] ?></li>
				<li><a href="collections/misc/collprofiles.php"><?= $LANG['COLLECTIONS'] ?></a> - <?= $LANG['LISTOFCOLL'] ?></li>
				<li><a href="collections/misc/collstats.php"><?= $LANG['COLLSTATS'] ?></a></li>
				<?php
				if(isset($ACTIVATE_EXSICCATI) && $ACTIVATE_EXSICCATI){
					echo '<li><a href="collections/exsiccati/index.php">' . $LANG['EXSICC'] . '</a></li>';
				}
				?>
				<li><?php echo $LANG['DATA_PUBLISHING']; ?></li>
				<li class="nested-li"><a href="collections/datasets/rsshandler.php" target="_blank"><?= $LANG['COLLECTIONS_RSS'] ?></a></li>
				<li class="nested-li"><a href="collections/datasets/datapublisher.php"><?= $LANG['DARWINCORE'] ?></a> - <?= $LANG['PUBDATA'] ?></li>
				<?php
				$rssPath = 'content/dwca/rss.xml';
				$deprecatedRssPath = 'webservices/dwc/rss.xml';
				if(!file_exists($GLOBALS['SERVER_ROOT'].$rssPath) && file_exists($GLOBALS['SERVER_ROOT'].$deprecatedRssPath)) $rssPath = $deprecatedRssPath;
				if(file_exists($GLOBALS['SERVER_ROOT'].$rssPath)) echo '<li style="margin-left:15px;"><a href="' . $CLIENT_ROOT . htmlspecialchars($rssPath, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '" target="_blank">' . $LANG['RSS'] . '</a></li>';
				?>
				<li><a href="collections/misc/protectedspecies.php"><?= $LANG['PROTECTED_SPECIES'] ?></a> - <?= $LANG['LISTOFTAXA'] ?></li>
			</ul>
			<div id="imglib">
				<h2><?php echo $LANG['IMGLIB'];?></h2>
			</div>
			<ul>
				<li><a href="imagelib/index.php"><?= $LANG['IMGLIB'] ?></a></li>
				<li><a href="imagelib/search.php"><?= $LANG['IMAGE_SEARCH'] ?></a></li>
				<li><a href="imagelib/contributors.php"><?= $LANG['CONTRIB'] ?></a></li>
				<li><a href="includes/usagepolicy.php"><?= $LANG['USAGEPOLICY'] ?></a></li>
			</ul>

			<div id="resources">
				<h2><?php echo $LANG['ADDITIONAL_RESOURCES']; ?></h2>
			</div>
			<ul>
				<?php
				if($smManager->hasGlossary()){
					?>
					<li><a href="glossary/index.php"><?= $LANG['GLOSSARY'] ?></a></li>
					<?php
				}
				?>
				<li><a href="taxa/taxonomy/taxonomydisplay.php"><?= $LANG['TAXTREE'] ?></a></li>
				<li><a href="taxa/taxonomy/taxonomydynamicdisplay.php"><?= $LANG['DYNTAXTREE'] ?></a></li>
			</ul>

			<?php
			$clList = $smManager->getChecklistList((array_key_exists('ClAdmin',$USER_RIGHTS)?$USER_RIGHTS['ClAdmin']:0));
			$clAdmin = array();
			if($clList && isset($USER_RIGHTS['ClAdmin'])){
				$clAdmin = array_intersect_key($clList,array_flip($USER_RIGHTS['ClAdmin']));
			}
			?>
			<div id="bioinventory">
				<h2><?php echo $LANG['BIOTIC_INVENTORIES']; ?></h2>
			</div>
			<ul>
				<?php
				$projList = $smManager->getProjectList();
				if($projList){
					foreach($projList as $pid => $pArr){
						echo "<li><a href='projects/index.php?pid=" . htmlspecialchars($pid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "'>" . $pArr["name"] . "</a></li>\n";
						echo "<li class='nested-li'>Manager: " . $pArr["managers"] . "</li>\n";
					}
				}
				?>
				<li><a href="checklists/index.php"><?= $LANG['ALL_CHECKLISTS']  ?></a></li>
			</ul>

			<div id="datasets">
				<h2><?php echo $LANG['DATASETS'] ;?></h2>
			</div>
			<ul>
				<li><a href="collections/datasets/publiclist.php"><?= $LANG['ALLPUBDAT'] ?></a></li>
			</ul>
			<div id="dynamiclists"><h2><?php echo $LANG['DYNAMIC']; ?></h2></div>
			<ul>
				<li>
					<a href="checklists/dynamicmap.php?interface=checklist">
						<?php echo $LANG['CHECKLIST'];?>
					</a> - <?php echo $LANG['BUILDCHECK'];?>
				</li>
				<li>
					<a href="checklists/dynamicmap.php?interface=key">
						<?php echo $LANG['DYNAMICKEY'];?>
					</a> - <?php echo $LANG['BUILDDKEY'];?>
				</li>
			</ul>

			<section id="admin" class="fieldset-like" style="padding: 1.6rem 0 0 0">
				<h1>
					<span>
						<?php echo $LANG['MANAGTOOL'];?>
					</span>
				</h1>
				<?php
				if($SYMB_UID){
					if($IS_ADMIN){
						?>
						<h2 class="subheader">
							<span>
								<?php echo $LANG['ADMIN'];?>
							</span>
						</h2>
						<ul>
							<li>
								<a href="profile/usermanagement.php"><?= $LANG['USERPERM'] ?></a>
							</li>
						<?php // TODO: Identification Editor features need to be reviewed and refactored
						/*
							<li>
								<a href="profile/usertaxonomymanager.php"><?= $LANG['TAXINTER'] ?></a>
							</li>
						*/
						?>
							<li>
								<a href="<?= $CLIENT_ROOT ?>/collections/misc/collmetadata.php">
									<?= $LANG['CREATENEWCOLL'] ?>
								</a>
							</li>
							<li>
								<a href="<?= $CLIENT_ROOT ?>/geothesaurus/index.php">
									<?= $LANG['GEOTHESAURUS']  ?>
								</a>
							</li>
							<!--
							<li>
								<a href="<?= $CLIENT_ROOT ?>/collections/cleaning/coordinatevalidator.php">
									<?= $LANG['COORDVALIDATOR'] ?>
								</a>
							</li>
							-->
							<li>
								<a href="<?= $CLIENT_ROOT ?>/imagelib/admin/thumbnailbuilder.php">
									<?= $LANG['THUMBNAIL_BUILDER'] ?>
								</a>
							</li>
							<li>
								<a href="<?= $CLIENT_ROOT ?>/collections/admin/guidmapper.php">
									<?= $LANG['GUIDMAP'] ?>
								</a>
							</li>
							<li>
								<a href="<?= $CLIENT_ROOT ?>/collections/specprocessor/salix/salixhandler.php">
									<?= $LANG['SALIX'] ?>
								</a>
							</li>
							<li>
								<a href="<?= $CLIENT_ROOT ?>/glossary/index.php">
									<?= $LANG['GLOSSARY']  ?>
								</a>
							</li>
							<li>
								<a href="collections/map/staticmaphandler.php"><?= $LANG['MANAGE_TAXON_THUMBNAILS'] ?></a>
							</li>
						</ul>
						<?php
					}
					if($KEY_MOD_IS_ACTIVE || array_key_exists("KeyAdmin",$USER_RIGHTS)){
						echo '<h2 class="subheader"><span>' . $LANG['IDKEYS'] . '<span></h2>';
						if(!$KEY_MOD_IS_ACTIVE && array_key_exists("KeyAdmin",$USER_RIGHTS)){
							?>
							<div id="keymodule">
								<?php echo $LANG['KEYMODULE'];?>
							</div>
							<?php
						}
						?>
						<ul>
							<?php
							if($IS_ADMIN || array_key_exists("KeyAdmin",$USER_RIGHTS)){
								?>
								<li>
									<?= $LANG['AUTHOKEY'] ?> <a href="<?= $CLIENT_ROOT ?>/ident/admin/index.php"><?= $LANG['CHARASTATES'] ?></a>
								</li>
								<?php
							}
							if($IS_ADMIN || array_key_exists("KeyEditor",$USER_RIGHTS) || array_key_exists("KeyAdmin",$USER_RIGHTS)){
								?>
								<li>
									<?php echo $LANG['AUTHIDKEY'];?>
								</li>
								<?php
								//Show Checklists that user has explicit editing rights
								if($clAdmin){
									echo '<li>' . $LANG['CODINGCHARA'] . '</li>';
									echo '<ul>';
									foreach($clAdmin as $vClid => $name){
										echo "<li><a href='" . $CLIENT_ROOT . "/ident/tools/matrixeditor.php?clid=" . htmlspecialchars($vClid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "'>" . htmlspecialchars($name, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "</a></li>";
									}
									echo '</ul>';
								}
							}
							else{
								?>
								<li><?php echo $LANG['NOTAUTHIDKEY'];?></li>
								<?php
							}
							?>
						</ul>
						<?php
					}
					?>
					<h2 class="subheader">
						<span>
							<?php echo $LANG['IMAGES'];?>
						</span>
					</h2>
					<div id="images">
						<p class="description">
							<?php echo $LANG['SEESYMBDOC'];?>
							<a href="https://biokic.github.io/symbiota-docs/editor/images/"><?= $LANG['IMGSUB'] ?></a>
							<?php echo $LANG['FORANOVERVIEW'];?>
						</p>
					</div>
					<ul>
						<?php
						if($IS_ADMIN || array_key_exists('TaxonProfile',$USER_RIGHTS)){
							?>
							<li>
								<a href="taxa/profile/tpeditor.php?tabindex=1" target="_blank">
									<?php echo $LANG['BASICFIELD'];?>
								</a>
							</li>
							<?php
						}
						if($IS_ADMIN || array_key_exists("CollAdmin",$USER_RIGHTS) || array_key_exists("CollEditor",$USER_RIGHTS)){
							?>
							<li>
								<a href="collections/editor/observationsubmit.php">
									<?php echo $LANG['IMGOBSER'];?>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
					<h2 class="subheader">
						<span>
							<?php echo $LANG['BIOTIC_INVENTORIES'];?>
						</span>
					</h2>
					<ul>
						<?php
						if($IS_ADMIN){
							echo '<li><a href="projects/index.php?newproj=1">' . $LANG['ADDNEWPROJ'] . '</a></li>';
							if($projList){
								echo '<li><b>' . $LANG['LISTOFCURR'] . '</b> ' . $LANG['CLICKEDIT'] . '</li>';
								foreach($projList as $pid => $pArr){
									echo '<li class="nested-li"><a href="' . $CLIENT_ROOT . '/projects/index.php?pid=' . htmlspecialchars($pid, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&emode=1">' . htmlspecialchars($pArr['name'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '</a></li>';
								}
							}
							else{
								echo '<li>'.$LANG['NOPROJ'].'</li>';
							}
						}
						else{
							echo '<li>'.$LANG['NOTEDITPROJ'].'</li>';
						}
						?>
					</ul>
					<h2 class="subheader">
						<span>
							<?php echo $LANG['DATASETS'] ;?>
						</span>
					</h2>
					<ul>
						<li><a href="collections/datasets/index.php"><?= $LANG['DATMANPAG'] ?></a> - <?= $LANG['DATA_AUTHORIZED_TO_EDIT'] ?></li>
					</ul>
					<h2 class="subheader">
						<span>
							<?php echo $LANG['TAXONPROF'];?>
						</span>
					</h2>
					<?php
					if($IS_ADMIN || array_key_exists("TaxonProfile",$USER_RIGHTS)){
						?>
						<p class="description">
							<?php echo $LANG['THEFOLLOWINGSPEC'];?>
					</p>
						<ul>
							<li><a href="taxa/profile/tpeditor.php?taxon="><?= $LANG['SYN_COM'] ?></a></li>
							<li><a href="taxa/profile/tpeditor.php?taxon=&tabindex=4"><?= $LANG['TEXTDESC'] ?></a></li>
							<li><a href="taxa/profile/tpeditor.php?taxon=&tabindex=1"><?= $LANG['EDITIMG'] ?></a></li>
							<li class="nested-li"><a href="taxa/profile/tpeditor.php?taxon=&category=imagequicksort&tabindex=2"><?php echo $LANG['IMGSORTORD'];?></a></li>
							<li class="nested-li"><a href="taxa/profile/tpeditor.php?taxon=&category=imageadd&tabindex=3"><?= $LANG['ADDNEWIMG'] ?></a></li>
						</ul>
						<?php
					}
					else{
						?>
						<ul>
							<li><?php echo $LANG['NOTAUTHOTAXONPAGE'];?></li>
						</ul>
						<?php
					}
					?>
					<h2 class="subheader">
						<span>
							<?php echo $LANG['TAXONOMY'];?>
						</span>
					</h2>
					<ul>
						<?php
						if($IS_ADMIN || array_key_exists("Taxonomy",$USER_RIGHTS)){
							?>
							<li><?php echo $LANG['EDITTAXPL'];?> <a href="taxa/taxonomy/taxonomydisplay.php"><?= $LANG['TAXTREEVIEW'] ?></a></li>
							<li><a href="taxa/taxonomy/taxonomyloader.php"><?= $LANG['ADDTAXANAME'] ?></a></li>
							<li><a href="taxa/taxonomy/batchloader.php"><?= $LANG['BATCHTAXA'] ?></a></li>
							<?php
							if($IS_ADMIN || array_key_exists("Taxonomy",$USER_RIGHTS)){
								?>
								<li><a href="taxa/profile/eolmapper.php"><?= $LANG['EOLLINK'] ?></a></li>
								<?php
							}
						}
						else{
							echo '<li>' . $LANG['NOTEDITTAXA'] . '</li>';
						}
						?>
					</ul>
					<h2 class="subheader" >
						<span>
							<?php echo $LANG['CHECKLISTS'];?>
						</span>
					</h2>
					<p class="description">
						<?php echo $LANG['TOOLSFORMANAGE'];?>.
					</p>
					<ul>
						<?php
						if($clAdmin){
							foreach($clAdmin as $k => $v){
								echo "<li><a href='" . $CLIENT_ROOT . "/checklists/checklist.php?clid=" . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "&emode=1'>$v</a></li>";
							}
						}
						else{
							echo "<li>" . $LANG['NOTEDITCHECK'] . "</li>";
						}
						?>
					</ul>
					<?php
					if(isset($ACTIVATE_EXSICCATI) && $ACTIVATE_EXSICCATI){
						?>
						<h2 class="subheader">
							<span>
								<?php echo $LANG['EXSICCATII'];?>
							</span>
						</h2>
						<p class="description">
							<?php echo $LANG['ESCMOD'];?>.
						</p>
						<ul>
							<li><a href="collections/exsiccati/index.php"><?= $LANG['EXSICC'] ?></a></li>
						</ul>
						<?php
					}
					?>
					<h2 class="subheader">
						<span>
							<?php echo $LANG['COLLECTIONS'];?>
						</span>
					</h2>
					<p class="description">
						<?php echo $LANG['PARA1'];?>
					</p>
					<h3 class="subheader">
						<span>
							<?php echo $LANG['COLLLIST'];?>
						</span>
					</h3>
					<div>
						<ul>
						<?php
						$smManager->setCollectionList();
						if($collList = $smManager->getCollArr()){
							foreach($collList as $k => $cArr){
								echo '<li>';
								echo '<a href="' . $CLIENT_ROOT . '/collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&emode=1">';
								echo $cArr['name'];
								echo '</a>';
								echo '</li>';
							}
						}
						else{
							echo "<li>".$LANG['NOEDITCOLL']."</li>";
						}
						?>
						</ul>
					</div>

					<h2 class="subheader">
						<span>
							<?php echo $LANG['OBSERV'];?>
						</span>
					</h2>
					<p class="description">
						<?php echo $LANG['PARA2'];?>
						<a href="https://biokic.github.io/symbiota-docs/col_obs/" target="_blank"><?= $LANG['SYMBDOCU'] ?></a> <?= $LANG['FORMOREINFO'] ?>.
					<p class="description">
					<h3 class="subheader">
						<span>
							<?php echo $LANG['OIVS'];?>
						</span>
					</h3>
					<div>
						<ul>
							<?php
							$obsList = $smManager->getObsArr();
							$genObsList = $smManager->getGenObsArr();
							$obsManagementStr = '';

							if($obsList){
								foreach($genObsList as $k => $oArr){
									?>
									<li>
										<a href="collections/editor/observationsubmit.php?collid=<?php echo htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
									if($oArr['isadmin']) $obsManagementStr .= '<li><a href="collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&emode=1">' . htmlspecialchars($oArr['name'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "</a></li>\n";
								}
								foreach($obsList as $k => $oArr){
									?>
									<li>
										<a href="collections/editor/observationsubmit.php?collid=<?php echo htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
									if($oArr['isadmin']) $obsManagementStr .= '<li><a href="collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . '&emode=1">' . htmlspecialchars($oArr['name'], ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE) . "</a></li>\n";
								}
							}
							else{
								echo "<li>" . $LANG['NOOBSPROJ'] ."</li>";
							}
							?>
						</ul>
						<?php
						if($genObsList){
							?>
							<h3 class="subheader"><span>
									<?php echo $LANG['PERSONAL'];?>
								</span>
							</h3>
							<ul>
								<?php
								foreach($genObsList as $k => $oArr){
									?>
									<li>
										<a href="collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($k, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>&emode=1">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
								}
								?>
							</ul>
							<?php
						}
						if($obsManagementStr){
							?>
							<h3 class="subheader">
								<span>
									<?php echo $LANG['OPM'];?>
								</span>
							</h3>
							<ul>
								<?php echo $obsManagementStr; ?>
							</ul>
						<?php
						}
					?>
					</div>
					<?php
				}
				else{
					echo '' . $LANG['PLEASE'] . ' <a href="' . $CLIENT_ROOT . '/profile/index.php?refurl=../sitemap.php">' . $LANG['LOGIN'] . '</a> ' . $LANG['TOACCESS'] . '<br/>' . $LANG['CONTACTPORTAL'] . '.';
				}
			?>
			</section>
			<div id="symbiotaschema">
				<img style="height:1.85rem" src="https://img.shields.io/badge/Symbiota-v<?= $CODE_VERSION ?>-blue.svg" alt="a blue badge depicting Symbiota software version" />
				<img style="height:1.85rem" src="https://img.shields.io/badge/Schema-v<?= $smManager->getSchemaVersion() ?>-blue.svg" alt="a blue badge depicting Symbiota database schema version" />
			</div>
		</div>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
