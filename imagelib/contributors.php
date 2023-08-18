<!DOCTYPE html>

<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageLibraryBrowser.php');
header("Content-Type: text/html; charset=".$CHARSET);

$imgManager = new ImageLibraryBrowser();
?>
<html lang="<?php echo $LANG_TAG ?>">
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Photographer List</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<meta name='keywords' content='Contributors, Specimens' />
</head>
<body>
	<?php
	$displayLeftMenu = (isset($imagelib_photographersMenu)?$imagelib_photographersMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php">Home</a> &gt;&gt;
		<a href="index.php">Image Library</a> &gt;&gt;
		<b>Image contributors</b>
	</div>

	<!-- This is inner text! -->
	<div id="innertext" style="height:100%">
		<?php
		$pList = $imgManager->getPhotographerList();
		if($pList){
			echo '<div style="float:left; margin-right:40px;">';
			echo '<h2>Image Contributors</h2>';
			echo '<div style="margin-left:15px">';
			foreach($pList as $uid => $pArr){
				echo '<div>';
				$phLink = 'search.php?imagetype=all&phuid='.$uid.'&submitaction=search';
				echo '<a href="' . htmlspecialchars($phLink, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($pArr['name'], HTML_SPECIAL_CHARS_FLAGS) . '</a> (' . htmlspecialchars($pArr['imgcnt'], HTML_SPECIAL_CHARS_FLAGS) . ')</div>';
			}
			echo '</div>';
			echo '</div>';
		}
		?>

		<div style="float:left">
			<?php
			ob_flush();
			flush();
			$collList = $imgManager->getCollectionImageList();
			$specList = $collList['coll'];
			if($specList){
				echo '<h2>Specimens</h2>';
				echo '<div style="margin-left:15px;margin-bottom:20px">';
				foreach($specList as $k => $cArr){
					echo '<div>';
					$phLink = 'search.php?taxontype=2&imagecount=all&imagetype=all&submitaction=search&db[]='.$k;
					echo '<a href="' . htmlspecialchars($phLink, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($cArr['name'], HTML_SPECIAL_CHARS_FLAGS) . '</a> (' . htmlspecialchars($cArr['imgcnt'], HTML_SPECIAL_CHARS_FLAGS) . ')</div>';
				}
				echo '</div>';
			}

			$obsList = $collList['obs'];
			if($obsList){
				echo '<h2>Observations</h2>';
				echo '<div style="margin-left:15px">';
				foreach($obsList as $k => $cArr){
					echo '<div>';
					$phLink = 'search.php?taxontype=2&imagecount=all&imagetype=all&submitaction=search&db[]='.$k;
					echo '<a href="' . htmlspecialchars($phLink, HTML_SPECIAL_CHARS_FLAGS) . '">' . htmlspecialchars($cArr['name'], HTML_SPECIAL_CHARS_FLAGS) . '</a> (' . htmlspecialchars($cArr['imgcnt'], HTML_SPECIAL_CHARS_FLAGS) . ')</div>';
				}
				echo '</div>';
			}
			?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>