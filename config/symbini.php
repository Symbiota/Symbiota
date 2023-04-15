<?php
// Configuration for test Symbiota instance in Vagrant
$DEFAULT_LANG = 'en';			//Default language
$DEFAULT_PROJ_ID = 1;
$DEFAULTCATID = 1;
$DEFAULT_TITLE = 'Symbiota - TEST';
$ADMIN_EMAIL = '';
$CHARSET = '';					//ISO-8859-1 or UTF-8
$PORTAL_GUID = '';				//Typically a UUID
$SECURITY_KEY = '';				//Typically a UUID used to verify access to certain web service 

$CLIENT_ROOT = '';				//URL path to project root folder (relative path w/o domain, e.g. '/seinet') 
$SERVER_ROOT = '/var/www/html/symbiota';				//Full path to Symbiota project root folder
$TEMP_DIR_ROOT = $SERVER_ROOT . '/temp';				//Must be writable by Apache; will use system default if not specified  
$LOG_PATH = $SERVER_ROOT.'/content/logs';					//Must be writable by Apache; will use <SYMBIOTA_ROOT>/temp/logs if not specified 

//the root for the image directory
$IMAGE_DOMAIN = '';				//Domain path to images, if different from portal 
$IMAGE_ROOT_URL = '';				//URL path to images
$IMAGE_ROOT_PATH = '';			//Writable path to images, especially needed for downloading images

//Pixel width of web images
$IMG_WEB_WIDTH = 1400;
$IMG_TN_WIDTH = 200;
$IMG_LG_WIDTH = 3200;
$IMG_FILE_SIZE_LIMIT = 300000;		//Files above this size limit and still within pixel width limits will still be resaved w/ some compression  

//Specimen Label and Batch Image Processor variables
//$USE_IMAGE_MAGICK = 0;		//1 = ImageMagick resize images, given that it's installed (faster, less memory intensive)
$TESSERACT_PATH = ''; 			//Needed for OCR function in the occurrence editor page
$NLP_LBCC_ACTIVATED = 0;
$NLP_SALIX_ACTIVATED = 0;

//Module activations
$OCCURRENCE_MOD_IS_ACTIVE = 1;
$FLORA_MOD_IS_ACTIVE = 1;
$KEY_MOD_IS_ACTIVE = 1;

$REQUESTED_TRACKING_IS_ACTIVE = 0;   // Allow users to request actions such as requests for images to be made for specimens

//Configurations for GeoServer integration
$GEOSERVER_URL = '';   // URL for Geoserver instance serving map data for this portal
$GEOSERVER_RECORD_LAYER = '';   // Name of Geoserver layer containing occurrence point data for this portal

//Configurations for Apache SOLR integration
$SOLR_URL = '';   // URL for SOLR instance indexing data for this portal
$SOLR_FULL_IMPORT_INTERVAL = 0;   // Number of hours between full imports of SOLR index.

//Configurations for publishing to GBIF
$GBIF_USERNAME = '';                //GBIF username which portal will use to publish
$GBIF_PASSWORD = '';                //GBIF password which portal will use to publish
$GBIF_ORG_KEY = '';                 //GBIF organization key for organization which is hosting this portal

$FP_ENABLED = 0;				//Enable Filtered-Push modules

//Misc variables
$GOOGLE_MAP_KEY = '';				//Needed for Google Map; get from Google 
$MAPPING_BOUNDARIES = '';			//Project bounding box; default map centering; (e.g. 42.3;-100.5;18.0;-127)
$ACTIVATE_GEOLOCATION = false;			//Activates HTML5 geolocation services in Map Search
$GOOGLE_ANALYTICS_KEY = '';			//Needed for setting up Google Analytics
$RECAPTCHA_PUBLIC_KEY = '';			//Now called site key
$RECAPTCHA_PRIVATE_KEY = '';		//Now called secret key
$EOL_KEY = '';						//Not required, but good to add a key if you plan to do a lot of EOL mapping
$QUICK_HOST_ENTRY_IS_ACTIVE = 0;   // Allows quick entry for host taxa in occurrence editor
$PORTAL_TAXA_DESC = '';		//Preferred taxa descriptions for the portal.
$GLOSSARY_EXPORT_BANNER = '';		//Banner image for glossary exports. Place in images/layout folder.
$DYN_CHECKLIST_RADIUS = 10;			//Controls size of concentric rings that are sampled when building Dynamic Checklist
$DISPLAY_COMMON_NAMES = 1;			//Display common names in species profile page and checklists displays
$ACTIVATE_EXSICCATI = 0;			//Activates exsiccati fields within data entry pages; adding link to exsiccati search tools to portal menu is recommended
$ACTIVATE_GEOLOCATE_TOOLKIT = 0;	//Activates GeoLocate Toolkit located within Exporter tab within Processing Toolkit menu item 

$RIGHTS_TERMS = array(
	'CC0 1.0 (Public-domain)' => 'http://creativecommons.org/publicdomain/zero/1.0/',
	'CC BY (Attribution)' => 'http://creativecommons.org/licenses/by/4.0/',
	'CC BY-NC (Attribution-Non-Commercial)' => 'http://creativecommons.org/licenses/by-nc/4.0/'
);
$CSS_VERSION_LOCAL = '20170414';		//Changing this variable will force a refresh of main.css styles within users browser cache for all pages

//set terms for reproductiveCondition drop-down menu
//$REPRODUCTIVE_CONDITION_TERMS = array("term1", "term2", "term3", "term4", "term5", "etc..");

//Individual page menu and navigation crumbs
//Menu variables turn on and off the display of left menu 
//Crumb variables allow the customization of the bread crumbs. A crumb variable with an empty value will cause crumbs to disappear
//Variable name should include path to file separated by underscores and then the file name ending with "Menu" or "Crumbs"
//checklists/
	$checklists_checklistMenu = 0;
	//$checklists_checklistCrumbs = "<a href='../index.php'>Home</a> &gt;&gt; <a href='index.php'>Checklists</a> &gt;&gt; ";	
//collections/
	$collections_indexMenu = 0;
	$collections_harvestparamsMenu = 0;
	//$collections_harvestparamsCrumbs = "<a href='index.php'>Collections</a> &gt;&gt; ";
	$collections_listMenu = 0;
	$collections_checklistMenu = 0;
	$collections_download_downloadMenu = 0;
	$collections_maps_indexMenu = 0;
	
//ident/
	$ident_keyMenu = 0;
	$ident_tools_chardeficitMenu = 0;
	$ident_tools_massupdateMenu = 0;
	$ident_tools_editorMenu = 0;
	
//taxa/
	$taxa_indexMenu = 0;
	$taxa_admin_tpeditorMenu = 0;
	
//glossary/
	$glossary_indexBanner = 0;
	
//loans/
	$collections_loans_indexMenu = 0;

//agents/
    $agents_indexMenu = TRUE;
    $agent_indexCrumbs = array();
    array_push($agent_indexCrumbs,"<a href='$CLIENT_ROOT/index.php'>Home</a>");
    array_push($agent_indexCrumbs,"<a href='$CLIENT_ROOT/agents/index.php'>Agents</a>");
		
//Base code shared by all pages; leave as is
include_once("symbbase.php");
/* --DO NOT ADD ANY EXTRA SPACES BELOW THIS LINE-- */?>
