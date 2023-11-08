/**
 * Adds functionality to label page (../../collections/reports/labeldynamic.php)
 * - Allows users to edit content directly in labels
 * - Adds "print/save" button
 * - Controls hidden from printed page via media query
 *
 * Requires modern browsers (HTML5)
 *
 *  Author: Laura Rocha Prado (lauraprado@asu.edu)
 *  Version: Fev 2021
 */

let labelPage = document.querySelector('.body');

let controls = document.createElement('div');
controls.classList.add('controls');
controls.style.width = '980px';
controls.style.margin = '0 auto';
controls.style.paddingBottom = '30px';

let editBtn = document.createElement('button');
editBtn.innerText = 'Edit Labels Content';
editBtn.id = 'edit';
editBtn.style.fontWeight = 'bold';
editBtn.onclick = toggleEdits;

let printBtn = document.createElement('button');
printBtn.innerText = 'Print/Save PDF';
printBtn.id = 'print';
printBtn.style.marginLeft = '30px';
printBtn.style.fontWeight = 'bold';
printBtn.onclick = function () {
  window.print();
};

controls.appendChild(editBtn);
controls.appendChild(printBtn);
document.body.prepend(controls);

function toggleEdits() {
  let isEditable = labelPage.contentEditable === 'true';
  if (isEditable) {
    console.log(isEditable);
    labelPage.contentEditable = 'false';
    document.querySelector('#edit').innerText = 'Edit Labels Text';
    labelPage.style.border = 'none';
  } else {
    console.log(isEditable);
    labelPage.contentEditable = 'true';
    document.querySelector('#edit').innerText = 'Save';
    labelPage.style.border = '2px solid #03fc88';
  }
}
