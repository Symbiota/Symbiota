import { expect, type Page } from '@playwright/test';
import { getSuite, Suite } from '../types/Suite';
import { Form } from '../forms/Form';

const searchFields = {
	taxa: 'text',
	taxontype: 'select',
	usethes: 'checkbox',
	locality: 'text',
	country: 'text',
	state: 'text',
	county: 'text',
	local: 'text',
	elevlow: 'text',
	elevhigh: 'text',
	upperlat: 'text',
	upperlat_NS: 'select',
	bottomlat: 'text',
	bottomlat_NS: 'select',
	leftlong: 'text',
	leftlong_EW: 'select',
	rightlong: 'text',
	rightlong_EW: 'select',
	pointlat: 'text',
	pointlat_NS: 'select',
	pointlong: 'text',
	pointlong_EW: 'select',
	radius: 'text',
	radiusunits: 'select',
	footprintGeoJson: 'textarea',
	eventdate1: 'text',
	eventdate2: 'text',
	collector: 'text',
	collnum: 'text',
	includeothercatnum: 'checkbox',
	catnum: 'text',
	typestatus: 'checkbox',
	hasimages: 'checkbox',
	hasaudio: 'checkbox',
	hasgenetic: 'checkbox',
	hascoords: 'checkbox',
	includecult: 'checkbox',
	'association-type': 'select',
	'associated-taxa': 'text',
	'taxontype-association': 'select',
	'usethes-associations': 'checkbox',
	'db[]': 'text',
	'display-format-pref': 'text',
};

export abstract class CollectionSearchPage {
	searchForm: Form
	abstract search(): Promise<void>
	abstract setTableResult(): Promise<void>;
	abstract setListResult(): Promise<void>;
	abstract expectListResult(): Promise<void>;
	abstract expectTableResult(): Promise<void>;
	abstract expectListCount(count: number): Promise<void>;
	abstract expectTableCount(count: number): Promise<void>;
	abstract expandAll(): Promise<void>;
	abstract setAllCollections(value: boolean): Promise<void>;
	abstract selectCollection(collId: number): Promise<void>;
	abstract goto(): Promise<void>;

	constructor(public readonly page: Page) {
		this.searchForm = new Form(page, searchFields);
	}

	static make(page: Page): CollectionSearchPage {
		switch(getSuite()) {
			case Suite.Laravel:
				throw new Error('ERROR: ' + Suite.Laravel + ' SUITE: NOT IMPLEMENTED');
			default:
				return new SymbCollectionSearchPage(page);
		}
	}
}

class SymbCollectionSearchPage extends CollectionSearchPage {
	async search(): Promise<void> {
		await this.page.locator('#search-btn').click({force: true})
	}

	async setTableResult(): Promise<void> {
		await this.page.locator('#table-button').click({force: true})
	}

	async setListResult(): Promise<void> {
		await this.page.locator('#list-button').click({force: true})
	}

	async expectListResult(): Promise<void>{
		await this.page.waitForURL('**/collections/list.php');
	}

	async expectTableResult(): Promise<void> {
		await this.page.waitForURL('**/collections/listtabledisplay.php');
	}

	async expectListCount(count: number): Promise<void> {
		await expect(this.page.getByText(`of ${count}`).nth(0)).toBeVisible();
	}

	async expectTableCount(count: number): Promise<void> {
		await expect(this.page.getByText(`of ${count}`).nth(0)).toBeVisible();
	}

	async expandAll(): Promise<void> {
		await this.page.locator('#expand-all-button').click({force: true})
	}

	async setAllCollections(value: boolean): Promise<void> {
		const checkbox = this.page.locator('#all_collections');
		await expect(checkbox).toBeVisible();
		await expect(checkbox).toBeEnabled();
		await checkbox.setChecked(value, {force: true});
	}

	async selectCollection(collId: number): Promise<void> {
		const expand = this.page.locator('span[id*="open_toggle"]');
		for (const el of await expand.all()) {
			await el.isVisible();
			await el.click({force: true});
		}

		const checkbox = this.page.locator(`input[name="db[]"][value="${collId}"]`);
		await checkbox.isVisible();
		await checkbox.setChecked(true, {force: true});
	}

	async goto(): Promise<void> {
		await this.page.goto('/collections/search/index.php')
	};
}
