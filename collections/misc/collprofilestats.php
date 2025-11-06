<?php
include_once($SERVER_ROOT.'/content/lang/collections/misc/collprofiles.'.$LANG_TAG.'.php');

$statDisplay = array_key_exists('stat', $_REQUEST) ? $_REQUEST['stat'] : '';
$collid = filter_var($collid, FILTER_SANITIZE_NUMBER_INT);

if($statDisplay == 'geography'){
	$countryDist = array_key_exists('country',$_REQUEST) ? $_REQUEST['country'] : '';
	$stateDist = array_key_exists('state',$_REQUEST) ? $_REQUEST['state'] : '';

	$distArr = $collManager->getGeographyStats($countryDist, $stateDist);
	if($distArr){
		?>
		<fieldset id="geographystats" style="margin:20px;width:90%;">
			<legend>
				<b>
					<?php
					echo $LANG['GEO_DIST'];
					if($stateDist) echo ' - '.$stateDist;
					elseif($countryDist) echo ' - '.$countryDist;
					?>
				</b>
			</legend>
			<div style="margin:15px;"><?= $LANG['CLICK_ON_SPEC_REC'] ?></div>
			<ul>
				<?php
				foreach($distArr as $term => $subArr){
					$cnt = $subArr['cnt'];
					$hasChild = false;
					if(!$stateDist && $subArr['hasChild']) $hasChild = true;
					$countryTerm = htmlspecialchars(($countryDist ? $countryDist : $term), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					$stateTerm = htmlspecialchars(($countryDist ? ($stateDist ? $stateDist : $term) : ''), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					$countyTerm = htmlspecialchars(($countryDist && $stateDist ? $term : ''), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					echo '<li>';
					if($hasChild) echo '<a href="collprofiles.php?collid=' . $collid . '&stat=geography&country=' . $countryTerm . '&state=' . $stateTerm . '#geographystats">';
					echo $term;
					if($hasChild) echo '</a>';
					echo ' (<a href="../list.php?db=' . $collid . '&reset=1&usethes=1&country=' . $countryTerm . '&state=' . $stateTerm . '&county=' . $countyTerm . '" target="_blank">' . $cnt . '</a>)';
					echo '</li>';
				}
				?>
			</ul>
		</fieldset>
		<?php
	}
}
elseif($statDisplay == 'taxonomy'){
	$famDist = array_key_exists('family', $_REQUEST) ? $collManager->cleanOutStr($_REQUEST['family']) : '';
	$taxArr = $collManager->getTaxonomyStats($famDist);
	?>
	<fieldset id="taxonomystats" style="margin:20px;width:90%;">
		<legend><b><?= $LANG['TAXON_DIST'] ?></b></legend>
		<div style="margin:15px;float:left;">
			<?= $LANG['TAXON_DIST'] ?>
		</div>
		<div style="clear:both;">
			<ul>
				<?php
				foreach($taxArr as $name => $subArr){
					$name = htmlspecialchars($name, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
					$cnt = $subArr['cnt'];
					$hasChild = false;
					if($subArr['hasChild']) $hasChild = true;
					echo '<li>';
					if($hasChild) echo '<a href="collprofiles.php?collid=' . $collid . '&stat=taxonomy&family=' . $name . '#taxonomystats">';
					echo $name;
					if($hasChild) echo '</a>';
					echo ' (<a href="../list.php?db=' . $collid . '&taxontype=' . ($famDist?2:3) . '&reset=1&usethes=1&taxa=' . $name . '" target="_blank">' . $cnt . '</a>)';
					echo '</li>';
				}
				?>
			</ul>
		</div>
	</fieldset>
	<?php
}