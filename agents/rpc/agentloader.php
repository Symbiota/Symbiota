<?php
// Proof of concept bulkloader for agent names.  

// Not yet ready for general use.
die;

// TODO: Change to back bulkloads of uploaded list of agent name data.
// TODO: Include ability to include GUIDs, remarks and other agent metadata beyond names and dates.

include_once('../../config/symbini.php');
$filename = "agentnames.csv";
$filename = preg_replace("[^A-Za-z_\.0-9\-]",'', array_key_exists("file",$_REQUEST) ? $_REQUEST["file"] : $filename );
include_once($SERVER_ROOT . '/classes/AgentManager.php');
include_once($SERVER_ROOT . '/classes/utilities/RdfUtil.php');

$debug = FALSE;

if ($argc==3) { 
  if ($argv[1]=="-f") { 
    $filename = $argv[2];
  }
}

$am = new AgentManager();

if (file_exists($filename) && is_readable($filename)) { 
   // read file
   $file = fopen($filename,"r"); 
   // iterate through lines
   //$line = '"Aall","Nicolai","Benjamin","","Nicolai Benjamin Aall","N","1805","1888"';
   //$line = '"Aaron","Samuel","Francis","","Samuel Francis Aaron","N","1862","1947"';
   while (($line = fgets($file)) !== false) {
      $others  = "";
      // TODO: Use a CSV parser
      $bits = explode('","',$line);
      if ($debug) { 
         echo '<pre>';
         foreach ($bits as $bit) {
            echo htmlspecialchars($bit, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401) . "\n";
         }
         echo '</pre>';
      }
      $familyname = trim(str_replace('"','',$bits[0])); 
      $firstname = trim(str_replace('"','',$bits[1])); 
      $middlename = trim(str_replace('"','',$bits[2])); 
      $suffix = trim(str_replace('"','',$bits[3]));
      $name = trim(str_replace('"','',$bits[4])); 
      $living = trim(str_replace('"','',$bits[5])); 
      $startyear = trim(str_replace('"','',$bits[6])); 
      $endyear = trim(str_replace('"','',$bits[7])); 
      if (count($bits)>8) { 
         $others = trim(str_replace('"','',$bits[8])); 
      } 
        
      echo "$name ($startyear-$endyear) ";
      $prefix = null;
      $guid = null;
      $notes = null;
      $result = $am->addAgentsFromPartsIfNotExistExt($name,$prefix,$firstname,$middlename,$familyname,$suffix,$startyear,$endyear,$living,$notes,$guid);
      if ($debug) { print_r($result); }
      $agentid = $result['agentid'];
  
      if (strlen($others)>0 && strlen($agentid)>0) { 
         $otherbits = explode("|",$others);
         foreach($otherbits as $otherbit) { 
            $othername = explode(":",$otherbit);
            $an = new agentnames();
            $an->setagentid($agentid);
            $type = $othername[0];
            // TODO: Add full range of types.
            if ($type=="standard" || $type=="Standard Abbreviation") { 
               $an->setType('Standard Abbreviation');
            } elseif ($type=="First Initials Last") { 
               $an->setType('First Initials Last');
            } else { 
               $an->setType('Also Known As');
            }
            $an->setname($othername[1]);
            if (!$an->save()) {
               if (strpos($an->errorMessage(),"Duplicate entry")===FALSE) {
                   // report problems other than duplicate entries
                   echo  "Error in saving agent name record: " . $an->errorMessage();
               }
            }
         }
      }

      if ($result['added']==1) { 
         echo "Saved ";
      } 
      echo "$agentid\n";
  
   } 
}

?>