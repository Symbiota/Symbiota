import { expect, mergeTests } from '@playwright/test';
import { test as testDB } from './fixtures/db';
import { test as testWithAdmin } from './fixtures/adminLogin';

const { OccurrenceEditorPage } = require('./pages/OccurrenceEditorPage');

const test = mergeTests(testDB, testWithAdmin);

test.beforeEach(async ({ adminLogin }) => {
	await adminLogin.expectLoggedIn();
});

// test('Create an occurrence record', async ({ page }) => {
// 	const collId = 0; 
// 	const inputs = {
// 		catalognumber: '000001',
// 	};
//
// 	let occurrenceEditor = new OccurrenceEditorPage(page);
// 	await occurrenceEditor.gotoNew(collId);
// 	await occurrenceEditor.setMany(inputs);
// 	await page.locator('input[name=gotomode][value="0"]').click({force: true});
// 	await occurrenceEditor.submitNewRecord();
// 	await expect(page.getByText('Public Display')).toBeVisible();	
// 	await occurrenceEditor.checkMany(inputs)
// })
