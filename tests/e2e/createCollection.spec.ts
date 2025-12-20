import { expect, mergeTests } from '@playwright/test';
import { test as testDB } from './fixtures/db';
import { test as testWithAdmin } from './fixtures/adminLogin';

const { CollectionCreatePage } = require('./pages/CollectionCreatePage');

const test = mergeTests(testDB, testWithAdmin);

test.beforeEach(async ({ page, adminLogin }) => {
	await adminLogin.expectLoggedIn();
});

test('Create an Collection', async ({ page, DB }, workerInfo) => {
	const collectionName = workerInfo.project.name + ' CI Collection';

	let collData = {
		institutionCode: 'SYMB',
		collectionCode: 'CICOL',
		collectionName: collectionName,
	}

	let collectionCreatePage = new CollectionCreatePage(page);
	await collectionCreatePage.goto();
	await collectionCreatePage.setMany(collData);
	await collectionCreatePage.setToLiveManaged(collData);
	await collectionCreatePage.submitCreate();
	await expect(page.getByText('New collection added successfully!')).toBeVisible();	
})

test.afterEach(async ({ DB }, workerInfo) => {
	const [ search ] = await DB.execute("SELECT collId from omcollections where collectionName = ?", [workerInfo.project.name + ' CI Collection']);
	if(search.length > 0) {
		await DB.execute('DELETE from omcollectionstats where collId = ?', [ search[0].collId ]);
		await DB.execute('DELETE from omcollections where collId = ?', [ search[0].collId ]);
	}
});
