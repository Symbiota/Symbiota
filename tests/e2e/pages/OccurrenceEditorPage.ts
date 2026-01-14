import type { Page, Locator } from '@playwright/test';
import { expect } from '@playwright/test';

export enum OccurrenceEditorTab {
	Occurrence = 'occTab',
	Determinations = 'detTab',
	Media = 'imgTab',
	LinkResources = 'resourceTab',
	Admin = 'adminTab'
}

export class OccurrenceEditorPage {
	private readonly submitNewButton: Locator;
	private readonly submitEditButton: Locator;
	private activeTab = 'occurrence';

	private fieldLocators = {};

	public readonly fields = {
		catalognumber: 'text',
		recordedby: 'text',
		recordNumber: 'text',
		eventdate: 'text',
		eventdate2: 'text',
		associatedcollectors: 'text',
		verbatimeventdate: 'text',

		ffsciname: 'text',
		scientificnameauthorship: 'text',
		identificationqualifier: 'text',
		family: 'text',
		identifiedby: 'text',
		dateidentified: 'text',

		ffcountry: 'text',
		ffstate: 'text',
		ffcounty: 'text',
		ffmunicipality: 'text',
		locationid: 'text',
		fflocality: 'text',
		recordsecurity: 'checkbox',
		localautodeactivated: 'text',
		decimallatitude: 'text',
		decimallongitude: 'text',
		coordinateuncertaintyinmeters: 'text',
		geodeticdatum: 'text',
		verbatimcoordinates: 'text',
		minimumelevationinmeters: 'text',
		maximumelevationinmeters: 'text',
		minimumdepthinmeters: 'text',
		maximumdepthinmeters: 'text',
		verbatimdepth: 'text',

		habitat: 'text',
		substrate: 'text',
		associatedtaxa: 'text',
		verbatimattributes: 'text',
		occurrenceremarks: 'text',
		lifestage: 'text',
		sex: 'text',
		individualcount: 'text',
		samplingprotocol: 'text',
		preparations: 'text',
		reproductivecondition: 'text',
		ffbehavior: 'text',
		ffvitality: 'text',
		establishmentmeans: 'text',
		cultivationstatus: 'checkbox',

		typestatus: 'text',
		disposition: 'text',
		occurrenceid: 'text',
		fieldnumber: 'text',
		language: 'text',
		labelproject: 'text',
		duplicatequantity: 'text',
		datageneralizations: 'text',

		institutioncode: 'text',
		collectioncode: 'text',
		ownerinstitutioncode: 'text',
		storagelocation: 'text',
		basisofrecord: 'select',
		processingstatus: 'select',
		assocrelation: 'select',
		carryover: 'radio', // [0:Collection Event fields, 1: All fields]
		carryoverimages : 'checkbox', 
		clonecount: 'text',
	};

	constructor(public readonly page: Page) {
		for(let fieldName of Object.keys(this.fields)) {
			this.fieldLocators[fieldName] = this.page.locator('input[name=' + fieldName + ']');
		}

		this.submitNewButton = this.page.locator('button[value=addOccurRecord]');
		this.submitEditButton = this.page.locator('button[value=saveOccurEdits]');
	}

	//await expect(this.page).toHaveUrl(url => url.pathname = '/profile/viewprofile.php') 	

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

	async checkMany(fields) {
		for(let [fieldName, value] of Object.entries(fields)) {
			expect(this.fields).toHaveProperty(fieldName);
			await expect(this.fieldLocators[fieldName]).toHaveValue(value);
		}
	}

	async submitNewRecord() {
		await this.submitNewButton.click({force: true});
	}

	async submitEdits() {
		await this.submitEditButton.click({force: true});
	}

	async gotoTab(newTab: OccurrenceEditorTab) {
		await this.page.locator(`li[id="${newTab}"]`).click({force: true});

		// Wait for ajax to load except for Occurrence tab
		if(OccurrenceEditorTab.Occurrence != newTab) {
			await this.page.getByText('Loading...').waitFor({ state: "detached" });
		}
	}
}
