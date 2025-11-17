<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');
Language::load('classes/utilities/Citation');
header('Content-Type: text/html; charset='.$CHARSET);

?>
<!DOCTYPE html>
<html lang="<?= $LANG_TAG ?>">
<head>
	<title>Admin: Citation Review</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
</head>
<body>
	<div class='navpath'>
		<?php
		include($SERVER_ROOT.'/includes/header.php');
		echo '<a href="../index.php">' . $LANG['NAV_HOME'] . '</a> &gt;&gt; ';
		echo '<b>Citation Review</b>';
		?>
	</div>
	<div id='innertext'>
		<?php
		echo '<h2>Portal Citation</h2>';
		Citation::portal();

		echo '<h2>Collection Citation</h2>';
		$collName = '';
		$dwcaUrl = '';
		$recordID = '';
		Citation::collection($collName, $dwcaUrl, $recordID);

		echo '<h2>Dataset Citation</h2>';
		$datasetName = '';
		$datasetID = '';
		Citation::dataset($datasetName, $datasetID);

		echo '<h2>GBIF Citation</h2>';
		$gbifTitle = '';
		$doi = '';
		Citation::GBIF($gbifTitle, $doi);
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
