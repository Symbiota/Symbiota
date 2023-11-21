<!DOCTYPE html>
<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');
header('Content-Type: text/html; charset=' . $CHARSET);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = '<p>Fetching matching occurrence...</p>';

    $catalogNumber = $_POST['catalog-number'];
    $taxon = $_POST['taxon-search'];
    $collid = $_POST['collid'];

    $occManager = new OccurrenceEditorManager();
    $collIdAsNum = (int)$collid;
    $occManager->setCollId($collIdAsNum);
    $occManager->setQueryVariables();
    $occIndex = 0;
    $recLimit = 10000; // @TODO this is likely not a sufficient long-term solution
    $recStart = floor($occIndex/$recLimit)*$recLimit;
    $recArr = $occManager->getOccurMap($recStart, $recLimit);
    if($catalogNumber !== ''){
        foreach ($recArr as $key => $element) {
            if (isset($element['catalognumber']) && $element['catalognumber'] === $catalogNumber) {
                $matchingElement = $element;
                break;
            }
        }
        $occid = $matchingElement['occid'] ?? '';
    }

    $shouldRedirect = false;


    $redirectURL = $CLIENT_ROOT . '/collections/editor/occurrencetabledisplay.php?displayquery=1&collid=' . $collid;
    if($catalogNumber === '' && $taxon !== ''){
        $redirectURL = $CLIENT_ROOT . '/collections/editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=sciname&q_customtype1=STARTS&q_customvalue1=' . urlencode($taxon) . '&collid=' . $collid;
        $shouldRedirect = true;
    }
    if($catalogNumber !== ''){
        if($occid == ''){
            $message = '<p>No matching catalog number could be found</p>';
        }else{
            $redirectURL = $CLIENT_ROOT . '/collections/editor/occurrenceeditor.php?csmode=0&occindex=0&occid=' . $occid . '&collid=' . $collid;
            $shouldRedirect = true;
        }
    }else{
        $shouldRedirect = true;
    }

    if($shouldRedirect){
        header("Location: $redirectURL");
        exit;
    } else{
        echo $message;
    }
}

?>