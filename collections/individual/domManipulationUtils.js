const reorderElements = (parentDivId, desiredDivIds, removeDivIds) => {
  const parent = document.getElementById(parentDivId);
  const allChildren = Array.from(parent.children);

  allChildren.forEach((childEl) => {
    const currentId = childEl.id;
    if (desiredDivIds.includes(currentId)) {
      currentChildIdxInDesiredList = desiredDivIds.indexOf(currentId);
      parent.appendChild(childEl);
      if (desiredDivIds[currentChildIdxInDesiredList + 1] === "hr") {
        const hrElement = document.createElement("hr");
        hrElement.style.cssText = "margin-bottom: 2rem;";
        parent.appendChild(hrElement);
      }
    }
    if (removeDivIds.includes(currentId)) {
      childEl.remove();
    }
  });
};
