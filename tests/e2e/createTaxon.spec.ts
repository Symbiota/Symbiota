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
