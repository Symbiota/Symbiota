<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/ProfileManager.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once($SERVER_ROOT . '/classes/utilities/Sanitize.php');

Language::load('profile/viewprofile');

header('Content-Type: text/html; charset=' . $CHARSET);

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
	<head>
		<title><?= $DEFAULT_TITLE . ' ' . $LANG['ADMIN_MENU'];?></title>
	</head>
	<div style="margin:10px;">
		<h1 class="page-heading screen-reader-only"><?= $LANG['ADMIN_MENU']; ?></h1>
		<?php
		if($IS_ADMIN){
			?>
			<section class="fieldset-like">
				<h2><span><?= $LANG['ADMIN_MENU']; ?></span></h2>
				<ul>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/profile/usermanagement.php"><?= $LANG['USER_PERMISSIONS'] ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/collections/misc/collmetadata.php"><?= $LANG['CREATE_NEW_COLLECTION'] ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/geothesaurus/index.php"><?= $LANG['GEO_THESAURUS']  ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/glossary/index.php"><?= $LANG['GLOSSARY']  ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/collections/admin/guidmapper.php"><?= $LANG['GUID_MAPPER'] ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/imagelib/admin/thumbnailbuilder.php"><?= $LANG['THUMBNAIL_BUILDER'] ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/collections/map/staticmaphandler.php"><?= $LANG['MANAGE_TAXON_MAP_THUMBNAIL'] ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/admin/othercatalog.php"><?= $LANG['OTHER_CAT_TRANSFER'] ?></a>
					</li>
					<li>
						<a href="<?= $CLIENT_ROOT ?>/collections/specprocessor/salix/salixhandler.php"><?= $LANG['SALIX'] ?></a>
					</li>
				</ul>
			</section>
			<?php
		}
		?>
	</div>
</html>
