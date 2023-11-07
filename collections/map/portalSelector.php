<?php
include_once('../../config/symbini.php');

$conn = MySQLiConnectionFactory::getCon('readonly');

//Using heredoc for Highlighting. Do not use it to query construction

$portals = $conn->query(<<<sql
SELECT portalName, urlRoot from portalindex p;
sql)->fetch_all(MYSQLI_ASSOC);
//$portals[0]['urlRoot'] . 
?>
<div>
   <script src="<?php echo $CLIENT_ROOT?>/js/autocomplete-input.js" type="module"></script>
   <div>
      <input data_role="none" type="checkbox" id="cross_portal_switch" name="cross_portal_switch"/>
      <label for="cross_portal_switch">
         <?php echo (isset($LANG['ENABLE_CROSS_PORTAL_SEARCH'])? $LANG['ENABLE_CROSS_PORTAL_SEARCH']: 'Enable Cross Portal Search')?>
      </label>
   </div>
   <br/>
   <div>   
      <label>Portal:</label>
      <select onchange="onPortalSelect(this.value)">
         <?php foreach($portals as $portal): ?>
         <option value="<?= $portal['urlRoot']?>"><?=$portal['portalName']?></option>
         <?php endforeach; ?>
      </select>
   </div>
   <br/>
   <div>
      <label>Taxa:</label>
      <autocomplete-input 
         response_type="json"
         json_label="value"
         json_value="id"
         completeUrl="<?= '/Portal/rpc/taxasuggest.php?term=??'?>">
      </autocomplete-input>
   </div>
</div>
