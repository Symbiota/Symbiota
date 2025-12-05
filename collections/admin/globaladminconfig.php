<?php
include_once('../../config/symbini.php');
$configVariables = [
    'DEFAULT_LANG',
    'DEFAULT_PROJ_ID',
    'DEFAULTCATID',
    'DEFAULT_TITLE',
    'EXTENDED_LANG',
    'TID_FOCUS',
    'ADMIN_EMAIL',
    'SYSTEM_EMAIL',
    'CHARSET',
    'PORTAL_GUID',
    'SECURITY_KEY',
    'SERVER_HOST',
    'CLIENT_ROOT',
    'SERVER_ROOT',
    'TEMP_DIR_ROOT',
    'LOG_PATH',
    'CSS_BASE_PATH',
    'PUBLIC_MEDIA_UPLOAD_ROOT',
    'MEDIA_DOMAIN',
    'MEDIA_ROOT_URL',
    'MEDIA_ROOT_PATH',
    'IMG_WEB_WIDTH',
    'IMG_TN_WIDTH',
    'IMG_LG_WIDTH',
    'MEDIA_FILE_SIZE_LIMIT',
    'IPLANT_IMAGE_IMPORT_PATH',
    'USE_IMAGE_MAGICK',
    'TESSERACT_PATH',
    'NLP_LBCC_ACTIVATED',
    'NLP_SALIX_ACTIVATED',
    'OCCURRENCE_MOD_IS_ACTIVE',
    'FLORA_MOD_IS_ACTIVE',
    'KEY_MOD_IS_ACTIVE',
    'GBIF_USERNAME',
    'GBIF_PASSWORD',
    'GBIF_ORG_KEY',
    'DEFAULT_TAXON_SEARCH',
    'GOOGLE_MAP_KEY',
    'MAPBOX_API_KEY',
    'MAP_THUMBNAILS',
    'STORE_STATISTICS',
    'MAPPING_BOUNDARIES',
    'ACTIVATE_GEOLOCATION',
    'GOOGLE_ANALYTICS_KEY',
    'GOOGLE_ANALYTICS_TAG_ID',
    'RECAPTCHA_PUBLIC_KEY',
    'RECAPTCHA_PRIVATE_KEY',
    'TAXONOMIC_AUTHORITIES',
    'QUICK_HOST_ENTRY_IS_ACTIVE',
    'GLOSSARY_EXPORT_BANNER',
    'DYN_CHECKLIST_RADIUS',
    'DISPLAY_COMMON_NAMES',
    'ACTIVATE_DUPLICATES',
    'ACTIVATE_EXSICCATI',
    'ACTIVATE_GEOLOCATE_TOOLKIT',
    'SEARCH_BY_TRAITS',
    'CALENDAR_TRAIT_PLOTS',
    'ACTIVATE_PALEO',
    'AUTH_PROVIDER',
    'IGSN_ACTIVATION',
    'WIKIPEDIA_TAXON_TAB',
    'OVERRIDE_DOWNLOAD_LOGIN_REQUIREMENT',
    'ALLOWEDCHARACTERS',
    'SMTP_ARR',
    'RIGHTS_TERMS',
    'SHOULD_BE_ABLE_TO_CREATE_PUBLIC_USER',
    'SYMBIOTA_LOGIN_ENABLED',
    'SHOULD_INCLUDE_CULTIVATED_AS_DEFAULT',
    'AUTH_PROVIDER',
    'LOGIN_ACTION_PAGE',
    'SHOULD_USE_HARVESTPARAMS',
    'THIRD_PARTY_OID_AUTH_ENABLED',
    'SHOULD_USE_MINIMAL_MAP_HEADER',
    'DATE_DEFAULT_TIMEZONE',
    'PRIVATE_VIEWING_ONLY',
    'PRIVATE_VIEWING_OVERRIDES',
    'GEO_JSON_LAYERS',
    'HTTPS_ONLY'
];

function makeLabel($varName) {
    $exceptions = [
        'Guid' => 'GUID',
        'Css' => 'CSS',
        'Api' => 'API',
        'Url' => 'URL',
    ];
    $label = str_replace('_', ' ', $varName);
    $label = ucwords(strtolower($label));
    $label = str_replace(array_keys($exceptions), array_values($exceptions), $label);
    return $label;
}

echo '<form method="POST" action="save_settings.php">';
echo '<h2>Configuration Settings</h2>';
echo '<div style="display:flex;flex-wrap:wrap;gap:1rem;">';

foreach ($configVariables as $var) {
    $label = makeLabel($var);
    echo '<div style="flex:1 1 300px;">';
    echo '<label for="' . $var . '">' . htmlspecialchars($label) . ':</label><br>';
    echo '<input type="text" id="' . $var . '" name="' . $var . '" value="' . htmlspecialchars(${$var} ?? '') . '" style="width:100%;padding:4px;">';
    echo '</div>';
}

echo '</div><br>';
echo '<button type="submit">Save</button>';
echo '</form>';
?>
