/***
 * NEON Herbarium Packets Custom Styles
 * Author: Laura Rocha Prado
 * Version: November 2021
 *
 * Features:
 * - Replaces standard Symbiota barcode with custom
 * - Barcodes courtesy of barcode.tec-it.com
 ***/
let domainsArr = new Array();
// async function fetchDomainName(domainnumber) {
//   const url = "http://github.localhost:8080/neon-biorepository/neon/neondomains.php";
//   const data = await (await fetch(url)).json();
//   const domainname = data.filter(d => d.domainnumber === domainnumber)[0].domainname;
//   console.log(domainname);
//   return domainname;
// };

async function fetchDomains() {
  const url = '../../neon/neondomains.php';
  const res = await (await fetch(url)).json();
  // console.log(res);
  return res;
}

async function fetchTaxonomy(sciname) {
  const url = '../../api/taxonomy/gettaxonomy.php?sciname=' + sciname;
  const res = await (await fetch(url)).json();
  return res;
}

function getVernacular(taxonomy) {
  let division = taxonomy.division;
  console.log(taxonomy);
  let vernacular = '';
  if (division != undefined) {
    switch (division) {
      case 'Tracheophyta':
        vernacular = 'Plants';
        break;
      case 'Bryophyta':
      case 'Anthocerotophyta':
      case 'Marchantiophyta':
        vernacular = 'Bryophytes';
        break;
      case 'Charophyta':
        vernacular = 'Macroalgae';
        break;
    }
  } else {
    vernacular = taxonomy.kingdom;
  }
  console.log(vernacular);
  return vernacular;
}

document.addEventListener('DOMContentLoaded', async function () {
  let domainsArr = [];
  domainsArr = await fetchDomains();
  let labels = document.querySelectorAll('.label');
  if (labels) {
    labels.forEach((label) => {
      // Moves family div to header to take advantage of layout
      let familyDiv = label.querySelector('.label-header');
      familyDiv.innerText = '';
      familyDiv.appendChild(label.querySelector('.family'));
      // Adds header
      let header = document.createElement('div');
      header.className = 'label-neon-header';
      let taxon = document.createElement('span');
      // Grabs taxonomy to create dynamic header
      let sciname = label.querySelector('.scientificname').innerText;
      fetchTaxonomy(sciname).then(
        (data) => (taxon.innerText = getVernacular(data.taxonomy) + ' of NEON')
      );
      console.log(taxon);
      header.appendChild(taxon);
      // fetchTaxonomy(sciname).then(data => taxon += data.taxonomy.family);
      let locality = label.querySelector('.locality');
      let hasLocality = locality != null;
      // Error handling when label does not include locality or domain info in locality
      if (hasLocality && locality.innerText.includes('Domain')) {
        let domainNumber = label
          .querySelector('.locality')
          .innerText.match(/(?<=Domain )(\d*)(?=,)/)[0];
        let domainNumberDiv = document.createElement('span');
        domainNumberDiv.innerText = ' Domain ' + domainNumber;
        let domainName = domainsArr.filter(
          (d) => d.domainnumber === 'D' + domainNumber
        )[0].domainname;
        let domainNameDiv = document.createElement('span');
        domainNameDiv.className = 'italic';
        domainNameDiv.innerText = ' - ' + domainName;
        // let domainNameDiv = '<span class="italic">' + domainName + '</span>';
        header.appendChild(domainNumberDiv);
        header.appendChild(domainNameDiv);
        // header.innerHTML += ' of NEON Domain ' + domainNumber + ' - ' + domainNameDiv;
      } else if (!hasLocality) {
        return;
        // header.innerText += ' of NEON';
      }
      label.insertBefore(header, familyDiv);
      let catNums = label.querySelector('.othercatalognumbers');
      let cArr = catNums.innerText.split(';');
      let newCatNum = '';
      let hasNeonBc = cArr.some((element) => element.includes('barcode'));
      let neonBarcode = '';
      cArr.forEach((catNum) => {
        // Grabs NEON barcode or IGSN to use in barcode image
        hasNeonBc == false
          ? (neonBarcode = label.querySelector('.catalognumber'))
          : (neonBarcode = cArr
              .find((element) => element.includes('barcode'))
              .match(/(?<=barcode\): ).*/)[0]
              .trim());
        // Splits other catalog numbers in paragraphs
        newCatNum += `<span class="${catNum.includes('barcode')}">${catNum.trim()}</span>`;
      });
      catNums.innerHTML = newCatNum;
      catNums.classList.add('mt-2');
      // Replaces barcode image
      let bc = label.querySelector('.cn-barcode');
      if (bc != null) {
        let bcImg = bc.querySelector('.cn-barcode > img');
        let newBcImgSrc =
          'https://barcode.tec-it.com/barcode.ashx?data=' +
          neonBarcode +
          '&code=Code128&multiplebarcodes=false&translate-esc=true&unit=Px&dpi=300&imagetype=Gif&rotation=0&color%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0&hidehrt=True&modulewidth=12';
        bcImg.src = newBcImgSrc;
        // Adds catalog number below barcode
        let catNumDiv = document.createElement('span');
        catNumDiv.innerText = neonBarcode;
        catNumDiv.className = 'font-family-arial';
        bc.appendChild(catNumDiv);
      }
      let page = label.parentElement;
      isOverflowing = page.scrollHeight > page.offsetHeight;
      if (isOverflowing) {
        // move label to new page
        let newPage = document.createElement('div');
        newPage.className = 'page';
        newPage.appendChild(label);
        page.after(newPage);
      }
    });
  }
});
