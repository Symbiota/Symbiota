import { expect, mergeTests } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testCollection } from './fixtures/collection';
import { test as testOccurrence } from './fixtures/occurrence';
import { OccurrenceEditorPage , OccurrenceEditorTab } from './pages/OccurrenceEditorPage'
import path from 'node:path';

const test = mergeTests(testWithAdmin, testCollection, testOccurrence);

/* TEST COLLECTION SETUP */
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

/* NEW OCCURRENCES FROM EDITOR TESTS */
const newOccurTest = test.extend<{ occurrenceEditor: OccurrenceEditorPage}>({
	occurrenceEditor: async ({ page }, use) => {
		const occurrenceEditor = OccurrenceEditorPage.make(page)
		await occurrenceEditor.gotoNew(collId);
		await use(occurrenceEditor)
		await occurrenceEditor.setGotoRecord()
		await occurrenceEditor.occurForm.submitNew();
		await occurrenceEditor.checkRecordSuccess();
		await occurrenceEditor.occurForm.checkSetFields()
	}
});

const newOccurrenceTests = {
	'Catalog Number Only': {
		catalognumber: '000001',
	},
	'Recorded By': {
		catalognumber: '000002',
		recordedby: 'First Last',
	}
}

newOccurTest.describe('Create new occurrence from occurrenceEditor', () => {
	for(let testName in newOccurrenceTests) {
		newOccurTest(testName, async({ occurrenceEditor }) => {
			await occurrenceEditor.occurForm.setMany(newOccurrenceTests[testName]);
		})
	}
});

/* NEW OCCURRENCES FROM IMAGE TESTS */
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

	let occurrenceEditor = OccurrenceEditorPage.make(page);
	await occurrenceEditor.gotoImageSubmit(collId);

	occurrenceEditor.occurForm.setScope('#imgoccurform');
	await occurrenceEditor.occurForm.setMany(inputs);

	await occurrenceEditor.swapToMediaEnterUrl();

	occurrenceEditor.mediaForm.setScope('#imgoccurform');
	await occurrenceEditor.mediaForm.setMany(mediaInputs);
	await occurrenceEditor.occurForm.submitSkeletalImage();

	const occId = await occurrenceEditor.getSkeletalImageOccid();
	await occurrenceEditor.gotoRecord(collId, occId)

	occurrenceEditor.mediaForm.setScope('[id^=img][id*=editdiv]');
	occurrenceEditor.occurForm.setScope('body');

	await occurrenceEditor.occurForm.checkMany(inputs);
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

	await occurrenceEditor.mediaForm.openEditForm();
	await occurrenceEditor.mediaForm.checkMany({
		originalUrl: url,
		url: url,
		thumbnailUrl: url,
	});
})

test('From image (File)', async ({ page }) => {
	const inputs = {
		catalognumber: collId + '00002',
	};

	let occurrenceEditor = OccurrenceEditorPage.make(page);
	await occurrenceEditor.gotoImageSubmit(collId);

	occurrenceEditor.occurForm.setScope('#imgoccurform');
	await occurrenceEditor.occurForm.setMany(inputs)
	await occurrenceEditor.occurForm.setFile('imgfile', path.join(__dirname, '../../images/world.png'));
	await occurrenceEditor.occurForm.submitSkeletalImage();

	const occId = await occurrenceEditor.getSkeletalImageOccid();
	await occurrenceEditor.gotoRecord(collId, occId)

	occurrenceEditor.occurForm.setScope('body');
	await occurrenceEditor.occurForm.checkMany(inputs)

	occurrenceEditor.mediaForm.setScope('[id^=img][id*=editdiv]');
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)
	await occurrenceEditor.mediaForm.openEditForm();
	await occurrenceEditor.mediaForm.checkMany({
		originalUrl: /.*world\.png/,
		url: /.*world_lg\.png/,
		thumbnailUrl: /.*world_tn\.png/
	});

	page.on('dialog', dialog => dialog.accept());
	await occurrenceEditor.mediaForm.set('removeimg', true);
	await occurrenceEditor.mediaForm.submitDelete();
})

test('From skeletal', async ({ page }) => {
	const inputs = {
		catalognumber: collId + '00003',
	};

	let occurrenceEditor = OccurrenceEditorPage.make(page);
	await occurrenceEditor.gotoSkeletalSubmit(collId);

	await occurrenceEditor.occurForm.setMany(inputs);
	await occurrenceEditor.occurForm.submitSkeletal();

	const occId = await occurrenceEditor.getSkeletalOccid();

	await occurrenceEditor.gotoRecord(collId, occId)
	await occurrenceEditor.occurForm.checkMany(inputs);
})

/* EDIT OCCURRENCES WITH EDITOR TESTS */
const editOccurTest = test.extend<{ occurrenceEditor: OccurrenceEditorPage}>({
	occurrenceEditor: async ({ page, occurrenceFactory }, use) => {
		let occId = await occurrenceFactory.getNewRecord(collId);
		let occurrenceEditor = OccurrenceEditorPage.make(page);
		await occurrenceEditor.gotoRecord(collId, occId)
		await use(occurrenceEditor);
		await occurrenceEditor.occurForm.submitEdit();
		await expect(page.getByText(occurrenceEditor.occurForm.EDIT_SUCCESS)).toBeVisible();
		await occurrenceEditor.occurForm.checkSetFields();
	}
});

editOccurTest.describe('Edit Record from Occurrence Editor', () => {
	const tests = {
		'Catalog Number Only': {
			catalognumber: collId + '00004',
		}	
	};

	for(let testName in tests) {
		editOccurTest(testName, async({ occurrenceEditor }) => {
			await occurrenceEditor.occurForm.setMany(tests[testName]);
		})
	}
})

/* DETERMINATIONS TESTS */
test('Add Determination', async ({ page, occurrenceFactory }) => {
	let occId = await occurrenceFactory.getNewRecord(collId);
	let occurrenceEditor = OccurrenceEditorPage.make(page);
	await occurrenceEditor.gotoRecord(collId, occId)
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Determinations)
	const inputs = {
		sciname: 'Genus Species',
		identifiedBy: 'CI TESTING',
		dateIdentified: '1/14/2026'
	}

	await occurrenceEditor.detForm.setToNew();
	await occurrenceEditor.detForm.setMany(inputs);
	await occurrenceEditor.detForm.submit();
	await occurrenceEditor.detForm.checkNewSuccess();

	const dets = await occurrenceFactory.getDeterminations(occId);

	expect(dets).toBeDefined();
	expect(dets.length).toBeGreaterThan(0);

	for(let [fieldName, value] of Object.entries(occurrenceEditor.detForm.setFields)) {
		expect(dets[0][fieldName]).toBe(value);
	}
})

// get occurrence with determinaton -> delete -> check that delete was successful

test('Delete Determination', async ({ page, occurrenceFactory }) => {
	let occId = await occurrenceFactory.getNewRecord(collId);
	let detId = await occurrenceFactory.newDetermination(occId);

	let occurrenceEditor = OccurrenceEditorPage.make(page);
	await occurrenceEditor.gotoRecord(collId, occId)
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Determinations)

	await occurrenceEditor.detForm.openEditForm(detId);
	occurrenceEditor.detForm.setToDelete(detId);
	page.on('dialog', dialog => dialog.accept());
	await occurrenceEditor.detForm.submit();
	await occurrenceEditor.detForm.checkDeleteSuccess();
})

/* MEDIA TESTS */
test('Add Media', async ({ page, occurrenceFactory }) => {
	let occId = await occurrenceFactory.getNewRecord(collId);
	let occurrenceEditor = OccurrenceEditorPage.make(page);
	await occurrenceEditor.gotoRecord(collId, occId)
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

	occurrenceEditor.mediaForm.setScope('form[name=imgnewform]');
	await occurrenceEditor.mediaForm.setFile('imgfile', path.join(__dirname, '../../images/world.png'));

	await occurrenceEditor.mediaForm.submitNew();
	await expect(page.getByText(occurrenceEditor.mediaForm.NEW_SUCCESS_MSG)).toBeVisible();
})

test('Delete Media', async ({ page, occurrenceFactory }) => {
	let occId = await occurrenceFactory.getNewRecord(collId);
	let mediaId = await occurrenceFactory.newMedia(occId);

	let occurrenceEditor = OccurrenceEditorPage.make(page);
	await occurrenceEditor.gotoRecord(collId, occId)
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)

	occurrenceEditor.mediaForm.setScope(`div[id=img${mediaId}editdiv]`);

	await occurrenceEditor.mediaForm.openEditForm();

	page.on('dialog', dialog => dialog.accept());
	await occurrenceEditor.mediaForm.set('removeimg', true);
	await occurrenceEditor.mediaForm.submitDelete();

	await expect(page.getByText(occurrenceEditor.mediaForm.DELETE_SUCCESS_MSG)).toBeVisible();
})
