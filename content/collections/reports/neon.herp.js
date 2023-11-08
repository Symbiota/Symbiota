/***
 * NEON Herptiles Vial Labels Custom Styles
 * Author: Laura Rocha Prado
 * Version: September 2022
 *
 * Features:
 * - Replaces standard Symbiota barcode with custom (using NEON barcode instead of catalogNumber)
 * - Barcodes courtesy of barcode.tec-it.com
 * - Removes orcid from recordedby
 * - Removes NEON UUID
 * - Deactivates this to remove prepBy line: "Gets `preparedBy` and `preparedDate` field from `dynamicProperties` if available"
 * - Removes newly added "NEON sampleID Hash" and barcode from last line (leave only sampleID)
 */

let labels = document.querySelectorAll('.label');
labels.forEach((label) => {
  let catNums = label.querySelector('.other-catalog-numbers');
  let cArr = catNums.innerText.split(';');
  let newCatNum = '';
  let hasBc = '';
  let bcSrc = label.querySelector('.cn-barcode img');
  cArr.forEach((catNum) => {
    // add barcode image
    if (catNum.includes('barcode')) {
      if (bcSrc) {
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
    } else if (catNum.includes('sampleID:')) {
      // add sampleID to bottom
      catNum = catNum.substr(
        catNum.indexOf('sampleID:') + 'sampleID:'.length,
        catNum.length
      );
      newCatNum += `<span class="block">${catNum.trim()}</span>`;
    }
    catNums.innerHTML = newCatNum;
    catNums.classList.add('mt-2');
  });
  // Removes ORCID from collector
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
  // Gets `preparedBy` field from `dynamicProperties` if available
  // let dynProps = label.querySelector('.dynamicproperties');
  // if (dynProps) {
  //   let prepBy = '';
  //   if (dynProps.innerText.includes('preparedBy')) {
  //     let arr = dynProps.innerText.split(',');
  //     // console.log(idx);
  //     arr.forEach((i) => {
  //       if (i.includes('preparedBy')) {
  //         prepBy = i.match(/(?<=preparedBy:).*/)[0].trim();
  //         return prepBy;
  //       }
  //     });

  //     let prepDate = '';
  //     if (dynProps.innerText.includes('preparedDate')) {
  //       let dArr = dynProps.innerText.split(',');
  //       dArr.forEach((j) => {
  //         if (j.includes('preparedDate')) {
  //           prepDate = j.match(/(?<=preparedDate:).*/)[0].trim();
  //           return prepDate;
  //         }
  //       });
  //     }
  //     // dynProps.innerText = 'Prep. by: ' + prepBy + ' (' + prepDate + ')';
  //     dynProps.innerText = 'Prep. by: ' + prepBy;
  //   } else {
  //     // dynProps.style = 'display:none';
  //     // Leave 'Prep. by:' even if empty
  //     dynProps.innerText = 'Prep. by: ';
  //   }
  // } else {
  //   // Create 'Prep. by:' when no dynProps are found, after collector div (only if collector exists)
  //   if (recordedBy) {
  //     let prepBy = document.createElement('span');
  //     prepBy.className = 'dynamicproperties block';
  //     prepBy.innerText = 'Prep. by: ';
  //     recordedBy.parentNode.appendChild(prepBy);
  //   }
  // }
});
