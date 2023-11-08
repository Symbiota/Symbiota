/***
 * NEON Herptiles Jar Individual Labels Custom Styles
 * Author: Laura Rocha Prado
 * Version: February 2022
 *
 * Features:
 * - Does not include barcode
 * - Does not include preparator information (only preparation)
 * - Gets site code from locality field
 */

let labels = document.querySelectorAll('.label');

labels.forEach((label) => {
  // General styling
  let scientificNameParent = label.getElementsByClassName('bar')[0];
  let year = label.getElementsByClassName('year')[0];
  year.classList.add('float-right');
  let locality = label.getElementsByClassName('locality')[0];
  let localityText = locality.innerText;
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

  // Catalog numbers clean up
  let catalogNumber = label.getElementsByClassName('catalognumber')[0];
  let catalogNumberParentNode = catalogNumber.parentNode;
  let otherCatalogNumbers = label.getElementsByClassName(
    'othercatalognumbers'
  )[0];
  let individualCount = label.getElementsByClassName('individualCount')[0];
  if (
    otherCatalogNumbers.innerText !== null &&
    otherCatalogNumbers.innerText.includes(';')
  ) {
    let otherCatalogNumbersArr = otherCatalogNumbers.innerText.split(';');
    otherCatalogNumbersArr = otherCatalogNumbersArr.filter(
      (el) => !el.includes('NEON sampleUUID: ')
    );
    otherCatalogNumbersArr = otherCatalogNumbersArr.map((el) =>
      el.substring(el.indexOf(':') + 1)
    );
    otherCatalogNumbersArr = otherCatalogNumbersArr.map((el) => el.trim());

    otherCatalogNumbers.parentElement.remove();

    catalogNumberParentNode.className = 'allcatalognumbers';
    otherCatalogNumbersArr.forEach((el) => {
      let span = document.createElement('span');
      span.className = 'othercatalognumbers';
      span.style.fontSize = '6pt';
      span.innerText = el;
      catalogNumberParentNode.appendChild(span);
    });
  }
});
