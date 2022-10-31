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
		<div id="innertext" style="margin:10px 20px">
			<h1><?php echo (isset($LANG['CONTACTS'])?$LANG['CONTACTS']:'Contacts'); ?>:</h1>
			<h2>The Madagascar Paleontology Project (MPP) Team:</h2>
			<p><b>The following researchers and administrators lead <a href="https://www.dmns.org/science/earth-sciences/projects/madagascar-paleontology-projects/">the MPP team</a>:</b></p>
			<ul>
				<li><b>-Dr. David W. Krause</b>, project co-lead, Senior Curator of Vertebrate Paleontology, Denver Museum of Nature & Science</li>
				<li><b>-Dr. Patrick M. O'Connor</b>, project co-lead, Professor, Department of Biomedical Sciences, Ohio University</li>
				<li><b>-Dr. Jean Freddy Ranaivoarisoa</b>, Head, Mention Anthropobiologie et Développement Durable, Université d'Antananarivo</li>
				<li><b>-Dr. Hasina N. Randrianaly</b>, Professor, Mention Bassins Sédimentaires Evolution Conservation, Université d'Antananarivo</li>
				<li><b>-Dr. Edmond Rasolofotiana</b>, Head, Mention Bassins Sédimentaires Evolution Conservation, Université d'Antananarivo</li>
				<li><b>-Dr. Raymond R. Rogers</b>, Professor, Department of Geology, Macalester College</li>
				<li><b>-Dr. Kristina Curry Rogers</b>, Professor, Biology and Geology Departments, Macalester College</li>
				<li><b>-Dr. Alan Turner</b>, Professor, Department of Anatomical Sciences, Stony Brook University</li>
			
			<p><b>The following individuals provide technical support for the collections work:</b></p>
				<li><b>-Joseph R. Groenke</b>, Laboratory Coordinator, Department of Biomedical Sciences, Ohio University</li>
				<li><b>-Kristen A. MacKenzie</b>, Earth Sciences Collections Manager, Denver Museum of Nature & Science</li>
				<li><b>-Nicole Neu-Yagle</b>, Earth Sciences Collections Manager, Denver Museum of Nature & Science</li>
				<li><b>-Bakoliarisoa Rakotozafy</b>, Collections Manager, Mention Bassins Sédimentaires Evolution Conservation, Université d'Antananarivo</li>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
