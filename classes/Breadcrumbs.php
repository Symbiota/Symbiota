<?php
/**
 * Class to assist and standarized breadcrumb rendering throughout the codebase.
 * Should only use static functions since it is just a helper.
 */
class Breadcrumbs {

	/**
	 * Function to render single breadcrumb item
	 *
	 * @param String $label Displayed text for item
	 * @param ?String $link Optional value for label's link
	 * @return String
	 **/
	public static function renderItem(String $label, ?String $link = null): String {
		$label = htmlspecialchars($label, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);

		if($link) {
			$link = htmlspecialchars($link, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
			return '<a href="' . $link. '">' . $label . '</a>';
		} else {
			return '<b>' . $label . '</b>';
		}
	}

	/**
	 * Function to render breadcrumbs component
	 *
	 * Following example to render a link to home and a none link lable for sitemap
	 * Ex: Breadcrumbs::renderMany(['Home' => '/', 'Sitemap'])
	 *
	 * @param Array $labelLinks Array of labels that optionally can have links as values
	 * @return String
	 **/
	public static function renderMany(Array $labelLinks): String {
		$html = '';
		$count = 0;
		foreach ($labelLinks as $key => $value) {
			if(!is_numeric($key)) {
				$html .= self::renderItem($key, $value);
			} else {
				$html .= self::renderItem($value, null);
			}

			if(++$count < count($labelLinks)) {
				$html .= ' &gt;&gt; ';
			}
		}
		return self::renderContainer($html);
	}

	private static function renderContainer(String $innerHtml): String {
		return '<div class="navpath" style="margin: 10px 0">' . $innerHtml . '</div>';
	}
}
