import { expect, mergeTests } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testCollection } from './fixtures/collection';
import { OccurrenceEditorPage } from './pages/OccurrenceEditorPage'

const test = mergeTests(testWithAdmin, testCollection);

test.describe('Create Occurrence Record', () => {
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

	test('From editor', async ({ page }) => {
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

	test('From image', async ({ page }) => {
		const inputs = {
			catalognumber: '000002',
		};

		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoImageOccurrenceSubmit(collId);
		await occurrenceEditor.setMany(inputs);
		await page.getByText("Enter Url").click({force: true});
		await page.locator('input[name=imgurl]').fill('http://localhost/images/world.png');
		await page.locator('input[name=weburl]').fill('http://localhost/images/world.png');
		await page.locator('input[name=tnurl]').fill('http://localhost/images/world.png');

		await page.locator('input[name=action][value="Submit Occurrence"]').click({force: true});

		// const newRecordLink = page.locator('a[href*="occurrenceeditor.php"]');
		// const occId = parseInt(await newRecordLink.innerText());
		// await newRecordLink.click({force: true})
		
		// await expect(page.getByText('Public Display')).toBeVisible();
		// await occurrenceEditor.checkMany(inputs)
	})

})
