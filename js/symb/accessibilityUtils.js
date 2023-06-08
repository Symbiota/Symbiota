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
  viewAccessible
) {
  try {
    const response = await sendRequest(
      pathToToggleStyles + "/toggle-styles.php",
      "POST",
      cssPath
    );
    handleResponse(response, viewCondensed, viewAccessible);
  } catch (error) {
    console.log(error);
  }
}

function handleResponse(activeStylesheet, viewCondensed, viewAccessible) {
  const links = document.getElementsByName("accessibility-css-link");
  const button = document.getElementById("accessibility-button");

  const isCurrentlyCondensed =
    activeStylesheet.indexOf("/symbiota/condensed.css?ver=6.css") > 0;
  const newCss = isCurrentlyCondensed
    ? "/symbiota/accessibility-compliant.css?ver=6.css"
    : "/symbiota/condensed.css?ver=6.css";
  button.setAttribute("data-target-css", newCss);

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
