import type { Page, Locator } from '@playwright/test';
import { Form } from "./Form";

const occurrenceFields = {
	catalognumber: 'text',
	recordedby: 'text',
	recordNumber: 'text',
	eventdate: 'text',
	eventdate2: 'text',
	associatedcollectors: 'text',
	verbatimeventdate: 'text',

	ffsciname: 'text',
	sciname: 'text',
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
	country: 'text',
	stateprovince: 'text',
	locality: 'text',
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
}

export class OccurrenceForm extends Form {
	private readonly submitNewButton: Locator;
	private readonly submitEditButton: Locator;
	private readonly submitSkeletalButton: Locator;
	private readonly submitSkeletalImageButton: Locator;
	private readonly submitObservationImageButton: Locator;

	public readonly EDIT_SUCCESS = "SUCCESS";

	constructor(page: Page) {
		super(page, occurrenceFields);
		this.submitNewButton = this.form.locator('button[value=addOccurRecord]');
		this.submitEditButton = this.form.locator('button[value=saveOccurEdits]');
		this.submitSkeletalButton = this.form.locator('button[name=recordsubmit]');
		this.submitSkeletalImageButton = this.form.locator('input[name=action][value="Submit Occurrence"]');
		this.submitObservationImageButton = this.form.locator('button[name=action][value="Submit"]');
	}

	async submitEdit() { return this.submitEditButton.click({force: true})}
	async submitNew() { return this.submitNewButton.click({force: true})}
	async submitSkeletal() { return this.submitSkeletalButton.click({force: true})}
	async submitSkeletalImage() { return this.submitSkeletalImageButton.click({force: true})}
	async submitObservationImage() { return this.submitObservationImageButton.click({force: true})}
}
