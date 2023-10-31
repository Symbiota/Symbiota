<?php
include_once('../../config/symbini.php');

$conn = MySQLiConnectionFactory::getCon('readonly');

//Using heredoc for Highlighting. Do not use it to query construction
$portals = $conn->query(<<<sql
SELECT portalName, urlRoot from portalindex p;
sql)->fetch_all(MYSQLI_ASSOC);

?>
<div>
   <script type="module">
   const template = document.createElement("template");

   template.innerHTML = `<div>
      <div>
         <input data-role="none" type="checkbox" name="usethes" value="1" checked/>
         <label for="usethes">
            <?php echo (isset($LANG['INCLUDE_SYNONYMS'])?$LANG['INCLUDE_SYNONYMS']:'Include Synonyms'); ?>
         </label>
      </div>
      <div>
         <label for="taxa-input">
            <?php echo (isset($LANG['TAXA'])?$LANG['TAXA']:'Taxa'); ?>:
         </label>
         <input id="taxa-input" name="taxa-input" type="text" style="width:275px"/>
      </div>
   </div>`;

   class TaxaSelector extends HTMLElement {

      constructor() {
         super();
         this.portalUrl = this.getAttribute("portalUrl");
         this.shadow = this.attachShadow({ mode: "open" });
         this.shadowRoot.appendChild(template.content.cloneNode(true));

         this.shadowRoot.querySelector("#taxa-input").addEventListener('input', e => {
            this.input = e.target.value;
            this.searchTaxa();
         });
      }

      async searchTaxa() {
         if(this.input && this.input.length < 4) return;

         let response = await fetch(`${this.portalUrl}/rpc/taxasuggest.php?term=${this.input}&t=${2}`, {
            method: "POST",
            credentials: "omit",
            mode: "cors",
            body: {term: this.input, t: 1},
            headers: {
               "Access-Control-Allow-Origin": "*"
            }
         });

         console.log(await response.json());

			//url: "../rpc/gettaxon.php",
         console.log('searching ' + this.input + ' for ' + this.portalUrl);
      }
   }
   customElements.define('taxa-selector', TaxaSelector);
   </script>

   <script type="text/javascript">
   function onPortalSelect(val) {

   }
   </script>

   <input data_role="none" type="checkbox" id="cross_portal_switch" name="cross_portal_switch"/>
   <label for="cross_portal_switch">
      <?php echo (isset($LANG['ENABLE_CROSS_PORTAL_SEARCH'])? $LANG['ENABLE_CROSS_PORTAL_SEARCH']: 'Enable Cross Portal Search')?>
   </label>
   <select onchange="onPortalSelect(this.value)">
    <?php foreach($portals as $portal): ?>
      <option value="<?= $portal['urlRoot']?>"><?=$portal['portalName']?></option>
    <?php endforeach; ?>
   </select>
   <br/>
   <taxa-selector portalUrl="<?= $portals[0]['urlRoot']?>"></taxa-selector>
</div>
