const addElemFirst = (parentDivId, targetChildDivId) => {
  const parent = document.getElementById(parentDivId);
  const targetChild = document.getElementById(targetChildDivId);
  if (!parent || !targetChild) {
    return;
  }
  if (!parent.contains(targetChild)) {
    return;
  }
  parent.prepend(targetChild);
};

const reorderElements = (parentDivId, desiredDivIds, removeDivIds) => {
  const parent = document.getElementById(parentDivId);
  const allChildren = Array.from(parent.children);
  const allChildrenIds = allChildren.map((child) => child.id);
  const revisedDesired = desiredDivIds.filter((desiredDiv) => {
    return (
      allChildrenIds.includes(desiredDiv) ||
      desiredDiv === "br" ||
      desiredDiv == "hr"
    );
  });

  revisedDesired.forEach((desired) => {
    //get tip of parent child array to make sure we're not repeating breaks or hrs
    const tipId = Array.from(parent.children)?.slice(-1)[0]?.id;

    if (desired === "hr" && tipId !== "") {
      // @TODO skip if preceding entry in parent's id is hr
      const hrElement = document.createElement("hr");
      hrElement.style.cssText = "margin-bottom: 2rem; clear: both;";
      parent.appendChild(hrElement);
    }
    if (desired === "br" && tipId !== "") {
      // @TODO skip if preceding entry in parent's id is hr
      const brElement = document.createElement("br");
      brElement.style.cssText = "margin-bottom: 2rem; clear: both;";
      parent.appendChild(brElement);
    }
    if (desired !== "hr" && desired !== "br") {
      const targetIndexInAllChildren = allChildrenIds.indexOf(desired);
      parent.appendChild(allChildren[targetIndexInAllChildren]);
    }
  });

  const leftOverChildren = allChildren.filter(
    (child) => !revisedDesired.includes(child.id)
  );
  if (leftOverChildren.length > 0) {
    const brElement = document.createElement("br");
    brElement.style.cssText = "margin-bottom: 2rem; clear: both;";
    parent.appendChild(brElement);
  }
  leftOverChildren.forEach((orphan) => {
    parent.appendChild(orphan);
  });
};

// Example implementation below. Add the following code (or something like it with the desired order of divs) the end of collections/individual/index.php

{
  /* <script type="text/javascript">
		document.addEventListener('DOMContentLoaded', ()=>{
			reorderElements("occur-div", ["cat-div", "hr", "sciname-div", "family-div","hr", "taxonremarks-div", "assoccatnum-div", "assoccatnum-div", "idqualifier-div","identref-div","identremarks-div", "determination-div", "hr", "identby-div", "identdate-div","verbeventid-div", "hr", "recordedby-div", "recordnumber-div", "eventdate-div", "hr", "locality-div", "latlng-div", "verbcoord-div", "elev-div", "habitat-div", "assoctaxa-div", "attr-div", "notes-div", "hr", "rights-div", "contact-div", "openeditor-div"], ["occurrenceid-div", "disposition-div"]);

		});
	</script> */
}
