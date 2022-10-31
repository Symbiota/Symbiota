<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
include_once('content/lang/misc/aboutproject.'.$LANG_TAG.'.php');
?>
<html>
	<head>
		<title><?php echo (isset($LANG['CONTACTS'])?$LANG['CONTACTS']:'Contacts'); ?></title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../index.php"><?php echo (isset($LANG['HOME'])?$LANG['HOME']:'Home'); ?></a> &gt;&gt;
			<b><?php echo (isset($LANG['CONTACTS'])?$LANG['CONTACTS']:'Contacts'); ?></b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
			<h1><?php echo (isset($LANG['CONTACTS'])?$LANG['CONTACTS']:'The Madagascar Paleontology Project (MPP) Team:'); ?>:</h1>
			<p>The following researchers and administrators lead <a href="https://www.dmns.org/science/earth-sciences/projects/madagascar-paleontology-projects/">the MPP team</a>:</p>
			<ul>
				<li>Dr. David W. Krause, project co-lead, Senior Curator of Vertebrate Paleontology, Denver Museum of Nature & Science</li>
				<li>Dr. Patrick M. O'Connor, project co-lead, Professor, Department of Biomedical Sciences, Ohio University</li>
				<li>Dr. Jean Freddy Ranaivoarisoa, Head, Mention Anthropobiologie et Développement Durable, Université d'Antananarivo</li>
				<li>Dr. Hasina N. Randrianaly, Professor, Mention Bassins Sédimentaires Evolution Conservation, Université d'Antananarivo</li>
				<li>Dr. Edmond Rasolofotiana, Head, Mention Bassins Sédimentaires Evolution Conservation, Université d'Antananarivo</li>
				<li>Dr. Raymond R. Rogers, Professor, Department of Geology, Macalester College</li>
				<li>Dr. Kristina Curry Rogers, Professor, Biology and Geology Departments, Macalester College</li>
				<li>Dr. Alan Turner, Professor, Department of Anatomical Sciences, Stony Brook University</li>
			</ul>
			<p>The following individuals provide technical support for the collections work:</p>
			<ul>
				<li>Joseph R. Groenke, Laboratory Coordinator, Department of Biomedical Sciences, Ohio University</li>
				<li>Kristen A. MacKenzie, Earth Sciences Collections Manager, Denver Museum of Nature & Science</li>
				<li>Nicole Neu-Yagle, Earth Sciences Collections Manager, Denver Museum of Nature & Science</li>
				<li>Bakoliarisoa Rakotozafy, Collections Manager, Mention Bassins Sédimentaires Evolution Conservation, Université d'Antananarivo</li>
			</ul>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
