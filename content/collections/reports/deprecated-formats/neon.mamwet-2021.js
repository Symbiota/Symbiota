/* For each label, change:
- how othercatalognumbers are displayed
- barcode number (uses NEON barcode instead of catalognumber)
- preparedBy and preparedDate from dynamicProperties
*/

let labels = document.querySelectorAll('.label');
labels.forEach((label) => {
  let catNums = label.querySelector('.other-catalog-numbers');
  let cArr = catNums.innerText.split(';');
  let newCatNum = '';
  let hasBc = '';
  cArr.forEach((catNum) => {
    newCatNum += `<span class="block">${catNum.trim()}</span>`;
    let bcSrc = label.querySelector('.cn-barcode img');
    if (bcSrc) {
      if (catNum.includes('barcode')) {
        let barcode = catNum.match(/(?<=barcode\): ).*/)[0].trim();
        // bcSrc.src = 'getBarcode.php?bcheight=60&bctext=' + barcode;
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
  });
  catNums.innerHTML = newCatNum;
  catNums.classList.add('mt-2');
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
    let recordedBy = label.querySelector('.recordedby');
    console.log('here');
    if (recordedBy) {
      let prepBy = document.createElement('span');
      prepBy.className = 'dynamicproperties block';
      prepBy.innerText = 'Prep. by: ';
      recordedBy.parentNode.appendChild(prepBy);
    }
  }
});
