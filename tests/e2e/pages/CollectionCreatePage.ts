import type { Page, Locator } from '@playwright/test';
import { expect } from '@playwright/test';
import { Form } from '../forms/Form';

interface CollectionCreatePage {
	collectionForm: Form
	setToLiveManaged(): Promise<void>
	setToSnapshot(): Promise<void>;
	setToAggregate(): Promise<void>;
	goto(): Promise<void>;
}

export class SymbCollectionCreatePage implements CollectionCreatePage {
	private readonly submitButton: Locator;
	private readonly liveManagedRadio: Locator;
	private readonly snapshotRadio: Locator;
	private readonly aggregateRadio: Locator;

	collectionForm: Form

	private fieldLocators = {};

	public readonly fields = {
		institutionCode: 'text',
		collectionCode: 'text',
		collectionName: 'text',
	}

	constructor(public readonly page: Page) {
		for(let fieldName of Object.keys(this.fields)) {
			this.fieldLocators[fieldName] = this.page.locator('input[name=' + fieldName + ']');
		}
		this.submitButton = this.page.locator('button[value=newCollection]');
		this.liveManagedRadio = this.page.locator('input[id=liveData]');
		this.snapshotRadio = this.page.locator('input[name=snapshot]');
		this.aggregateRadio = this.page.locator('input[name=aggregate]');
	}

	async setToLiveManaged() { this.liveManagedRadio.click({force: true}); }
	async setToSnapshot() { this.snapshotRadio.click({force: true}); }
	async setToAggregate() { this.aggregateRadio.click({force: true}); }

	async goto() {
		await this.page.goto('/collections/misc/collmetadata.php');
	}

	async set(fieldName, value) {
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

	async setMany(fields) {
		for(let [key, value] of Object.entries(fields)) {
			await this.set(key, value);
		}
	}

	async submitCreate() {
		await this.submitButton.click({force: true});
	}

	async checkMany(fields) {
		for(let [fieldName, value] of Object.entries(fields)) {
			expect(this.fields).toHaveProperty(fieldName);
			await expect(this.fieldLocators[fieldName]).toHaveValue(value);
		}
	}
}
