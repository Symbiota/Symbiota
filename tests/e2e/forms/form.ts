import { type Page, type Locator, expect } from '@playwright/test';

export class Form {
	public form: Locator;
	private fieldLocators = {};

	constructor(
		public readonly selector: string,
		public readonly page: Page,
		public readonly fields: Object
	) {
		for(let fieldName of Object.keys(this.fields)) {
			this.form = this.page.locator(selector);
			this.fieldLocators[fieldName] = this.form.locator('input[name=' + fieldName + ']');
		}
	}

	async set(fieldName: string, value: number|string|boolean) {
		expect(this.fields).toHaveProperty(fieldName);

		switch (this.fields[fieldName]) {
			case 'select':
				await this.fieldLocators[fieldName].selectOption(value);
				break;
			case 'checkbox':
				await this.fieldLocators[fieldName].setChecked(value);
				break;
			case 'text':
				await this.fieldLocators[fieldName].fill(value);
				break;
			default:
				break;
		}
	}

	async setMany(fields: Object) {
		for(let [key, value] of Object.entries(fields)) {
			await this.set(key, value);
		}
	}

	async checkMany(fields: Object) {
		for(let [fieldName, value] of Object.entries(fields)) {
			expect(this.fields).toHaveProperty(fieldName);
			await expect(this.fieldLocators[fieldName]).toHaveValue(value);
		}
	}

	async setFile(name: string, path: string) {
		const fileChooserPromise = this.page.waitForEvent('filechooser');
		await this.form.locator(`input[name="${name}"]`).click({force: true});

		const fileChooser = await fileChooserPromise;
		await fileChooser.setFiles(path);
	}
}
