import type { Page } from '@playwright/test';

export enum OccurrenceEditorTab {
	Occurrence = 'occTab',
	Determinations = 'detTab',
	Media = 'imgTab',
	LinkResources = 'resourceTab',
	Admin = 'adminTab'
}

export class OccurrenceEditorPage {
	constructor(public readonly page: Page) {}

	async gotoNew(collId: number) {
		await this.page.goto('collections/editor/occurrenceeditor.php?gotomode=1&collid=' + collId);
	}

	async gotoRecord(collId: number, occId: number) {
		await this.page.goto(`collections/editor/occurrenceeditor.php?csmode=0&occindex=0&occid=${occId}&collid=${collId}`);
	}

	async gotoImageSubmit(collId: number) {
		await this.page.goto(`/collections/editor/imageoccursubmit.php?collid=${collId}`);
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
}
