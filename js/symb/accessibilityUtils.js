function sendRequest(url, method) {
  return new Promise((resolve, reject) => {
    const xmlRequest = new XMLHttpRequest();
    xmlRequest.open(method, url);
    xmlRequest.onreadystatechange = () => {
      if (xmlRequest.readyState === 4) {
        if (xmlRequest.status === 200) {
          resolve(xmlRequest.responseText);
        } else {
          reject(xmlRequest.statusText);
        }
      }
    };
    xmlRequest.send();
  });
}

async function toggleAccessibilityStyles(urlBase) {
  try {
    console.log("deleteMe got here a1");
    console.log("deleteMe urlBase is: ");
    console.log(urlBase);
    const response = await sendRequest(urlBase + "/toggle-styles.php", "POST");
    handleResponse(response);
  } catch (error) {
    console.log(error);
  }
}

function handleResponse(activeStylesheet) {
  const links = document.getElementsByName("accessibility-css-link");
  const button = document.getElementById("accessibility-button");

  const regexQuery = RegExp(".*(/symbiota)"); // @TODO figure out how to generalize this more
  const secondpart = activeStylesheet.replace(regexQuery, "$1");
  console.log("deleteMe secondpart is: ");
  console.log(secondpart);
  const newCss =
    secondpart === "/symbiota/condensed.css?ver=6.css"
      ? "/symbiota/accessibility-compliant.css?ver=6.css"
      : "/symbiota/condensed.css?ver=6.css"; // @TODO generalize this
  button.setAttribute("data-target-css", newCss);

  const currentText = button.textContent;
  const newText =
    secondpart === "/symbiota/condensed.css?ver=6.css"
      ? "View condensed form"
      : "View accessible form";
  button.textContent = newText;

  for (let i = 0; i < links.length; i++) {
    if (links[i].getAttribute("href") === activeStylesheet) {
      links[i].disabled = true;
    } else {
      links[i].disabled = false;
    }
  }
}
