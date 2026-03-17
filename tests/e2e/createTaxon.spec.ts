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
  await expect(taxonCreationPage.taxonCreationForm.getFieldLocator('unitname3')).not.toBeVisible();
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
  await expect(taxonCreationPage.taxonCreationForm.getFieldLocator('unitname3')).toBeVisible();
  const expectedPopulatedFields = {
    quickparser: "",
    rankid: "220",
    unitname1: "Testus",
    unitname2: "taxonus",
    unitname3: "testensis",
  };
  await taxonCreationPage.taxonCreationForm.checkMany(
    expectedPopulatedFields,
    false,
  );
});
