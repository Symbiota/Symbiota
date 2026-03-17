import { expect, Locator, type Page } from "@playwright/test";
import { Form } from "../forms/Form";
import { getSuite, Suite } from "../types/Suite";

const taxonCreationFields = {
  quickparser: "text",
  sciname: "text",
  rankid: "select",
  unitind1: "checkbox",
  unitname1: "text",
  unitind2: "checkbox",
  unitname2: "text",
  author: "text",
  unitind3: "text",
  unitname3: "text",
  cultivarEpithet: "text",
  tradeName: "text",
  parentname: "text",
  parentid: "text",
  notes: "text",
  source: "text",
  securityStatus: "select",
  isaccepted: "checkbox",
  isnotaccepted: "checkbox",
  acceptedstr: "text",
  tidaccepted: "text",
  unacceptabilityreason: "text",
};

export abstract class TaxonCreationPage {
  taxonCreationForm: Form;
  submitButton: Locator;
  parseButton: Locator;
  abstract submitCreateTaxon(): Promise<void>;
  abstract expectSuccess(): Promise<void>;
  abstract expectError(): Promise<void>;
  abstract goto(): Promise<void>;

  constructor(public readonly page: Page) {
    this.taxonCreationForm = new Form(this.page, taxonCreationFields);
    this.submitButton = this.page.locator("button[value=submitNewName]");
    this.parseButton = this.page.locator("button[id=quick-parse-button]");
  }

  static make(page: Page): TaxonCreationPage {
    switch (getSuite()) {
      case Suite.Laravel:
        throw new Error("ERROR: " + Suite.Laravel + " SUITE: NOT IMPLEMENTED"); // @TODO create LaravelTaxonCreationPage class
      default:
        return new SymbTaxonCreationPage(page);
    }
  }
}

class SymbTaxonCreationPage extends TaxonCreationPage {
  async submitCreateTaxon(): Promise<void> {
    await this.submitButton.click({ force: true });
  }
  async goto(): Promise<void> {
    await this.page.goto("/taxa/taxonomy/taxonomyloader.php"); // adjust URL as needed
  }

  async expectSuccess(): Promise<void> {
    await expect(this.page.locator("text=Success")).toBeVisible(); // adjust selector
  }

  async expectError(): Promise<void> {
    await expect(this.page.locator('[role="alert"]')).toBeVisible(); // adjust selector
  }
}
