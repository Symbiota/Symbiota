import { expect, mergeTests } from "@playwright/test";
// import { test as testTaxonomyCreation } from "./fixtures/collection";
import { test as testWithAdmin } from "./fixtures/adminLogin";
import { TaxonCreationPage } from "./pages/TaxonCreationPage";

const test = mergeTests(testWithAdmin);

test.beforeEach(async ({ adminLogin }) => await adminLogin.expectLoggedIn());

test("Quick parser populates species", async ({ page }) => {
  const taxonCreationPage = TaxonCreationPage.make(page);
  await taxonCreationPage.goto();
  await taxonCreationPage.taxonCreationForm.setMany({
    quickparser: "Testus taxonus",
  });
  await taxonCreationPage.parseButton.click({ force: true });
  await expect(
    taxonCreationPage.taxonCreationForm.getFieldLocator("unitname3"),
  ).not.toBeVisible();
  const expectedPopulatedFields = {
    quickparser: "",
    rankid: "220",
    unitname1: "Testus",
    unitname2: "taxonus",
  };
  await taxonCreationPage.taxonCreationForm.checkMany(
    expectedPopulatedFields,
    false,
  );
});

test("Quick parser populates subspecies and the unitnam3 field appears", async ({
  page,
}) => {
  const taxonCreationPage = TaxonCreationPage.make(page);
  await taxonCreationPage.goto();
  await taxonCreationPage.taxonCreationForm.setMany({
    quickparser: "Testus taxonus testensis",
  });
  await taxonCreationPage.parseButton.click({ force: true });
  await expect(
    taxonCreationPage.taxonCreationForm.getFieldLocator("unitname3"),
  ).toBeVisible();
  const expectedPopulatedFields = {
    quickparser: "",
    rankid: "230",
    unitname1: "Testus",
    unitname2: "taxonus",
    unitname3: "testensis",
  };
  await taxonCreationPage.taxonCreationForm.checkMany(
    expectedPopulatedFields,
    false,
  );
  await expect(
    await taxonCreationPage.getElementById("unitind3"),
  ).toBeVisible();
  await expect(
    await taxonCreationPage.getElementById("cultivarEpithet"),
  ).not.toBeVisible();
  await expect(
    await taxonCreationPage.getElementById("tradeName"),
  ).not.toBeVisible();
});

test("Cultivar epithet and tradename labels are only visible for cultivar taxon rank", async ({
  page,
}) => {
  const taxonCreationPage = TaxonCreationPage.make(page);
  await taxonCreationPage.goto();
  await taxonCreationPage.taxonCreationForm.setMany({
    quickparser: "Testus taxonus testensis",
  });
  await taxonCreationPage.parseButton.click({ force: true });
  await (await taxonCreationPage.getElementById("rankid")).selectOption("300");
  await expect(
    await taxonCreationPage.getElementById("cultivarEpithet"),
  ).toBeVisible();
  await expect(
    await taxonCreationPage.getElementById("tradeName"),
  ).toBeVisible();
});
