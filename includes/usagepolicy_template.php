<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
include_once ($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');
include_once ($SERVER_ROOT . '/classes/utilities/Citation.php');

Language::load('templates/usagepolicy');

header("Content-Type: text/html; charset=" . $CHARSET);
$serverHost = GeneralUtil::getDomain();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?= $DEFAULT_TITLE . $LANG['DATA_USAGE_GUIDELINES'] ?></title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
</head>

<body>
	<?php
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?= $CLIENT_ROOT ?>/index.php"><?= $LANG['HOME'] ?></a> &gt;&gt;
		<b><?= $LANG['DATA_USAGE_GUIDELINES'] ?></b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?= $LANG ['GUIDE_ACCESSIBLE'] ?></h1>
		<h2><?= $LANG ['REC_CITATION'] ?></h2>
		<p><?= $LANG ['USE_FOLLOWING'] . ' ' . $DEFAULT_TITLE . ' ' . $LANG ['NETWORK'] ?>:</p>
		<h3><?= $LANG ['GENERAL_CITATION'] ?></h3>
		<blockquote>
			<?php
			Citation::portal();
			?>
		</blockquote>

		<h3><?= $LANG ['USAGE_FROM'] ?></h3>
		<p><?= $LANG ['ACCESS_EACH'] ?>.</p>
		<h4><?= $LANG ['EXAMPLE'] ?></h4>
		<blockquote>
			<?php
			$collData['collectionname'] = $LANG['NAME_INST_COLL'];
			$collData['dwcaurl'] = $serverHost . $CLIENT_ROOT . '/portal/content/dwca/NIC_DwC-A.zip';
			if (file_exists($SERVER_ROOT . '/includes/citationcollection.php')) {
				include($SERVER_ROOT . '/includes/citationcollection.php');
			} else {
				echo $LANG['NAME_INST_OCCUR'] . ' ' . 'http://gh.local/Symbiota/portal/content/dwca/' . $LANG['ACCESSED_VIA'] . ', ' . 'http://gh.local/Symbiota' . ', 2022-07-25.';
			}
			?>
		</blockquote>
		<h3><?= $LANG ['GLOSSARY'] ?></h3>
		<p><?= $LANG ['PLEASE_CITE'] ?>:</p>
		<blockquote>
			<?php
				if ($DEFAULT_TITLE) {
					echo $DEFAULT_TITLE;
				}
				else {
					echo $LANG['RESPONSIBLE_FOR'];
				};
				echo '. Glossary. ' . $serverHost . $CLIENT_ROOT . 'glossary/index.php. Accessed: ' . date('Y-m-d') . '.';
			?>
		</blockquote>

		<h2><?= $LANG ['RECORD_USE_POLICY'] ?></h2>
		<div>
			<ul>
				<li>
					<?= $LANG ['OCC_REC_POLICY_1_1'] . ' ' . $DEFAULT_TITLE . ' ' . $LANG ['OCC_REC_POLICY_1_2'] ?>
				</li>
				<li>
					<?= $DEFAULT_TITLE . ' ' . $LANG ['OCC_REC_POLICY_2'] ?>
				</li>
				<li>
					<?= $LANG ['OCC_REC_POLICY_3'] ?>
				</li>
				<li>
					<?= $DEFAULT_TITLE . ' ' . $LANG ['OCC_REC_POLICY_4'] ?>
				</li>
			</ul>
		</div>

		<h2><?= $LANG ['IMAGES'] ?></h2>
		<p>
			<?= $LANG ['IMAGES_POLICY_1'] ?>
			(<a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">CC BY-SA</a>).
			<?= $LANG ['IMAGES_POLICY_2'] ?>
		</p>

		<h2><?= $LANG ['NOTES_REC_IMG'] ?></h2>
		<p><?= $LANG ['REC_IMG_DESC'] ?></p>

		<p><b><?= $LANG ['DISCLAIMER'] ?>: </b> <?= $LANG ['DISCLAIMER_DESC'] ?></p>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
