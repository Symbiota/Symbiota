<?php
// Ajax responder to query for action requests

include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ActionManager.php');

$requesttype = null;
$priority = null;
$state = null;
$collid = null;
$text = null;

$aManager = new ActionManager(null,'readonly');

$raw = $_POST['query'];
// Convert to parsable json
$raw = "{ $raw }"; 
$raw = str_replace('\"','"',$raw);
$raw = str_replace("requesttype",'"requesttype"',$raw);
$raw = str_replace("state",'"state"',$raw);
$raw = str_replace("resolution",'"resolution"',$raw);
$raw = str_replace("priority",'"priority"',$raw);
$raw = str_replace("collid",'"collid"',$raw);

//TODO: text is coming in without a key, thus invalid json.
//$raw = str_replace("text",'"text"',$raw);  

// TODO: Currently only handling the last instance of a particular key, allow multiple (connect with OR).
$query = json_decode($raw);

$requesttype=preg_replace("/[^a-zA-Z]/","",$query->{'requesttype'});
$priority=preg_replace("/[^0-9]/","",$query->{'priority'});
$state=preg_replace("/[^a-zA-Z]/","",$query->{'state'});
$resolution=preg_replace("/[^a-zA-Z]/","",$query->{'resolution'});
$collid=preg_replace("/[^0-9]/","",$query->{'collid'});
$text=$query->{'text'};
// workaround for text as invalid json
if ($query==null) { 
   $text = $_POST['query'];
}

// run query
$actionArr = $aManager->queryActionRequestsObjArr($requesttype,$priority,$state,$resolution,$collid,$text);

// Report what the query was interpreted as:
if (strlen($priority)>0) { $priority = "Priority:P$priority"; } 
if (strlen($state)>0) { $state = "State:$state"; } 
if (strlen($resolution)>0) { $resolution = "Resolution:$resolution"; } 
if (strlen($requesttype)>0) { $requesttype = "RequestFor:$requesttype"; } 
if (strlen($text)>0) { $text = "Text:$text"; } 
echo "<strong>Search for: $requesttype $priority $state $resolution $collid $text</strong><br/> ";
echo "<strong>Found ". count($actionArr) ." matching requests.</strong><br/> ";
echo $aManager->getErrorMessage();

// iterate through the results
foreach ($actionArr as $action) {
    echo "<a href='index.php?actionrequestid=$action->actionrequestid'>Request for $action->requesttype</a> on ".$action->getLinkToRow()." by  $action->requestor on  $action->requestdate  $action->requestremarks, Priority: P$action->priority, State:$action->state $action->resolution $action->statesetdate $action->resolutionremarks $action->fullfillor </br>\n";
}


?>
