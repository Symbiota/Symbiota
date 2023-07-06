<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/WordCloud.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collidStr = array_key_exists('collidstr',$_REQUEST)?$_REQUEST['collidstr']:false;
$csMode = array_key_exists('csmode',$_REQUEST)?$_REQUEST['csmode']:false;

?>
<html>
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE; ?> - Word Cloud Handler Collections</title>
		<?php

		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script type="text/javascript">
		</script>
		<style type="text/css">
			.url-div{ margin-bottom: 5px; }
		</style>
	</head>
	<body>
		<!-- This is inner text! -->
		<div id="innertext">
			<?php
			$cloudHandler = new WordCloud();
			$cloudHandler->setWidth(800);
			if(is_numeric($collidStr)){
				$collidArr = explode(',',$collidStr);
				foreach($collidArr as $collid){
					$url = $cloudHandler->buildWordCloud($collid,$csMode);
					echo '<div class="url-div"><a href="' . htmlspecialchars($url, HTML_SPECIAL_CHARS_FLAGS) . '" target="_blank">' . htmlspecialchars($url, HTML_SPECIAL_CHARS_FLAGS).'</a></div>';
				}
			}
			else echo '<div>No collid target submitted</div>';
			?>
		</div>
	</body>
</html>