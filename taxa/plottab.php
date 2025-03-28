<?php
  include_once($SERVER_ROOT . '/config/symbini.php');
  include_once($SERVER_ROOT.'/content/lang/taxa/index.'.$LANG_TAG.'.php');
  include_once($SERVER_ROOT.'/classes/TaxonProfile.php');
  include_once($SERVER_ROOT.'/classes/TraitPlotManager.php');
  include_once($SERVER_ROOT.'/classes/TraitPolarPlot.php');
  Header('Content-Type: text/html; charset='.$CHARSET);

  $tid = $_REQUEST['tid'];
  $taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;
  if(isset($CALENDAR_TRAIT_PLOTS)) {
    $traitstateids = explode(",", $CALENDAR_TRAIT_PLOTS);
    $traitstateids = array_map('trim', $traitstateids);
  } else {
    $traitstateids = array("0");
  }

  //Sanitation
  if(!is_numeric($tid)) $tid = 0;
  if(!is_array($traitstateids)) $traitstateids = array(0);
?>

<script type="text/javascript">
  function toggleDisplay(id) {
    const plotids = ["barplots", "lineplots", "polarplots"];
    var elem = document.getElementById(id);
    var idx = plotids.indexOf(id);
    if (idx > -1) {
      plotids.splice(idx, 1);
    }
    if (elem.style.display === "none") {
      elem.style.display = "block";
    } else {
      elem.style.display = "none";
    }
    for (let i in plotids) {
      document.getElementById(plotids[i]).style.display = "none";
    }
  }
</script>

<button class="PlotButton" onclick="toggleDisplay('barplots')"><img class="PlotIcon" src="../images/barplot.png" alt="bar plot icon" /></button>
<button class="PlotButton" onclick="toggleDisplay('lineplots')"><img class="PlotIcon" src="../images/lineplot.png" alt="line plot icon" /></button>
<button class="PlotButton" onclick="toggleDisplay('polarplots')"><img class="PlotIcon" src="../images/polarplot.png" alt="line plot icon" /></button>

<!--label class="PlotSwitch" for="_0"><img class="PlotIcon" src="../images/barplot.png" alt="bar plot icon" /></label>
<input id="_0" type="radio" name="c1"-->
<?php
  echo '<div id="barplots" class="sptab">';
  foreach($traitstateids as $tsid) {
    if(!is_numeric($tsid)) continue;
    $traitPlotter = new TraitPlotManager("bar");
    if($tid) $traitPlotter->setTid($tid);
    $traitPlotter->setTraitStateId($tsid);
    $traitPlotter->calendarPlot();
    if($traitPlotter->getNumberOfSpecimens() > 0){
      echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
      echo '<svg height="'.$traitPlotter->getViewboxHeight().'px" viewbox="0 0 ' . $traitPlotter->getViewboxWidth() . ' ' . $traitPlotter->getViewboxHeight() . '" ><g>' . PHP_EOL;
      echo $traitPlotter->display();
      echo '</g></svg>';
      echo '<p class="PlotCaption">'.$traitPlotter->getPlotCaption().'</p>';
    } else {
      echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
      echo '<p class="PlotCaption">No specimens encoded for '.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</p>';
    }

  }
  echo '</div>';

  echo '<div id="lineplots" class="sptab" style="display:none;">';
  foreach($traitstateids as $tsid) {
    if(!is_numeric($tsid)) continue;
    $traitPlotter = new TraitPlotManager("line");
    if($tid) $traitPlotter->setTid($tid);
    $traitPlotter->setTraitStateId($tsid);
    $traitPlotter->calendarPlot();
    if($traitPlotter->getNumberOfSpecimens() > 0){
      echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
      echo '<svg height="'.$traitPlotter->getViewboxHeight().'px" viewbox="0 0 ' . $traitPlotter->getViewboxWidth() . ' ' . $traitPlotter->getViewboxHeight() . '" ><g>' . PHP_EOL;
      echo $traitPlotter->display();
      echo '</g></svg>';
      echo '<p class="PlotCaption">'.$traitPlotter->getPlotCaption().'</p>';
    } else {
      echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
      echo '<p class="PlotCaption">No specimens encoded for '.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</p>';
    }

  }
  echo '</div>';

  echo '<div id="polarplots" class="sptab" style="display:none;">';
  foreach($traitstateids as $tsid) {
    if(!is_numeric($tsid)) continue;
    $traitPlotter = new TraitPlotManager("polar");
    if($tid) $traitPlotter->setTid($tid);
    $traitPlotter->setTraitStateId($tsid);
    $traitPlotter->calendarPlot();
    if($traitPlotter->getNumberOfSpecimens() > 0){
      echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
      echo '<svg height="'.$traitPlotter->getViewboxHeight().'px" viewbox="0 0 ' . $traitPlotter->getViewboxWidth() . ' ' . $traitPlotter->getViewboxHeight() . '" ><g>' . PHP_EOL;
      echo $traitPlotter->display();
      echo '</g></svg>';
      echo '<p class="PlotCaption">'.$traitPlotter->getPlotCaption().'</p>';
    } else {
      echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
      echo '<p class="PlotCaption">No specimens encoded for '.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</p>';
    }
  }
  echo '</div>';
?>
