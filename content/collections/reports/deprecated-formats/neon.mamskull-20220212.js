/* For each label, change:
- how othercatalognumbers are displayed
- barcode number (uses NEON barcode instead of catalognumber)
- preparedBy and preparedDate from dynamicProperties
*/

let labels = document.querySelectorAll('.label');
labels.forEach((label) => {
  // Moves collection name to label top div
  let coll = label.querySelector('.col-title');
  let header = label.querySelector('.label-header');
  header.appendChild(coll);
  let catNums = label.querySelector('.other-catalog-numbers');
  let cArr = catNums.innerText.split(';');
  let newCatNum = '';
  let hasBc = '';
  cArr.forEach((catNum) => {
    // Skips adding barcode to 'other-catalog-numbers' because it's
    // displayed in actual barcode image
    let bcSrc = label.querySelector('.cn-barcode img');
    if (bcSrc) {
      if (catNum.includes('barcode')) {
        let barcode = catNum.match(/(?<=barcode\): ).*/)[0].trim();
        // bcSrc.src = 'getBarcode.php?bcheight=60&bctext=' + barcode;
        bcSrc.src =
          'https://barcode.tec-it.com/barcode.ashx?data=' +
          barcode +
          '&code=Code128&hidehrt=False';
        hasBc = 'true';
        return hasBc;
      } else {
        let sIdPrefix = 'NEON sampleID: ';
        let sId = catNum.slice(sIdPrefix.length);
        newCatNum += `<span class="block">${sId.trim()}</span>`;
      }

      if (hasBc != 'true') {
        // if there is no NEON barcode, uses the IGSN (catalogNumber instead)
        bcSrc.src =
          'https://barcode.tec-it.com/barcode.ashx?data=' +
          label.querySelector('.catalognumber').innerText +
          '&code=Code128&hidehrt=False';
      }
    }
  });
  catNums.innerHTML = newCatNum;
  catNums.classList.add('mt-2');
  // Removes ORCID from recordedby
  let recordedBy = label.querySelector('.recordedby');
  let orcid = recordedBy.innerText.indexOf(' (ORCID');
  if (orcid != -1) {
    let newRecordedBy = recordedBy.innerText.slice(0, orcid);
    recordedBy.innerText = newRecordedBy;
  }
  // Gets `preparedBy` field from `dynamicProperties` if available
  let dynProps = label.querySelector('.dynamicproperties');
  if (dynProps) {
    let prepBy = '';
    if (dynProps.innerText.includes('preparedBy')) {
      let arr = dynProps.innerText.split(',');
      // console.log(idx);
      arr.forEach((i) => {
        if (i.includes('preparedBy')) {
          prepBy = i.match(/(?<=preparedBy:).*/)[0].trim();
          return prepBy;
        }
      });

      let prepDate = '';
      if (dynProps.innerText.includes('preparedDate')) {
        let dArr = dynProps.innerText.split(',');
        dArr.forEach((j) => {
          if (j.includes('preparedDate')) {
            prepDate = j.match(/(?<=preparedDate:).*/)[0].trim();
            return prepDate;
          }
        });
      }
      // dynProps.innerText = 'Prep. by: ' + prepBy + ' (' + prepDate + ')';
      dynProps.innerText = 'Prep. by: ' + prepBy;
    } else {
      // dynProps.style = 'display:none';
      // Leave 'Prep. by:' even if empty
      dynProps.innerText = 'Prep. by: ';
    }
  } else {
    // Create 'Prep. by:' when no dynProps are found, after collector div (only if collector exists)
    if (recordedBy) {
      let prepBy = document.createElement('span');
      prepBy.className = 'dynamicproperties block';
      prepBy.innerText = 'Prep. by: ';
      recordedBy.parentNode.appendChild(prepBy);
    }
  }
});
