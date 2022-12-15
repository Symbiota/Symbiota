/**
 *  Symbiota Label Builder Functions
 *  Author: Laura Rocha Prado
 *  Version: 2021
 */

/** Creating Page Elements/Controls
 ******************************
 */

// Defines formattable items in label (also used to create preview elements)
const fieldProps = [
  {
    block: 'labelBlock',
    name: 'Occurrence ID',
    id: 'occid',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Catalog Number',
    id: 'catalognumber',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Other Catalog Numbers',
    id: 'othercatalognumbers',
    group: 'specimen',
  },
  { block: 'labelBlock', name: 'Family', id: 'family', group: 'taxon' },
  {
    block: 'labelBlock',
    name: 'Scientific Name',
    id: 'scientificname',
    group: 'taxon',
  },
  {
    block: 'labelBlock',
    name: 'Binomial Name',
    id: 'speciesname',
    group: 'taxon',
  },
  { block: 'labelBlock', name: 'Taxon Rank', id: 'taxonrank', group: 'taxon' },
  {
    block: 'labelBlock',
    name: 'Infraspecific Epithet',
    id: 'infraspecificepithet',
    group: 'taxon',
  },
  {
    block: 'labelBlock',
    name: 'Scientific Name Authorship',
    id: 'scientificnameauthorship',
    group: 'taxon',
  },
  {
    block: 'labelBlock',
    name: 'Parent Author',
    id: 'parentauthor',
    group: 'taxon',
  },
  {
    block: 'labelBlock',
    name: 'Identified By',
    id: 'identifiedby',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Date Identified',
    id: 'dateidentified',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Identification References',
    id: 'identificationreferences',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Identification Remarks',
    id: 'identificationremarks',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Taxon Remarks',
    id: 'taxonremarks',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Identification Qualifier',
    id: 'identificationqualifier',
    group: 'determination',
  },
  {
    block: 'labelBlock',
    name: 'Type Status',
    id: 'typestatus',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Recorded By',
    id: 'recordedby',
    group: 'event',
  },
  {
    block: 'labelBlock',
    name: 'Record Number',
    id: 'recordnumber',
    group: 'event',
  },
  {
    block: 'labelBlock',
    name: 'Associated Collectors',
    id: 'associatedcollectors',
    group: 'event',
  },
  { block: 'labelBlock', name: 'Event Date', id: 'eventdate', group: 'event' },
  { block: 'labelBlock', name: 'Year', id: 'year', group: 'event' },
  { block: 'labelBlock', name: 'Month', id: 'month', group: 'event' },
  { block: 'labelBlock', name: 'Month Name', id: 'monthname', group: 'event' },
  { block: 'labelBlock', name: 'Day', id: 'day', group: 'event' },
  {
    block: 'labelBlock',
    name: 'Verbatim Event Date',
    id: 'verbatimeventdate',
    group: 'event',
  },
  { block: 'labelBlock', name: 'Habitat', id: 'habitat', group: 'event' },
  { block: 'labelBlock', name: 'Substrate', id: 'substrate', group: 'event' },
  {
    block: 'labelBlock',
    name: 'Occurrence Remarks',
    id: 'occurrenceremarks',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Associated Taxa',
    id: 'associatedtaxa',
    group: 'taxon',
  },
  // { block: 'labelBlock', name: 'Dynamic Properties', id: 'dynamicProperties' },
  {
    block: 'labelBlock',
    name: 'Verbatim Attributes',
    id: 'verbatimattributes',
    group: 'event',
  },
  { block: 'labelBlock', name: 'Behavior', id: 'behavior', group: 'specimen' },
  {
    block: 'labelBlock',
    name: 'Reproductive Condition',
    id: 'reproductivecondition',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Cultivation Status',
    id: 'cultivationstatus',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Establishment Means',
    id: 'establishmentmeans',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Life Stage',
    id: 'lifeStage',
    group: 'specimen',
  },
  { block: 'labelBlock', name: 'Sex', id: 'sex', group: 'specimen' },
  {
    block: 'labelBlock',
    name: 'Individual Count',
    id: 'individualcount',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Sampling Protocol',
    id: 'samplingprotocol',
    group: 'specimen',
  },
  {
    block: 'labelBlock',
    name: 'Preparations',
    id: 'preparations',
    group: 'specimen',
  },
  { block: 'labelBlock', name: 'Country', id: 'country', group: 'locality' },
  {
    block: 'labelBlock',
    name: 'State/Province',
    id: 'stateprovince',
    group: 'locality',
  },
  { block: 'labelBlock', name: 'County', id: 'county', group: 'locality' },
  {
    block: 'labelBlock',
    name: 'Municipality',
    id: 'municipality',
    group: 'locality',
  },
  { block: 'labelBlock', name: 'Locality', id: 'locality', group: 'locality' },
  {
    block: 'labelBlock',
    name: 'Decimal Latitude',
    id: 'decimallatitude',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Decimal Longitude',
    id: 'decimallongitude',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Geodetic Datum',
    id: 'geodeticdatum',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Coordinate Uncertainty In Meters',
    id: 'coordinateuncertaintyinmeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Verbatim Coordinates',
    id: 'verbatimcoordinates',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Elevation In Meters',
    id: 'elevationinmeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Verbatim Elevation',
    id: 'verbatimelevation',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Minimum Depth In Meters',
    id: 'minimumdepthinmeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Maximum Depth In Meters',
    id: 'maximumdepthinmeters',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Verbatim Depth',
    id: 'verbatimdepth',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Disposition',
    id: 'disposition',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Storage Location',
    id: 'storagelocation',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Duplicate Quantity',
    id: 'duplicatequantity',
    group: 'locality',
  },
  {
    block: 'labelBlock',
    name: 'Date Last Modified',
    id: 'datelastmodified',
    group: 'event',
  },
];

// Defines formatting buttons
const formatsArr = [
  { group: 'field', func: 'font-bold', icon: 'format_bold', title: 'Bold' },
  { group: 'field', func: 'italic', icon: 'format_italic', title: 'Italic' },
  {
    group: 'field',
    func: 'underline',
    icon: 'format_underlined',
    title: 'Underline',
  },
  {
    group: 'field',
    func: 'uppercase',
    icon: 'format_size',
    title: 'Uppercase',
  },
  {
    group: 'field-block',
    func: 'bar',
    icon: '',
    name: 'Bar Below',
    title: 'Add bar below line',
  },
  {
    group: 'field-block',
    func: 'bar-top',
    icon: '',
    name: 'Bar Above',
    title: 'Add bar above line',
  },
];

// Defines dropdown style groups
const dropdownsArr = [
  {
    id: 'text',
    name: 'font-size',
    group: 'field',
    options: [
      { value: '', text: 'Font Size' },
      { value: 'text-xs', text: 'X-Small' },
      { value: 'text-sm', text: 'Small' },
      { value: 'text-base', text: 'Normal' },
      { value: 'text-lg', text: 'Large' },
      { value: 'text-xl', text: 'X-Large' },
      { value: 'text-2xl', text: '2X-Large' },
      { value: 'text-3xl', text: '3X-Large' },
      { value: 'text-4xl', text: '4X-Large' },
      { value: 'text-5xl', text: '5X-Large' },
      { value: 'text-6xl', text: '6X-Large' },
    ],
  },
  {
    id: 'float',
    name: 'float',
    group: 'field',
    options: [
      { value: '', text: 'Position in Line' },
      // { value: 'float-none', text: 'None' },
      { value: 'float-left', text: 'Left' },
      { value: 'float-right', text: 'Right' },
    ],
  },
  {
    id: 'font-family',
    name: 'font-family',
    group: 'field',
    options: [
      { value: '', text: 'Font Family' },
      { value: 'font-family-arial', text: 'Arial (sans-serif)' },
      { value: 'font-family-verdana', text: 'Verdana (sans-serif)' },
      { value: 'font-family-helvetica', text: 'Helvetica (sans-serif)' },
      { value: 'font-family-tahoma', text: 'Tahoma (sans-serif)' },
      { value: 'font-family-trebuchet', text: 'Trebuchet (sans-serif)' },
      { value: 'font-family-times', text: 'Times New Roman (serif)' },
      { value: 'font-family-georgia', text: 'Georgia (serif)' },
      { value: 'font-family-garamond', text: 'Garamond (serif)' },
      { value: 'font-family-courier', text: 'Courier New (monospace)' },
      { value: 'font-family-brush', text: 'Brush Script MT (cursive)' },
    ],
  },
  {
    id: 'text-align',
    name: 'text-align',
    group: 'field-block',
    options: [
      { value: '', text: 'Text Align' },
      { value: 'text-align-center', text: 'Center' },
      { value: 'text-align-right', text: 'Right' },
      // { value: 'text-align-justify', text: 'Justify' },
      { value: 'text-align-left', text: 'Left' },
    ],
  },
  {
    id: 'mt',
    name: 'spacing-top',
    group: 'field-block',
    options: [
      { value: '', text: 'Line Spacing Top' },
      { value: 'mt-0', text: '0' },
      { value: 'mt-1', text: '1' },
      { value: 'mt-2', text: '2' },
      { value: 'mt-3', text: '3' },
      { value: 'mt-4', text: '4' },
      { value: 'mt-5', text: '5' },
      { value: 'mt-6', text: '6' },
      { value: 'mt-8', text: '8' },
      { value: 'mt-10', text: '10' },
      { value: 'mt-12', text: '12' },
    ],
  },
  {
    id: 'mb',
    name: 'spacing-bottom',
    group: 'field-block',
    options: [
      { value: '', text: 'Line Spacing Bottom' },
      { value: 'mb-0', text: '0' },
      { value: 'mb-1', text: '1' },
      { value: 'mb-2', text: '2' },
      { value: 'mb-3', text: '3' },
      { value: 'mb-4', text: '4' },
      { value: 'mb-5', text: '5' },
      { value: 'mb-6', text: '6' },
      { value: 'mb-8', text: '8' },
      { value: 'mb-10', text: '10' },
      { value: 'mb-12', text: '12' },
    ],
  },
  {
    id: 'ml',
    name: 'spacing-left',
    group: 'field-block',
    options: [
      { value: '', text: 'Line Spacing Left' },
      { value: 'ml-0', text: '0' },
      { value: 'ml-1', text: '1' },
      { value: 'ml-2', text: '2' },
      { value: 'ml-3', text: '3' },
      { value: 'ml-4', text: '4' },
      { value: 'ml-5', text: '5' },
      { value: 'ml-6', text: '6' },
      { value: 'ml-8', text: '8' },
      { value: 'ml-10', text: '10' },
      { value: 'ml-12', text: '12' },
    ],
  },
  // {
  //   id: 'mr',
  //   name: 'spacing-right',
  //   group: 'field-block',
  //   options: [
  //     { value: '', text: 'Line Spacing Right' },
  //     { value: 'mr-0', text: '0' },
  //     { value: 'mr-1', text: '1' },
  //     { value: 'mr-2', text: '2' },
  //     { value: 'mr-3', text: '3' },
  //     { value: 'mr-4', text: '4' },
  //     { value: 'mr-5', text: '5' },
  //     { value: 'mr-6', text: '6' },
  //     { value: 'mr-8', text: '8' },
  //     { value: 'mr-10', text: '10' },
  //     { value: 'mr-12', text: '12' },
  //   ],
  // },
];
const dummy = document.getElementById('dummy');
const fieldDiv = document.getElementById('fields');
const fieldListDiv = document.getElementById('fields-list');
const controlDiv = document.getElementById('controls');
const fieldsFilter = document.getElementById('fields-filter');
const labelMid = document.getElementById('label-middle');

// Initially creates all fields
createFields(fieldProps, fieldListDiv);

// Creates formatting (button) controls in page
formatsArr.forEach((format) => {
  let targetDiv = document.getElementById(`${format.group}-options`);
  let btn = document.createElement('button');
  btn.classList.add('control');
  btn.disabled = true;
  btn.dataset.func = format.func;
  btn.dataset.group = format.group;
  btn.title = format.title;
  if (format.icon !== '') {
    let icon = document.createElement('span');
    icon.classList.add('material-icons');
    icon.innerText = format.icon;
    btn.appendChild(icon);
  } else {
    btn.innerText = format.name;
  }
  targetDiv.appendChild(btn);
});

// Creates formatting (dropdown) controls in page
dropdownsArr.forEach((dropObj) => {
  let targetDiv = document.getElementById(`${dropObj.group}-options`);
  let lbl = document.createElement('label');
  lbl.htmlFor = dropObj.id;
  lbl.innerText = dropObj.options[0].text + ':';
  lbl.style = 'display: block;';
  let slct = document.createElement('select');
  slct.dataset.group = dropObj.group;
  slct.classList.add('control');
  slct.name = dropObj.name;
  slct.id = dropObj.id;
  slct.disabled = true;
  dropObj.options.forEach((choice) => {
    let opt = document.createElement('option');
    opt.value = choice.value;
    opt.innerText = choice.text;
    slct.appendChild(opt);
  });
  targetDiv.appendChild(lbl);
  targetDiv.appendChild(slct);
});

// Grabs elements
const containers = document.querySelectorAll('.container');
const draggables = document.querySelectorAll('.draggable');
const build = document.getElementById('build-label');
const preview = document.getElementById('preview-label');
const controls = document.querySelectorAll('.control');
const inputs = document.querySelectorAll('input');

// JSON TRANSLATION
function translateJson(source) {
  // Source has to be "simple", as in: following structure output by generateJson()
  let srcLines = source[0].divBlock.blocks;
  srcLines
    ? ''
    : (preview.innerText =
        '<h1>ERROR</h1><p>Your label format is not translatable at this time. Please adjust your JSON definition and try again, or create a new format from scratch using this visual builder.</p>');
  let lineCount = srcLines.length;
  // Create additional blocks in label builder
  for (i = 0; i < lineCount - 1; i++) {
    addLine();
  }
  let lbBlocks = labelMid.querySelectorAll('.field-block');
  // Add field(s) inside line[i]
  srcLines.forEach((srcLine, i) => {
    // Style fieldblocks
    let lbBlock = lbBlocks[i];
    srcLine.delimiter !== undefined
      ? (lbBlock.dataset.delimiter = srcLine.delimiter)
      : '';
    srcLine.className !== undefined
      ? (lbBlock.className = lbBlock.className + ' ' + srcLine.className)
      : '';
    // Array of fields based on fieldProps filtered by current fields in json format
    let fieldsArr = srcLine.fieldBlock;
    if (fieldsArr !== undefined) {
      let propsArr = [];
      fieldsArr.forEach(({ field, className }) => {
        let props = fieldProps.find((obj) => obj.id === field);
        propsArr.push(props);
      });
      createFields(propsArr, lbBlocks[i]);
    } else {
      preview.innerText = 'Error';
    }
    // Select created item in label build (have to limit to one line at a time)
    let createdLis = lbBlocks[i].querySelectorAll('.draggable');
    // Add classes from json to item
    createdLis.forEach((li, j) => {
      let srcFieldsArr = srcLines[i].fieldBlock;
      let srcPropsArr = srcFieldsArr[j];
      let fieldId = srcPropsArr.field;
      let classes = srcPropsArr.className;
      let prefix = srcPropsArr.prefix;
      let suffix = srcPropsArr.suffix;
      if (li.id === fieldId) {
        classes !== undefined ? (li.className = 'draggable ' + classes) : '';
        prefix !== undefined ? (li.dataset.prefix = prefix) : '';
        suffix !== undefined ? (li.dataset.suffix = suffix) : '';
      }
    });
  });
  refreshAvailFields();
  refreshPreview();
  console.log('inside translator');
}
// Initially sets state of lines
refreshLineState();

/** Methods
 ******************************
 */

/**
 * Displays user instructions overlay
 */
const overlay = document.getElementById('instructions');
function openOverlay() {
  overlay.classList.remove('hidden');
}

/**
 * Hides user instructions overlay
 */
function closeOverlay() {
  overlay.classList.add('hidden');
}

/**
 * Filters array based on desired property
 * @param {Array} arr Array to be filtered
 * @param {Object} criteria Pair or pairs of property and criterion
 */
function filterObject(arr, criteria) {
  return arr.filter(function (obj) {
    return Object.keys(criteria).every(function (c) {
      return obj[c] == criteria[c];
    });
  });
}

/**
 * Removes object from array based on desired property
 * @param {Array} arr Array to be cleaned
 * @param {Object} criteria Pair or pairs of property and criterion
 */
function removeObject(arr, criteria) {
  return arr.filter(function (obj) {
    return Object.keys(criteria).every(function (c) {
      return obj[c] !== criteria[c];
    });
  });
}

/**
 * Gets list of fields currently available to drag to label build area
 */
function getCurrFields() {
  let currFields = fieldProps;
  let usedFields = document.querySelectorAll('#label-middle .draggable');
  if (usedFields.length > 0) {
    usedFields.forEach((usedField) => {
      currFields = removeObject(currFields, { id: usedField.id });
    });
  }
  return currFields;
}

/**
 * Filters available fields on select option
 */
function filterFields(value) {
  // let value = this.value;
  let filteredFields = '';
  value === 'all'
    ? (filteredFields = getCurrFields())
    : (filteredFields = filterObject(getCurrFields(), { group: value }));
  fieldListDiv.innerHTML = '';
  createFields(filteredFields, fieldListDiv);
}

function refreshAvailFields() {
  let available = getCurrFields();
  fieldListDiv.innerHTML = '';
  let selectedFilter = fieldsFilter.value;
  selectedFilter != 'all'
    ? filterFields(selectedFilter)
    : createFields(available, fieldListDiv);
}

/**
 * Creates draggable elements
 * @param {Arr} arr Array with list of currently available fields
 */
function createFields(arr, target) {
  arr.forEach((field) => {
    let li = document.createElement('li');
    li.innerHTML = field.name;
    li.id = field.id;
    if (field.block === 'labelBlock') {
      let closeBtn = document.createElement('span');
      closeBtn.classList.add('material-icons');
      closeBtn.innerText = 'cancel';
      closeBtn.addEventListener('click', removeField, false);
      li.appendChild(closeBtn);
      li.draggable = 'true';
      li.classList.add('draggable');
      li.dataset.category = field.group;
      li.addEventListener('dragstart', handleDragStart, false);
      li.addEventListener('dragover', handleDragOver, false);
      li.addEventListener('drop', handleDrop, false);
      li.addEventListener('dragend', handleDragEnd, false);
      target.appendChild(li);
    }
  });
}

/**
 * Appends line (fieldBlock) to label builder
 * Binded to button, adds editable div
 */
function addLine() {
  let line = document.createElement('div');
  line.classList.add('field-block', 'container');
  let midBlocks = document.querySelectorAll('#label-middle > .field-block');
  let close = document.createElement('span');
  close.classList.add('material-icons');
  close.innerText = 'close';
  line.appendChild(close);
  let up = document.createElement('span');
  up.classList.add('material-icons');
  up.innerText = 'keyboard_arrow_up';
  line.appendChild(up);
  let down = document.createElement('span');
  down.classList.add('material-icons');
  down.innerText = 'keyboard_arrow_down';
  line.appendChild(down);
  let lastBlock = midBlocks[midBlocks.length - 1];
  lastBlock.parentNode.insertBefore(line, lastBlock.nextSibling);
  // Allows items to be added/reordered inside fieldBlock
  line.addEventListener('dragover', (e) => {
    e.preventDefault();
    const dragging = document.querySelector('.dragging');
    dragging !== null ? line.appendChild(dragging) : '';
  });
  refreshLineState();
}

/**
 * Refreshes line state
 * If there is only one line, disables line controls
 */
function refreshLineState() {
  let lines = labelMid.querySelectorAll('.field-block');
  let icons = lines[0].querySelectorAll('.material-icons');
  let isSingleLine = lines.length == 1;
  icons.forEach((icon) => {
    isSingleLine
      ? icon.classList.add('disabled')
      : icon.classList.remove('disabled');
  });
}

/**
 * Removes line from label-middle
 * @param {Object} line node to be removed
 */
function removeLine(line) {
  let lineCount = labelMid.querySelectorAll('.field-block').length;
  lineCount > 1 ? line.remove() : false;
  refreshLineState();
  refreshAvailFields();
}

/**
 * Removes field from label-middle
 * @param {Object} field node to be removed
 */
function removeField(field) {
  field.target.parentNode.remove();
  // Refresh available fields list
  refreshAvailFields();
}

/**
 * Refreshes label preview
 * Triggered every time items are updated
 */
function refreshPreview() {
  let labelList = [];
  let fieldBlocks = document.querySelectorAll('#build-label .field-block');
  // Builds array with directives (labelList)
  fieldBlocks.forEach((block) => {
    let itemsArr = [];
    let items = block.querySelectorAll('li');
    items.forEach((item) => {
      let itemObj = {};
      let className = Array.from(item.classList).filter(isPrintStyle);
      itemObj.field = item.id;
      itemObj.className = className;
      itemObj.prefix = item.dataset.prefix;
      itemObj.suffix = item.dataset.suffix;
      itemsArr.push(itemObj);
    });
    labelList.push(itemsArr);
    let fieldBlockStyles = Array.from(block.classList).filter(isPrintStyle);
    fieldBlockStyles ? (itemsArr.className = fieldBlockStyles) : '';
    let fieldBlockDelim = block.dataset.delimiter;
    fieldBlockDelim == undefined
      ? (itemsArr.delimiter = ' ')
      : (itemsArr.delimiter = fieldBlockDelim);
  });
  // Clears preview div before appending elements
  preview.innerHTML = '';
  // Creates HTML elements and appends to preview div
  labelList.forEach((labelItem, blockIdx) => {
    let blockLen = labelItem.length;
    let fieldBlock = document.createElement('div');
    fieldBlock.classList.add('field-block');
    let labelItemStyles = labelItem.className;
    labelItemStyles.forEach((style) => {
      fieldBlock.classList.add(style);
    });
    preview.appendChild(fieldBlock);
    labelItem.forEach((field, fieldIdx) => {
      createPreviewEl(field, fieldBlock);
      let isLast = fieldIdx == blockLen - 1;
      // Adds delimiter if existing up to last element
      if (!isLast) {
        let preview = document.getElementsByClassName(field.field);
        let delim = document.createElement('span');
        delim.innerText = labelItem.delimiter;
        preview[0].after(delim);
      }
    });
  });

  return labelList;
}

/**
 * Creates elements in preview div, based on controls in build
 * @param {Object} element Field, constructed in `refreshPreview()`
 * @param {DOM Node} parent DOM Node where element will be inserted
 */
function createPreviewEl(element, parent) {
  // Grabs information from fieldProps array to create elements matching on id
  let fieldInfo =
    fieldProps[fieldProps.findIndex((x) => x.id === element.field)];
  let div = document.createElement('div');
  div.innerHTML = fieldInfo.name.split(' ').join('');
  div.classList.add(fieldInfo.id);
  div.classList.add(...element.className);
  parent.appendChild(div);
  let hasPrefix = element.prefix != undefined;
  let hasSuffix = element.suffix != undefined;
  if (hasPrefix) {
    let currText = div.innerText;
    let prefSpan = `<span>${element.prefix}</span>`;
    div.innerHTML = prefSpan + currText;
  }
  if (hasSuffix) {
    let sufSpan = document.createElement('span');
    sufSpan.innerText = element.suffix;
    div.appendChild(sufSpan);
  }
}

/**
 * Returns true if item is formattable
 * @param {Object} element
 */
function isFormattable(element) {
  if (
    element.classList.contains('field-block') ||
    element.classList.contains('draggable')
  ) {
    return true;
  } else {
    return false;
  }
}

/**
 * Checks if class should be output in JSON
 * @param {String} className found in item
 */
function isPrintStyle(className) {
  const functionalStyles = [
    'draggable',
    'selected',
    'field-block',
    'container',
  ];
  return !functionalStyles.includes(className);
}

/**
 * Generate JSON string for current configurations
 * @param {Array} list Array of fields, built by `refreshPreview()`
 */
function generateJson(list) {
  let wrapper = [
    {
      divBlock: {
        className: 'label-blocks',
        style: '',
        blocks: [],
      },
    },
  ];
  // console.log(wrapper.divBlock.blocks.length);
  let labelBlocks = [];
  // Parse nested array
  Object.keys(list).forEach((index) => {
    let fieldBlockObj = {};
    // Joins array of className items for fields
    let fieldItem = list[index];
    fieldItem.map((prop) => {
      prop.className.length > 0
        ? (prop.className = prop.className.join(' '))
        : delete prop.className;
    });
    fieldBlockObj.fieldBlock = fieldItem;
    let fieldBlockDelim = fieldItem.delimiter;
    fieldBlockDelim !== undefined
      ? (fieldBlockObj.delimiter = fieldBlockDelim)
      : '';
    let fieldBlockStyles = fieldItem.className;
    fieldBlockStyles.length > 0
      ? (fieldBlockObj.className = fieldItem.className.join(' '))
      : delete fieldBlockObj.className;
    labelBlocks.push(fieldBlockObj);
  });
  wrapper[0].divBlock.blocks = labelBlocks;
  // console.log(wrapper);
  // let json = JSON.stringify(labelBlocks, null, 2);
  let json = JSON.stringify(wrapper, null, 2);
  // console.log(json);
  return json;
}

/**
 * Prints JSON in interface
 *
 */
function printJson() {
  let list = refreshPreview();
  let copyBtn = document.getElementById('copyBtn');
  let isEmpty = list[0].length == 0;
  let message = '';
  if (isEmpty) {
    copyBtn.style.display = 'none';
    alert(
      'Label format is empty! Please drag some items to the build area before trying again'
    );
  } else {
    let json = generateJson(refreshPreview());
    copyBtn.style.display = 'inline-block';
    dummy.value = json;
  }
}
/**
 * Provides textarea where users can paste JSON format for validation
 */
function loadJson() {
  let currBlocks = labelMid.querySelectorAll('.field-block');
  let numBlocks = currBlocks.length;
  // Clears lines & fields if already used
  if (numBlocks > 1) {
    for (i = 1; i < numBlocks; i++) {
      removeLine(currBlocks[i]);
    }
  }
  let firstBlock = currBlocks[0];
  let currFields = firstBlock.querySelectorAll('.draggable');
  currFields.forEach((currField) => {
    currField.remove();
  });
  let sourceStr = dummy.value.replace(/'/g, '"');
  sourceJson = false;
  try {
    sourceJson = JSON.parse(sourceStr);
    // console.log(sourceJson);
  } catch (error) {
    console.log(error);
    window.alert(
      'There is an issue with your JSON format. If your label format is very customized, that could interfere with its correct display.'
    );
  }
  if (sourceJson) {
    translateJson(sourceJson);
    refreshLineState();
  } else {
    preview.innerText = '';
  }
}

/**
 * Copies JSON output to user's clipboard
 */
function copyJson() {
  dummy.select();
  dummy.setSelectionRange(0, 99999); /* For mobile devices */
  document.execCommand('copy');
  /* Alert the copied text */
  alert('Copied JSON to clipboard');
}

/**
 * Toggles select/deselect clicked element
 * @param {DOM Node} element
 */
function toggleSelect(element) {
  element.classList.toggle('selected');
  let isSelected = element.classList.contains('selected');
  return isSelected;
}

/**
 * Toggles formatting controls based on filter and state
 * @param {String} filter Class of formatting control (field or field-block)
 * @param {Boolean} bool
 */
function activateControls(filter, bool) {
  let filtered = document.querySelectorAll(`[data-group=${filter}]`);
  filtered.forEach((control) => {
    bool ? (control.disabled = false) : (control.disabled = true);
  });
}

/**
 * Deactivates all controls
 */
function deactivateControls() {
  controls.forEach((control) => {
    control.disabled = true;
  });
}

/**
 * Gets selected item state (formatted classes)
 * @param {DOM Node} item Field in build label area
 */
function getState(item) {
  // console.log(item);
  let delimiter = item.dataset.delimiter;
  if (delimiter) {
    let delimiterInput = document.getElementById('delimiter');
    delimiterInput.value = delimiter;
  }
  let formatList = Array.from(item.classList);
  // Removes '.draggable' and '.selected' from array
  printableList = formatList.filter(isPrintStyle);
  // console.log(printableList);
  if (printableList.length > 0) {
    // Render state of each formatting button
    printableList.forEach((formatItem) => {
      // Check if class is a choice in a dropdown by matching first part of class
      let strArr = formatItem.split('-');
      let str = '';
      strArr.length == 3
        ? (str = strArr[0] + '-' + strArr[1])
        : (str = strArr[0]);
      // console.log(str);
      // Loop through each item in array
      dropdownsArr.forEach((dropdown) => {
        let isDropdownStyle = str === dropdown.id;
        if (isDropdownStyle) {
          let selDropdown = document.getElementById(str);
          selDropdown.value = formatItem;
        }
      });
      controls.forEach((control) => {
        // Select that format and activate it
        if (formatItem === control.dataset.func) {
          control.classList.add('selected');
        }
      });
    });
  }

  // Get state of prefix/suffix for fields
  let hasPrefix = item.dataset.prefix != null;
  let prefixInput = document.getElementById('prefix');
  hasPrefix ? (prefixInput.value = item.dataset.prefix) : '';
  let hasSuffix = item.dataset.suffix != null;
  let suffixInput = document.getElementById('suffix');
  hasSuffix ? (suffixInput.value = item.dataset.suffix) : '';
}

/**
 * Applies selected control styles to selected items
 * @param {String} control ID of formatting control (button or select)
 * @param {Array} selectedItems Items to be formatted (selected)
 * @param {Boolean} bool If style will be added or removed, depends on state of control (important for buttons)
 */
function toggleStyle(control, selectedItems, bool) {
  selectedItems.forEach((item) => {
    // Double-checking if item is selected
    if (item.classList.contains('selected')) {
      // Deals with buttons
      // if formatting button is selected, add class, else remove
      bool
        ? item.classList.add(control.dataset.func)
        : item.classList.remove(control.dataset.func);
      //
    } else {
      return false;
    }
    refreshPreview();
  });
}

/**
 * Applies selected dropdown styles to selected items
 * @param {String} dropdown ID of dropdown
 * @param {Array} selectedItems Items to be formatted (selected)
 */
function addReplaceStyle(dropdown, selectedItems) {
  // Deals with selection
  dropdown.addEventListener('input', function () {
    selectedItems.forEach((item) => {
      let option = dropdown.value;
      if (option !== '') {
        // Check if item already has styles in this group
        let group = new RegExp(`${dropdown.id}-*`);
        let hasGroup = item.className.split(' ').some(function (c) {
          return group.test(c);
        });
        if (item.classList.contains('selected')) {
          if (!hasGroup) {
            // If not, add it
            item.classList.add(option);
            console.log(`added ${option} to ${item.id}`);
          } else {
            // If yes, replace it
            item.classList.forEach((className) => {
              if (className.startsWith(dropdown.id)) {
                item.classList.remove(className);
              }
            });
            item.classList.add(option);
          }
        }
      }
    });
  });
  refreshPreview();
}

/**
 * Clears/resets controls state
 */
function resetControls() {
  controls.forEach((control) => {
    // Deal with select input
    let isDropdown = control.tagName === 'SELECT';
    isDropdown ? (control.value = '') : '';
    control.classList.remove('selected');
    let isInput = control.tagName === 'INPUT';
    isInput ? (control.value = '') : '';
  });
}

/**
 * Updates optional field content (prefix/suffix)
 * @param {DOM Node} content Optional content input
 * @param {DOM Node} item Field to be modified
 */
function updateFieldContent(content, item) {
  let option = content.id;
  item.setAttribute('data-' + option, content.value);
  console.log(content, item);
}

/**
 * Tags dragging elements and copies their content
 * @param {Event} e
 */
function handleDragStart(e) {
  dragSrcEl = this;
  this.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
}

/**
 * Moves content of dragged element when done moving
 * @param {Event} e
 */
function handleDragOver(e) {
  if (e.preventDefault) {
    e.preventDefault();
  }
  e.dataTransfer.dropEffect = 'move';
  return false;
}

/**
 * Reorders element based on position when dropped
 * @param {Event} e
 */
let dragSrcEl = null;
function handleDrop(e) {
  if (e.stopPropagation) {
    e.stopPropagation();
  }
  if (dragSrcEl != this) {
    this.parentNode.insertBefore(dragSrcEl, this);
  }
  return false;
}

/**
 * Removes tag from dragging element
 * @param {Event} e
 */
function handleDragEnd(e) {
  this.classList.remove('dragging');
  refreshPreview();
  return false;
}

function saveJson() {
  let formatId = dummy.dataset.formatId;
  let formatTextArea = window.opener.document.querySelector(formatId);
  let list = refreshPreview();
  let isEmpty = list[0].length == 0;
  let message = '';
  if (isEmpty) {
    alert(
      'Label format is empty! Please drag some items to the build area before trying again'
    );
  } else {
    let json = generateJson(refreshPreview());
    dummy.value = json;
    formatTextArea.value = json;
    window.close();
  }
}

function cancelWindow() {
  window.close();
}
/** Event Listeners
 ******************************
 */
fieldsFilter.addEventListener('change', function (e) {
  filterFields(e.target.value);
});

draggables.forEach((draggable) => {
  draggable.addEventListener('dragstart', handleDragStart, false);
  draggable.addEventListener('dragover', handleDragOver, false);
  draggable.addEventListener('drop', handleDrop, false);
  draggable.addEventListener('dragend', handleDragEnd, false);
});

containers.forEach((container) => {
  container.addEventListener('dragover', (e) => {
    e.preventDefault();
    const dragging = document.querySelector('.dragging');
    dragging !== null ? container.appendChild(dragging) : '';
  });
});

// Elements in '#label-middle'
labelMid.addEventListener('click', (e) => {
  if (e.target.matches('.material-icons')) {
    if (e.target.innerText === 'keyboard_arrow_up') {
      let first = labelMid.getElementsByClassName('field-block')[0];
      let curr = e.target.parentNode;
      // reorder only if item is not first in list already
      if (curr !== first) {
        let prev = e.target.parentNode.previousSibling;
        // move current into prev
        prev.replaceWith(curr);
        // insert current after prev
        curr.parentNode.insertBefore(prev, curr.nextSibling);
      }
    } else if (e.target.innerText === 'keyboard_arrow_down') {
      let next = e.target.parentNode.nextSibling;
      let curr = e.target.parentNode;
      if (next) {
        // move current into next
        curr.replaceWith(next);
        // insert current after next
        next.parentNode.insertBefore(curr, next.nextSibling);
      }
    } else if (e.target.innerText === 'close') {
      let line = e.target.parentNode;
      removeLine(line);
    }
    refreshPreview();
  } else {
    if (isFormattable(e.target)) {
      // Add ".selected" to clicked item, removing it from others
      let lines = labelMid.querySelectorAll('.field-block');
      lines.forEach((line) => {
        line.classList.remove('selected');
      });
      let fields = labelMid.querySelectorAll('.draggable');
      fields.forEach((field) => {
        field.classList.remove('selected');
      });
      e.target.classList.add('selected');
    }
    // Everytime item is clicked, display list of selected items:
    let selectedItems = build.querySelectorAll('.selected');
    if (selectedItems.length == 1) {
      let itemType = '';
      // Refreshes buttons according to applied styles in selected item
      let item = build.querySelector('.selected');
      if (item.matches('.draggable')) {
        itemType = 'field';
        // deactivate 'field-block' items
        activateControls('field-block', false);
      } else if (item.matches('.field-block')) {
        itemType = 'field-block';
        // deactivate 'field' items
        activateControls('field', false);
      }
      resetControls();
      activateControls(itemType, true);
      getState(item);
    } else {
      return false;
    }
  }
});

// Formatting controls
controlDiv.addEventListener('click', (e) => {
  // Gets selected items to format
  let formatItems = build.querySelectorAll('.selected');
  let isFormatSelected = toggleSelect(e.target);
  console.log(isFormatSelected);
  let isButton = e.target.tagName === 'BUTTON';
  let isDropdown = e.target.tagName === 'SELECT';
  // Buttons
  if (isButton) {
    toggleStyle(e.target, formatItems, isFormatSelected);
  } else if (isDropdown) {
    addReplaceStyle(e.target, formatItems);
  }
});

// Field and Block options (prefix/suffix, delimiters)
// Listen to input changes
inputs.forEach((input) => {
  input.addEventListener('input', (e) => {
    let formatItem = build.querySelector('.selected');
    updateFieldContent(e.target, formatItem);
    console.log(e.target, formatItem);
    refreshPreview();
  });
});
