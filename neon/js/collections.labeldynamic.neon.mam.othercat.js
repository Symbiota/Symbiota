let labels = document.querySelectorAll('.othercatalognumbers');
undefined;
labels.forEach((label) => {
  let arr = label.innerText.split(';');
  let newLabel = '';
  arr.forEach((item) => {
    newLabel += `<span class="block">${item.trim()}</span>`;
  });
  label.innerHTML = newLabel;
});
