<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT . '/classes/OccurrenceDataset.php');
header("Content-Type: text/html; charset=" . $CHARSET);

// Datasets
$datasetid = array_key_exists('datasetid', $_REQUEST) ? $_REQUEST['datasetid'] : 0;

if (!is_numeric($datasetid)) $datasetid = 0;

$datasetManager = new OccurrenceDataset();
$dArr = $datasetManager->getPublicDatasetMetadata($datasetid);
$searchUrl = '../../collections/list.php?datasetid=' . $datasetid;
$tableUrl = '../../collections/listtabledisplay.php?datasetid=' . $datasetid;
$taxaUrl = '../../collections/list.php?datasetid=' . $datasetid . '&tabindex=0';
// $downloadUrl = '../../collections/download/index.php?datasetid='.$datasetid;
$ocArr = $datasetManager->getOccurrences($datasetid);
?>
<html>

<head>
	<title>Dataset: <?php echo $dArr['name']; ?></title>
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
		<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
		<b>Dataset: <?php echo $dArr['name']; ?></b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1>Dataset: <?php echo $dArr['name']; ?></h1>
		<ul>
			<!-- Metadata -->
			<div><?php echo $dArr['description']; ?></div>
			<!-- Occurrences Summary -->
			<p>This dataset includes <?php echo count($ocArr); ?> records.</p>
			<!-- Is published at EDI? -->
			<?php
			if ($dArr['dynamicproperties'] && file_exists($SERVER_ROOT . '/includes/citationedi.php')) {
				$dpArr = json_decode($dArr['dynamicproperties'], true);
				if (array_key_exists('edi', $dpArr)) {
					$doiNum = $dpArr['edi'];
					if (substr($doiNum, 0, 4) == 'doi:') $doiNum = substr($doiNum, 4);
					$dArr['doi'] = $doiNum;
					$collData['collectionname'] = $dArr['name'];
					$collData['doi'] = $doiNum;
					$_SESSION['datasetdata'] = $dArr; ?>
					<h4>View and download dataset:</h4>
					<ul>
						<li><a class="btn" href="<?php echo $searchUrl; ?>">View and download samples in this Dataset (List view)</a></li>
						<li><a class="btn" href="<?php echo $tableUrl; ?>">View samples in this Dataset (Table view)</a></li>
						<li><a class="btn" href="<?php echo $taxaUrl; ?>">View list of taxa in this Dataset</a></li>
						<li><a class="btn" href="<?php echo 'https://doi.org/' . $doiNum; ?>">View data package version published at the Environmental Data Initiative</a></li>
					</ul>
					<h4>Cite this dataset:</h4>
					<blockquote>
						<?php include($SERVER_ROOT . '/includes/citationedi.php'); ?>
					</blockquote>
				<?php
				}
			} else {
				?>
				<h4>View and download dataset:</h4>
				<ul>
					<li><a class="btn" href="<?php echo $searchUrl; ?>">View and download samples in this Dataset (List view)</a></li>
					<li><a class="btn" href="<?php echo $tableUrl; ?>">View samples in this Dataset (Table view)</a></li>
					<li><a class="btn" href="<?php echo $taxaUrl; ?>">View list of taxa in this Dataset</a></li>
				</ul>
				<h4>Cite this dataset:</h4>
				<blockquote>
				<?php include($SERVER_ROOT . '/includes/citationdataset.php');
			}
				?>
				</blockquote>
		</ul>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>