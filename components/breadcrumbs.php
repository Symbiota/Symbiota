<?php
function breadcrumb($label, $link = null) {
	$breadcrumbHtml = [];
	$label = htmlspecialchars($label, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);

	if($link) {
		$link = htmlspecialchars($link, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
		return '<a href="' . $link. '">' . $label . '</a>';
	} else {
		return '<b>' . $label . '</b>';
	}
}

function breadcrumbs(array $labelLinks) {
	$breadcrumbHtml = [];
	$count = 0;
	echo '<div class="navpath">';
	foreach ($labelLinks as $key => $value) {
		if(!is_numeric($key)) {
			echo breadcrumb($key, $value);
		} else {
			echo breadcrumb($value, null);
		}

		if(++$count < count($labelLinks)) {
			echo ' &gt;&gt; ';
		}
	}
	echo '</div>';
}
