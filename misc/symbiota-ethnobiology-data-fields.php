<?php
include_once("config/symbini.php");
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<meta http-equiv="X-Frame-Options" content="deny">
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<link href="css/base.css?<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="css/main.css?<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/config/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/header.php');
	?> 
	<!-- This is inner text! -->
	<div  id="innertext">
		<h1></h1>

		
		<div style="padding: 0px 10px;">
			<b>Symbiota Ethnobiology Data Fields</b><br><br>
The Symbiota ethnobiology data schema is strongly aligned to the Darwin Core data exchange standard. For more details, links to the EthnobiologyCore definitions are supplied for each term. Since portals have the ability to customize the field names found on their data entry form, field names may differ from what is used below.<br><br>

<b>ETHNOBIOLOGY INFORMATION</b><br><br>
<b>Project Personnel</b>: The name of a category of person.
<br>Ex: Project Manager, Community Project Manager, Data Entry Personnel
<br>This field is required. Not a Darwin Core term.<br><br>
<b>Consulant ID</b>: The unique identifier (primary key) for the consultant name record. This field should be used to store the consultant number (personnel only). This field is enforced to be unique per collection.
<br>Ex:</br></b>
This field is required. Not a Darwin Core term.<br><br>
<b>Sex</b>: The sex of the biological individual(s) represented in the Occurrence. Recommended best practice is to use a controlled vocabulary.	 
<br>Ex: "male", "female". For discussion see http://terms.tdwg.org/wiki/dwc:sex	
http://rs.tdwg.org/dwc/terms/sex	
http://rs.tdwg.org/dwc/terms/Occurrence<br>	
This field is required. Not a Darwin Core term.
<br><br>
<b>Birth Year</b>: The year of the person’s birth. [Year, Month, Day: The numeric value of the year, month, and day at the time of birth. These fields are automatically populated when the date is entered.]
<br>Ex: 1959
<br>This field is required. See Darwin Core’s year, month, day.<br>
<br>
<b>First Name</b>: The first name of a person.
<br>Ex: Jonathan<br>
This field is required. Not a Darwin Core term.<br><br>
<b>Last Name</b>: The last name of a person.
<br>Ex: Amith<br>
This field is required. Not a Darwin Core term.<br><br>
<b>Language family</b>
<br>Ex:</br></b>
Not a Darwin Core term.<br>
<br><br>
<b>VILLAGE INFORMATION</b>
<br><br><b>Country</b></b>: The name of the country in which the person is located. To aid data entry, a drop down menu will appear as one types, though names outside of the list can still be entered.
<br>Ex: USA, Mexico, Canada
<br>See Darwin Core’s country.<br>
<br><b>State/Province</b>: The name of the state or province in which the specimen was collected. As one types, a selection list will appear for the given country.
<br>Ex: New York, Arizona, Sonora
<br>See Darwin Core’s stateProvince.
<br><br><b>Municipality</b>: The name of the municipality in which the person is located. 
<br>Ex: Paradise Valley
<br>See Darwin Core’s municipality.<br>
<br><b>Village</b>: The name of the village in which the person is located. 
<br>Ex: village name 
<br>Not a Darwin Core term.<br> 
<br><b>Language Family</b>: The name of the language family spoken by a person.
<br>Ex: Indo-European</b><br>	
<br><br>
<b>LANGUAGE INFORMATION (INPUT)</b>
<br><br><b>Language Family</b>: The name of the language family spoken by a person.
<br>Ex: Indo-European
<br>This field is required. Not a Darwin Core term.
<br><br><b>Language Group</b>: The name of the language group spoken by a person.
<br>Ex: english
<br>This field is required. Not a Darwin Core term.
<br><br><b>Language SubGroup</b>: The name of the language subgroup spoken by a person.
<br>Ex: 
<br>Not a Darwin Core term.
<br><br><b>Language ISO Code</b>: The name of the language code spoke by a person.
<br>Ex: eng (follow ISO 639-3)
<br>This field is required. 
<br>Not a Darwin Core term.	
<br><br><br>
<b>LEXICON INFORMATION</b>
<br><b>Vernacular name:</b> 
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br><b>Parent taxon:</b>   
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br><b>Sibling taxa:</b>   
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br><b>Child taxa:</b>
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br><b>Discussion:</b>   
<br>Ex: 
<br>Not a Darwin Core term.
<br><br>
<b>VERNACULAR INFORMATION</b><br>
<br><b>Vernacular name:</b>
<br>Ex: 
<br>Not a Darwin Core term.
<br><br><b>Parse:</b>  
<br>Ex: 
<br>Not a Darwin Core term.
<br><br><b>Gloss:</b>  
<br>Ex: 
<br>Not a Darwin Core term.
<br><br><b>Typology:</b> 
<br>Ex: 
<br>Not a Darwin Core term.
<br><br><b>Semantics:</b>  
<br>Ex: 
<br>Not a Darwin Core term.
<br><br><b>Discussion:</b>
<br>Ex: 
<br>Not a Darwin Core term.
<br><br><b>Language family:</b> 	
<br>Ex: 
<br>Not a Darwin Core term.
<br><br> 
<b>USE INFORMATION</b><br>
<br><b>Use category:</b>
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br><b>Specific use:</b>  	 
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br><b>Part used:</b>  	 
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br>Ex: 
<br><b>Other taxa:</b>  	 
<br>Ex: 
<br>Not a Darwin Core term.<br>
<br><b>Discussion of use:</b>  	 
<br>Ex: 
<br>Not a Darwin Core term.<br>
--------------------------------------------------------------------------------------
		
		</div>
	</div>

	<?php
	include($SERVER_ROOT.'/footer.php');
	?> 
</body>
</html>
