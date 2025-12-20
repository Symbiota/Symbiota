import { expect, mergeTests } from '@playwright/test';
import { test as testDB } from './fixtures/db';
import { test as testWithAdmin } from './fixtures/adminLogin';

const { CollectionCreatePage } = require('./pages/CollectionCreatePage');

const test = mergeTests(testDB, testWithAdmin);

test.beforeEach(async ({ page, adminLogin }) => {
	await adminLogin.expectLoggedIn();
});

test('Create an Collection', async ({ page }, workerInfo) => {
	let collData = {
		institutionCode: 'SYMB',
		collectionCode: 'CICOL',
		collectionName: workerInfo.project.name + ' CI Collection',
	}

	let collectionCreatePage = new CollectionCreatePage(page);
	await collectionCreatePage.goto();
	await collectionCreatePage.setMany(collData);
	await collectionCreatePage.setToLiveManaged(collData);
	await collectionCreatePage.submitCreate();
	await expect(page.getByText('New collection added successfully!')).toBeVisible();	
})

test.afterEach(async ({ DB }) => {
	await DB.execute('DELETE from omcollectionstats');
	await DB.execute('DELETE FROM omcollections');
});
