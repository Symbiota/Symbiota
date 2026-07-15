import { expect, Locator, type Page } from "@playwright/test";
import { nav } from "../fixtures/nav";
import { LocatorMap } from "../types";
import { test as base } from '../seeders/Seeder';
import { MediaForm } from "../forms/MediaForm";
import { getSuite, Suite } from "../types/Suite";

export enum TaxonomyEditorTab {
	CommonName = 'CommonName',
	Media = 'Media',
	MediaSort = 'MediaSort',
	AddMedia = 'AddMedia',
	Descriptions = 'Descriptions',
	Admin = 'Admin'
};

export abstract class TaxonomyEditor {
  mediaSubmit: Locator;

  MEDIA_ADD_SUCESS_MSG: string = '';

  constructor(public readonly page: Page, public readonly tabs: LocatorMap) {}

  static make(page: Page): TaxonomyEditor {
	  switch(getSuite()) {
		  case Suite.Laravel:
			  throw new Error('ERROR: ' + Suite.Laravel + ' SUITE: NOT IMPLEMENTED');
		  default:
			  return new SymbTaxonCreationPage(page);
	  }
  }

  async goto(tid: string|number) {
	await this.page.goto(nav().Taxonomy.Editor(tid));
  }

  async gotoTab(tab: TaxonomyEditorTab) {
	expect(this.tabs).toHaveProperty(tab);
	await this.tabs[tab].click({force: true});
  }

  abstract hasMedia(): Promise<void>;
}

export class SymbTaxonCreationPage extends TaxonomyEditor {
  constructor(public readonly page: Page) {
	  super(page, {
		  [TaxonomyEditorTab.CommonName]: page.locator('#ui-id-1'),
		  [TaxonomyEditorTab.Media]: page.locator('#ui-id-2'),
		  [TaxonomyEditorTab.MediaSort]: page.locator('#ui-id-4'),
		  [TaxonomyEditorTab.AddMedia]: page.locator('#ui-id-6'),
		  [TaxonomyEditorTab.Descriptions]: page.locator('#ui-id-8'),
	  })
	  
	  this.mediaSubmit = page.locator('#imgaddsubmit');
  }

  async hasMedia() {
	  await expect(this.page.getByText('Page 1')).toBeVisible();
  }
}

export const test = base.extend<{
	taxonomyEditor: TaxonomyEditor,
	addMedia: MediaForm,
}>({
	taxonomyEditor: async({ page, tid }, use) => {
		const taxonomyEditor = TaxonomyEditor.make(page);
		await taxonomyEditor.goto(tid);
		await use(taxonomyEditor);
	},
	addMedia: async({ page, taxonomyEditor, tid}, use) => {
		await taxonomyEditor.gotoTab(TaxonomyEditorTab.AddMedia)
		const mediaForm = MediaForm.make(page)
		await use(mediaForm);
		await taxonomyEditor.mediaSubmit.click();
		await taxonomyEditor.hasMedia();
	}
})
