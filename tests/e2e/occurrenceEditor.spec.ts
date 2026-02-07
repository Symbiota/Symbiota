import { expect, mergeTests } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testCollection } from './fixtures/collection';
import { test as testOccurrence } from './fixtures/occurrence';
import { OccurrenceEditorPage, OccurrenceEditorTab } from './pages/OccurrenceEditorPage'
import path from 'node:path';

const test = mergeTests(testWithAdmin, testCollection, testOccurrence);

test.describe('Create Occurrence Record', () => {
	let collId: number = 0;

	test.beforeAll(async ({ collection }, workerInfo) => {
		collId = await collection.getOrCreate(workerInfo.parallelIndex + workerInfo.project.name + ' Global CI Collection');
	})

	test.beforeEach(async ({ adminLogin }) => {
		await adminLogin.expectLoggedIn()
	});

	test.afterAll(async ({ collection, browserName }) => {
		await collection.deleteByCollId(collId)
	});

	test.describe('From Editor', () => {
		const inputs = {
			'Catalog Number Only': {
				catalognumber: '000001',
			},
			'Recorded By': {
				catalognumber: '000002',
				recordedby: 'First Last',
			}
		}

		for(let testName in inputs) {
			test(testName, async({ page }) => {
				let occurrenceEditor = new OccurrenceEditorPage(page);
				await occurrenceEditor.gotoNew(collId);
				await occurrenceEditor.setMany(inputs[testName]);
				await page.locator('input[name=gotomode][value="0"]').click({force: true});
				await occurrenceEditor.submitNewRecord();
				await expect(page.getByText('Public Display')).toBeVisible();
				await occurrenceEditor.checkMany(inputs[testName])
			})
		}
	})

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

		await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

		const mediaEdit = page.locator('img[src*="/images/edit.png"]');
		await mediaEdit.click({force: true});

		await expect(page.locator('form[name*="editform"] input[name="originalUrl"]')).toHaveValue(url);
		await expect(page.locator('form[name*="editform"] input[name="url"]')).toHaveValue(url);
		await expect(page.locator('form[name*="editform"] input[name="thumbnailUrl"]')).toHaveValue(url);
	})

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

		await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

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

	test('Edit record', async ({ page, occurrenceFactory }) => {	
		let occId = await occurrenceFactory.getNewRecord(collId);

		const inputs = {
			catalognumber: occId + '00004',
		};

		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoRecord(collId, occId)
		await occurrenceEditor.setMany(inputs);
		await occurrenceEditor.submitEdits();
		await expect(page.getByText('SUCCESS')).toBeVisible();
		await occurrenceEditor.checkMany(inputs);
	})


	test('Add Determination', async ({ page, occurrenceFactory }) => {
		let occId = await occurrenceFactory.getNewRecord(collId);
		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoRecord(collId, occId)
		await occurrenceEditor.gotoTab(OccurrenceEditorTab.Determinations)

		const addForm = page.locator('form[name=detaddform]');

		await addForm.locator('input[name=sciname]').fill('Genus Species');
		await addForm.locator('input[name=identifiedby]').fill('CI Testing');
		await addForm.locator('input[name=dateidentified]').fill('1/14/2026');
		await addForm.locator('button[name=submitaction]').click({force: true});

		await expect(page.getByText('Genus Species')).toBeVisible()
		await expect(page.getByText('CI TESTING')).toBeVisible()
		await expect(page.getByText('1/14/2026')).toBeVisible()
	})

	test('Delete Determination', async ({ page, occurrenceFactory }) => {
		let occId = await occurrenceFactory.getNewRecord(collId);
		let detId = await occurrenceFactory.newDetermination(occId);

		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoRecord(collId, occId)
		await occurrenceEditor.gotoTab(OccurrenceEditorTab.Determinations)

		const detDiv = page.locator(`div[id=detdiv-${detId}]`);
		const editDetDiv = page.locator(`div[id=editdetdiv-${detId}]`);

		await detDiv.locator('a[title="Edit Determination"]').click({force: true});

		page.on('dialog', dialog => dialog.accept());
		await editDetDiv.locator('button[value="Delete Determination"]').click({force: true});

		await expect(page.getByText('Determination deleted successfully')).toBeVisible();
		await expect(detDiv).not.toBeAttached();
	})

	test('Add Media', async ({ page, occurrenceFactory }) => {
		let occId = await occurrenceFactory.getNewRecord(collId);
		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoRecord(collId, occId)

		await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

		const newForm = page.locator('form[name=imgnewform]');

		const fileChooserPromise = page.waitForEvent('filechooser');
		await newForm.locator('input[name="imgfile"]').click({force: true});

		const fileChooser = await fileChooserPromise;
		await fileChooser.setFiles(path.join(__dirname, '../../images/world.png'));

		await newForm.locator('button[name="submitaction"]').click({force: true});

		await expect(page.getByText('Media added successfully')).toBeVisible();
	})

	test('Delete Media', async ({ page, occurrenceFactory }) => {
		let occId = await occurrenceFactory.getNewRecord(collId);
		let mediaId = await occurrenceFactory.newMedia(occId);

		let occurrenceEditor = new OccurrenceEditorPage(page);
		await occurrenceEditor.gotoRecord(collId, occId)
		await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

		await page.locator('div[title="Edit Resource MetaData"]').click({force: true});
		const mediaForm = page.locator(`div[id=img${mediaId}editdiv]`);

		page.on('dialog', dialog => dialog.accept());
		await mediaForm.locator('input[name=removeimg]').click({force: true})
		await mediaForm.locator('button[value="Delete Image"]').click({force: true})

		await expect(page.getByText(' Media deleted successfully')).toBeVisible();
	})
})
