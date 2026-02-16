import { type Page, type Locator, expect } from '@playwright/test';

export class Form {
	public form: Locator;

	constructor(public readonly page: Page, public readonly fields: Object) {
		this.form = this.page.locator('body');
	}

	private getFieldLocator(fieldName: string): Locator {
		expect(this.fields).toHaveProperty(fieldName);

		return this.form.locator('input[name=' + fieldName + ']');
	}

	async set(fieldName: string, value: any) {
		const locator = this.getFieldLocator(fieldName);

		switch (this.fields[fieldName]) {
			case 'select':
				await locator.selectOption(value);
				break;
			case 'checkbox':
				await locator.setChecked(value);
				break;
			case 'text':
				await locator.fill(value);
				break;
			default:
				break;
		}
	}

	setScope(selector: string) {
		this.form = this.page.locator(selector);
	}

	async setMany(fields: Object) {
		for(let [key, value] of Object.entries(fields)) {
			await this.set(key, value);
		}
	}

	async checkMany(fields: Object) {
		for(let [fieldName, value] of Object.entries(fields)) {
			await expect(this.getFieldLocator(fieldName)).toHaveValue(value)
		}
	}

	async setFile(name: string, path: string) {
		const fileChooserPromise = this.page.waitForEvent('filechooser');
		await this.form.locator(`input[name="${name}"]`).click({force: true});

		const fileChooser = await fileChooserPromise;
		await fileChooser.setFiles(path);
	}
}
