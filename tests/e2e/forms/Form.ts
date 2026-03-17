import { type Page, type Locator, expect } from "@playwright/test";

enum InputTypes {
  Select = "select",
  Text = "text",
  Area = "textarea",
  Checkbox = "checkbox",
}

export class Form {
  public form: Locator;
  public setFields: Object = {};
  protected fieldSelectorOverrides: Object = {};

  constructor(
    public readonly page: Page,
    public readonly fields: Object,
  ) {
    this.form = this.page.locator("body");
  }

  private getFieldLocator(fieldName: string): Locator {
    expect(this.fields).toHaveProperty(fieldName);

    if (this.fieldSelectorOverrides.hasOwnProperty(fieldName)) {
      return this.form.locator(this.fieldSelectorOverrides[fieldName]);
    } else {
      if (this.fields[fieldName] == InputTypes.Select) {
        return this.form.locator("select[name=" + fieldName + "]");
      } else if (this.fields[fieldName] == InputTypes.Area) {
        return this.form.locator("textarea[name=" + fieldName + "]");
      } else {
        return this.form.locator("input[name=" + fieldName + "]");
      }
    }
  }

  async set(fieldName: string, value: any) {
    const locator = this.getFieldLocator(fieldName);

    switch (this.fields[fieldName]) {
      case InputTypes.Select:
        await locator.selectOption(value);
        break;
      case InputTypes.Checkbox:
        await locator.setChecked(value);
        break;
      case InputTypes.Text:
        await locator.fill(value);
        break;
      case InputTypes.Area:
        await locator.fill(value);
        break;
      default:
        return;
    }

    this.setFields[fieldName] = value;
  }

  setScope(selector: string) {
    this.form = this.page.locator(selector);
  }

  async setMany(fields: Object) {
    for (let [key, value] of Object.entries(fields)) {
      await this.set(key, value);
    }
  }

  async check(fieldName, value) {
    const locator = this.getFieldLocator(fieldName);
    switch (this.fields[fieldName]) {
      case InputTypes.Checkbox:
        if (value) {
          await expect(locator).toBeChecked(value);
        } else {
          await expect(locator).not.toBeChecked(value);
        }
        break;
      default:
        await expect(locator).toHaveValue(value);
        return;
    }
  }

  async checkSetFields() {
    for (let [fieldName, value] of Object.entries(this.setFields)) {
      await this.check(fieldName, value);
    }
  }

  async checkMany(fields: Object, overrideSetFields: boolean = true) {
    for (let [fieldName, value] of Object.entries(fields)) {
      await expect(this.getFieldLocator(fieldName)).toHaveValue(value);
    }

    if (overrideSetFields) {
      this.setFields = {};
    }
  }

  async setFile(name: string, path: string) {
    const fileChooserPromise = this.page.waitForEvent("filechooser");
    await this.form.locator(`input[name="${name}"]`).click({ force: true });

    const fileChooser = await fileChooserPromise;
    await fileChooser.setFiles(path);
  }
}
