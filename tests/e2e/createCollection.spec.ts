import { expect, mergeTests } from '@playwright/test';
import { test as testCollection } from './fixtures/collection';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { CollectionCreatePage } from './pages/CollectionCreatePage';

const test = mergeTests(testCollection, testWithAdmin);

test.beforeEach(async ({ adminLogin }) => await adminLogin.expectLoggedIn());

test.afterEach(async ({ collection, browserName }) => {
	let collId = await collection.getByName(browserName + ' CI Collection')
	await collection.deleteByCollId(collId);
});

test('Create an Collection', async ({ page, browserName }, workerInfo) => {
	const collectionName = browserName + ' CI Collection';

	let collData = {
		institutionCode: 'SYMB',
		collectionCode: collectionName.slice(0, 4) + '_CICOL' + workerInfo.parallelIndex,
		collectionName: collectionName,
	}

	let collectionCreatePage = new CollectionCreatePage(page);
	await collectionCreatePage.goto();
	await collectionCreatePage.setMany(collData);
	await collectionCreatePage.setToLiveManaged();
	await collectionCreatePage.submitCreate();
	await expect(page.getByText('New collection added successfully!')).toBeVisible();	
})
