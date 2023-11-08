/***
 * NEON Barcode-only Custom Styles
 * Author: Ed Gilbert
 * Version: Sept 2023
 *
 * Features:
 * - Replaces standard Symbiota barcode with custom (using NEON tag (barcode) instead of catalogNumber)
 * - Barcodes courtesy of barcode.tec-it.com
 * - Removes all other identifiers except for IGSN
 */

let labels = document.querySelectorAll(".label");
labels.forEach((label) => {
	let catNums = label.querySelector(".other-catalog-numbers");
	let catNumsText = catNums.innerText;
	let bcSrc = label.querySelector(".cn-barcode img");
	catNums.innerHTML = bcSrc.src.split("bctext=")[1];

	let newBcSrc = '';
	let cArr = catNumsText.split(";");
	cArr.forEach((catNum) => {
		if (catNum.includes("barcode")) {
			let barcode = catNum.match(/(?<=barcode\): ).*/)[0].trim();
			newBcSrc = "https://barcode.tec-it.com/barcode.ashx?data=" + barcode + "&code=Code128";
			return true;
		}
	});
	bcSrc.src = newBcSrc;
});
