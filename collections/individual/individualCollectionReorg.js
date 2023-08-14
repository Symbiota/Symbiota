const reorderElements = (parentDivId, desiredDivIds, removeDivIds) => {
  const parent = document.getElementById(parentDivId);
  const allChildren = Array.from(parent.children);
  const hrElement = document.createElement("hr");

  allChildren.forEach((childEl) => {
    // const currentChild = document.getElementById(childEl);
    const currentId = childEl.id;
    // console.log("deleteMe currentId is: ");
    // console.log(currentId);
    if (desiredDivIds.includes(currentId)) {
      currentChildIdx = desiredDivIds.indexOf(currentId);
      console.log("deleteMe currentChildIdx is: ");
      console.log(currentChildIdx);
      parent.appendChild(childEl);
      if (desiredDivIds[currentChildIdx + 1] === "hr")
        parent.appendChild(hrElement);
    }
    if (removeDivIds.includes(currentId)) {
      childEl.remove();
    }
  });

  // Reorder div elements
  //   parent.appendChild(secondDiv); // Move secondDiv to the end
  //   parent.insertBefore(thirdDiv, firstDiv); // Move thirdDiv before firstDiv
};
