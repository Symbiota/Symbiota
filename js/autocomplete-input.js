//This File must be imported as a module
const template = document.createElement("template");
template.innerHTML = `<span style="display: inline-block; font-size 1em; position: relative; width:300px">
   <input id="dropdown-input" style="width:inherit;"></input>
   <div id="suggestions" style="max-height:20rem; overflow-y:scroll; width: inherit; position: absolute; background-color:#fff;cursor:pointer !important; display: none; border: 1px solid gray; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);"></div>
</span>`

class AutocompleteInput extends HTMLElement {

   constructor() {
      super();
      this.completeUrl= this.getAttribute("completeUrl");

      //Represents what pattern in completeUrl will be replaced for the value
      this.url_delimter = this.getAttribute("url_delimiter")? 
         this.getAttribute("url_delimiter"):
         '??';

      //Delimter to single when multiple autocomple requests are needed
      this.input_delimter = this.getAttribute("input_delimiter")? 
         this.getAttribute("input_delimiter"):
         ',';

      this.response_type = this.getAttribute("response_type")? this.getAttribute("response_type"):'html';

      this.json_label = this.getAttribute("json_label")? this.getAttribute("json_label"):'label';
      this.json_value = this.getAttribute("json_value")? this.getAttribute("json_value"):'value';

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
         suggestions.scrollTop = 0;
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

      if(!this.selected_index || this.selected_index >= options.length) this.selected_index = 0;

      options[this.selected_index].style['background-color'] = null;

      if(options.length - 1 < new_index) new_index = 0;
      if(new_index < 0) new_index = options.length - 1;

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
         const selected_option = this.getSelection();
         if(selected_option) {
            let values = this._inputEl.value.split(this.input_delimter);
            if(values.length > 1) {
               values[values.length - 1] = selected_option.innerHTML
               this._inputEl.value = values.join(this.input_delimter);
            } else {
               this._inputEl.value = selected_option.innerHTML;
            }
         }

         this.selected_index = 0;
      });

      el.addEventListener('input', e => {
         this._inputEl = e.target;

         const values = e.target.value.split(this.input_delimter);
         let value = values.length > 1? values[values.length - 1]: values[0];

         this.onSearch(value.trim()).then(res => {
            this._swapSuggestionList(res);
            this.toggleMenu(true);
         });
      });

      el.addEventListener('blur', () => this.toggleMenu(false));

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
                  let values = this._inputEl.value.split(this.input_delimter);
                  if(values.length > 1) {
                     values[values.length - 1] = selected_option.innerHTML
                     this._inputEl.value = values.join(this.input_delimter);
                  } else {
                     this._inputEl.value = selected_option.innerHTML;
                  }
               }
               this.toggleMenu(false);
               break;
         }
      })
   }

   async resolveJson(response) {
      try { 
         let innerHtml ='';
         for(let option of await response.json()) {
            innerHtml += `<div data-value="${option[this.json_value]}" style="padding: 0.2rem">${option[this.json_label]}</div>`
         }
         return innerHtml;
      } catch(e) { 
         return "Error" 
      }
   }
   async resolveText() {
      try { 
         return await response.text() 
      } catch(e) { 
         return "Error" 
      }
   }

   async onSearch(value) {
      if(!this.completeUrl) {
         console.warn("completeUrl attribute is not set for autocomplete-input of id: " + this.id)
         return "";
      }

      let response = await fetch(this.completeUrl.replace(this.url_delimter, value), {
         method: "POST",
         credentials: "omit",
         mode: "cors",
         headers: {
            "Access-Control-Allow-Origin": "*"
         }
      });

      switch (this.response_type) {
         case "json": 
            return await this.resolveJson(response);
         default: 
            return await this.resolveText(response)
      }
   }
}
customElements.define('autocomplete-input', AutocompleteInput);
