import { expect, mergeTests } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testCollection } from './fixtures/collection';
import { OccurrenceEditorPage } from './pages/OccurrenceEditorPage'
import path from 'node:path';

const test = mergeTests(testWithAdmin, testCollection);

test.describe('Create Occurrence Record', () => {
	let collId: number = 0;

	test.beforeAll(async ({ collection, browserName }, workerInfo) => {
		collId = await collection.getOrCreate(workerInfo.workerIndex + workerInfo.project.name + ' Global CI Collection');
	})

	test.beforeEach(async ({ adminLogin }) => {
		await adminLogin.expectLoggedIn()
	});

	test.afterAll(async ({ collection, browserName }) => {
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

	/* Currently waiting on 3.4_rc to pull into development before this will pass
	test('From image (Link)', async ({ page }) => {
		const inputs = {
			catalognumber: '000002',
		};

		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoImageSubmit(collId);
		await occurrenceEditor.setMany(inputs);
		await page.getByText("Enter Url").click({force: true});

		const url = 'http://localhost/images/world.png';
		await page.locator('input[name=originalUrl]').fill(url);
		await page.locator('input[name=weburl]').fill(url);
		await page.locator('input[name=thumbnailUrl]').fill(url);

		await page.locator('input[name=action][value="Submit Occurrence"]').click({force: true});

		const newRecordLink = page.locator('a[href*="occurrenceeditor.php"]');
		const occId = parseInt(await newRecordLink.innerText());
		await occurrenceEditor.gotoRecord(collId, occId)
		await occurrenceEditor.checkMany(inputs);

		await page.locator('li[id="imgTab"]').click({force: true});
		await page.getByText('Loading...').waitFor({ state: "detached" });

		const mediaEdit = page.locator('img[src*="/images/edit.png"]');
		await mediaEdit.click({force: true});

		await expect(page.locator('form[name*="editform"] input[name="originalUrl"]')).toHaveValue(url);
		await expect(page.locator('form[name*="editform"] input[name="url"]')).toHaveValue(url);
		await expect(page.locator('form[name*="editform"] input[name="thumbnailUrl"]')).toHaveValue(url);
	})
	*/

	/* Needs the media root set for this to function */
	test('From image (File)', async ({ page }) => {
		const inputs = {
			catalognumber: '000002',
		};

		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoImageSubmit(collId);
		await occurrenceEditor.setMany(inputs);

		const fileChooserPromise = page.waitForEvent('filechooser');
		await page.locator('input[name="imgfile"]').click({force: true});

		const fileChooser = await fileChooserPromise;
		await fileChooser.setFiles(path.join(__dirname, '../../images/world.png'));

		await page.locator('input[name=action][value="Submit Occurrence"]').click({force: true});

		const newRecordLink = page.locator('a[href*="occurrenceeditor.php"]');
		const occId = parseInt(await newRecordLink.innerText());
		await occurrenceEditor.gotoRecord(collId, occId)
		await occurrenceEditor.checkMany(inputs);

		await page.locator('li[id="imgTab"]').click({force: true});
		await page.getByText('Loading...').waitFor({ state: "detached" });

		const mediaEdit = page.locator('img[src*="/images/edit.png"]');
		await mediaEdit.click({force: true});

		await expect(page.locator('form[name*="editform"] input[name="originalUrl"]')).toHaveValue(/.*world\.png/);
		await expect(page.locator('form[name*="editform"] input[name="url"]')).toHaveValue(/.*world_lg\.png/);
		await expect(page.locator('form[name*="editform"] input[name="thumbnailUrl"]')).toHaveValue(/.*world_tn\.png/);

		page.on('dialog', dialog => dialog.accept());
		await page.locator('input[name="removeimg"]').check();
		await page.locator('button[value="Delete Image"]').click({force: true});
	})

	test('From skeletal', async ({ page }) => {
		const inputs = {
			catalognumber: '000003',
		};

		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoSkeletalSubmit(collId);
		await occurrenceEditor.setMany(inputs);

		await page.locator('button[name=recordsubmit]').click({force: true});
		const newRecordLink = await page.waitForSelector('div[id="occurlistdiv"] a[id*="a-"]', { state: 'attached' });

		const id = await newRecordLink.getAttribute('id');
		expect(id).toBeDefined();

		const occId = id? parseInt(id.replace('a-', '')): 0;
		await occurrenceEditor.gotoRecord(collId, occId)
		await occurrenceEditor.checkMany(inputs);
	})

	// test('Edit record', async ({ page }) => {
	// 	
	// 	const inputs = {
	// 		catalognumber: '000003',
	// 	};
	//
	// 	let occurrenceEditor = new OccurrenceEditorPage(page);
	// 	await occurrenceEditor.gotoSkeletalSubmit(collId);
	// 	await occurrenceEditor.setMany(inputs);
	//
	// 	await page.locator('button[name=recordsubmit]').click({force: true});
	// 	const newRecordLink = await page.waitForSelector('div[id=occurlistdiv] a[id*="a-"]', { state: 'attached' });
	//
	// 	const id = await newRecordLink.getAttribute('id');
	// 	expect(id).toBeDefined();
	//
	// 	const occId = id? parseInt(id.replace('a-', '')): 0;
	// 	await occurrenceEditor.gotoRecord(collId, occId)
	// 	await occurrenceEditor.checkMany(inputs);
	// })
})
