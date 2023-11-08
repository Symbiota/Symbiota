/***
 * NEON Herptiles Jar Labels Custom Styles
 * Author: Laura Rocha Prado
 * Version: April 2023
 *
 * Features:
 * - Merges labels that share same species, locality, collecting date and collector information
 * - Hides individual labels
 * - Does not include barcode
 * - Does not include collector information
 * - Does not include preparator information (only preparation)
 * - Only includes year of collecting date
 * - Gets site code from locality field
 * - Removes "Fish Point" from locality field to allow pooling
 */

let labels = document.querySelectorAll('.label');

labelStrArr = [];
uniqueStrArr = [];
multStrArr = [];
labels.forEach((label) => {
  let scientificNameParent = label.getElementsByClassName('bar')[0];
  let year = label.getElementsByClassName('year')[0];
  year.classList.add('float-right');
  let locality = label.getElementsByClassName('locality')[0];
  let localityText = locality.innerText;
  if (localityText.includes('Fish Point')) {
    localityText = localityText.replace(/, \d{1,2} Fish Point/g, '');
    locality.innerText = localityText;
  }
  let siteCodeRegex = /\([A-Z]{4}\)/g;
  let siteCode = localityText.match(siteCodeRegex);
  let yearText = year.innerText;
  if (siteCode !== null) {
    siteCode = siteCode[0].replace(/[\(\)]/g, '');
    year.innerText = siteCode + ' - ' + yearText;
  } else {
    year.innerText = 'SITE - ' + yearText;
    year.style.backgroundColor = 'red';
  }

  scientificNameParent.appendChild(year);

  // Get the label's .catalognumber and adds it to the label as an ID
  let catalogNumberVal =
    label.getElementsByClassName('catalognumber')[0].innerText;
  label.id = catalogNumberVal;

  // For each label, get a string with the following information:
  // - species
  // - locality
  // - collecting year
  let scientificNameVal =
    label.getElementsByClassName('scientificname')[0].innerText;
  let localityVal = label.getElementsByClassName('locality')[0].innerText;
  let collYearVal = label.getElementsByClassName('year')[0].innerText;
  let labelStr = scientificNameVal + localityVal + collYearVal;
  // push the catalogNumber and labelStr to an array
  labelStrArr.push({ id: catalogNumberVal, str: labelStr });
  uniqueStrArr.push(labelStr);
});

// Keep only unique strings in uniqueStrArr
uniqueStrArr = [...new Set(uniqueStrArr)];

uniqueStrArr.forEach((uniqueStr) => {
  let multStr = [];
  labelStrArr.forEach((labelStr) => {
    if (labelStr.str === uniqueStr) {
      multStr.push(labelStr.id);
    }
  });
  multStrArr.push(multStr);
});

// Hide individual labels
multStrArr.forEach((arr) => {
  if (arr.length === 1) {
    let label = document.getElementById(arr[0]);
    label.style.display = 'none';
  }
});

// Remove from multStrArr any array with only one element
multStrArr = multStrArr.filter((arr) => arr.length > 1);

// Cleaning up otherCatalogNumbers
multStrArr.forEach((arr) => {
  arr.forEach((id) => {
    let label = document.getElementById(id);
    let otherCatalogNumbers = label.getElementsByClassName(
      'othercatalognumbers'
    )[0];
    otherCatalogNumbersArr = otherCatalogNumbers.innerText.split(';');
    otherCatalogNumbersArr = otherCatalogNumbersArr.filter(
      (el) => !el.includes('NEON sampleUUID: ')
    );
    otherCatalogNumbersArr = otherCatalogNumbersArr.filter(
      (el) => !el.includes('NEON sampleID Hash: ')
    );
    otherCatalogNumbersArr = otherCatalogNumbersArr.map((el) =>
      el.substring(el.indexOf(':') + 1)
    );
    otherCatalogNumbersArr = otherCatalogNumbersArr.map((el) => el.trim());
    otherCatalogNumbers.parentElement.remove();
    let catalogNumber = label.getElementsByClassName('catalognumber')[0];
    let catalogNumberParentNode = catalogNumber.parentNode;
    catalogNumberParentNode.className = 'allcatalognumbers';
    otherCatalogNumbersArr.forEach((el) => {
      let span = document.createElement('span');
      span.className = 'othercatalognumbers';
      span.style.fontSize = '7pt';
      span.innerText = el;
      catalogNumberParentNode.appendChild(span);
    });
    // Check for individualCount
    if (label.getElementsByClassName('individualCount').length > 0) {
      let individualCount = label.getElementsByClassName('individualCount')[0];
      catalogNumberParentNode.appendChild(individualCount);
    }
  });
});

// Merging labels
multStrArr.forEach((arr) => {
  let mergedLabel = document.getElementById(multStrArr[0][0]);
  // Grab .allcatalognumbers node from other labels in multStrArr (other than the first one)
  let mergeNums = document.querySelectorAll('.allcatalognumbers');
  // Remove from mergeNums the first element
  mergeNums = Array.from(mergeNums).slice(1);

  mergeNums.forEach((mergeNum) => {
    mergedLabel.getElementsByClassName('label-blocks')[0].appendChild(mergeNum);
  });

  multStrArr.forEach((arr) => {
    arr.forEach((id) => {
      if (id !== multStrArr[0][0]) {
        let label = document.getElementById(id);
        label.classList.add('hidden');
      }
    });
  });
});

window.addEventListener('load', function () {
  let disclaimer = document.createElement('h1');
  disclaimer.innerText = 'Disclaimer: Pooled Labels Only';
  disclaimer.style.textAlign = 'center';
  controls.appendChild(disclaimer);
});
