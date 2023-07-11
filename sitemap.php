<!DOCTYPE html>
<?php
include_once('config/symbini.php');
include_once($SERVER_ROOT.'/classes/SiteMapManager.php');
include_once($SERVER_ROOT.'/content/lang/sitemap.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
$submitAction = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$smManager = new SiteMapManager();
?>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE.' '.$LANG['SITEMAP'];?></title>
	<?php

	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');

	//detect custom css file
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$CSS_BASE_PATH.'/symbiota/sitemap.css')){
		echo '<link href="' . htmlspecialchars($CSS_BASE_PATH, HTML_SPECIAL_CHARS_FLAGS) . '/symbiota/sitemap.css" type="text/css" rel="stylesheet">'."\r\n";
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
</head>
<body>
	<?php
	$displayLeftMenu = (isset($sitemapMenu)?$sitemapMenu:"true");
	include($SERVER_ROOT.'/includes/header.php');
	echo '<div class="navpath">';
	echo '<a href="index.php">'.$LANG['HOME'].'</a> &gt; ';
	echo ' <b>'.$LANG['SITEMAP'].'</b>';
	echo '</div>';
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1><?php echo $LANG['SITEMAP']; ?></h1>
		<div id="sitemap">
			<h2><?php echo $LANG['COLLECTIONS']; ?></h2>
			<ul>
				<li><a href="collections/index.php"><?php echo htmlspecialchars($LANG['SEARCHENGINE'], HTML_SPECIAL_CHARS_FLAGS);?></a> - <?php echo htmlspecialchars($LANG['SEARCH_COLL'], HTML_SPECIAL_CHARS_FLAGS);?></li>
				<li><a href="collections/misc/collprofiles.php"><?php echo htmlspecialchars($LANG['COLLECTIONS'], HTML_SPECIAL_CHARS_FLAGS);?></a> - <?php echo htmlspecialchars($LANG['LISTOFCOLL'], HTML_SPECIAL_CHARS_FLAGS);?></li>
				<li><a href="collections/misc/collstats.php"><?php echo htmlspecialchars($LANG['COLLSTATS'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
				<?php
				if(isset($ACTIVATE_EXSICCATI) && $ACTIVATE_EXSICCATI){
					echo '<li><a href="collections/exsiccati/index.php">' . htmlspecialchars($LANG['EXSICC'], HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
				}
				?>
				<li><?php echo (isset($LANG['DATA_PUBLISHING'])?$LANG['DATA_PUBLISHING']:'Data Publishing');?></li>
				<li class="nested-li"><a href="collections/datasets/rsshandler.php" target="_blank"><?php echo htmlspecialchars($LANG['COLLECTIONS_RSS'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
				<li class="nested-li"><a href="collections/datasets/datapublisher.php"><?php echo htmlspecialchars($LANG['DARWINCORE'], HTML_SPECIAL_CHARS_FLAGS);?></a> - <?php echo htmlspecialchars($LANG['PUBDATA'], HTML_SPECIAL_CHARS_FLAGS);?></li>
				<?php
				$rssPath = 'content/dwca/rss.xml';
				$deprecatedRssPath = 'webservices/dwc/rss.xml';
				if(!file_exists($GLOBALS['SERVER_ROOT'].$rssPath) && file_exists($GLOBALS['SERVER_ROOT'].$deprecatedRssPath)) $rssPath = $deprecatedRssPath;
				if(file_exists($GLOBALS['SERVER_ROOT'].$rssPath)) echo '<li style="margin-left:15px;"><a href="' . htmlspecialchars($GLOBALS['CLIENT_ROOT'], HTML_SPECIAL_CHARS_FLAGS) . htmlspecialchars($rssPath, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">' . htmlspecialchars($LANG['RSS'], HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
				?>
				<li><a href="collections/misc/protectedspecies.php"><?php echo htmlspecialchars($LANG['PROTECTED_SPECIES'], HTML_SPECIAL_CHARS_FLAGS);?></a> - <?php echo htmlspecialchars($LANG['LISTOFTAXA'], HTML_SPECIAL_CHARS_FLAGS);?></li>
			</ul>
			<div id="imglib"><h2><?php echo $LANG['IMGLIB'];?></h2></div>
			<ul>
				<li><a href="imagelib/index.php"><?php echo htmlspecialchars($LANG['IMGLIB'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
				<li><a href="imagelib/search.php"><?php echo htmlspecialchars(($LANG['IMAGE_SEARCH']?$LANG['IMAGE_SEARCH']:'Interactive Search Tool'), HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
				<li><a href="imagelib/contributors.php"><?php echo htmlspecialchars($LANG['CONTRIB'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
				<li><a href="includes/usagepolicy.php"><?php echo htmlspecialchars($LANG['USAGEPOLICY'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
			</ul>

			<div id="resources"><h2><?php echo isset($LANG['ADDITIONAL_RESOURCES'])?$LANG['ADDITIONAL_RESOURCES']:'Additional Resources';?></h2></div>
			<ul>
				<?php
				if($smManager->hasGlossary()){
					?>
					<li><a href="glossary/index.php"><?php echo htmlspecialchars(isset($LANG['GLOSSARY'])?$LANG['GLOSSARY']:'Glossary', HTML_SPECIAL_CHARS_FLAGS);?></a></li>
					<?php
				}
				?>
				<li><a href="taxa/taxonomy/taxonomydisplay.php"><?php echo htmlspecialchars($LANG['TAXTREE'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
				<li><a href="taxa/taxonomy/taxonomydynamicdisplay.php"><?php echo htmlspecialchars($LANG['DYNTAXTREE'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
			</ul>

			<?php
			$clList = $smManager->getChecklistList((array_key_exists('ClAdmin',$USER_RIGHTS)?$USER_RIGHTS['ClAdmin']:0));
			$clAdmin = array();
			if($clList && isset($USER_RIGHTS['ClAdmin'])){
				$clAdmin = array_intersect_key($clList,array_flip($USER_RIGHTS['ClAdmin']));
			}
			?>
			<div id="bioinventory"><h2><?php echo (isset($LANG['BIOTIC_INVENTORIES'])?$LANG['BIOTIC_INVENTORIES']:'Biotic Inventory Projects'); ?></h2></div>
			<ul>
				<?php
				$projList = $smManager->getProjectList();
				if($projList){
					foreach($projList as $pid => $pArr){
						echo "<li><a href='projects/index.php?pid=" . htmlspecialchars($pid, HTML_SPECIAL_CHARS_FLAGS) . "'>" . $pArr["name"] . "</a></li>\n";
						echo "<li class='nested-li'>Manager: " . $pArr["managers"] . "</li>\n";
					}
				}
				?>
				<li><a href="checklists/index.php"><?php echo htmlspecialchars((isset($LANG['ALL_CHECKLISTS'])?$LANG['ALL_CHECKLISTS']:'All Public Checklists'), HTML_SPECIAL_CHARS_FLAGS); ?></a></li>
			</ul>

			<h2><?php echo (isset($LANG['DATASETS'])?$LANG['DATASETS']:'Datasets') ;?></h2>
			<ul>
				<li><a href="collections/datasets/publiclist.php"><?php echo htmlspecialchars((isset($LANG['ALLPUBDAT'])?$LANG['ALLPUBDAT']:'All Publicly Viewable Datasets'), HTML_SPECIAL_CHARS_FLAGS) ;?></a></li>
			</ul>
			<div id="dynamiclists"><h2><?php echo $LANG['DYNAMIC'];?></h2></div>
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

			<section id="admin" class="fieldset-like">
				<h1>
					<span>
						<?php echo $LANG['MANAGTOOL'];?>
					</span>
				</h1>
				<?php
				if($SYMB_UID){
					if($IS_ADMIN){
						?>
						<h3><?php echo $LANG['ADMIN'];?></h3>
						<ul>
							<li>
								<a href="profile/usermanagement.php"><?php echo htmlspecialchars($LANG['USERPERM'], HTML_SPECIAL_CHARS_FLAGS);?></a>
							</li>
							<li>
								<a href="profile/usertaxonomymanager.php"><?php echo htmlspecialchars($LANG['TAXINTER'], HTML_SPECIAL_CHARS_FLAGS);?></a>
							</li>
							<li>
								<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/misc/collmetadata.php">
									<?php echo htmlspecialchars($LANG['CREATENEWCOLL'], HTML_SPECIAL_CHARS_FLAGS);?>
								</a>
							</li>
							<li>
								<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/geothesaurus/index.php">
									<?php echo htmlspecialchars(isset($LANG['GEOTHESAURUS'])?$LANG['GEOTHESAURUS']:'Geographic Thesaurus', HTML_SPECIAL_CHARS_FLAGS); ?>
								</a>
							</li>
							<!--
							<li>
								<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/cleaning/coordinatevalidator.php">
									<?php echo htmlspecialchars(isset($LANG['COORDVALIDATOR'])?$LANG['COORDVALIDATOR']:'Verify coordinates against political boundaries', HTML_SPECIAL_CHARS_FLAGS);?>
								</a>
							</li>
							-->
							<li>
								<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/imagelib/admin/thumbnailbuilder.php">
									<?php echo htmlspecialchars($LANG['THUMBNAIL_BUILDER'], HTML_SPECIAL_CHARS_FLAGS);?>
								</a>
							</li>
							<li>
								<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/admin/guidmapper.php">
									<?php echo htmlspecialchars($LANG['GUIDMAP'], HTML_SPECIAL_CHARS_FLAGS);?>
								</a>
							</li>
							<li>
								<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/collections/specprocessor/salix/salixhandler.php">
									<?php echo htmlspecialchars($LANG['SALIX'], HTML_SPECIAL_CHARS_FLAGS);?>
								</a>
							</li>
							<li>
								<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/glossary/index.php">
									<?php echo htmlspecialchars($LANG['GLOSSARY'], HTML_SPECIAL_CHARS_FLAGS);?>
								</a>
							</li>
						</ul>
						<?php
					}
					if($KEY_MOD_IS_ACTIVE || array_key_exists("KeyAdmin",$USER_RIGHTS)){
						echo '<h3>'.$LANG['IDKEYS'].'</h3>';
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
									<?php echo htmlspecialchars($LANG['AUTHOKEY'], HTML_SPECIAL_CHARS_FLAGS);?> <a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/ident/admin/index.php"><?php echo htmlspecialchars($LANG['CHARASTATES'], HTML_SPECIAL_CHARS_FLAGS);?></a>
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
									echo '<li>'.$LANG['CODINGCHARA'].'</li>';
									echo '<ul>';
									foreach($clAdmin as $vClid => $name){
										echo "<li><a href='" . htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . "/ident/tools/matrixeditor.php?clid=" . htmlspecialchars($vClid, HTML_SPECIAL_CHARS_FLAGS) . "'>" . htmlspecialchars($name, HTML_SPECIAL_CHARS_FLAGS) . "</a></li>";
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
					<h3><?php echo $LANG['IMAGES'];?></h3>
					<div id="images">
						<p class="description">
							<?php echo $LANG['SEESYMBDOC'];?>
							<a href="https://biokic.github.io/symbiota-docs/editor/images/"><?php echo htmlspecialchars($LANG['IMGSUB'], HTML_SPECIAL_CHARS_FLAGS);?></a>
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

					<h3><?php echo $LANG['BIOTIC_INVENTORIES'];?></h3>
					<ul>
						<?php
						if($IS_ADMIN){
							echo '<li><a href="projects/index.php?newproj=1">' . htmlspecialchars($LANG['ADDNEWPROJ'], HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
							if($projList){
								echo '<li><b>'.$LANG['LISTOFCURR'].'</b> '.$LANG['CLICKEDIT'].'</li>';
								echo '<ul>';
								foreach($projList as $pid => $pArr){
									echo '<li><a href="' . htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . '/projects/index.php?pid=' . htmlspecialchars($pid, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . htmlspecialchars($pArr['name'], HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
								}
								echo '</ul>';
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

					<h3><?php echo (isset($LANG['DATASETS'])?$LANG['DATASETS']:'Datasets') ;?></h3>
					<ul>
						<li><a href="collections/datasets/index.php"><?php echo htmlspecialchars((isset($LANG['DATMANPAG'])?$LANG['DATMANPAG']:'Dataset Management Page</a> - datasets you are authorized to edit'), HTML_SPECIAL_CHARS_FLAGS) ;?></li>
					</ul>
					<h3><?php echo $LANG['TAXONPROF'];?></h3>
					<?php
					if($IS_ADMIN || array_key_exists("TaxonProfile",$USER_RIGHTS)){
						?>
						<p class="description">
							<?php echo $LANG['THEFOLLOWINGSPEC'];?>
					</p>
						<ul>
							<li><a href="taxa/profile/tpeditor.php?taxon="><?php echo htmlspecialchars($LANG['SYN_COM'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
							<li><a href="taxa/profile/tpeditor.php?taxon=&tabindex=4"><?php echo htmlspecialchars($LANG['TEXTDESC'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
							<li><a href="taxa/profile/tpeditor.php?taxon=&tabindex=1"><?php echo htmlspecialchars($LANG['EDITIMG'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
							<ul>
								<li><a href="taxa/profile/tpeditor.php?taxon=&category=imagequicksort&tabindex=2"><?php echo $LANG['IMGSORTORD'];?></a></li>
								<li><a href="taxa/profile/tpeditor.php?taxon=&category=imageadd&tabindex=3"><?php echo htmlspecialchars($LANG['ADDNEWIMG'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
							</ul>
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
					<h3><?php echo $LANG['TAXONOMY'];?></h3>
					<ul>
						<?php
						if($IS_ADMIN || array_key_exists("Taxonomy",$USER_RIGHTS)){
							?>
							<li><?php echo $LANG['EDITTAXPL'];?> <a href="taxa/taxonomy/taxonomydisplay.php"><?php echo htmlspecialchars($LANG['TAXTREEVIEW'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
							<li><a href="taxa/taxonomy/taxonomyloader.php"><?php echo htmlspecialchars($LANG['ADDTAXANAME'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
							<li><a href="taxa/taxonomy/batchloader.php"><?php echo htmlspecialchars($LANG['BATCHTAXA'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
							<?php
							if($IS_ADMIN || array_key_exists("Taxonomy",$USER_RIGHTS)){
								?>
								<li><a href="taxa/profile/eolmapper.php"><?php echo htmlspecialchars($LANG['EOLLINK'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
								<?php
							}
						}
						else{
							echo '<li>'.$LANG['NOTEDITTAXA'].'</li>';
						}
						?>
					</ul>

					<h3><?php echo $LANG['CHECKLISTS'];?></h3>
					<p class="description">
						<?php echo $LANG['TOOLSFORMANAGE'];?>.
					</p>
					<ul>
						<?php
						if($clAdmin){
							foreach($clAdmin as $k => $v){
								echo "<li><a href='" . htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . "/checklists/checklist.php?clid=" . htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS) . "&emode=1'>$v</a></li>";
							}
						}
						else{
							echo "<li>".$LANG['NOTEDITCHECK']."</li>";
						}
						?>
					</ul>

					<?php
					if(isset($ACTIVATE_EXSICCATI) && $ACTIVATE_EXSICCATI){
						?>
						<h3><?php echo $LANG['EXSICCATII'];?></h3>
						<p class="description">
							<?php echo $LANG['ESCMOD'];?>.
						</p>
						<ul>
							<li><a href="collections/exsiccati/index.php"><?php echo htmlspecialchars($LANG['EXSICC'], HTML_SPECIAL_CHARS_FLAGS);?></a></li>
						</ul>
						<?php
					}
					?>

					<h3><?php echo $LANG['COLLECTIONS'];?></h3>
					<p class="description">
						<?php echo $LANG['PARA1'];?>
					</p>
					<div id="admincollection">
						<h4>
							<?php echo $LANG['COLLLIST'];?>
						</h4>
						<ul>
						<?php
						$smManager->setCollectionList();
						if($collList = $smManager->getCollArr()){
							foreach($collList as $k => $cArr){
								echo '<li>';
								echo '<a href="' . htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . '/collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">';
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

					<h3><?php echo $LANG['OBSERV'];?></h3>
					<p class="description">
						<?php echo $LANG['PARA2'];?>
						<a href="https://symbiota.org/specimen-data-management/" target="_blank"><?php echo htmlspecialchars($LANG['SYMBDOCU'], HTML_SPECIAL_CHARS_FLAGS);?></a> <?php echo htmlspecialchars($LANG['FORMOREINFO'], HTML_SPECIAL_CHARS_FLAGS);?>.
					<p class="description">
					<div id="adminobservation">
						<h4>
							<?php echo $LANG['OIVS'];?>
						</h4>
						<ul>
							<?php
							$obsList = $smManager->getObsArr();
							$genObsList = $smManager->getGenObsArr();
							$obsManagementStr = '';

							if($obsList){
								foreach($genObsList as $k => $oArr){
									?>
									<li>
										<a href="collections/editor/observationsubmit.php?collid=<?php echo htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS); ?>">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
									if($oArr['isadmin']) $obsManagementStr .= '<li><a href="collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . htmlspecialchars($oArr['name'], HTML_SPECIAL_CHARS_FLAGS) . "</a></li>\n";
								}
								foreach($obsList as $k => $oArr){
									?>
									<li>
										<a href="collections/editor/observationsubmit.php?collid=<?php echo htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS); ?>">
											<?php echo $oArr['name']; ?>
										</a>
									</li>
									<?php
									if($oArr['isadmin']) $obsManagementStr .= '<li><a href="collections/misc/collprofiles.php?collid=' . htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS) . '&emode=1">' . htmlspecialchars($oArr['name'], HTML_SPECIAL_CHARS_FLAGS) . "</a></li>\n";
								}
							}
							else{
								echo "<li>".$LANG['NOOBSPROJ']."</li>";
							}
							?>
						</ul>
						<?php
						if($genObsList){
							?>
							<h4>
								<?php echo $LANG['PERSONAL'];?>
							</h4>
							<ul>
								<?php
								foreach($genObsList as $k => $oArr){
									?>
									<li>
										<a href="collections/misc/collprofiles.php?collid=<?php echo htmlspecialchars($k, HTML_SPECIAL_CHARS_FLAGS); ?>&emode=1">
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
							<h4>
								<?php echo $LANG['OPM'];?>
							</h4>
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
					echo ''.$LANG['PLEASE'].' <a href="' . htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS) . '/profile/index.php?refurl=../sitemap.php">' . htmlspecialchars($LANG['LOGIN'], HTML_SPECIAL_CHARS_FLAGS) . '</a>' . htmlspecialchars($LANG['TOACCESS'], HTML_SPECIAL_CHARS_FLAGS) . '<br/>' . htmlspecialchars($LANG['CONTACTPORTAL'], HTML_SPECIAL_CHARS_FLAGS) . '.';
				}
			?>
			</section>
			<div id="symbiotaschema">
				<img src="https://img.shields.io/badge/Symbiota-v<?php echo $CODE_VERSION; ?>-blue.svg" alt="a blue badge depicting Symbiota software version" />
				<img src="https://img.shields.io/badge/Schema-<?php echo 'v'.$smManager->getSchemaVersion(); ?>-blue.svg" alt="a blue badge depicting Symbiota database schema version" />
			</div>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
