import { expect, type Page } from '@playwright/test';
import { test as base, Seeder } from '../seeders/Seeder';
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
	collId: number;
	occId: number;
	mediaIds: Array<number> = [];
	detIds: Array<number> = [];

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

	abstract gotoNew(collId: number): Promise<void>;
	abstract gotoRecord(collId: number, occId: number): Promise<void>;
	abstract gotoImageSubmit(collId: number): Promise<void>;
	abstract gotoObservationImageSubmit(collId: number): Promise<void>;
	abstract gotoSkeletalSubmit(collId: number): Promise<void>;
	abstract gotoTab(newTab: OccurrenceEditorTab): Promise<void>;
	abstract getSkeletalOccid(): Promise<number>;
	abstract getSkeletalImageOccid(): Promise<number>;
	abstract getObservationImageOccid(): Promise<number>;

	abstract swapToMediaEnterUrl(): Promise<void>;

	abstract setGotoRecord(): Promise<void>;

	abstract checkRecordSuccess(): Promise<void>;
	abstract deleteOccurrence(): Promise<void>;
}

export class SymbOccurrenceEditorPage extends OccurrenceEditorPage {
	async gotoNew(collId: number) {
		await this.page.goto('collections/editor/occurrenceeditor.php?gotomode=1&collid=' + collId);
	}

	async gotoRecord(collId: number, occId: number) {
		await this.page.goto(`collections/editor/occurrenceeditor.php?csmode=0&occindex=0&occid=${occId}&collid=${collId}`);
	}

	async gotoImageSubmit(collId: number) {
		await this.page.goto(`/collections/editor/imageoccursubmit.php?collid=${collId}`);
	}

	async gotoObservationImageSubmit(collId: number) {
		await this.page.goto(`/collections/editor/observationsubmit.php?collid=${collId}`);
	}

	async gotoSkeletalSubmit(collId: number) {
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

	async getObservationImageOccid() {
		await expect(this.page.getByText('Occurrence Details Viewer')).toBeVisible();
		const newRecordLink = this.page.locator('a[href*="individual/index.php"]');
		const href = await newRecordLink.getAttribute('href');
		const search_parts = href?.split('?')?.[0]?.split('&');

		let id = 0;
		if(search_parts) {
			search_parts.forEach(element => {
				const param_parts = element.split('=');
				if(param_parts && param_parts.length > 1 && param_parts[0] === 'occid') {
					id = parseInt(param_parts[1]);
				}
			});
		}

		return id;
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

	async deleteOccurrence() {
		await this.gotoTab(OccurrenceEditorTab.Admin);
		await this.page.locator('button[name=verifydelete]').click();
		this.page.on('dialog', dialog => dialog.accept());
		await this.page.locator('button[value="Delete Occurrence"]').click();
	}
}

export const test = base.extend<{
	occurrenceEditor: OccurrenceEditorPage, 

	occurrenceSkeletalNew: OccurrenceEditorPage,

	editOccurrence: OccurrenceEditorPage
	submitEditOccurrence: OccurrenceEditorPage
	createOccurrence: OccurrenceEditorPage

	editDet: OccurrenceEditorPage
	newDet: OccurrenceEditorPage

	newMedia: OccurrenceEditorPage
	editMedia: OccurrenceEditorPage
}>({
	occurrenceEditor: async ({ collId, page }, use) => {
		const occurrenceEditor = OccurrenceEditorPage.make(page);
		occurrenceEditor.collId = collId;
		await use(occurrenceEditor);
	},
	/* OCCURRENCE STATES */
	createOccurrence: async ({ occurrenceEditor }, use) => {
		await occurrenceEditor.gotoNew(occurrenceEditor.collId);
		const catalogNumber = test.info().workerIndex + '000001';
		await occurrenceEditor.occurForm.set('catalognumber', catalogNumber);
		await use(occurrenceEditor)
		await occurrenceEditor.setGotoRecord()
		await occurrenceEditor.occurForm.submitNew();
		await occurrenceEditor.checkRecordSuccess();
		await occurrenceEditor.occurForm.checkSetFields()
	},
	editOccurrence: async ({ occurrenceEditor, occId }, use) => {
		occurrenceEditor.occId = occId;
		await occurrenceEditor.gotoRecord(occurrenceEditor.collId, occId)
		await use(occurrenceEditor);
	},
	submitEditOccurrence: async ({ editOccurrence, page}, use) => {
		const catalogNumber = test.info().workerIndex + '000002';
		await editOccurrence.occurForm.set('catalognumber', catalogNumber);
		await use(editOccurrence);
		await editOccurrence.occurForm.submitEdit();
		await expect(page.getByText(editOccurrence.occurForm.EDIT_SUCCESS)).toBeVisible();
		await editOccurrence.occurForm.checkSetFields();
	},

	/* SKELETAL STATES */
	occurrenceSkeletalNew: async ({ occurrenceEditor }, use) => {
		await occurrenceEditor.gotoSkeletalSubmit(occurrenceEditor.collId);
		await use(occurrenceEditor);
		await occurrenceEditor.occurForm.submitSkeletal();
		const occId = await occurrenceEditor.getSkeletalOccid();
		await occurrenceEditor.gotoRecord(occurrenceEditor.collId, occId)
		await occurrenceEditor.occurForm.checkSetFields();
	},

	/* DETERMINATION STATES */
	editDet: async ({ editOccurrence }, use) => {
		await editOccurrence.gotoTab(OccurrenceEditorTab.Determinations)
		await use(editOccurrence)
	},
	newDet: async ({ editOccurrence, occId, DB}, use) => {
		await editOccurrence.gotoTab(OccurrenceEditorTab.Determinations);
		await editOccurrence.detForm.setToNew();
		await use(editOccurrence);
		await editOccurrence.detForm.submit();
		await editOccurrence.detForm.checkNewSuccess();
		const dets = await Seeder.getDeterminations(occId, DB);

		expect(dets).toBeDefined();
		expect(dets.length).toBeGreaterThan(0);
		for(let [fieldName, value] of Object.entries(editOccurrence.detForm.setFields)) {
			expect(dets[0][fieldName]).toBe(value);
		}
	},

	/* MEDIA STATES */
	newMedia: async ({ editOccurrence, page}, use) => {
		await editOccurrence.gotoTab(OccurrenceEditorTab.Media)
		editOccurrence.mediaForm.setScope('form[name=imgnewform]');
		await use(editOccurrence)
		await editOccurrence.mediaForm.submitNew();
		await expect(page.getByText(editOccurrence.mediaForm.NEW_SUCCESS_MSG)).toBeVisible();
	},
	editMedia: async ({ editOccurrence, mediaId }, use) => {
		await editOccurrence.gotoTab(OccurrenceEditorTab.Media)
		editOccurrence.mediaForm.setScope(`div[id=img${mediaId}editdiv]`)
		await editOccurrence.mediaForm.openEditForm()
		await use(editOccurrence);
	},
});
