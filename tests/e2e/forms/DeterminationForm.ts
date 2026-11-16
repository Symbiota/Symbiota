import { type Page, type Locator, expect } from '@playwright/test';
import { Form } from "./Form";
import { getSuite, Suite } from '../types/Suite';

const determinationFields = {
	sciname: 'text',
	identifiedBy: 'text',
	dateIdentified: 'text'
}

export abstract class DeterminationForm extends Form {
	protected submitButton: Locator;

	static make(page: Page): DeterminationForm {
		switch(getSuite()) {
			case Suite.Laravel:
				throw new Error('ERROR: ' + Suite.Laravel + ' SUITE: NOT IMPLEMENTED');
			default:
				return new SymbDeterminationForm(page);
		}
	}

	async submit() { return this.submitButton.click({force: true})}

	abstract checkNewSuccess(): Promise<void>;
	abstract checkDeleteSuccess(): Promise<void>;

	abstract setToNew(): Promise<void>;
	abstract setToEdit(detId: number): Promise<void>;
	abstract setToDelete(detId: number): Promise<void>;

	abstract openEditForm(detId: number): Promise<void>;
}

export class SymbDeterminationForm extends DeterminationForm {
	public readonly NEW_SUCCESS_MSG = "Determination submitted successfully";
	public readonly DELETE_SUCCESS_MSG = "Determination deleted successfully";

	protected fieldSelectorOverrides = {
		identifiedBy: 'input[name=identifiedby]',
		dateIdentified: 'input[name=dateidentified]'
	}

	constructor(page: Page) {
		super(page, determinationFields);
	}

	async checkNewSuccess() {
		await expect(this.page.getByText(this.NEW_SUCCESS_MSG)).toBeVisible();
	}

	async checkDeleteSuccess() {
		await expect(this.page.getByText(this.DELETE_SUCCESS_MSG)).toBeVisible();
	}

	async openEditForm(detId: number) {
		const detDiv= this.page.locator(`div[id=detdiv-${detId}]`);
		await detDiv.locator('a[title="Edit Determination"]').click({force: true});
	}

	async setToNew() {
		this.setScope('form[name=detaddform]');
		this.submitButton = this.form.locator('button[name=submitaction]')
	}

	async setToEdit(detId: number) {
		this.setScope(`div[id=editdetdiv-${detId}]`);
		this.submitButton = this.form.locator('button[name="deteditform"]')
	}

	async setToDelete(detId: number) {
		this.setScope(`div[id=editdetdiv-${detId}]`);
		this.submitButton = this.form.locator('button[value="Delete Determination"]')
	}
}
