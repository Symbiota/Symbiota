/***
 * Sets alert messages in chosen page
 * Version: Apr 2021
 * Last updated: Jul 2021
 * Dependencies: classes 'alerts' and 'visually-hidden'
 */

/**
 * Adds styled alert messages in page
 * @param {Array} alerts Array with objects
 * OPTIONAL @param {integer} duration of alert in milliseconds
 * Object contains at minimum a property 'alertMsg'
 * 'alertMsg' can be a string or HTML code
 *
 */
const main = document.getElementById('innertext');
let div = document.createElement('div');
div.id = 'alert-msgs';
div.classList.add('alerts');
main.appendChild(div);

function handleAlerts(alerts, duration) {
  if (duration === undefined) {
    let alertDiv = document.getElementById('alert-msgs');
    console.log(alertDiv);
    alertDiv.innerHTML = '';
    alerts.map((alert) => {
      alertDiv.classList.remove('visually-hidden');
      let alertP = document.createElement('p');
      alertP.classList.add('alert');
      alertP.innerHTML = alert.alertMsg + ' Click to dismiss.';
      alertP.onclick = function () {
        alertP.remove();
      };
      alertDiv.appendChild(alertP);
    });
  } else {
    let alertDiv = document.getElementById('alert-msgs');
    console.log(alertDiv);
    alertDiv.innerHTML = '';
    alerts.map((alert) => {
      alertDiv.classList.remove('visually-hidden');
      let alertP = document.createElement('p');
      alertP.classList.add('alert');
      alertP.innerHTML =
        alert.alertMsg +
        ' Click to dismiss or wait ' +
        duration / 1000 +
        ' seconds for automatic dismissal.';
      alertP.onclick = function () {
        alertP.remove();
      };
      alertDiv.appendChild(alertP);
    });
    setTimeout(function () {
      alertDiv.classList.add('visually-hidden');
    }, duration);
  }
}
