/**
 * GLOBAL VARIABLES
 */
const criteriaPanel = document.getElementById("criteria-panel") || null;
const form = document.getElementById("params-form") || null;
const formColls = document.getElementById("search-form-colls") || null;
const formSites = document.getElementById("site-list") || null;
const searchFormColls = document.getElementById("search-form-colls") || null;
const searchFormPaleo = document.getElementById("search-form-geocontext") || null;

// Helper function to get currentPage value, initializing if necessary
function getCurrentPage() {
	if (typeof window.currentPage === 'undefined') {
		window.currentPage = JSON.parse(document.getElementById("all_collections_parent_container")?.dataset?.config || "{}")?.CURRENT_URL;
	}
	return window.currentPage;
}

// Initialize currentPage when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
	getCurrentPage();
});

const uLat = document.getElementById("upperlat") || null;
const uLatNs = document.getElementById("upperlat_NS") || null;
const bLat = document.getElementById("bottomlat") || null;
const bLatNs = document.getElementById("bottomlat_NS") || null;
const lLng = document.getElementById("leftlong") || null;
const lLngEw = document.getElementById("leftlong_EW") || null;
const rLng = document.getElementById("rightlong") || null;
const rLngEw = document.getElementById("rightlong_EW") || null;
const pLat = document.getElementById("pointlat") || null;
const pLatNs = document.getElementById("pointlat_NS") || null;
const pLng = document.getElementById("pointlong") || null;
const pLngEw = document.getElementById("pointlong_EW") || null;
const pRadius = document.getElementById("radius") || null;
const pRadiusUn = document.getElementById("radiusunits") || null;
let formInputs = null;

let paramsArr = {};
//////////////////////////////////////////////////////////////////////////

/**
 * METHODS
 */

/**
 * Toggles tab selection for collection picking options in modal
 * Uses jQuery
 */
$('input[type="radio"]')?.click(function () {
  const inputValue = $(this)?.attr("value");
  const targetBox = $("#" + inputValue);
  $(".box")?.not(targetBox)?.hide();
  $(targetBox)?.show();
  $(this)?.parent()?.addClass("tab-active");
  $(this)?.parent()?.siblings()?.removeClass("tab-active");
});

/**
 * Chips
 */

/**
 * Adds default chips
 * @param {HTMLObjectElement} element Input for which chips are going to be created by default
 */
function addChip(element) {
  if (!element || (!element.name && element?.tagName !== "OPTION")) return;
  let inputChip = document.createElement("span") || null;
  if (!inputChip) return;
  inputChip?.classList?.add("chip");
  let chipBtn = document.createElement("button") || null;
  if (!chipBtn) return;
  chipBtn?.setAttribute("type", "button");
  chipBtn?.classList?.add("chip-remove-btn");

  // if element is domain or site, pass other content
  if (element?.name == "some-datasetid") {
    if (element.text != "" && inputChip && chipBtn) {
      inputChip.id = "chip-some-datasetids";
      inputChip.textContent = element?.text;
      chipBtn.onclick = function () {
        uncheckAll(document.getElementById("all-sites"));
        removeChip(inputChip);
      };
    }
  } else if (
    (element.name == "neonext-collections-list") |
    (element.name == "ext-collections-list") |
    (element.name == "taxonomic-cat") |
    (element.name == "neon-theme") |
    (element.name == "sample-type")
  ) {
    inputChip.id = `chip-some-${element.name}-collids`;
    inputChip.textContent = element.text;
    chipBtn.onclick = function () {
      uncheckAll(document.getElementById(element.name));
      removeChip(inputChip);
    };
  }
  else if (element.tagName === "OPTION") {
    const selectElement = element.closest("select");
    if (selectElement && selectElement.multiple) {
        //if multiple options (like polygons)
        let oldChip = document.getElementById("chip-" + selectElement.dataset.chip);
        if (oldChip) removeChip(oldChip);

        const selected = Array.from(selectElement.selectedOptions).map(opt => opt.textContent);
        if (selected.length > 0) {
            inputChip.id = "chip-" + selectElement.dataset.chip;
            inputChip.textContent =
                (selectElement.dataset.chip ? selectElement.dataset.chip + ": " : "") +
                selected.join(", ");

            chipBtn.onclick = () => {
                Array.from(selectElement.options).forEach(opt => (opt.selected = false));
                removeChip(inputChip);
            };
        }
    } else {
        //if single option
        inputChip.id = "chip-" + element.dataset.chip;
        inputChip.textContent = (element.dataset.chip ? element.dataset.chip + ": " : "") + element.textContent;

        chipBtn.onclick = () => handleRemoval(element, inputChip);
    }
    inputChip.appendChild(chipBtn);
  }
  else {
    inputChip.id = "chip-" + element.id;
    let isTextOrNum = (element.type == "text") | (element.type == "number");
    isTextOrNum
      ? (inputChip.textContent = `${element.dataset.chip}: ${element.value}`)
      : (inputChip.textContent = element.dataset.chip);
    chipBtn.onclick = () => handleRemoval(element, inputChip);
  }
  let screenReaderSpan = document.createElement("span");
  const dataChipText = element.getAttribute("data-chip");
  const screenReaderText = dataChipText
    ? dataChipText
    : element?.text || "Unknown chip";
  screenReaderSpan.textContent =
    "Remove " + screenReaderText + " from search criteria";
  screenReaderSpan?.classList?.add("screen-reader-only");
  chipBtn.appendChild(screenReaderSpan);
  inputChip.appendChild(chipBtn);
  document.getElementById("chips")?.appendChild(inputChip);
}

function handleRemoval(element, inputChip) {
  element.type === "checkbox"
    ? (element.checked = false)
    : (element.value = element.defaultValue);
    if (element.tagName === "OPTION") {
      const selectElement = element.closest('select');
      if (selectElement) {
        element.selected = false;
      }
    }
  if (element.getAttribute("id") === "all_collections") {
    const targetCategoryCheckboxes =
      document.querySelectorAll('input[id^="cat-"]');
    targetCategoryCheckboxes.forEach((collection) => {
      collection.checked = false;
    });
    const targetCheckboxes = document.querySelectorAll('input[id^="coll-"]');
    targetCheckboxes.forEach((collection) => {
      collection.checked = false;
    });
    //do the same for collections with slightly different format
    const targetCheckboxAlts = document.querySelectorAll(
      'input[id^="collection-"]'
    );
    targetCheckboxAlts.forEach((collection) => {
      collection.checked = false;
    });
  }
  setAssociationRelationshipTypeToDefault(element);
  setMaterialSampleToDefault(element);
  setTaxonTypeToDefault(element);
  setAssociationTaxonTypeToDefault(element);
  element.dataset.formId ? uncheckAll(element) : "";
  removeChip(inputChip);
}

function setMaterialSampleToDefault(element) {
  if (element?.getAttribute("id")?.startsWith("materialsampletype")) {
    const targetIndex = document.getElementById(
      "materialsampletype-none"
    ).selectedIndex;
    document.getElementById("materialsampletype").selectedIndex = targetIndex;
  }
}

function setTaxonTypeToDefault(element) {
  if (element?.getAttribute("id")?.startsWith("taxontype")) {
    const targetIndex = document.getElementById("taxontype-any")?.selectedIndex;
    document.getElementById("taxontype").selectedIndex = targetIndex;
  }
}

function setAssociationTaxonTypeToDefault(element) {
  if (element?.getAttribute("id")?.startsWith("taxontype-association-")) {
    const targetIndex = document.getElementById(
      "taxontype-association-scientific"
    )?.selectedIndex;
    document.getElementById("taxontype-association").selectedIndex =
      targetIndex;
  }
}

function setAssociationRelationshipTypeToDefault(element) {
  if (element?.getAttribute("id")?.startsWith("association-type-")) {
    const targetIndex = document.getElementById(
      "association-type-none"
    )?.selectedIndex;
    document.getElementById("association-type").selectedIndex = targetIndex;
  }
}

/**
 * Removes chip
 * @param {HTMLObjectElement} chip Chip element
 */
function removeChip(chip) {
  chip != null ? chip.remove() : "";
}

/**
 * Updateds chips based on selected options
 * @param {Event} e
 */
function updateChip(e, isInitialConfig=false) {
  document.getElementById("chips") ? document.getElementById("chips").innerHTML = "" : "";
  // first go through collections and sites

  // No sites in Symbiota, so this stuff just gets ignored
  // if any domains (except for "all") is selected, then add chip
  let dSList = document.querySelectorAll("#site-list input[type=checkbox]");
  let dSChecked = document.querySelectorAll(
    "#site-list input[type=checkbox]:checked"
  );
  if (
    dSList &&
    dSChecked &&
    dSChecked.length > 0 &&
    dSChecked.length < dSList.length
  ) {
    addChip(getDomainsSitesChips());
  }

  const individualCollectionsChecked = Array.from(
    document.querySelectorAll(`#search-form-colls input[name="db[]"]:checked:not(#all_collections)`)
  );
  const individualCollectionsCheckedIds = individualCollectionsChecked.map(coll => coll.value);

  const allPossibleSpecimenCollections = calculateAllPossibleCollectionsInScope("specimens_collections");
  const didAllSpecimenCollectionGetSelected = contains(individualCollectionsCheckedIds, allPossibleSpecimenCollections);

  const allPossibleObservationCollections = calculateAllPossibleCollectionsInScope("observations_collections");
  const didAllObservationCollectionGetSelected = contains(individualCollectionsCheckedIds, allPossibleObservationCollections);

  const allPossibleCollections = calculateAllPossibleCollectionsInScope("search-form-colls");
  const didAllCollectionGetSelected = areSame(allPossibleCollections, individualCollectionsCheckedIds);

  // if any additional NEON colls are selected (except for "all"), then add chip
  const addCols = document.querySelectorAll(
    "#neonext-collections-list input[type=checkbox]"
  );
  const addColsChecked = document.querySelectorAll(
    "#neonext-collections-list input[type=checkbox]:checked"
  );
  if (addColsChecked.length > 0 && addColsChecked.length < addCols.length) {
    addChip(getCollsChips("neonext-collections-list", "Some Add NEON Colls"));
  }
  // if any external NEON colls are selected (except for "all"), then add chip
  const extCols = document.querySelectorAll(
    "#ext-collections-list input[type=checkbox]"
  );
  const extColsChecked = document.querySelectorAll(
    "#ext-collections-list input[type=checkbox]:checked"
  );
  if (extColsChecked.length > 0 && extColsChecked.length < extCols.length) {
    addChip(getCollsChips("ext-collections-list", "Some Ext NEON Colls"));
  }
  // then go through remaining inputs (exclude db and datasetid)
  if(!isInitialConfig){
    const isCollectionRelated = e?.currentTarget?.name === "db[]" || e?.currentTarget?.name?.startsWith("Specimens_") || e?.currentTarget?.name?.startsWith("Observations_") || e?.currentTarget?.id === "all_collections" || e?.currentTarget?.id === "all_specimen_collections" || e?.currentTarget?.id === "all_observation_collections";
    if(isCollectionRelated){
      const checkedCollections = calculateAllPossibleCollectionsInScope('search-form-colls', ':checked',true);
      const updatedQueriedCollections = updateQueryListWithTypeCollections(checkedCollections);
      checkTheCollectionsThatShouldBeChecked(updatedQueriedCollections);
      updateCategoryCheckboxes();
      closeAllCategories();
      expandCategoriesWithSomeCheckedChildren();
    }
  }
  
  if (!formInputs) {
    formInputs = document.querySelectorAll(".content input");
  }
  
  formInputs.forEach((item) => {
    if ((item.name != "db") | (item.name != "datasetid")) {
      if (
        (item.type == "checkbox" && item.checked) |
        (item.type == "text" && item.value != "") |
        (item.type == "number" && item.value != "")
      ) {
        if(didAllCollectionGetSelected && item.id === "all_collections"){
          addChip(item);
        }
        if(didAllSpecimenCollectionGetSelected && item.id === "all_specimen_collections"){
          addChip(item);
        }
        if(didAllObservationCollectionGetSelected && item.id === "all_observation_collections"){
          addChip(item);
        }
        if (
          (didAllCollectionGetSelected) &&
          item.name === "db[]" &&
          item.id !== "all_collections" &&
          item.id !== "all_specimen_collections" &&
          item.id !== "all_observation_collections"
        ) {
          // don't add these chips;
        } else {
          // add chips depending on type of item
          const defaultValues = [
            { id: "usethes", value: "1" },
            { id: "includeothercatnum", value: "1" },
            { id: "usethes-associations", value: "1" },
          ];
          const isInDefaultValList = defaultValues.some(
            (val) => val.id === item.id && val.value === item.value
          );
          if(!isInDefaultValList && item.hasAttribute("data-chip")) {
            const itemIsOutsidePanTypeSelections = calculateWhetherItemIsOutsidePanTypeSelections(item, didAllSpecimenCollectionGetSelected, didAllObservationCollectionGetSelected, allPossibleSpecimenCollections, allPossibleObservationCollections);
            const itemIsCollectionRelated = item?.name === "db[]" || item?.id?.startsWith("Specimens_") || item?.id?.startsWith("Observations_") || item?.id === "all_collections" || item?.id === "all_specimen_collections" || item?.id === "all_observation_collections";
            if(itemIsOutsidePanTypeSelections || !itemIsCollectionRelated){
              addChip(item);
            }
          }
        }
      }
    }
    // print inputs checked or filled in
  });

  // then go through remaining options and find selected items
  const optionElements = document.querySelectorAll(".content option");
  const defaultValues = [
    { id: "taxontype-scientific", value: "2" },
    { id: "association-type-none", value: "none" },
    { id: "taxontype-association-scientific", value: "2" },
  ];
  optionElements.forEach((item) => {
    if (item.selected && item.value && item.hasAttribute("data-chip")) {
      const isInDefaultValList = defaultValues.some(
        (val) => val.id === item.id && val.value === item.value
      );
      if (!isInDefaultValList) {
        const itemIsOutsidePanTypeSelections = calculateWhetherItemIsOutsidePanTypeSelections(item, didAllSpecimenCollectionGetSelected, didAllObservationCollectionGetSelected, allPossibleSpecimenCollections, allPossibleObservationCollections);
        if(itemIsOutsidePanTypeSelections){
          addChip(item);
        }
      }
    }
  });
}

function calculateWhetherItemIsOutsidePanTypeSelections(item, didAllSpecimenCollectionGetSelected, didAllObservationCollectionGetSelected, allPossibleSpecimenCollections, allPossibleObservationCollections){
  if(item?.tagName === 'OPTION' || item?.nodeName === 'OPTION') return true;
  if(didAllSpecimenCollectionGetSelected && didAllObservationCollectionGetSelected){
    return false;
  }
  if(didAllSpecimenCollectionGetSelected==null || didAllObservationCollectionGetSelected==null || allPossibleSpecimenCollections==null || allPossibleObservationCollections==null){
    return true;
  }
  const allSpecimenInputExplicitlySelected = document.getElementById("all_specimen_collections")?.checked || false;
  const allObservationInputExplicitlySelected = document.getElementById("all_observation_collections")?.checked || false;
  const shouldExcludeBecauseInAllSpecimens = ((didAllSpecimenCollectionGetSelected||allSpecimenInputExplicitlySelected) && (allPossibleSpecimenCollections.includes(item.value))) || item.id === "all_specimen_collections";
  const shouldExcludeBecauseInAllObservations = ((didAllObservationCollectionGetSelected||allObservationInputExplicitlySelected) && (allPossibleObservationCollections.includes(item.value))) || item.id === "all_observation_collections";
  return !shouldExcludeBecauseInAllSpecimens && !shouldExcludeBecauseInAllObservations;
}

/**
 * Gets collections chips
 * @param {String} listId id of coll list element
 * @param {String} chipText explanatory text to be addded to chip
 * @returns {Object} chipEl chip element with text and name props
 */
function getCollsChips(listId, chipText) {
  // Goes through list of collection options
  let collOptions = document.querySelectorAll(
    `#${listId} input[type=checkbox]`
  );
  let collSelected = document.querySelectorAll(
    `#${listId} input[type=checkbox]:checked`
  );
  // If 'all' is not selected, picks which are selected
  collsArr = [];
  let chipEl = {};

  if (collOptions.length > collSelected.length) {
    // Generates chip element object
    collSelected.forEach((coll) => {
      // check if we're inside biorepo coll form
      let isColl = coll.dataset.cat != undefined;
      if (isColl) {
        let isCatSel = document.getElementById(coll.dataset.cat).checked;
        isCatSel ? "" : collsArr.push(coll.dataset.ccode);
      } else {
        collsArr.push(coll.dataset.ccode);
      }
    });
  }
  chipEl.text = `${chipText}: ${collsArr.join(", ")}`;
  chipEl.name = listId;
  return chipEl;
}

/**
 * Gets selected domains and sites to generate chips
 * @returns {Object} chipEl chip element with text and name props
 */
function getDomainsSitesChips() {
  let boxes = document.getElementsByName("datasetid");
  let dArr = [];
  let sArr = [];
  boxes.forEach((box) => {
    if (box.checked) {
      let isSite = box.dataset.domain != undefined;
      if (isSite) {
        let isDomainSel = document.getElementById(box.dataset.domain).checked;
        isDomainSel ? "" : sArr.push(box.id);
      } else {
        dArr.push(box.id);
      }
    }
  });
  let dStr = "";
  let sStr = "";
  dArr.length > 0 ? (dStr = `Domain(s): ${dArr.join(", ")} `) : "";
  sArr.length > 0 ? (sStr = `Sites: ${sArr.join(", ")}`) : "";
  let chipEl = {
    text: dStr + sStr,
    name: "some-datasetid",
  };
  return chipEl;
}
/////////

/**
 * Toggles state of checkboxes in nested lists when clicking an "all-selector" element
 * Uses jQuery
 */
function toggleAllSelector() {
  $(this)
    .siblings()
    .find("input[type=checkbox]:enabled")
    .prop("checked", this.checked)
    .attr("checked", this.checked);
}

/**
 * Triggers toggling of checked/unchecked boxes in nested lists
 * Default is all boxes are checked in HTML.
 * @param {String} e.data.element Selector for element containing
 * list, should be passed when binding function to element
 */
function autoToggleSelector(e) {
  if (e.type == "click" || e.type == "change") {
    let isChild = e.target.classList.contains("child");
    if (isChild) {
      let nearParentNode = e.target.closest("ul").parentNode;
      let nearParentOpt = e.target
        .closest("ul")
        .parentNode.querySelector(".all-selector");
      let numOptions = nearParentNode.querySelectorAll(
        "ul > li input.child:not(.all-selector):enabled"
      ).length;
      let numOpChecked = nearParentNode.querySelectorAll(
        "ul > li input.child:not(.all-selector):checked"
      ).length;
      numOptions == numOpChecked
        ? (nearParentOpt.checked = true)
        : (nearParentOpt.checked = false);

      if (nearParentOpt.classList.contains("child")) {
        let parentAllNode = nearParentNode.closest("ul").parentNode;
        let parentAllOpt = parentAllNode.querySelector(".all-selector");
        let numOptionsAll = parentAllNode.querySelectorAll(
          "input.child:enabled"
        ).length;
        let numOpCheckedAll = parentAllNode.querySelectorAll(
          "input.child:checked"
        ).length;
        numOptionsAll == numOpCheckedAll
          ? (parentAllOpt.checked = true)
          : (parentAllOpt.checked = false);
      }
    }
  }
}

/**
 * Finds all collections selected
 * Uses active tab in modal
 */
function getCollsSelected() {
  const selectedInForm = Array.from(
    document.querySelectorAll(
      '#search-form-colls input[name="db"]:checked, ' +
        '#search-form-colls input[name="db[]"]:checked:not(#all_collections)'
    )
  );
  return selectedInForm;
}

/**
 * Form validation functions
 * @returns {Array} errors Array of errors objects with form element it refers to (elId), for highlighting, and errorMsg
 */
function validateForm() {
  const errors = [];
  // DB
  const anyCollsSelected = getCollsSelected();
  if (anyCollsSelected.length === 0) {
    errors.push({
      elId: "search-form-colls",
      errorMsg: "Please select at least one collection.",
    });
  }
  // HTML5 built-in validation
  const invalidInputs = document.querySelectorAll("input:invalid");
  if (invalidInputs.length > 0) {
    invalidInputs.forEach((inp) => {
      errors.push({
        elId: inp.id,
        errorMsg: `Please check values in field ${inp.dataset.chip}.`,
      });
    });
  }
  // Bounding Box
  const bBoxNums = document.querySelectorAll(
    "#bounding-box-form input[type=number]"
  );
  const bBoxNumArr = [];
  bBoxNums.forEach((el) => {
    el.value != "" ? bBoxNumArr.push(el.value) : false;
  });
  const bBoxCardinals = document.querySelectorAll("#bounding-box-form select");
  const selectedCardinals = [];
  bBoxCardinals.forEach((hItem) => {
    hItem.value != "" ? selectedCardinals.push(hItem.id) : false;
  });
  if (bBoxNumArr.length > 0 && bBoxNumArr.length < bBoxNums.length) {
    errors.push({
      elId: "bounding-box-form",
      errorMsg:
        "Please make sure either all Lat/Long bounding box values contain a value, or all are empty.",
    });
  } else if (bBoxNumArr.length > 0 && selectedCardinals.length == 0) {
    errors.push({
      elId: "bounding-box-form",
      errorMsg: "Please select hemisphere values.",
    });
  } else if (bBoxNumArr.length > 0 && selectedCardinals.length > 0) {
    let uLatVal = uLat.value;
    let uLatNsVal = uLatNs.value;
    let bLatVal = bLat.value;
    let bLatNsVal = bLatNs.value;

    if (uLatNsVal == "S" && bLatNsVal == "S") {
      uLatVal = uLatVal * -1;
      bLatVal = bLatVal * -1;
      if (uLatVal < bLatVal) {
        errors.push({
          elId: "bounding-box-form",
          errorMsg:
            "Your northern latitude value is less than your southern latitude value.",
        });
      }
    }

    let lLngVal = lLng.value;
    let lLngEwVal = lLngEw.value;
    let rLngVal = rLng.value;
    let rLngEwVal = rLngEw.value;

    if (lLngEwVal == "W" && rLngEwVal == "W") {
      lLngVal = lLngVal * -1;
      rLngVal = rLngVal * -1;
      if (lLngVal > rLngVal) {
        errors.push({
          elId: "bounding-box-form",
          errorMsg:
            "Your western longitude value is greater than your eastern longitude value. Note that western hemisphere longitudes in the decimal format are negative.",
        });
      }
    }
  }

  // Geo Context
  if (searchFormPaleo) {
    let early = form.earlyInterval.value;
    let late = form.lateInterval.value;
    if ((early !== "" && late === "") || (early === "" && late !== "")) {
      errors.push({
        elId: "search-form-geocontext",
        errorMsg:
          translations.INTERVAL_MISSING,
      });
    }

    if (early in paleoTimes && late in paleoTimes && paleoTimes[early].myaStart <= paleoTimes[late].myaEnd) {
      errors.push({
        elId: "search-form-geocontext",
        errorMsg:
          translations.INTERVALS_WRONG_ORDER,
      });
    }
  }

  return errors;
}

/**
 * Gets validation errors, outputs alerts with error messages and highlights form element with error
 * @param {Array} errors Array with error objects with form element it refers to (elId), for highlighting, and errorMsg
 */
function handleValErrors(errors) {
  const errorDiv = document.getElementById("error-msgs");
  errorDiv.innerHTML = "";
  errors.map((err) => {
    let element = document.getElementById(err.elId);
    element.classList.add("invalid");
    errorDiv.classList.remove("visually-hidden");
    let errorP = document.createElement("p");
    errorP.classList.add("error");
    errorP.innerText = err.errorMsg + " Click to dismiss.";
    errorP.onclick = function () {
      errorP.remove();
      element.classList.remove("invalid");
    };
    errorDiv.appendChild(errorP);
  });
}

function validateCollections(optionalCallback=null) {
  let alerts = document.getElementById("alert-msgs");
  alerts != null ? (alerts.innerHTML = "") : "";
  let errors = [];
  errors = validateForm();
  let isValid = errors.length == 0;
  if (isValid) {
    if (optionalCallback && typeof optionalCallback === "function") {
      optionalCallback();
    }
    return true;
  } else {
    handleValErrors(errors);
    return false;
  }
}

function simpleSearch() {
  validateCollections(optionalCallback = ()=>{
    const submitForm = document.getElementById("params-form");
    storeFormDataInSessionStorage(submitForm);
    submitForm.submit();
  });
}

function storeFormDataInSessionStorage(submitForm) {
  if(!submitForm || Array.from(submitForm.elements).length < 1) return;
    clearPageSpecificSessionStorageItems();
    Array.from(submitForm.elements).forEach(formElem => {
      if (
        (formElem.type == "checkbox" && formElem.checked) ||
        (formElem.type == "text" && formElem.value != "") ||
        (formElem.type == "number" && formElem.value != "") ||
        (formElem.type == "textarea" && formElem.value != "") ||
        (formElem.tagName === "SELECT" && formElem.value != "")
      ) {
        const revisedFormElemName = (formElem.name == "db[]") ? "db" : formElem.name;
        let previousValue = '';
        let newValue = formElem.value;
        if(revisedFormElemName === "db" ){
          previousValue = sessionStorage.getItem("querystr" + getCurrentPage() + "/" + revisedFormElemName);
          const existingValues = previousValue ? previousValue.split(",") : [];
          if(existingValues.includes(formElem.value)){
            return; // skip adding duplicate collection
          }else{
            newValue = previousValue ? previousValue + "," + formElem.value : formElem.value;
          }
        }
        sessionStorage.setItem("querystr" + getCurrentPage() + "/" + revisedFormElemName, newValue);
      }
    });
}

function clearPageSpecificSessionStorageItems() {
  const keysToRemove = Object.keys(sessionStorage).filter(key => key.startsWith("querystr" + getCurrentPage() + "/"));
  keysToRemove.forEach(key => sessionStorage.removeItem(key));
}

/**
 * Hides selected collections checkboxes (for whatever reason)
 * @param {integer} collid
 */
function hideColCheckbox(collid) {
  let colsToHide = document.querySelectorAll(
    `input[type='checkbox'][value='${collid}']`
  );
  colsToHide.forEach((col) => {
    let li = col?.closest("li");
    if (li) {
      li.style.display = "none";
    }
  });
}

function uncheckEverythingInCollections() {
  const checkUncheckAllElem = document.getElementById("all_collections");
  checkUncheckAllElem.checked = false;
  const allSpecimenCollectionsElem = document.getElementById("all_specimen_collections");
  allSpecimenCollectionsElem.checked = false;
  const allObservationCollectionsElem = document.getElementById("all_observation_collections");
  allObservationCollectionsElem.checked = false;
  const categoryCollectionsChecked = Array.from(
    document.querySelectorAll(`#search-form-colls input[id^="Specimens_"]:checked, #search-form-colls input[id^="Observations_"]:checked`)
  );
  categoryCollectionsChecked.forEach((individualCategoryChecked) => {
    individualCategoryChecked.checked = false;
  });

  const individualCollectionsChecked = Array.from(
    document.querySelectorAll(`#search-form-colls input[name="db[]"]:checked:not(#all_collections)`)
  );
  individualCollectionsChecked.forEach((individualCollectionChecked) => {
    individualCollectionChecked.checked = false;
  });
}

function checkEverythingInCollections() {
  const checkUncheckAllElem = document.getElementById("all_collections");
  checkUncheckAllElem.checked = true;
  const allSpecimenCollectionsElem = document.getElementById("all_specimen_collections");
  allSpecimenCollectionsElem.checked = false;
  const allObservationCollectionsElem = document.getElementById("all_observation_collections");
  allObservationCollectionsElem.checked = false;
  const categoryCollectionsChecked = Array.from(
    document.querySelectorAll(`#search-form-colls input[id^="Specimens_"]:not(:checked), #search-form-colls input[id^="Observations_"]:not(:checked)`)
  );
  categoryCollectionsChecked.forEach((individualCategoryChecked) => {
    individualCategoryChecked.checked = true;
  });

  const individualCollectionsChecked = Array.from(
    document.querySelectorAll(`#search-form-colls input[name="db[]"]:not(:checked):not(#all_collections)`)
  );
  individualCollectionsChecked.forEach((individualCollectionChecked) => {
    individualCollectionChecked.checked = true;
  });
}

function handleCategoryChunks(parentBoxCheckStatus, collectionType) {
  const categoryLevelFieldSets = document.querySelectorAll(`fieldset[id^="${collectionType}_"][id$="_container"]`);
  categoryLevelFieldSets.forEach((categoryFieldset) => {
    const inputElements = categoryFieldset.querySelectorAll('input');
    inputElements.forEach((inputElem) => {
      inputElem.checked = parentBoxCheckStatus;
    })
  });
}

function areSame(arr1, arr2) {
  if (arr1.length !== arr2.length) return false;
  const sorted1 = [...arr1].sort();
  const sorted2 = [...arr2].sort();
  return sorted1.every((val, index) => val === sorted2[index]);
};

function contains(bigger, smaller) {
  if (bigger.length < smaller.length) return false;
  const sorted1 = [...bigger].sort();
  const sorted2 = [...smaller].sort();
  return sorted2.every((val) => sorted1.includes(val));
};

function checkTheCollectionsThatShouldBeCheckedBasedOnConfig() {
  const targetCollectionCategoriesCheckedStatuses = JSON.parse(document.getElementById("all_collections_parent_container")?.dataset?.config || "{}")?.CATCHK;
  const queriedCollectionsCategories = targetCollectionCategoriesCheckedStatuses;
  if(queriedCollectionsCategories.length>0){
    uncheckEverythingInCollections();
    queriedCollectionsCategories.forEach((queriedCollectionCategory) => {
      const targetElems = document.querySelectorAll(`#${queriedCollectionCategory}`);
      targetElems.forEach((targetElem) => {
        targetElem.checked = true;
        const divWithChildren = document.getElementById(targetElem.id + "_inputs");
        const childCheckboxes = divWithChildren.querySelectorAll('input[type="checkbox"]');
        childCheckboxes.forEach((childCheckbox) => {
          childCheckbox.checked = true;
        });
      });
    });
  } else{
    checkEverythingInCollections();
  }
  updateCategoryCheckboxes();
}


function checkTheCollectionsThatShouldBeChecked(queriedCollections) {
  queriedCollections.forEach((queriedCollection) => {
    let targetElem = document.querySelector(`[id$="_${queriedCollection}"]:not([id^="Specimens_"]):not([id^="Observations_"]):not([id="m_all"])`);
    if (!targetElem) {
      if (queriedCollection.includes("all") && !queriedCollection.includes("allspec") && !queriedCollection.includes("allobs")) {
        targetElem = document.getElementById("all_collections");
        if (targetElem) {
          targetElem.checked = true;
          handleCategoryChunks(true, "Specimens");
          handleCategoryChunks(true, "Observations");
        }
        return;
      } else if (queriedCollection.includes("allspec")) {
        targetElem = document.getElementById("all_specimen_collections");
        if (targetElem) {
          targetElem.checked = true;
          handleCategoryChunks(true, "Specimens");
        }
        return;
      } else if (queriedCollection.includes("allobs")) {
        targetElem = document.getElementById("all_observation_collections");
        if (targetElem) {
          targetElem.checked = true;
          handleCategoryChunks(true, "Observations");
        }
        return;
      } else {
        // Do nothing
      }
    }
    else {
      targetElem.checked = true; 
    }
  });
  updateCategoryCheckboxes();
}

function generateTargetInputElementsForCategory(callbackFn) {
  const categoryFieldsets = document.querySelectorAll(
    'fieldset[id^="Specimens_"][id$="_container"], fieldset[id^="Observations_"][id$="_container"]'
  );
  categoryFieldsets.forEach((categoryFieldset) => {
    const categoryFieldsetId = categoryFieldset.id;
    const categoryPattern = categoryFieldsetId.match(/(.*)_container/)?.[1];
    const inputContainer = document.getElementById(categoryPattern+"_inputs");
    const targetInputElems = inputContainer.querySelectorAll('input');
    if (targetInputElems.length > 0) {
      callbackFn(categoryPattern, targetInputElems);
    }
  });
}

function updateCategoryCheckboxes() {
  generateTargetInputElementsForCategory((categoryPattern, targetInputElems) => {
    const checkedChildren = Array.from(targetInputElems).filter(checkbox => checkbox.checked);
      if (checkedChildren.length === targetInputElems.length) {
        const targetCategoryInput = document.getElementById(categoryPattern);
        targetCategoryInput.checked = true;
      }
  });
}

function closeAllCategories() {
  const allCategoryInputElements = Array.from(document.querySelectorAll('input[id^="Specimens_"], input[id^="Observations_"]'));
  const uniqueCategoryIds = allCategoryInputElements.reduce((acc, inputElem) => {
    const parts = inputElem?.id?.split('_');
    const categoryId = parts && parts.length > 1 ? parts[1] : null;
    if (categoryId && !acc.includes(categoryId)) {
      acc.push(categoryId);
    }
    return acc;
  }, []);
  uniqueCategoryIds.forEach((categoryId) => {
    if(!categoryId) return;
    const specimenCategoryPattern = "Specimens_" + categoryId;
    const specimenInputsForCategory = document.getElementById(specimenCategoryPattern + '_inputs');
    if (specimenInputsForCategory?.style?.display !== 'none') {
      toggleCategory(specimenCategoryPattern);
    }
    const observationCategoryPattern = "Observations_" + categoryId;
    const observationInputsForCategory = document.getElementById(observationCategoryPattern + '_inputs');
    if (observationInputsForCategory?.style?.display !== 'none') {
      toggleCategory(observationCategoryPattern);
    }
  });
}


function expandCategoriesBasedOnConfig() {
  const targetCategoriesToExpandFromConfig = JSON.parse(document.getElementById("all_collections_parent_container")?.dataset?.config || "{}")?.CATEXPND;
  targetCategoriesToExpandFromConfig?.forEach(targetCategoryToExpand => {
    const specimenCategoryPattern = targetCategoryToExpand;
    const specimenInputsForCategory = document.getElementById(specimenCategoryPattern + '_inputs');
    if (specimenInputsForCategory?.style?.display === 'none') {
      toggleCategory(specimenCategoryPattern);
    }
    const observationCategoryPattern = targetCategoryToExpand;
    const observationInputsForCategory = document.getElementById(observationCategoryPattern + '_inputs');
    if (observationInputsForCategory?.style?.display === 'none') {
      toggleCategory(observationCategoryPattern);
    }
  });
}

function expandCategoriesWithSomeCheckedChildren() {
  generateTargetInputElementsForCategory((categoryPattern, targetInputElems) => {
    const container = document.getElementById(categoryPattern + '_inputs');
    const checkedChildren = Array.from(targetInputElems).filter(checkbox => checkbox.checked);
    if (checkedChildren.length === targetInputElems.length && container.style.display === 'flex' && !container.classList.contains('explicitly-collapsed')) {
      toggleCategory(categoryPattern);
    }
    else if (checkedChildren.length > 0 && checkedChildren.length < targetInputElems.length && container.style.display === 'none' && !container.classList.contains('explicitly-collapsed')) {
      toggleCategory(categoryPattern);
    } else if(checkedChildren.length === 0 && container.style.display === 'flex' && !container.classList.contains('explicitly-collapsed')) {
      toggleCategory(categoryPattern);
    }
  });
}

  function closeCollectionsDialog() {
    const submitForm = document.getElementById("params-form");
    storeFormDataInSessionStorage(submitForm);
    const dialog = document.getElementById('collections_dialog');
    if (dialog) {
      dialog.close();
    }
  }

  function openCollectionsDialog() {
    const dialog = document.getElementById('collections_dialog');
    dialog.showModal();
    setSessionQueryStr();

    const form = document.getElementById('params-form');
    if (form) {
      setSearchForm(form);
      form.addEventListener("submit", function(event) {
        event.preventDefault();
        simpleSearch();
      });
      document.getElementById("reset-btn").addEventListener("click", function (event) {
        document.getElementById("params-form").reset();
        clearPageSpecificSessionStorageItems();
        checkTheCollectionsThatShouldBeCheckedBasedOnConfig();
        closeAllCategories();
        expandCategoriesBasedOnConfig();
        updateChip(event, isInitialConfig=true);
      });
    }
  }

function setSearchForm(frm) {
  if (!frm) return;
  const sessionStorageKeys = Object.keys(sessionStorage);
  const hasSessionInfo = sessionStorageKeys.some(key => {
    const currentVal = sessionStorage.getItem(key);
    return key.startsWith("querystr" + getCurrentPage()) && key !== ("querystr" + getCurrentPage() + "/" + "accordionIds") && currentVal !== "null"
  });
  if(!hasSessionInfo){
    uncheckEverythingInCollections();
    checkTheCollectionsThatShouldBeCheckedBasedOnConfig();
    closeAllCategories();
    expandCategoriesBasedOnConfig();
    updateChip(null, isInitialConfig=true);
  }
  else {
    const urlVariablesFromSessionStorage = concatenateUrlVariablesFromSessionStorage();
    const urlVar = parseUrlVariables(urlVariablesFromSessionStorage.replaceAll('&quot;', '"'));
    if (
      typeof urlVar.usethes !== "undefined" &&
      (urlVar.usethes == "" || urlVar.usethes == "0")
    ) {
      frm.usethes.checked = false;
    }
    if (
      typeof urlVar["usethes-associations"] !== "undefined" &&
      (urlVar["usethes-associations"] == "" ||
        urlVar["usethes-associations"] == "0")
    ) {
      frm["usethes-associations"].checked = false;
    }
    if (urlVar.taxontype) {
      if (frm?.taxontype) {
        frm.taxontype.value = urlVar.taxontype;
      }
    }
    if (urlVar.taxa) {
      if (frm?.taxa) {
        frm.taxa.value = urlVar.taxa;
      }
    }

    if (urlVar["associated-taxa"]) {
      if (frm["associated-taxa"]) {
        frm["associated-taxa"].value = urlVar["associated-taxa"];
      }
    }
    if (urlVar["association-type"]) {
      if (frm["association-type"]) {
        frm["association-type"].value = urlVar["association-type"];
      }
    }

    if (urlVar["associated-taxon-type"]) {
      if (frm["taxontype-association"]) {
        frm["taxontype-association"].value = urlVar["associated-taxon-type"];
      }
    }

    if (urlVar["earlyInterval"]) {
      if (frm["earlyInterval"]) {
        frm["earlyInterval"].value = urlVar["earlyInterval"];
      }
    }
    if (urlVar["lateInterval"]) {
      if (frm["lateInterval"]) {
        frm["lateInterval"].value = urlVar["lateInterval"];
      }
    }
    if (urlVar["lithogroup"]) {
      if (frm["lithogroup"]) {
        frm["lithogroup"].value = urlVar["lithogroup"];
      }
    }
    if (urlVar["formation"]) {
      if (frm["formation"]) {
        frm["formation"].value = urlVar["formation"];
      }
    }
    if (urlVar["member"]) {
      if (frm["member"]) {
        frm["member"].value = urlVar["member"];
      }
    }
    if (urlVar["bed"]) {
      if (frm["bed"]) {
        frm["bed"].value = urlVar["bed"];
      }
    }
    if (urlVar["polygons"]) {
      if (frm["polygons"]) {
        frm["polygons"].value = urlVar["polygons"];
      }
    }

    if (urlVar.country) {
      countryStr = urlVar.country;
      countryArr = countryStr.split(";");
      if (countryArr.indexOf("USA") > -1 || countryArr.indexOf("usa") > -1)
        countryStr = countryArr[0];
      //if(countryStr.indexOf('United States') > -1) countryStr = 'United States';
      if (frm?.country) {
        frm.country.value = countryStr;
      }
    }
    if (urlVar.state) {
      frm.state.value = urlVar.state;
    }
    if (urlVar.county) {
      frm.county.value = urlVar.county;
    }
    if (urlVar.local) {
      frm.local.value = urlVar.local;
    }
    if (urlVar.elevlow) {
      frm.elevlow.value = urlVar.elevlow;
    }
    if (urlVar.elevhigh) {
      frm.elevhigh.value = urlVar.elevhigh;
    }
    if (urlVar.llbound) {
      const coordArr = urlVar.llbound.split(";");
      frm.upperlat.value = Math.abs(parseFloat(coordArr[0]));
      frm.upperlat_NS.value = parseFloat(coordArr[0]) > 0 ? "N" : "S";

      frm.bottomlat.value = Math.abs(parseFloat(coordArr[1]));
      frm.bottomlat_NS.value = parseFloat(coordArr[1]) > 0 ? "N" : "S";

      frm.leftlong.value = Math.abs(parseFloat(coordArr[2]));
      frm.leftlong_EW.value = parseFloat(coordArr[2]) > 0 ? "E" : "W";

      frm.rightlong.value = Math.abs(parseFloat(coordArr[3]));
      frm.rightlong_EW.value = parseFloat(coordArr[3]) > 0 ? "E" : "W";
    }
    if (urlVar.footprintGeoJson) {
      frm.footprintwkt.value = urlVar.footprintGeoJson;
    }
    if (urlVar.llpoint) {
      const coordArr = urlVar.llpoint.split(";");
      frm.pointlat.value = Math.abs(parseFloat(coordArr[0]));
      frm.pointlat_NS.value = parseFloat(coordArr[0]) > 0 ? "N" : "S";

      frm.pointlong.value = Math.abs(parseFloat(coordArr[1]));
      frm.pointlong_EW.value = parseFloat(coordArr[1]) > 0 ? "E" : "W";

      frm.radius.value = Math.abs(parseFloat(coordArr[2]));
      if (coordArr[3] === "mi") frm.radiusunits.value = "mi";
      else if (coordArr[3] === "km") frm.radiusunits.value = "km";
    }
    if (urlVar.collector) {
      frm.collector.value = urlVar.collector;
    }
    if (urlVar.collnum) {
      frm.collnum.value = urlVar.collnum;
    }
    if (urlVar.eventdate1) {
      frm.eventdate1.value = urlVar.eventdate1;
    }
    if (urlVar.eventdate2) {
      frm.eventdate2.value = urlVar.eventdate2;
    }
    if (urlVar.catnum) {
      frm.catnum.value = urlVar.catnum;
    }
    //if(!urlVar.othercatnum){frm.includeothercatnum.checked = false;}
    if (urlVar.eventdate2) {
      frm.eventdate2.value = urlVar.eventdate2;
    }
    if (urlVar.materialsampletype) {
      frm.materialsampletype.value = urlVar.materialsampletype;
    }
    if (typeof urlVar.typestatus !== "undefined") {
      frm.typestatus.checked = true;
    }
    if (typeof urlVar.hasimages !== "undefined") {
      frm.hasimages.checked = true;
    }
    if (typeof urlVar.hasgenetic !== "undefined") {
      frm.hasgenetic.checked = true;
    }
    if (typeof urlVar.hascoords !== "undefined") {
      frm.hascoords.checked = true;
    }
    if (typeof urlVar.includecult !== "undefined") {
      if (frm?.includecult) {
        frm.includecult.checked = true;
      }
    }
    if (urlVar.db) {
      const queriedCollections = urlVar.db.split(",");
      const updatedQueriedCollections = updateQueryListWithTypeCollections(queriedCollections);
      if (updatedQueriedCollections.length > 0) {
        uncheckEverythingInCollections();
        checkTheCollectionsThatShouldBeChecked(updatedQueriedCollections);
        closeAllCategories();
        expandCategoriesWithSomeCheckedChildren();
      }
    }
    for (const i in urlVar) {
      if (`${i}`.indexOf("traitid-") == 0) {
        const traitInput = document.getElementById("traitstateid-" + urlVar[i]);
        if (traitInput.type == "checkbox" || traitInput.type == "radio") {
          traitInput.checked = true;
        }
        // if(traitInput.type == 'select') { traitInput.value = urlVar[i]; }; // Must improve this to deal with multiple possible selections
      }
    }
    updateChip();
  } 
}

function updateQueryListWithTypeCollections(queryList){
  let newQueryList = queryList;
  const allPossibleSpecimenCollections = calculateAllPossibleCollectionsInScope("specimens_collections");
  const didAllSpecimenCollectionGetSelected = contains(newQueryList,allPossibleSpecimenCollections);
  if(didAllSpecimenCollectionGetSelected) newQueryList = [...newQueryList, "allspec"];

  const allPossibleObservationCollections = calculateAllPossibleCollectionsInScope("observations_collections");
  const didAllObservationCollectionGetSelected = contains(newQueryList, allPossibleObservationCollections);
  if(didAllObservationCollectionGetSelected) newQueryList = [...newQueryList, "allobs"];

  const allPossibleCollections = calculateAllPossibleCollectionsInScope("search-form-colls");
  const didAllCollectionGetSelected = contains(newQueryList, allPossibleCollections);
  if(didAllCollectionGetSelected) newQueryList = ["all"];
  return newQueryList;
}

function calculateAllPossibleCollectionsInScope(scope, modifier = '', shouldSplit=true) {
  return Array.from(document.querySelectorAll(`#${scope} input[name="db[]"]:not(#all_collections)${modifier}`)).map(input => {
    if(shouldSplit) {
      return input.id.split("_")[1];
    } else{
      return input.id;
    }
  });
}

function parseUrlVariables(varStr) {
  const result = {};
  varStr.split("&").forEach(function (part) {
    if (!part) return;
    part = part.split("+").join(" ");
    const eq = part.indexOf("=");
    const key = eq > -1 ? part.substr(0, eq) : part;
    const val = eq > -1 ? decodeURIComponent(part.substr(eq + 1)) : "";
    result[key] = val;
  });
  return result;
}

function concatenateUrlVariablesFromSessionStorage() {
  let returnVal = '';
  const sessionStorageKeys = Object.keys(sessionStorage);
  const relevantKeys = sessionStorageKeys.filter(key => key.startsWith("querystr" + getCurrentPage()) && key.value !== "null");
  relevantKeys.forEach((relevantKey) => {
    const justFormFieldName = relevantKey.replace("querystr" + getCurrentPage() + "/", "");
    if(justFormFieldName){
      const relevantVal = sessionStorage.getItem(relevantKey);
      returnVal += justFormFieldName + "=" + encodeURIComponent(relevantVal) + "&"; // @TODO encodeURIComponent may not be necessary here
    }
  });
  return returnVal.slice(0, -1);
}

function toggleAccordionsFromSessionStorage(accordionIds) {
  const accordions = document.querySelectorAll(
    'input[class="accordion-selector"]'
  );
  accordions.forEach((accordion) => {
    if(accordion.id !== "taxonomy") accordion.checked = false;
    if(accordion.id === "taxonomy" && sessionStorage.getItem("querystr" + getCurrentPage() + "/" + "taxonomyAccordionClosed")) accordion.checked = false;
  });
  accordions.forEach((accordion) => {
    const currentId = accordion.getAttribute("id");
    if (accordionIds.includes(currentId)) {
      accordion.checked = true;
    }
  });
}

function toggleCharacterGroup(charID) {
  const plus = document.getElementById('plus-' + charID);
  const minus = document.getElementById('minus-' + charID);
  const block = document.getElementById('char-block-' + charID);

  if (!plus || !minus || !block) return;

  const isVisible = block.style.display === 'block';

  block.style.display = isVisible ? 'none' : 'block';
  plus.style.display = isVisible ? 'inline' : 'none';
  minus.style.display = isVisible ? 'none' : 'inline';
}

//////////////////////////////////////////////////////////////////////////

/**
 * EVENT LISTENERS/INITIALIZERS
 */

// When checking any 'all-selector', toggle children checkboxes
$(".all-selector").click(toggleAllSelector);
formColls?.addEventListener("click", autoToggleSelector, false);
formColls?.addEventListener("change", autoToggleSelector, false);
formSites?.addEventListener("click", autoToggleSelector, false);
searchFormColls?.addEventListener("click", autoToggleSelector, false);
searchFormColls?.addEventListener("change", autoToggleSelector, false);

function initializeFormInputs() {
  if (!formInputs || formInputs.length === 0) {
    formInputs = document.querySelectorAll(".content input");
  }
  formInputs.forEach((formInput) => {
    formInput.addEventListener("change", (e)=>{
      const isCollectionRelated = e?.currentTarget?.name === "db[]" || e?.currentTarget?.name?.startsWith("Specimens_") || e?.currentTarget?.name?.startsWith("Observations_") || e?.currentTarget?.id === "all_collections" || e?.currentTarget?.id === "all_specimen_collections" || e?.currentTarget?.id === "all_observation_collections";
      if(isCollectionRelated) {
        const queriedCollections = Array.from(document.querySelectorAll(`#search-form-colls input[name="db[]"]:checked:not(#all_collections)`)).filter(elem=>elem.id.split("_")[1]!==undefined).map(elem=>elem.id.split("_")[1]);
        const updatedQueriedCollections = updateQueryListWithTypeCollections(queriedCollections);
        uncheckEverythingInCollections();
        checkTheCollectionsThatShouldBeChecked(updatedQueriedCollections);
        closeAllCategories();
        expandCategoriesWithSomeCheckedChildren();
      }
      updateChip(e);
      setSessionQueryStr();
    });
  });
}

const selectionElements = document.querySelectorAll(".content select");
selectionElements.forEach((selectionElement) => {
  selectionElement.addEventListener("change", updateChip);
});

// on default (on document load): All Neon Collections, All Domains & Sites, Include other IDs, All Domains & Sites
// document.addEventListener("DOMContentLoaded", updateChip); // @TODO I don't think that this is necessary even in NEON anymore?

// Binds expansion function to plus and minus icons in selectors, uses jQuery
$(".expansion-icon").click(function () {
  if ($(this).siblings("ul").hasClass("collapsed")) {
    $(this)
      .html("indeterminate_check_box")
      .siblings("ul")
      .removeClass("collapsed");
  } else {
    $(this).html("add_box").siblings("ul").addClass("collapsed");
  }
});
// Hides MOSC-BU checkboxes
hideColCheckbox(58); // @TODO is this NEON specific? Should I remove?

function setSessionStorageForAccordions() {
  const accordions = document.querySelectorAll(
    'input[class="accordion-selector"]'
  );
  accordions.forEach((accordion) => {
    accordion.addEventListener("click", (event) => {
      const currentAccordionIds = sessionStorage.getItem("querystr" + currentPage + "/" + "accordionIds") ?.split(",") || [];
      const currentId = event.target.id;
      if (currentAccordionIds.includes(currentId)) {
        const targetIdx = currentAccordionIds.indexOf(currentId);
        currentAccordionIds.splice(targetIdx, 1);
        if(currentId==="taxonomy") {
          sessionStorage.setItem("querystr" + currentPage + "/" + "taxonomyAccordionClosed", true);
        }
      } else {
        currentAccordionIds.push(currentId);
        if(currentId==="taxonomy") sessionStorage.setItem("querystr" + currentPage + "/" + "taxonomyAccordionClosed", false);
      }
      sessionStorage.setItem("querystr" + currentPage + "/" + "accordionIds", currentAccordionIds);
    });
  });
}

document.addEventListener('DOMContentLoaded', function() {
  initializeFormInputs();
  const form = document.getElementById('params-form');
  if (form) {
    setSearchForm(form);
  }
  setSessionStorageForAccordions(); // @TODO I'm not sure whether this is necessary yet
  updateChip();

});