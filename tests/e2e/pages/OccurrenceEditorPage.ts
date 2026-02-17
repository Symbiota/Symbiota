import { expect, type Page } from '@playwright/test';
import { MediaForm } from '../forms/MediaForm';
import { getSuite, Suite } from '../types/Suite';
import { OccurrenceForm } from '../forms/OccurrenceForm';
import { DeterminationForm } from '../forms/DeterminationForm';

export enum OccurrenceEditorTab {
	Occurrence = 'occTab',
	Determinations = 'detTab',
	Media = 'imgTab',
	LinkResources = 'resourceTab',
	Admin = 'adminTab'
}

export abstract class OccurrenceEditorPage {
	occurForm: OccurrenceForm;
	mediaForm: MediaForm;
	detForm: DeterminationForm;
	collId: Number;
	occId: Number;
	mediaIds: Array<Number> = [];
	detIds: Array<Number> = [];

	constructor(public readonly page: Page) {
		this.mediaForm = MediaForm.make(page);
		this.detForm= DeterminationForm.make(page);
		this.occurForm = new OccurrenceForm(page);
	}

	static make(page: Page): OccurrenceEditorPage {
		switch(getSuite()) {
			case Suite.Laravel:
				throw new Error('ERROR: ' + Suite.Laravel + ' SUITE: NOT IMPLEMENTED');
			default:
				return new SymbOccurrenceEditorPage(page);
		}
	}

	abstract gotoNew(collId: Number): Promise<void>;
	abstract gotoRecord(collId: Number, occId: Number): Promise<void>;
	abstract gotoImageSubmit(collId: Number): Promise<void>;
	abstract gotoSkeletalSubmit(collId: Number): Promise<void>;
	abstract gotoTab(newTab: OccurrenceEditorTab): Promise<void>;
	abstract getSkeletalOccid(): Promise<Number>;
	abstract getSkeletalImageOccid(): Promise<Number>;

	abstract swapToMediaEnterUrl(): Promise<void>;

	abstract setGotoRecord(): Promise<void>;

	abstract checkRecordSuccess(): Promise<void>;
}

export class SymbOccurrenceEditorPage extends OccurrenceEditorPage {
	async gotoNew(collId: Number) {
		await this.page.goto('collections/editor/occurrenceeditor.php?gotomode=1&collid=' + collId);
	}

	async gotoRecord(collId: Number, occId: Number) {
		await this.page.goto(`collections/editor/occurrenceeditor.php?csmode=0&occindex=0&occid=${occId}&collid=${collId}`);
	}

	async gotoImageSubmit(collId: Number) {
		await this.page.goto(`/collections/editor/imageoccursubmit.php?collid=${collId}`);
	}

	async gotoSkeletalSubmit(collId: Number) {
		await this.page.goto(`/collections/editor/skeletalsubmit.php?collid=${collId}`);
	}

	async gotoTab(newTab: OccurrenceEditorTab) {
		await this.page.locator(`li[id="${newTab}"]`).click({force: true});

		// Wait for ajax to load except for Occurrence tab
		if(OccurrenceEditorTab.Occurrence != newTab) {
			await this.page.getByText('Loading...').waitFor({ state: "detached" });
		}
	}

	async getSkeletalOccid() {
		const newRecordLink = await this.page.waitForSelector('div[id="occurlistdiv"] a[id*="a-"]', { state: 'attached' });
		const id = await newRecordLink.getAttribute('id');
		return id? parseInt(id.replace('a-', '')): 0;
	}

	async getSkeletalImageOccid() {
		const newRecordLink = this.page.locator('a[href*="occurrenceeditor.php"]');
		return parseInt(await newRecordLink.innerText());
	}

	async swapToMediaEnterUrl() {
		await this.page.getByText("Enter Url").click({force: true});
	}

	async setGotoRecord() {
		await this.page.locator('input[name=gotomode][value="0"]').click({force: true});
	}

	async checkRecordSuccess() {
		await expect(this.page.getByText('Public Display')).toBeVisible();
	}
}
