<?php
// Rename labeljson_template.php to labeljson.php and modify or replace example label formats below
$LABEL_FORMAT_JSON = '{"labelFormats": [
	{
		"name":"Generic Herbarium Label",
		"displaySpeciesAuthor":1,
		"displayBarcode":0,
		"columnCount":"2",
		"defaultStyles":"font-style:time roman;font-size:10pt",
		"defaultCss":"",
		"labelHeader":{
			"hPrefix":"Flora of ",
			"hMidCol":3,
			"hSuffix":" county",
			"style":"text-align:center;margin-bottom:10px;font:bold 14pt arial,sans-serif;clear:both;"
		},
		"labelFooter":{
			"textValue":"",
			"style":"text-align:center;margin-top:10px;font:bold 10pt arial,sans-serif;clear:both;"
		},
		"labelBlocks":[
			{"divBlock":{"className":"labelBlockDiv","blocks":[
				{"divBlock":{"className":"taxonomyDiv","style":"margin-top:5px;font-size:11pt;","blocks":[
					{"fieldBlock":[
						{"field":"identificationqualifier"},
						{"field":"speciesname","style":"font-weight:bold;font-style:italic"},
						{"field":"parentauthor"},
						{"field":"taxonrank","style":"font-weight:bold"},
						{"field":"infraspecificepithet","style":"font-weight:bold;font-style:italic"},
						{"field":"scientificnameauthorship"}
						],"delimiter":" "
					},
					{"fieldBlock":[{"field":"family","styles":["float:right"]}]}
				]}},
				{"fieldBlock":[{"field":"identifiedby","prefix":"Det by: "},{"field":"dateidentified"}]},
				{"fieldBlock":[{"field":"identificationreferences"}]},
				{"fieldBlock":[{"field":"identificationremarks"}]},
				{"fieldBlock":[{"field":"taxonremarks"}]},
				{"divBlock":{"className":"localDiv","style":"margin-top:10px;font-size:11pt","blocks":[
					{"fieldBlock":[{"field":"country","style":"font-weight:bold"},{"field":"stateprovince","style":"font-weight:bold"},{"field":"county"},{"field":"municipality"},{"field":"locality"}],"delimiter":", "}
				]}},
				{"fieldBlock":[{"field":"decimallatitude"},{"field":"decimallongitude","style":"margin-left:10px"},{"field":"coordinateuncertaintyinmeters","prefix":"+-","suffix":" meters","style":"margin-left:10px"},{"field":"geodeticdatum","prefix":"[","suffix":"]","style":"margin-left:10px"}]},
				{"fieldBlock":[{"field":"verbatimcoordinates"}]},
				{"fieldBlock":[{"field":"elevationinmeters","prefix":"Elev: ","suffix":"m. "},{"field":"verbatimelevation"}]},
				{"fieldBlock":[{"field":"habitat","suffix":"."}]},
				{"fieldBlock":[{"field":"substrate","suffix":"."}]},
				{"fieldBlock":[{"field":"verbatimattributes"},{"field":"establishmentmeans"}],"delimiter":"; "},
				{"fieldBlock":[{"field":"associatedtaxa","prefix":"Associated species: ","style":"font-style:italic"}]},
				{"fieldBlock":[{"field":"occurrenceremarks"}]},
				{"fieldBlock":[{"field":"typestatus"}]},
				{"divBlock":{"className":"collectorDiv","style":"margin-top:10px;","blocks":[
					{"fieldBlock":[{"field":"recordedby","style":"float:left"},{"field":"recordnumber","style":"float:left;margin-left:10px"},{"field":"eventdate","style":"float:right"}]},
					{"fieldBlock":[{"field":"associatedcollectors","prefix":"with: "}],"style":"clear:both; margin-left:10px;"}
				]}}
			]}}
		]
	},
	{
		"name":"Generic Vertebrate Label",
		"displaySpeciesAuthor":0,
		"displayBarcode":0,
		"columnCount":"3",
		"defaultStyles":"font-style:time roman;font-size:8pt",
		"defaultCss":"",
		"labelHeader":{
			"hPrefix":"",
			"hMidCol":0,
			"hSuffix":"",
			"style":"text-align:center;margin-bottom:5px;font:bold 7pt arial,sans-serif;clear:both;"
		},
		"labelFooter":{
			"textValue":"",
			"style":"text-align:center;margin-top:10px;font:bold 10pt arial,sans-serif;clear:both;"
		},
		"labelBlocks":[
			{"divBlock":{"className":"labelBlockDiv","blocks":[
				{"fieldBlock":[{"field":"family","styles":["margin-bottom:2px;font-size:pt"]}]},
				{"divBlock":{"className":"taxonomyDiv","style":"font-size:10pt;","blocks":[
					{"fieldBlock":[
						{"field":"identificationqualifier"},
						{"field":"speciesname","style":"font-weight:bold;font-style:italic"},
						{"field":"parentauthor"},
						{"field":"taxonrank","style":"font-weight:bold"},
						{"field":"infraspecificepithet","style":"font-weight:bold;font-style:italic"},
						{"field":"scientificnameauthorship"}
						],"delimiter":" "
					}
				]}},
				{"fieldBlock":[{"field":"identifiedby","prefix":"Det by: "},{"field":"dateidentified"}]},
				{"fieldBlock":[{"field":"identificationreferences"}]},
				{"fieldBlock":[{"field":"identificationremarks"}]},
				{"fieldBlock":[{"field":"taxonremarks"}]},
				{"fieldBlock":[{"field":"catalognumber","style":"font-weight:bold;font-size:14pt;margin:5pt 0pt;"}]},
				{"divBlock":{"className":"localDiv","style":"margin-top:3px;padding-top:3px;border-top:3px solid black","blocks":[
					{"fieldBlock":[{"field":"country"},{"field":"stateprovince","prefix":", "},{"field":"county","prefix":", "},{"field":"municipality","prefix":", "},{"field":"locality","prefix":": "},{"field":"decimallatitude","prefix":": ","suffix":"° N"},{"field":"decimallongitude","prefix":" ","suffix":"° W"},{"field":"coordinateuncertaintyinmeters","prefix":" +-","suffix":" meters","style":"margin-left:10px"},{"field":"elevationinmeters","prefix":", ","suffix":"m."}]}
				]}},
				{"divBlock":{"className":"collectorDiv","style":"margin-top:10px;font-size:6pt;clear:both;","blocks":[
					{"fieldBlock":[{"field":"recordedby","style":"float:left;","prefix":"Coll.: "},{"field":"preparations","style":"float:right","prefix":"Prep.: "}]}
				]}},
				{"divBlock":{"className":"collectorDiv","style":"margin-top:10px;font-size:6pt;clear:both;","blocks":[
					{"fieldBlock":[{"field":"recordnumber","style":"float:left;","prefix":"Coll. No: "},{"field":"eventdate","style":"float:right","prefix":"Date: "}]}
				]}}
			]}}
		]
	},
		{
		"name":"Generic Pinned Insect Label",
		"displaySpeciesAuthor":0,
		"displayBarcode":0,
		"columnCount":"8",
		"defaultStyles":"font-style:arial, helvetica, sans-serif;font-size:3.5pt",
		"defaultCss":"",
		"labelHeader":{
		},
		"labelFooter":{
		},
		"labelBlocks":[
				{"fieldBlock":[{"field":"country"},{"field":"stateprovince","prefix":": "},{"field":"county","prefix":", "},{"field":"locality","prefix":": "},{"field":"decimallatitude","prefix":": ","suffix":"° N"},{"field":"decimallongitude","prefix":" ","suffix":"° W"},{"field":"coordinateuncertaintyinmeters","prefix":" +-","suffix":" meters","style":"margin-left:10px"},]}
				{"fieldBlock":[{"field":"locality",},{"field":"decimallatitude","prefix":" ","suffix":"°"},{"field":"decimallongitude","prefix":",","suffix":"°"},{"field":"elevationinmeters","prefix":", ","suffix":"m."}]},
				{"fieldBlock":[{"field":"recordedby","suffix":" leg."}]}


				{"divBlock":{"className":"taxonomyDiv","style":"font-size:4pt;padding:1px; border:2px solid black","blocks":[
					{"fieldBlock":[
						{"field":"identificationqualifier"},
						{"field":"speciesname","style":"font-weight:bold;font-style:italic"},
						{"field":"infraspecificepithet","style":"font-weight:bold;font-style:italic"},
						{"field":"scientificnameauthorship"}
						],"delimiter":" "
					{"fieldBlock":[{"field":"identifiedby","prefix":"Det. "},{"field":"dateidentified","prefix":" "}]},
					}
				]}},

				{"fieldBlock":[{"field":"catalognumber","style":"font-weight:bold;font-size:6pt;margin:5pt 0pt;border:2px solid black;"}]}

		]
	}
]}';
?>
