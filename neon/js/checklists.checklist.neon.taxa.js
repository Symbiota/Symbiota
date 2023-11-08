/**
 * Checks whether taxon or specimen has a NEON voucher in checklist.
 * Applies different color to links for non-NEON taxa/vouchers.
 * Author: Laura Rocha Prado
 * Version: August 2021
 */
const taxCont = document.querySelectorAll('.taxon-container');

var hasNeon = (el) => el.innerText.includes('[NEON]');

taxCont.forEach((cont) => {
  let taxA = cont.querySelector('.taxon-div a');
  let noteAs = cont.querySelectorAll('.note-div a');
  if (!hasNeon(cont)) {
    taxA.style.color = '#565a5c';
    noteAs.forEach((note) => {
      if (!hasNeon(note)) {
        note.style.color = '#565a5c';
      }
    });
  }
});
