<?php
/**
* Utility functions for working with iPlant resources.
*/

$test = FALSE;
if ($test) { 
   //$file = 'file://data.iplantcollaborative.org/iplant/home/shared/NEVP/HUH/2014-01-17-190012/NEBC00485241.dng';
   $file = 'file://data.bisque.cyverse.org/iplant/home/shared/NEVP/HUH/2014-01-17-190012/NEBC00485241.dng';
   // expected result values are: 
   // uri=https://bisque.cyverse.org/data_service/image/4976710
   // resource_uniq = 00-CiMyDXbqTSF8pL6mPgfswW
   // filter=value=irods://data.iplantcollaborative.org/iplant/home/shared/NEVP/HUH/2014-01-17-190012/NEBC00485241.dng
   $filter = str_replace('/','\/',$file);
   $filter = preg_replace('/^file:/','irods:',$filter);
   echo "$file\n";
   $result = getiPlantID($file,$filter);
   echo htmlspecialchars($result->statusok. "\n", ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
   echo htmlspecialchars($result->error. "\n", ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
   echo htmlspecialchars($result->uri. "\n", ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
   echo htmlspecialchars($result->resource_uniq. "\n", ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
   echo htmlspecialchars($result->value. "\n", ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
}

/**
* Data structure to hold results of attempted query on bisque, including
* status and errors from request.
*/
class BisqueResult { 
   public $statusok;
   public $error;
   public $responsedocument;  
   // values from response
   public $created;
   public $name;
   public $owner;
   public $permission;
   public $resource_uniq;
   public $ts;
   public $uri;
   public $value;

/* Example response document 
<resource uri="https://bisque.cyverse.org/data_service/image?tag_query=filename:IMG_0046.dng"><image created="2013-10-16T05:45:14.361834" name="IMG_0046.dng" owner="https://bisque.cyverse.org/data_service/user/4892555" permission="published" resource_uniq="yKWNDWG6heD2QFWnHES4fF" ts="2013-10-16T05:45:14.361834" uri="https://bisque.cyverse.org/data_service/image/4892591" value="irods://data.iplantcollaborative.org/iplant/home/shared/NEVP/BRU/IMG_0046.dng"/></resource>

*/

}

/**
* Given a filename or irods:// URI of a resource that has been uploaded to iPlant 
* and an irods path in iPlant, obtain the iPlant resource_uniq identifier for the resource.
*
* @author Patrick Sweeney
* @author Paul J. Morris
* 
* @param inputFilename a full irods path to a file, or just a filename without the path.
*
* @param irodsPath  Fragment of an iRods path to filter on, broad filter is 
* "irods:\/\/data.iplantcollaborative.org\/iplant\/home\/shared\/.*", 
* project specfic filter is "irods:\/\/data.iplantcollaborative.org\/iplant\/home\/shared\/NEVP.*"
* can also be the specific irods path to a file (e.g. the same as inputFilename but
* with / escaped.  Is placed into a regex /^$irodsPath$/ for matching.
* 
* @returns a BisqueResult object.
*/
function getiPlantID($inputFilename,$irodsPath){
    $result = new BisqueResult();
    $result->statusok = FALSE;
    // strip path, if any from filename
    $filename = preg_replace('/^.+[\\\\\\/]/', '', $inputFilename);
    // query bisque for a metadata document on the resource using the filename.
    $url = "https://bisque.cyverse.org/data_service/image?tag_query=filename:".$filename;
    //$url = "http://bisque.iplantcollaborative.org/data_service/image?tag_query=filename:".$filename;
    $contents = @file_get_contents($url);
    if ($contents!==FALSE) { 
        // parse the returned document
        if ($contents!="") {
           $result->responsedocument = $contents; 
           $result->statusok = FALSE;
           $xml = new SimpleXMLElement($contents);
           foreach ($xml->image as $entry) {
               if (preg_match( '/^'.$irodsPath.'$/', (string)$entry->attributes()->value) ){
                   $result->resource_uniq = (string)$entry->attributes()->resource_uniq;
                   $result->created = (string)$entry->attributes()->created;
                   $result->name = (string)$entry->attributes()->name;
                   $result->owner = (string)$entry->attributes()->owner;
                   $result->permission = (string)$entry->attributes()->permission;
                   $result->ts = (string)$entry->attributes()->ts;
                   $result->uri = (string)$entry->attributes()->uri;
                   $result->value = (string)$entry->attributes()->value;
                   $result->statusok = TRUE;
               }
           }
           if ($result->statusok===FALSE) { 
                $result->error = "No match found for $irodsPath in response from $url.";
           }
       } else { 
          $result->error = "Empty Response document from $url";
          $result->statusok = FALSE;
       }
    } else { 
       $result->error = "No response from $url";
       $result->statusok = FALSE;
    }
    return $result;  
}
?>