const standardizeCultivarEpithet = (unstandardizedCultivarEpithet) => {
  if (unstandardizedCultivarEpithet) {
    const cleanString = unstandardizedCultivarEpithet.replace(
      /(^['"“”]+)|(['"“”]+$)/g,
      ""
    );
    return "'" + cleanString + "'";
  } else {
    return "";
  }
};

const standardizeTradeName = (unstandardizedTradeName) => {
  if (unstandardizedTradeName) {
    return unstandardizedTradeName.toUpperCase();
  } else {
    return "";
  }
};

const debounce = (func, delay) => {
  // thanks for the idea, chatGtp!
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
};

const rankIdsToHideUnit2From = {
  "non-ranked node": 0,
  organism: 1,
  kingdom: 10,
  subkingdom: 20,
  division: 30,
  subdivision: 40,
  superclass: 50,
  class: 60,
  subclass: 70,
  order: 100,
  suborder: 110,
  family: 140,
  subfamily: 150,
  tribe: 160,
  subtribe: 170,
  genus: 180,
  subgenus: 190,
  section: 200,
  subsection: 210,
};
const { ...rest } = rankIdsToHideUnit2From;
const rankIdsToHideUnit3From = { ...rest, species: 220 };
const { ...rest2 } = rankIdsToHideUnit3From;

const rankIdsToHideUnit4From = {
  ...rest2,
  subspecies: 230,
  variety: 240,
  subvariety: 250,
  form: 260,
  subform: 270,
};
const { ...rest3 } = rankIdsToHideUnit4From;
const rankIdsToHideUnit5From = { ...rest3 };

const allRankIds = { ...rest3, cultivar: 300 };

function removeFromSciName(targetForRemoval) {
  const oldValue = document.getElementById("sciname").value;
  const newValue = oldValue
    .replace(targetForRemoval, "")
    .replace("  ", " ")
    .trim();
  document.getElementById("sciname").value = newValue;
  const scinameDisplay = document.getElementById("scinamedisplay");
  if (scinameDisplay) scinameDisplay.textContent = newValue;
}

async function handleFieldChange(form, silent = false, submitButtonId) {
  console.log("deleteMe handleFieldChange entered");
  // const submitButton = document.getElementById("taxoneditsubmit");
  const submitButton = document.getElementById(submitButtonId);
  submitButton.disabled = true;
  submitButton.textContent = "Checking for existing entry...";
  const isOk = await verifyLoadForm(form, silent);
  console.log("deleteMe isOk is: ");
  console.log(isOk);
  if (!isOk) {
    submitButton.textContent = "Duplicate Detected - Button Disabled";
    submitButton.disabled = true;
  } else {
    submitButton.textContent = "Submit Edits";
    submitButton.disabled = false;
  }
}

async function verifyLoadFormCore(f, silent = false) {
  const isUniqueEntry = await checkNameExistence(f, silent);
  console.log("deleteMe isUniqueEntry is: ");
  console.log(isUniqueEntry);
  console.log("deleteMe f is: ");
  console.log(f);
  if (!isUniqueEntry) {
    return false;
  }
  if (f.unitname1.value == "") {
    console.log("deleteMe got here c1");
    alert("Unit Name 1 (genus or uninomial) field required.");
    return false;
  }
  var rankId = f.rankid.value;
  console.log("deleteMe rankId is: ");
  console.log(rankId);
  if (rankId == "") {
    console.log("deleteMe got here c2");
    alert("Taxon rank field required.");
    return false;
  }
  return true;
}

function checkNameExistence(f, silent = false) {
  return new Promise((resolve, reject) => {
    if (!f?.sciname?.value || !f?.rankid?.value) {
      resolve(false);
    } else {
      $.ajax({
        type: "POST",
        url: "rpc/gettid.php",
        data: {
          sciname: f.sciname.value,
          rankid: f.rankid.value,
          author: f.author.value,
        },
        success: function (msg) {
          if (msg != "0") {
            if (!silent) {
              alert(
                "Taxon " +
                  f.sciname.value +
                  " " +
                  f.author.value +
                  " (" +
                  msg +
                  ") already exists in database"
              );
            }
            resolve(false);
          } else {
            resolve(true);
          }
        },
        error: function (error) {
          console.error("Error during AJAX request", error);
          reject(error);
        },
      });
    }
  });
}

function updateFullnameCore(f) {
  let sciname =
    f.unitind1.value +
    f.unitname1.value +
    " " +
    f.unitind2.value +
    f.unitname2.value +
    " ";
  if (f.unitname3.value) {
    sciname = sciname + (f.unitind3.value + " " + f.unitname3.value).trim();
  }
  if (f.cultivarEpithet.value) {
    sciname += " " + standardizeCultivarEpithet(f.cultivarEpithet.value);
  }
  if (f.tradeName.value) {
    sciname += " " + standardizeTradeName(f.tradeName.value);
  }
  f.sciname.value = sciname.trim();
  checkNameExistence(f);
  // return f;
}
