function sendRequest(url, method, data) {
  return new Promise((resolve, reject) => {
    const xmlRequest = new XMLHttpRequest();
    xmlRequest.open(method, url);
    xmlRequest.setRequestHeader("Content-Type", "application/json");
    xmlRequest.onreadystatechange = () => {
      if (xmlRequest.readyState === 4) {
        if (xmlRequest.status === 200) {
          resolve(xmlRequest.responseText);
        } else {
          reject(xmlRequest.statusText);
        }
      }
    };
    xmlRequest.send(JSON.stringify({ data: data }));
  });
}

async function toggleAccessibilityStyles(
  pathToToggleStyles,
  cssPath,
  viewCondensed,
  viewAccessible,
  alternateButtonId = ""
) {
  try {
    const response = await sendRequest(
      pathToToggleStyles + "/toggle-styles.php",
      "POST",
      cssPath
    );
    handleResponse(response, viewCondensed, viewAccessible, alternateButtonId);
  } catch (error) {
    console.log(error);
  }
}

function handleResponse(
  activeStylesheet,
  viewCondensed,
  viewAccessible,
  alternateButtonId
) {
  const links = document.getElementsByName("accessibility-css-link");
  let button = document.getElementById("accessibility-button");
  if (alternateButtonId) {
    button = document.getElementById(alternateButtonId);
  }

  const isCurrentlyCondensed =
    activeStylesheet.indexOf("/symbiota/condensed.css?ver=6.css") > 0;

  const newText = isCurrentlyCondensed ? viewCondensed : viewAccessible;
  button.textContent = newText;

  for (let i = 0; i < links.length; i++) {
    if (links[i].getAttribute("href") === activeStylesheet) {
      links[i].disabled = true;
    } else {
      links[i].disabled = false;
    }
  }
}
