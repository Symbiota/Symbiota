<?php
include_once('../config/symbini.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/templates/usagepolicy.' . $LANG_TAG . '.php')) include_once($SERVER_ROOT.'/content/lang/templates/usagepolicy.' . $LANG_TAG . '.php');
else include_once($SERVER_ROOT . '/content/lang/templates/usagepolicy.en.php');
include_once ($SERVER_ROOT . '/classes/utilities/GeneralUtil.php');

header("Content-Type: text/html; charset=" . $CHARSET);
$serverHost = GeneralUtil::getDomain();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo $DEFAULT_TITLE . $LANG['DATA_USAGE_GUIDELINES']; ?></title>
	<?php
	include_once($SERVER_ROOT . '/includes/head.php');
	?>
</head>

<body>
	<?php
	$displayLeftMenu = true;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<div class="navpath">
		<a href="<?php echo htmlspecialchars($CLIENT_ROOT, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE); ?>/index.php"><?php echo $LANG['HOME']; ?></a> &gt;&gt;
		<b><?php echo $LANG['DATA_USAGE_GUIDELINES']; ?></b>
	</div>
	<!-- This is inner text! -->
	<div role="main" id="innertext">
		<h1 class="page-heading"><?php echo $LANG ['GUIDE_ACCESSIBLE']; ?></h1>
		<h2><?php echo $LANG ['REC_CITATION']; ?></h2>
		<p><?php echo $LANG ['USE_FOLLOWING'] . " " . $DEFAULT_TITLE . " " . $LANG ['NETWORK']; ?>:</p>
		<h3><?php echo $LANG ['GENERAL_CITATION']; ?></h3>
		<blockquote>
			<?php
			if (file_exists($SERVER_ROOT . '/includes/citationportal.php')) {
				include($SERVER_ROOT . '/includes/citationportal.php');
			}
			else {
				echo $LANG['PUBLISHED_BY'] . ': ';
				if ($DEFAULT_TITLE) {
					echo $DEFAULT_TITLE;
				}
				else {
					echo $LANG['RESPONSIBLE_FOR'];
				};
				echo ' ' . $LANG['ACCESSED_THROUGH'] . ' ';
				if ($DEFAULT_TITLE) {
					echo $DEFAULT_TITLE;
				}
				else {
					echo $LANG['RESPONSIBLE_FOR'];
				};
				echo ' ' . $LANG['PORTAL'] . ' ' . $serverHost . $CLIENT_ROOT . ', ' . date('Y-m-d') . ').';
			};
			?>
		</blockquote>

		<h3><?php echo $LANG ['USAGE_FROM']; ?></h3>
		<p><?php echo $LANG ['ACCESS_EACH']; ?>.</p>
		<h4><?php echo $LANG ['EXAMPLE']; ?></h4>
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
		<h3><?php echo $LANG ['GLOSSARY']; ?></h3>
		<p><?php echo $LANG ['PLEASE_CITE']; ?>:</p>
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

		<h2><?php echo $LANG ['RECORD_USE_POLICY']; ?></h2>
		<div>
			<ul>
				<li>
					<?php echo $LANG ['OCC_REC_POLICY_1_1'] . ' ' . $DEFAULT_TITLE . ' ' . $LANG ['OCC_REC_POLICY_1_2']; ?>
				</li>
				<li>
					<?php echo $DEFAULT_TITLE . ' ' . $LANG ['OCC_REC_POLICY_2']; ?>
				</li>
				<li>
					<?php echo $LANG ['OCC_REC_POLICY_3']; ?>
				</li>
				<li>
					<?php echo $DEFAULT_TITLE . ' ' . $LANG ['OCC_REC_POLICY_4']; ?>
				</li>
			</ul>
		</div>

		<h2><?php echo $LANG ['IMAGES']; ?></h2>
		<p><?php echo $LANG ['IMAGES_POLICY_1']; ?>
		(<a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">CC BY-SA</a>).
		<?php echo $LANG ['IMAGES_POLICY_2']; ?>
		</p>

		<h2><?php echo $LANG ['NOTES_REC_IMG']; ?></h2>
		<p><?php echo $LANG ['REC_IMG_DESC']; ?>
		</p>

		<p><b><?php echo $LANG ['DISCLAIMER'] . ': ' ?> </b> <?php echo $LANG ['DISCLAIMER_DESC']; ?>
		</p>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>
</html>
