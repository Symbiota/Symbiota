import type { Page, Locator } from '@playwright/test';
import { Form } from "./Form";
import { getSuite, Suite } from '../types/Suite';

const mediaFields = {
	removeimg: 'checkbox',
	caption: 'text',
	creatorUid: 'select',
	creator: 'text',
	notes: 'text',
	copyright: 'text',
	sourceUrl: 'text',
	url: 'text',
	weburl: 'text',
	renameweburl: 'checkbox',
	originalUrl: 'text',
	renameorigurl: 'checkbox',
	thumbnailUrl: 'text',
	renametnurl: 'checkbox',
	sortOccurrence: 'text',
	ch_HasOrganism: 'checkbox',
	ch_HasLabel: 'checkbox',
	ch_HasIDLabel: 'checkbox',
	ch_TypedText: 'checkbox',
	ch_Handwriting: 'checkbox',
	ch_ShowsHabitat: 'checkbox',
	ch_HasProblem: 'checkbox',
	ch_Diagnostic: 'checkbox',
	ch_ImageOfAdult: 'checkbox',
	ch_ImageOfImmature: 'checkbox',
}

/* Abstract class */
export abstract class MediaForm extends Form {
	protected submitDeleteButton: Locator;
	protected submitEditButton: Locator;
	protected submitRemapBlankButton: Locator;
	protected submitDisassociateButton: Locator;
	protected submitNewButton: Locator;

	protected openEditFormToggle: Locator;

	public readonly DELETE_SUCCESS_MSG = "Media deleted successfully";
	public readonly NEW_SUCCESS_MSG = "Media added successfully";

	static make(page: Page): MediaForm {
		switch(getSuite()) {
			case Suite.Laravel: 
				throw new Error('ERROR: ' + Suite.Laravel + ' SUITE: NOT IMPLEMENTED');
			default: 
				return new SymbMediaForm(page);
		}
	}

	async submitEdit() { return this.submitEditButton.click({force: true})}
	async submitDelete() { return this.submitDeleteButton.click({force: true})}
	async submitRemapBlank() { return this.submitRemapBlankButton.click({force: true})}
	async submitDisassociate() { return this.submitDisassociateButton.click({force: true})}
	async submitNew() { return this.submitNewButton.click({force: true})}

	// Warning will not work with multiple media because not unique
	async openEditForm() { return this.openEditFormToggle.click({force: true}) }
}

class SymbMediaForm extends MediaForm {
	public readonly DELETE_SUCCESS_MSG = "Media deleted successfully";
	public readonly NEW_SUCCESS_MSG = "Media added successfully";

	constructor(page: Page) {
		super(page, mediaFields);

		this.submitEditButton = this.form.locator('button[value="Submit Image Edits"]');
		this.submitDeleteButton = this.form.locator('button[value="Delete Image"]');
		this.submitRemapBlankButton = this.form.locator('button[value="remapImageToNewRecord"]');
		this.submitDisassociateButton = this.form.locator('button[value="Disassociate Image"]');
		this.submitNewButton = this.form.locator('button[value="Submit New Image"]');

		// Warning will not work with multiple media because not unique
		this.openEditFormToggle = page.locator('div[title="Edit Resource MetaData"]');
	}
}
