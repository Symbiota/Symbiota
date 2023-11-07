<?php
include_once('../../config/symbini.php');

$conn = MySQLiConnectionFactory::getCon('readonly');

//Using heredoc for Highlighting. Do not use it to query construction

$portals = $conn->query(<<<sql
SELECT portalName, urlRoot from portalindex p;
sql)->fetch_all(MYSQLI_ASSOC);

//Kinda a getto way of ensuring unique id's if multiple of this file is
//included. 
$PORTAL_SELECTOR_ID = !isset($PORTAL_SELECTOR_ID) || !is_int($PORTAL_SELECTOR_ID)? 0: $PORTAL_SELECTOR_ID + 1;

?>
<div>
   <script src="<?php echo $CLIENT_ROOT?>/js/autocomplete-input.js" type="module"></script>
   <script type="text/javascript">
   function onPortalSelect(v) {
      let input = document.getElementById("portal-taxa-suggest-<?php echo $PORTAL_SELECTOR_ID?>")
      input.completeUrl = v + '/rpc/taxasuggest.php?term=??';
   }

   function onEnablePortalSelector(on) {
      let selector = document.getElementById("portal-selector-<?php echo $PORTAL_SELECTOR_ID?>")
      selector.style.display= on ?'block': 'none';
   }
   </script>
   <div>
      <input 
         onchange="onEnablePortalSelector(this.checked)"
         data_role="none" 
         type="checkbox" 
         name="cross_portal_switch"/>
      <label for="cross_portal_switch">
         <?php echo (isset($LANG['ENABLE_CROSS_PORTAL_SEARCH'])? $LANG['ENABLE_CROSS_PORTAL_SEARCH']: 'Enable Cross Portal Search')?>
      </label>
   </div>
   <br/>
   <div id="portal-selector-<?php echo $PORTAL_SELECTOR_ID?>" style="display:none">
      <div>   
         <label>Portal:</label>
         <select value="Choose a Portal to Search" onchange="onPortalSelect(this.value)">
            <?php foreach($portals as $portal): ?>
            <option value="<?= $portal['urlRoot']?>"><?=$portal['portalName']?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <br/>
      <div>
         <label>Taxa:</label>
         <autocomplete-input 
            id="portal-taxa-suggest-<?php echo $PORTAL_SELECTOR_ID?>"
            response_type="json"
            json_label="value"
            json_value="id"
            completeUrl="<?= '/Portal/rpc/taxasuggest.php?term=??'?>">
         </autocomplete-input>
      </div>
   </div>
</div>
