<?php
include_once($SERVER_ROOT . '/content/lang/header.' . $LANG_TAG . '.php');

$isNeonEditor = false;
if ($IS_ADMIN) $isNeonEditor = true;
elseif (array_key_exists('CollAdmin', $USER_RIGHTS) || array_key_exists('CollEditor', $USER_RIGHTS)) $isNeonEditor = true;
?>
<div id="main-header">
	<!-- Symbiota Main Header -->
	<header id="header">
		<ul id="biorepo-header">
			<!-- Main Site Logo -->
			<li id="branding" style="vertical-align: top;">
				<!-- NSF/Neon logo -->
				<a href="https://www.neonscience.org/" title="Home" rel="home" class="branding-link active"><img src="<?php echo $CLIENT_ROOT; ?>/neon/images/neon-white-logo.svg" alt="NSF NEON | Open Data to Understand our Ecosystems"></a>
			</li>
			<li class="header-title">
				<h1><a href="<?php echo $CLIENT_ROOT; ?>/">Biorepository Data Portal</a></h1>
			</li>
		</ul>
		<!-- Symbiota Navigation -->
		<ul id="hor_dropdown">
			<li>
				<a href="<?php echo $CLIENT_ROOT; ?>/index.php" class="biorepo-home-icon"><span class="visually-hidden">Home</span></a>
			</li>
			<li>
				<a href="#" alt="Search"><?php echo (isset($LANG['H_SEARCH']) ? $LANG['H_SEARCH'] : 'Search'); ?></a>
				<ul>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/neon/search/index.php" alt="New Sample Search">Sample search</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank" alt="Map search">Map search</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/checklists/dynamicmap.php?interface=checklist" alt="<?php echo (isset($LANG['H_DYN_LISTS']) ? $LANG['H_DYN_LISTS'] : 'Dynamic Checklist'); ?>"><?php echo (isset($LANG['H_DYN_LISTS']) ? $LANG['H_DYN_LISTS'] : 'Dynamic Checklist'); ?></a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/taxa/taxonomy/taxonomydynamicdisplay.php" alt="<?php echo (isset($LANG['H_TAXONOMIC_EXPLORER']) ? $LANG['H_TAXONOMIC_EXPLORER'] : 'Taxonomy Explorer'); ?>"><?php echo (isset($LANG['H_TAXONOMIC_EXPLORER']) ? $LANG['H_TAXONOMIC_EXPLORER'] : 'Taxonomy Explorer'); ?></a>
					</li>
				</ul>
			</li>
			<li>
				<a href="#" alt="<?php echo (isset($LANG['H_IMAGES']) ? $LANG['H_IMAGES'] : 'Images'); ?>"><?php echo (isset($LANG['H_IMAGES']) ? $LANG['H_IMAGES'] : 'Images'); ?></a>
				<ul>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/index.php" alt="<?php echo (isset($LANG['H_IMAGE_BROWSER']) ? $LANG['H_IMAGE_BROWSER'] : 'Image Browser'); ?>"><?php echo (isset($LANG['H_IMAGE_BROWSER']) ? $LANG['H_IMAGE_BROWSER'] : 'Image Browser'); ?></a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/imagelib/search.php" alt="<?php echo (isset($LANG['H_IMAGE_SEARCH']) ? $LANG['H_IMAGE_SEARCH'] : 'Search Images'); ?>"><?php echo (isset($LANG['H_IMAGE_SEARCH']) ? $LANG['H_IMAGE_SEARCH'] : 'Search Images'); ?></a>
					</li>
				</ul>
			</li>
			<li>
				<a href="#" alt="Checklists">Datasets</a>
				<ul>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/collections/datasets/publiclist.php">Research Datasets and Special Collections</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=8">Carabidae Checklists with Keys</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=9">Mosquito Checklists with Keys</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=6">Checklist: Research Sites - Invertebrates</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=1">Checklist: Research Sites - Plants</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=7">Checklist: Research Sites - Vertebrates</a>
					</li>
					<?php
					if ($isNeonEditor) {
					?>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=3">Checklist: In Progress - Invertebrates (private)</a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=2">Checklist: In Progress - Plants (private)</a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=5">Checklist: In Progress - Vertebrates (private)</a>
						</li>
					<?php
					}
					?>
				</ul>
			</li>
			<li>
				<a href="#" alt="Sample Use">Sample Use</a>
				<ul>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/misc/samplepolicy.php" alt="Sample Use Policy">Sample Use Policy</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/misc/samplerequest.php" alt="Sample Request">Sample Request</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/misc/samplearchiverequest.php" alt="Sample Archival Request">Sample Archival Request</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/misc/datasetpublishing.php" alt="Dataset Publishing">Dataset Publishing</a>
					</li>
				</ul>
			</li>
			<li>
				<a href="<?php echo $CLIENT_ROOT; ?>/misc/cite.php">How to Cite</a>
			</li>
			<li>
				<a href="#" alt="Additional Information">Additional Information</a>
				<ul>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/misc/tutorials.php" target="_blank" rel="noopener noreferrer" alt="Tutorials and Help">Tutorials and Help</a>
					</li>
					<li>
						<a href="<?php echo $CLIENT_ROOT; ?>/misc/staff.php" target="_blank" rel="noopener noreferrer" alt="NEON Biorepository Staff">Biorepository Staff</a>
					</li>
					<li>
						<a href="https://www.neonscience.org" target="_blank" alt="About NEON">About NEON</a>
					</li>
					<li>
						<a href="https://data.neonscience.org/" target="_blank" alt="NEON Data Portal">NEON Data Portal</a>
					</li>
					<li>
						<a href="https://biokic.asu.edu/collections" target="_blank" alt="ASU Biocollections">ASU Biocollections</a>
					</li>
					<li>
						<a href="https://bdj.pensoft.net/articles.php?id=1114" target="_blank" alt="About Symbiota">About Symbiota</a>
					</li>
				</ul>
			</li>
			<li style="background-color: #004cc4;">
				<a href="<?php echo $CLIENT_ROOT; ?>/misc/gettingstarted.php" alt="Getting Started">Getting Started</a>
			</li>
			<?php
			if ($isNeonEditor) {
			?>
				<li>
					<a href="#" alt="Management Tools">Management Tools</a>
					<ul>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/neon/index.php" alt="Management Menu">Management Menu</a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/neon/shipment/manifestsearch.php" alt="Manifest Search">Manifest Search</a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/neon/shipment/manifestloader.php" alt="Submit New Manifest">Submit New Manifest</a>
						</li>
						<li>
							<a href="<?php echo $CLIENT_ROOT; ?>/neon/shipment/samplecheckin.php" alt="Sample Check-In">Sample Check-In</a>
						</li>
					</ul>
				</li>
			<?php
			}
			?>
		</ul>
	</header>
</div>
<!-- Symbiota log in bar -->
<div id="top_navbar">
	<div id="right_navbarlinks">
		<?php
		if ($USER_DISPLAY_NAME) {
		?>
			<span style="">
				<?php echo (isset($LANG['H_WELCOME']) ? $LANG['H_WELCOME'] : 'Welcome') . ' ' . $USER_DISPLAY_NAME; ?>!
			</span>
			<span style="margin-left:5px;">
				<a href="<?php echo $CLIENT_ROOT; ?>/profile/viewprofile.php" alt="<?php echo (isset($LANG['H_MY_PROFILE']) ? $LANG['H_MY_PROFILE'] : 'My Profile') ?>"><?php echo (isset($LANG['H_MY_PROFILE']) ? $LANG['H_MY_PROFILE'] : 'My Profile') ?></a>
			</span>
			<span style="margin-left:5px;">
				<a href="<?php echo $CLIENT_ROOT; ?>/profile/index.php?submit=logout" alt="<?php echo (isset($LANG['H_LOGOUT']) ? $LANG['H_LOGOUT'] : 'Logout') ?>"><?php echo (isset($LANG['H_LOGOUT']) ? $LANG['H_LOGOUT'] : 'Logout') ?></a>
			</span>
		<?php
		} else {
		?>
			<span style="">
				<a href="<?php echo $CLIENT_ROOT . "/profile/index.php?refurl=" . $_SERVER['SCRIPT_NAME'] . "?" . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>" alt="<?php echo (isset($LANG['H_LOGIN']) ? $LANG['H_LOGIN'] : 'Login') ?>"><?php echo (isset($LANG['H_LOGIN']) ? $LANG['H_LOGIN'] : 'Login') ?></a>
			</span>
			<span style="margin-left:5px;">
				<a href="<?php echo $CLIENT_ROOT; ?>/profile/newprofile.php" alt="<?php echo (isset($LANG['H_NEW_ACCOUNT']) ? $LANG['H_NEW_ACCOUNT'] : 'New Account') ?>"><?php echo (isset($LANG['H_NEW_ACCOUNT']) ? $LANG['H_NEW_ACCOUNT'] : 'New Account') ?></a>
			</span>
		<?php
		}
		?>
		<span style="margin-left:5px;">
			<a href="<?php echo $CLIENT_ROOT; ?>/sitemap.php" alt="<?php echo (isset($LANG['H_SITEMAP']) ? $LANG['H_SITEMAP'] : 'Sitemap'); ?>"><?php echo (isset($LANG['H_SITEMAP']) ? $LANG['H_SITEMAP'] : 'Sitemap'); ?></a>
		</span>
	</div>
</div>