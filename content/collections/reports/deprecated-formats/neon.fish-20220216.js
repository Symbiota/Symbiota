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
});
