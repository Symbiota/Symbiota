<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDataset.php');
header('Content-Type: text/html; charset='.$CHARSET);

$datasetManager = new OccurrenceDataset();
$dArr = $datasetManager->getPublicDatasets();
?>
<html>
	<head>
		<title>Public Datasets List</title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo htmlspecialchars($CLIENT_ROOT, HTML_SPECIAL_CHARS_FLAGS); ?>/index.php">Home</a> &gt;&gt;
			<b>Public Datasets List</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
			<h1>Public Datasets List</h1>
			<ul>
				<?php
				if($dArr){
					$catArr = array();
					// Creates categories array
					foreach($dArr as $row) {
						if (array_key_exists('category', $row)) {
							($row['category']) ? array_push($catArr, $row['category']) : array_push($catArr, NULL);
						}
						else {
							echo '<li><a href="public.php?datasetid=' . htmlspecialchars($row['datasetid'], HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($row['name'], HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
						}
					}
					if (count($catArr) > 1) {
						$catArr = array_unique($catArr);
						foreach($catArr as $cat) {
							echo ($cat) ? '<h3>'.$cat.'</h3>' : '';
							foreach($dArr as $row){
								if ($cat === $row['category']) {
									echo '<li><a href="public.php?datasetid=' . htmlspecialchars($row['datasetid'], HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($row['name'], HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
								}
							}
						}
					}
					else {
						echo '<li><a href="public.php?datasetid=' . htmlspecialchars($row['datasetid'], HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($row['name'], HTML_SPECIAL_CHARS_FLAGS) . '</a></li>';
					}
				}
				?>
			</ul>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
