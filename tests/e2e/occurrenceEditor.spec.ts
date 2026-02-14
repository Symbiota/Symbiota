import { expect, mergeTests } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testCollection } from './fixtures/collection';
import { test as testOccurrence } from './fixtures/occurrence';
import { OccurrenceEditorPage, OccurrenceEditorTab } from './pages/OccurrenceEditorPage'
import path from 'node:path';
import { MediaForm } from './forms/mediaForm';
import { OccurrenceForm } from './forms/occurrenceForm';

const test = mergeTests(testWithAdmin, testCollection, testOccurrence);

let collId: number = 0;

test.beforeAll(async ({ collection }, workerInfo) => {
	collId = await collection.getOrCreate(workerInfo.parallelIndex + workerInfo.project.name + ' Global CI Collection');
})

test.beforeEach(async ({ adminLogin }) => {
	await adminLogin.expectLoggedIn()
});

test.afterAll(async ({ collection }) => {
	await collection.deleteByCollId(collId)
});

/* OCCURRENCE TESTS */
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

			let form = new OccurrenceForm('#fullform', page);
			await form.setMany(inputs[testName]);
			await page.locator('input[name=gotomode][value="0"]').click({force: true});
			await form.submitNew();
			await expect(page.getByText('Public Display')).toBeVisible();
			await form.checkMany(inputs[testName])
		})
	}
})

test('From image (Link)', async ({ page }) => {
	const inputs = {
		catalognumber: collId + '00002',
	};

	const url = 'http://localhost/images/world.png';
	const mediaInputs = {
		originalUrl: url,
		weburl: url,
		thumbnailUrl: url,
	};

	let occurrenceEditor = new OccurrenceEditorPage(page);
	await occurrenceEditor.gotoImageSubmit(collId);

	let skelForm = new OccurrenceForm('#imgoccurform', page);
	await skelForm.setMany(inputs);

	await page.getByText("Enter Url").click({force: true});
	let skelMediaForm = new MediaForm('#imgoccurform', page);
	await skelMediaForm.setMany(mediaInputs);
	await skelForm.submitSkeletalImage();

	const newRecordLink = page.locator('a[href*="occurrenceeditor.php"]');
	const occId = parseInt(await newRecordLink.innerText());
	await occurrenceEditor.gotoRecord(collId, occId)

	let occForm = new OccurrenceForm('#fullform', page);
	await occForm.checkMany(inputs);

	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

	let mediaForm = new MediaForm('[id^=img][id*=editdiv]', page);
	await mediaForm.openEditForm()
	await mediaForm.checkMany({
		originalUrl: url,
		url: url,
		thumbnailUrl: url,
	});
})

/* Needs the media root set for this to function */
test('From image (File)', async ({ page }) => {
	const inputs = {
		catalognumber: collId + '00002',
	};

	let occurrenceEditor = new OccurrenceEditorPage(page);
	await occurrenceEditor.gotoImageSubmit(collId);

	let skelForm = new OccurrenceForm('#imgoccurform', page);
	await skelForm.setMany(inputs)
	await skelForm.setFile('imgfile', path.join(__dirname, '../../images/world.png'));
	await skelForm.submitSkeletalImage()

	const newRecordLink = page.locator('a[href*="occurrenceeditor.php"]');
	const occId = parseInt(await newRecordLink.innerText());
	await occurrenceEditor.gotoRecord(collId, occId)

	let occForm = new OccurrenceForm('#fullform', page);
	await occForm.checkMany(inputs);

	let mediaForm = new MediaForm('[id^=img][id*=editdiv]', page);
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)
	await mediaForm.openEditForm();
	await mediaForm.checkMany({
		originalUrl: /.*world\.png/,
		url: /.*world_lg\.png/,
		thumbnailUrl: /.*world_tn\.png/
	})

	page.on('dialog', dialog => dialog.accept());
	await mediaForm.set('removeimg', true);
	await mediaForm.submitDelete();
})

test('From skeletal', async ({ page }) => {
	const inputs = {
		catalognumber: collId + '00003',
	};

	let occurrenceEditor = new OccurrenceEditorPage(page);
	await occurrenceEditor.gotoSkeletalSubmit(collId);

	let skelForm = new OccurrenceForm('#defaultform', page);
	await skelForm.setMany(inputs);
	await skelForm.submitSkeletal();

	const newRecordLink = await page.waitForSelector('div[id="occurlistdiv"] a[id*="a-"]', { state: 'attached' });

	const id = await newRecordLink.getAttribute('id');
	expect(id).toBeDefined();

	const occId = id? parseInt(id.replace('a-', '')): 0;
	await occurrenceEditor.gotoRecord(collId, occId)

	let occForm = new OccurrenceForm('#fullform', page);
	await occForm.checkMany(inputs);
})

test('Edit record', async ({ page, occurrenceFactory }) => {	
	let occId = await occurrenceFactory.getNewRecord(collId);

	const inputs = {
		catalognumber: occId + '00004',
	};

	let occurrenceEditor = new OccurrenceEditorPage(page);
	await occurrenceEditor.gotoRecord(collId, occId)

	let occForm = new OccurrenceForm('#fullform', page);
	await occForm.setMany(inputs);
	await occForm.submitEdit();
	await expect(page.getByText(occForm.EDIT_SUCCESS)).toBeVisible();
	await occForm.checkMany(inputs);
})

/* DETERMINATIONS TESTS */
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


/* MEDIA TESTS */
test('Add Media', async ({ page, occurrenceFactory }) => {
	let occId = await occurrenceFactory.getNewRecord(collId);
	let occurrenceEditor = new OccurrenceEditorPage(page);
	await occurrenceEditor.gotoRecord(collId, occId)
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

	let mediaForm = new MediaForm('form[name=imgnewform]', page);
	await mediaForm.setFile('imgfile', path.join(__dirname, '../../images/world.png'));

	await mediaForm.submitNew();
	await expect(page.getByText(mediaForm.NEW_SUCCESS_MSG)).toBeVisible();
})

test('Delete Media', async ({ page, occurrenceFactory }) => {
	let occId = await occurrenceFactory.getNewRecord(collId);
	let mediaId = await occurrenceFactory.newMedia(occId);

	let occurrenceEditor = new OccurrenceEditorPage(page);
	await occurrenceEditor.gotoRecord(collId, occId)
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

	let mediaForm = new MediaForm(`div[id=img${mediaId}editdiv]`, page);
	await mediaForm.openEditForm();

	page.on('dialog', dialog => dialog.accept());
	await mediaForm.set('removeimg', true);
	await mediaForm.submitDelete();

	await expect(page.getByText(mediaForm.DELETE_SUCCESS_MSG)).toBeVisible();
})
