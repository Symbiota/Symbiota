const openDialogButton = document.getElementById('accessibility-options-button');
const accessibilityDialog = document.getElementById('accessibility-modal');

document.addEventListener('DOMContentLoaded', ()=>{
	document.getElementById('accessibility-button').disabled=false;
	updateButtonTextBasedOnEnabledStylesheet();
});

openDialogButton.addEventListener('click', function() {
	accessibilityDialog.showModal();
});

function sendRequest(url, method, currentEnabledStylesheet) {
  return new Promise((resolve, reject) => {
    const xmlRequest = new XMLHttpRequest();
    xmlRequest.open(method, url);
	xmlRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlRequest.onreadystatechange = () => {
      if (xmlRequest.readyState === 4) {
        if (xmlRequest.status === 200) {
          resolve(xmlRequest.responseText);
        } else {
          reject(xmlRequest.statusText);
        }
      }
    };
    xmlRequest.send("currentEnabledStylesheet=" + currentEnabledStylesheet);
  });
}

async function toggleAccessibilityStyles() {
  try {
    const currentEnabledStylesheet = getEnabledLink().getAttribute("href");
    const response = await sendRequest(
      clientRootPath + "/accessibility/rpc/toggle-styles.php",
      "POST",
      currentEnabledStylesheet
    );
    await handleResponse(response);
  } catch (error) {
    console.log(error);
  }
}

async function handleResponse(stylesheetReferencedInSession) {
  let links = document.querySelectorAll("[data-accessibility-link]");

  for (let i = 0; i < links.length; i++) {
    if (links[i].getAttribute("href") === stylesheetReferencedInSession) {
      links[i].disabled = false;
    } else {
      links[i].disabled = true;
    }
  }
  updateButtonTextBasedOnEnabledStylesheet();
}

function updateButtonTextBasedOnEnabledStylesheet() {
  let enabledLink = getEnabledLink();
  const isAccessibleLinkEnabled =
    enabledLink
      ?.getAttribute("href")
      ?.indexOf("/symbiota/accessibility-compliant.css") > 0;
  let buttons = document.querySelectorAll("[data-accessibility]");
  const newText = isAccessibleLinkEnabled ? toggleOff508Text : toggleOn508Text;
  for (let j = 0; j < buttons.length; j++) {
    buttons[j].textContent = newText;
  }
}

function getEnabledLink() {
  let links = document.querySelectorAll("[data-accessibility-link]");
  let enabledLink;
  for (let i = 0; i < links.length; i++) {
    if (!links[i].disabled) {
      enabledLink = links[i];
    }
  }
  return enabledLink;
}
