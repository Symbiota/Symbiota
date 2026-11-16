import { expect, mergeTests } from "@playwright/test";
import { test as testTaxonomyCreation } from "./fixtures/taxon";
import { test as testWithAdmin } from "./fixtures/adminLogin";
import { TaxonCreationPage } from "./pages/TaxonCreationPage";
import { Seeder, Tables } from "./seeders/Seeder";

const test = mergeTests(testWithAdmin, testTaxonomyCreation);

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
});

test("Cultivar epithet and tradename are not visible for subspecies taxon rank selection", async ({
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

test("Cultivar epithet and tradename labels are visible when cultivar taxon rank is selected", async ({
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

test("Cannot create a taxon that already exists in the database", async ({
  page,
  DB,
}) => {
  // Clean up any existing entries first to avoid duplicates
  await Seeder.deleteTaxonIfExists("Plantae", 10, "Plantae", DB);
  await Seeder.deleteTaxonIfExists("Testaceae", 140, "Plantae", DB);

  // seed the database with Plantae kingdom
  const matchTid = await Seeder.taxonWithStatus(
    {
      sciName: "Plantae",
      unitName1: "Plantae",
      kingdomName: "Plantae",
      rankID: 10,
      author: "",
      securityStatus: 0,
    },
    undefined, // no parent for kingdom
    DB,
  );

  const familyTid = await Seeder.taxonWithStatus(
    {
      sciName: "Testaceae",
      unitName1: "Testaceae",
      kingdomName: "Plantae",
      rankID: 140,
      author: "",
      securityStatus: 0,
    },
    matchTid,
    DB,
  );

  // Try to create the Testaceae family again, but it already exists
  const taxonCreationPage = TaxonCreationPage.make(page);
  await taxonCreationPage.goto();
  await taxonCreationPage.taxonCreationForm.setMany({
    quickparser: "Testaceae",
  });
  await taxonCreationPage.parseButton.click({ force: true });
  const parentNameField = await taxonCreationPage.getElementById("parentname");
  await parentNameField.fill("Plantae");

  // The autocomplete API behaves really weirdly in playwright; 
  // a lot of weirdness below seemed required to get the form validation to think that a parenttid had indeed been selected
  const suggestions = await page.evaluate(async () => {
    const response = await fetch(
      "/taxa/taxonomy/rpc/gettaxasuggest.php?term=Plantae&rhigh=140",
    );
    return await response.json();
  });
  const actualParentId = suggestions.length > 0 ? suggestions[0].id : "1";

  await page.evaluate((parentId) => {
    const parentTidInput = document.getElementById(
      "parenttid",
    ) as HTMLInputElement;
    const parentNameInput = document.getElementById(
      "parentname",
    ) as HTMLInputElement;
    parentTidInput.value = parentId;
    parentNameInput.value = "Plantae";

    // Force the form to recognize the change
    const changeEvent = new Event("change", { bubbles: true });
    parentTidInput.dispatchEvent(changeEvent);
    parentNameInput.dispatchEvent(changeEvent);
  }, actualParentId);

  await taxonCreationPage.submitCreateTaxon();
  await expect(taxonCreationPage.errorMessage).toBeVisible();
  await expect(taxonCreationPage.errorMessage).toContainText("already exists");

  await Seeder.remove(familyTid, Tables.TaxStatus, DB);
  await Seeder.remove(familyTid, Tables.Taxa , DB);
  await Seeder.remove(matchTid, Tables.TaxStatus, DB);
  await Seeder.remove(matchTid, Tables.Taxa , DB);
});
