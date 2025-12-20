import { expect, mergeTests } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testCollection } from './fixtures/collection';
import { OccurrenceEditorPage } from './pages/OccurrenceEditorPage'

const test = mergeTests(testWithAdmin, testCollection);
let collId: number = 0;

test.beforeAll(async ({ collection, browserName }) => {
	collId = await collection.getOrCreate(browserName + ' CI Global Collection');
})
test.beforeEach(async ({ adminLogin }) => {
	await adminLogin.expectLoggedIn()
});
test.afterAll(async ({ collection }) => {
	await collection.deleteByCollId(collId)
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
	await expect(page.getByText('Public Display')).toBeVisible();
	await occurrenceEditor.checkMany(inputs)
})
