<?php
//error_reporting(E_ALL);
 include_once('../config/symbini.php');
 header("Content-Type: text/html; charset=".$CHARSET);

?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Data Usage Guidelines</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<!-- This is inner text! -->
		<div id="innertext">
			<h1>Guidelines for Acceptable Use of Data</h1><br />

			<h2>Recommended Citation Formats</h2>
			<div style="margin:10px">
				Use one of the following formats to cite data retrieved from the <?php echo $DEFAULT_TITLE; ?> network:
				<div style="font-weight:bold;margin-top:10px;">
					General Citation:
				</div>
				<div style="margin:10px;">
					<?php
					echo $DEFAULT_TITLE.'. '.date('Y').'. ';
					echo 'https://'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/').'index.php. ';
					echo 'Accessed on '.date('F d').'. ';
					?>
				</div>

				<div style="font-weight:bold;margin-top:10px;">
					Usage of occurrence data from specific institutions:
				</div>
				<div style="margin:10px;">
					Biodiversity occurrence data published by: &lt;List of Collections&gt;
					(Accessed through <?php echo $DEFAULT_TITLE; ?> Data Portal,
					<?php echo 'https://'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/').'index.php'; ?>, YYYY-MM-DD)<br/><br/>
					<b>For example:</b><br/>
					Biodiversity occurrence data published by:
					Field Museum of Natural History, Museum of Vertebrate Zoology, and New York Botanical Garden
					(Accessed through <?php echo $DEFAULT_TITLE; ?> Data Portal,
					<?php echo 'https://'.$_SERVER['HTTP_HOST'].$CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/').'index.php, '.date('Y-m-d').')'; ?>
				</div>
			</div>
			<div>
			</div>

			<a name="occurrences"></a>
			<h2>Occurrence Record Use Policy</h2>
		    <div style="margin:10px;">
				<ul>
					<li>
						While <?php echo $DEFAULT_TITLE; ?> will make every effort possible to control and document the quality
						of the data it publishes, the data are made available "as is". Any report of errors in the data should be
						directed to the appropriate curators and/or collections managers.
					</li>
					<li>
						<?php echo $DEFAULT_TITLE; ?> cannot assume responsibility for damages resulting from mis-use or
						mis-interpretation of datasets or from errors or omissions that may exist in the data.
					</li>
					<li>
						It is considered a matter of professional ethics to cite and acknowledge the work of other scientists that
						has resulted in data used in subsequent research. We encourages users to
						contact the original investigator responsible for the data that they are accessing.
					</li>
					<li>
						<?php echo $DEFAULT_TITLE; ?> asks that users not redistribute data obtained from this site without permission for data owners.
						However, links or references to this site may be freely posted.
					</li>
				</ul>
		    </div>

			<a name="images"></a>
			<h2>Images</h2>
		    <div style="margin:15px;">
		    	Images within this website have been generously contributed by their owners to
		    	promote education and research. These contributors retain the full copyright for
		    	their images. Unless stated otherwise, images are made available under the Creative Commons
		    	Attribution-ShareAlike (<a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA</a>)
				Users are allowed to copy, transmit, reuse, and/or adapt content, as long as attribution
				regarding the source of the content is made. If the content is altered, transformed, or enhanced,
				it may be re-distributed only under the same or similar license by which it was acquired.
		    </div>

			<h2>Notes on Specimen Records and Images</h2>
		    <div style="margin:15px;">
				Specimens are used for scientific research and because of skilled preparation and
				careful use they may last for hundreds of years. Some collections have specimens
				that were collected over 100 years ago that are no longer occur within the area.
				By making these specimens available on the web as images, their availability and
				value improves without an increase in inadvertent damage caused by use. Note that
				if you are considering making specimens, remember collecting normally requires
				permission of the landowner and, in the case of rare and endangered plants,
				additional permits may be required. It is best to coordinate such efforts with a
				regional institution that manages a publically accessible collection.
			</div>
						
			<h2>Statement on Offensive Content in Specimen Collections</h2>
			<div style="margin:15px;">
				The CCH acknowledges that, as historical specimens, herbarium specimen labels may
				contain offensive language. In the interests of historical integrity, we are not removing
				these words from occurrence records when they reflect locality information. We aim to include
				a flag on such records alongside these data that describes the currently accepted description
				or locality along with a reference to this Statement.
			</div>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
