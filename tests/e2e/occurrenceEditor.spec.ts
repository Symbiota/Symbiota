import { expect, mergeTests } from '@playwright/test';
import { test as testDB } from './fixtures/db';
import { test as testWithAdmin } from './fixtures/adminLogin';

const { OccurrenceEditorPage } = require('./pages/OccurrenceEditorPage');

const test = mergeTests(testDB, testWithAdmin);
let collId = null;

test.beforeAll(async ({ DB }, workerinfo) => {
	const globalCollectionName = workerinfo.project.name + ' CI Global Collection';
	const workerCode = workerinfo.project.name.slice(0, 4);

	const insert = await DB.execute(
		"INSERT omcollections (institutionCode, collectionCode, collectionName, managementType) VALUES (?, ?, ?, ?)",
		['SYMB', workerCode, globalCollectionName, 'Live Data']
	);

	const [ search ] = await DB.execute("SELECT collId from omcollections where collectionName = ?", [globalCollectionName]);

	if(search.length > 0) {
		collId = search[0].collId;
	}
})

test.beforeEach(async ({ adminLogin }) => {
	await adminLogin.expectLoggedIn();
});

test.afterAll(async ({ DB }) => {
	await DB.execute('DELETE from omcollectionstats where collId = ?', [ collId ]);
	await DB.execute('DELETE from omcollections where collId = ?', [ collId ]);
});

test('Create an occurrence record', async ({ page }) => {
	const inputs = {
		catalognumber: '000001',
	};

	let occurrenceEditor = new OccurrenceEditorPage(page);
	await occurrenceEditor.gotoNew(collId);
	await occurrenceEditor.setMany(inputs);
	await page.locator('input[name=gotomode][value="0"]').click({force: true});
	await occurrenceEditor.submitNewRecord();
	await occurrenceEditor.checkMany(inputs)
})
