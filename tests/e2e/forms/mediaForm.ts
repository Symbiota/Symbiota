import type { Page, Locator } from '@playwright/test';
import { Form } from "./form";

const mediaFields = {
	removeimg: 'checkbox',
	caption: 'text',
	creatorUid: 'select',
	creator: 'text',
	notes: 'text',
	copyright: 'text',
	sourceUrl: 'text',
	url: 'text',
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

export class MediaForm extends Form {
	private readonly submitDeleteButton: Locator;
	private readonly submitEditButton: Locator;
	private readonly submitRemapBlankButton: Locator;
	private readonly submitDisassociateButton: Locator;
	private readonly submitNewButton: Locator;

	public readonly DELETE_SUCCESS_MSG = "Media deleted successfully";
	public readonly NEW_SUCCESS_MSG = "Media added successfully";

	constructor(selector: string, page: Page) {
		super(selector, page, mediaFields);

		this.submitEditButton = this.form.locator('button[value="Submit Image Edits"]');
		this.submitDeleteButton = this.form.locator('button[value="Delete Image"]');
		this.submitRemapBlankButton = this.form.locator('button[value="remapImageToNewRecord"]');
		this.submitDisassociateButton = this.form.locator('button[value="Disassociate Image"]');
		this.submitNewButton = this.form.locator('button[value="Submit New Image"]');
	}

	async submitEdit() { return this.submitEditButton.click({force: true})}
	async submitDelete() { return this.submitDeleteButton.click({force: true})}
	async submitRemapBlank() { return this.submitRemapBlankButton.click({force: true})}
	async submitDisassociate() { return this.submitDisassociateButton.click({force: true})}
	async submitNew() { return this.submitNewButton.click({force: true})}

	// Warning will not work with multiple media because not unique
	async openEditForm() { 
		return this.page.locator('div[title="Edit Resource MetaData"]').click({force: true}) 
	}
}
