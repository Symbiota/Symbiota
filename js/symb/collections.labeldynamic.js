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

let DocXForm = document.createElement('form');
DocXForm.style.marginLeft = '30px';
DocXForm.style.width= 'fit-content';
DocXForm.style.display= 'inline';
DocXForm.method="POST"
DocXForm.action="htmlLabelsToDocX.php"

let printDocXBtn = document.createElement('button');
printDocXBtn.innerText = 'Print/Save Docx';
printDocXBtn.id = 'print_docx';
printDocXBtn.style.fontWeight = 'bold';
//printDocXBtn.type = 'button';

printDocXBtn.onclick = function () {
	//Remove All inputs
	DocXForm.querySelectorAll('input').forEach(node => node.remove());

	let headers = document.querySelectorAll('.label-header');
	const header_style = getInlineStyle('.label-header');
	for(let header of headers) {
		header.style = header_style;
	}

	let field_blocks = document.querySelectorAll('.field-block');

	for(let field_block of field_blocks) {
		for(let field of field_block.children) {
			let field_style = "";
			for(let class_name of field.classList) {
				field_style += getInlineStyle('.' + class_name);
			}

			field.style = field_style;

			if(field_style.includes('font-weight')) {
				if(parseInt(field.style['font-weight']) > 500) {
					field.style['font-weight'] = 'bold';
				}
			}
		}
	}

	let footers = document.querySelectorAll('.label-footer');
	const footer_style = getInlineStyle('.label-footer');
	for(let footer of footers) {
		footer.style = footer_style;
	}

	const labels = document.querySelectorAll('.label');

	//Create Label Chunks
	for(let label of labels) {
		let pageHtmlInput= document.createElement('input');
		pageHtmlInput.type="hidden";
		pageHtmlInput.value= label.outerHTML;
		pageHtmlInput.name="htmlLabels[]";
		DocXForm.appendChild(pageHtmlInput);
	}
};

DocXForm.appendChild(printDocXBtn);

controls.appendChild(editBtn);
controls.appendChild(printBtn);
controls.appendChild(DocXForm);
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

function getInlineStyle(className) {
	var cssText = "";
	'.label-footer { clear: both; text-align: center; font-weight: bold; font-size: 12pt; }'

    for (var i = 0; i < document.styleSheets.length; i++) {        
		var classes = document.styleSheets[i].rules || document.styleSheets[i].cssRules;
		for (var x = 0; x < classes.length; x++) {        
			if (classes[x].selectorText == className) {
				for(let rule of Object.values(classes[x].style)) {
					cssText += `${rule}:${classes[x].style[rule]};`
				}
			}         
		}
	}
    return cssText;
}
