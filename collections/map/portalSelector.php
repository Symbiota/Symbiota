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
   template.innerHTML = `<span style="display: inline-block; font-size 1em; position: relative; width:300px">
      <input id="dropdown-input" style="width:inherit;"></input>
      <div id="suggestions" style="font-size: 0.84rem; width: inherit; position: absolute; background-color:#fff;cursor:pointer !important; display: none; border: 1px solid gray; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);"></div>
   </span>`

   class AutocompleteInput extends HTMLElement {

      constructor() {
         super();
         this.completeUrl= this.getAttribute("completeUrl");
         this.shadow = this.attachShadow({ mode: "open" });
         this.shadowRoot.appendChild(template.content.cloneNode(true));
         this.selected_index = 0;
         this.highlight_color = "#E9E9ED";
      }

      getInputElement() {
         if(!this._inputEl) {
            this._inputEl = this.shadowRoot.querySelector("#dropdown-input");
         }

         return this._inputEl;
      }

      _swapSuggestionList(newInnerHmtl) {
         const suggestions = this.shadowRoot.querySelector("#suggestions");

         if(suggestions) {
            suggestions.style.display ='block';
            suggestions.innerHTML = newInnerHmtl;
            this._changeSelection(0);

            for(let i = 0; i < suggestions.children.length; i++) {
               suggestions.children[i].addEventListener('mouseover', () => {
                  this._changeSelection(i);
               });
            }
         }
      }
      getSelection() {
         const suggestions = this.shadowRoot.querySelector("#suggestions");
         if(!suggestions) return;

         const options = suggestions.children;
         if(options.length === 0) return;

         return options[this.selected_index];
      }

      _changeSelection(new_index) {
         const suggestions = this.shadowRoot.querySelector("#suggestions");
         if(!suggestions) return;

         const options = suggestions.children;
         if(options.length === 0) {
            suggestions.style.display = 'none';
            return;
         }

         if(!this.selected_index) this.selected_index = 0;

         options[this.selected_index].style['background-color'] = null;

         if(options.length - 1 < new_index) new_index = 0;
         if(new_index < 0) new_index = options.length - 1;
         console.log(new_index)

         this.selected_index = new_index;

         options[this.selected_index].style['background-color'] = this.highlight_color;
      }

      toggleMenu(val) { 
         this.menu.style.display = val && this.menu.children.length > 0? 
            'block': 
            'none';
      }

      connectedCallback() {
         const el = this.getInputElement();
         this.menu = this.shadowRoot.querySelector("#suggestions");


         this.menu.addEventListener('mousedown', () => {
            this._inputEl.value = this.getSelection().innerHTML;
            this.onSearch(this._inputEl.value).then(res => this._swapSuggestionList(res));
         });

         el.addEventListener('input', e => {
            this._inputEl = e.target;

            const values = e.target.value.split(',');
            let value = values.length > 1? values[values.length - 1]: values[0];

            this.onSearch(value.trim()).then(res => {
               this._swapSuggestionList(res);
               this.toggleMenu(true);
            });
         });

         //el.addEventListener('blur', e => this.toggleMenu(false));

         el.addEventListener('keydown', e => {
            switch(e.key) {
               case "ArrowUp":
                  this._changeSelection(this.selected_index - 1);
                  break;
               case "ArrowDown":
                  this._changeSelection(this.selected_index + 1);
                  break;
               case "Enter":
                  const selected_option = this.getSelection();
                  if(selected_option) {

                     let values = this._inputEl.value.split(',');
                     if(values.length > 1) {
                        values[values.length - 1] = selected_option.innerHTML
                        this._inputEl.value = values.join(",");
                     } else {
                        this._inputEl.value = selected_option.innerHTML;
                     }
                  }
                  this.toggleMenu(false);
               break;
            }
         })
      }

      async onSearch(value) {
         let url = `/Portal/rpc/taxasuggest.php?term=${value}&t=${2}`;

         let response = await fetch(url, {
            method: "POST",
            credentials: "omit",
            mode: "cors",
            body: {term: value, t: 2},
            headers: {
               "Access-Control-Allow-Origin": "*"
            }
         });

         try { return response.text() } catch(e) { return "Error" }
      }
   }
   customElements.define('autocomplete-input', AutocompleteInput);
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
   <label>Taxa:</label>
   <autocomplete-input completeUrl="<?= $portals[0]['urlRoot']?>"></autocomplete-input>
</div>
