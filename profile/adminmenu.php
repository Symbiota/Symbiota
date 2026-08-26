<?php
Language::load(['profile/adminmenu']);

if($IS_ADMIN){
	?>
	<ul>
		<li>
			<a href="<?= $CLIENT_ROOT ?>/profile/usermanagement.php"><?= $LANG['USER_PERMISSIONS'] ?></a>
		</li>
		<li>
			<a href="<?= $CLIENT_ROOT ?>/collections/misc/collmetadata.php"><?= $LANG['CREATE_NEW_COLLECTION'] ?></a>
		</li>
		<!--
		<li>
			<a href="<?= $CLIENT_ROOT ?>/collections/cleaning/coordinatevalidator.php"><?= $LANG['COORD_VALIDATOR'] ?></a>
		</li>
		-->
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
		<!--
		<li>
			<a href="<?= $CLIENT_ROOT ?>/imagelib/admin/mediatools.php"><?= $LANG['MEDIA_MIGRATION'] ?></a>
		</li>
		-->
		<li>
			<a href="<?= $CLIENT_ROOT ?>/collections/map/staticmaphandler.php"><?= $LANG['MANAGE_TAXON_MAP_THUMBNAIL'] ?></a>
		</li>
		<li>
			<a href="<?= $CLIENT_ROOT ?>/admin/othercatalog.php"><?= $LANG['OTHER_CAT_TRANSFER'] ?></a>
		</li>
		<li>
			<a href="<?= $CLIENT_ROOT ?>/collections/specprocessor/salix/salixhandler.php"><?= $LANG['SALIX'] ?></a>
		</li>
		<li>
			<a href="<?= $CLIENT_ROOT ?>"><?= $LANG['TAXONOMIC_CLEANER'] ?></a>
		</li>
		<?php // TODO: Identification Editor features need to be reviewed and refactored
		/*
		<li>
			<a href="profile/usertaxonomymanager.php"><?= $LANG['TAXONOMIC_INTEREST'] ?></a>
		</li>
		*/
		?>
		<li>
			<a href="<?= $CLIENT_ROOT ?>/admin/batchupdatestats.php"><?= $LANG['BATCH_UPDATE_STATS'] ?></a>
		</li>
	</ul>
	<?php
}
?>
