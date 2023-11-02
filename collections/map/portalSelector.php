<?php
include_once('../../config/symbini.php');

$conn = MySQLiConnectionFactory::getCon('readonly');

//Using heredoc for Highlighting. Do not use it to query construction

$portals = $conn->query(<<<sql
SELECT portalName, urlRoot from portalindex p;
sql)->fetch_all(MYSQLI_ASSOC);

?>
<div>
   <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
   <script src="../../js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<script src="../../js/symb/api.taxonomy.taxasuggest.js?ver=4" type="text/javascript"></script>
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
         <label for="taxa">
            <?php echo (isset($LANG['TAXA'])?$LANG['TAXA']:'Taxa'); ?>:
         </label>
         <span style="display:block; width:275px">
            <input data-role="none" id="taxa" name="taxa" type="text" style="width:275px;margin-bottom: 0.2rem"/>
            <div id="suggestions" style="display: none; border: 1px solid gray; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); padding: 1rem">
            </div>
         </span>
      </div>
   </div>`;

   class TaxaSelector extends HTMLElement {

      constructor() {
         super();
         this.portalUrl = this.getAttribute("portalUrl");
         this.shadow = this.attachShadow({ mode: "open" });
         this.shadowRoot.appendChild(template.content.cloneNode(true));
         this.selected_child = 0;
         this.selected_option;

         this.shadowRoot.querySelector("#taxa").addEventListener('input', e => {
            this.input = e.target.value;
            this.searchTaxa();
         });

         //Open menu on focus
         this.shadowRoot.querySelector("#taxa").addEventListener('focus', e => {
            this.shadowRoot.querySelector("#suggestions").style.display = 'block';
         });
/*
         //Close menu on blur
         this.shadowRoot.addEventListener('blur', e => {
            this.shadowRoot.querySelector("#suggestions").style.display = 'none';
         });*/

         this.shadowRoot.querySelector("#suggestions").addEventListener('click', e => {
            this.shadowRoot.querySelector("#taxa").value = this.selected_option.textContent;
            this.shadowRoot.querySelector("#suggestions").style.display='none';
         });

         this.shadowRoot.querySelector("#taxa").addEventListener('keydown', e => {
            if(e.key === "ArrowUp") {
               if(0 >= this.selected_child) return;
               let children = this.shadowRoot.querySelector("#suggestions").children;

               children[this.selected_child].style['background-color'] = null;
               this.selected_child -= 1;
               children[this.selected_child].style['background-color'] = "#E9E9ED";

               this.selected_option = children[this.selected_child];

               console.log(this.selected_child);
            }
            else if(e.key === "ArrowDown") {
               let children = this.shadowRoot.querySelector("#suggestions").children;

               if(children.length <= this.selected_child) return;

               children[this.selected_child].style['background-color'] = null;
               this.selected_child += 1;
               children[this.selected_child].style['background-color'] = "#E9E9ED";

               this.selected_option = children[this.selected_child];

               console.log(this.selected_child);
            } 
         });
      }

      async searchTaxa() {
         if(this.input && this.input.length < 4) return;
         //let url = `${this.portalUrl}/rpc/taxasuggest.php?term=${this.input}&t=${2}`;
         let url = `/Portal/rpc/taxasuggest.php?term=${this.input}&t=${2}`;
         //let url = `/Portal/rpc/crossPortalHeaders.php?term=${this.input}&t=${2}`;
         this.selected_child = 0;
         this.selected_option = null;

         let response = await fetch(url, {
            method: "POST",
            credentials: "omit",
            mode: "cors",
            body: {term: this.input, t: 1},
            headers: {
               "Access-Control-Allow-Origin": "*"
            }
         });

         let taxons;

         try {
            taxons = await response.text();
         } catch(e) {
            taxons = 'No results';
         }


         let suggestions = this.shadowRoot.querySelector("#suggestions");

         suggestions.innerHTML = taxons;
         suggestions.children[this.selected_child].style['background-color'] = "#E9E9ED";

         for(let i = 0; i < suggestions.children.length; i++) {
            suggestions.children[i].addEventListener('mouseover', function() {
               suggestions.children[this.selected_child].style['background-color'] = null;
               this.selected_child = i;
               this.selected_option = suggestions.children[this.selected_child];
               suggestions.children[this.selected_child].style['background-color'] = "#E9E9ED";
            }.bind(this));

         }

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
