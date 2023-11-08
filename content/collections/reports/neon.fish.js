/***
 * NEON Fish Label Custom Styles
 * Author: Laura Rocha Prado
 * Version: February 2022
 *
 * Features:
 * - Replaces standard Symbiota barcode with custom
 * - Barcodes courtesy of barcode.tec-it.com
 * - Removes NEON UUID
 * - Removes ORCID from collector
 * - Adds preparedBy and preparedDate from dynamicProperties
 ***/
let labels = document.querySelectorAll('.label');
labels.forEach((label) => {
  let recordedBy = label.querySelector('.recordedby');
  if (recordedBy) {
    let hasOrcid = recordedBy.innerText.toLowerCase().includes('orcid');
    if (hasOrcid) {
      // remove ORCID from recordedBy
      let orcidIdx = recordedBy.innerText;
      let orcid = '(' + orcidIdx.match(/(?<=\()[^)]*(?=\))/)[0] + ')';
      let newRecordedBy = recordedBy.innerText.replace(orcid, '');
      recordedBy.innerText = newRecordedBy;
    }
  }
  let catNums = label.querySelector('.other-catalog-numbers');
  let cArr = catNums.innerText.split(';');
  let newCatNum = '';
  let hasBc = '';
  cArr.forEach((catNum) => {
    // Skip if it's a NEON UUID
    if (catNum.includes('sampleUUID')) {
      return;
    } else {
      newCatNum += `<span class="block">${catNum.trim()}</span>`;
      let bcSrc = label.querySelector('.cn-barcode img');
      if (bcSrc) {
        if (catNum.includes('barcode')) {
          let barcode = catNum.match(/(?<=barcode\): ).*/)[0].trim();
          bcSrc.src =
            'https://barcode.tec-it.com/barcode.ashx?data=' +
            barcode +
            '&code=Code128';
          hasBc = 'true';
          return hasBc;
        }

        if (hasBc != 'true') {
          // if there is no NEON barcode, uses the IGSN (catalogNumber instead)
          bcSrc.src =
            'https://barcode.tec-it.com/barcode.ashx?data=' +
            label.querySelector('.catalognumber').innerText +
            '&code=Code128';
        }
      }
    }
  });
  catNums.innerHTML = newCatNum;
  catNums.classList.add('mt-2');
});
