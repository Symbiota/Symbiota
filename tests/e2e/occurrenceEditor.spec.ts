import { expect, mergeTests, test as base } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testCollection } from './fixtures/collection';
import { test as testOccurrence } from './fixtures/occurrence';
import { OccurrenceEditorPage , OccurrenceEditorTab } from './pages/OccurrenceEditorPage'
import path from 'node:path';

const test = mergeTests(testWithAdmin, testCollection, testOccurrence);

const withCollId = test.extend<{ collId: number, occId: number, detId: number}>({
	collId: async ({ collection }, use) => {
		const workerInfo = occurTest.info();
		const collectionName = workerInfo.workerIndex
			+ workerInfo.project.name
			+ ' CI Collection';
		const collId = await collection.getOrCreate(collectionName);
		await use(collId);
		await collection.deleteByCollId(collId)
	},
	occId: async ({ occurrenceFactory, collId }, use) => {
		const occId = await occurrenceFactory.getNewRecord(collId)
		await use(occId);
	},
	detId: async ({ occurrenceFactory, occId }, use) => {
		await use(await occurrenceFactory.newDetermination(occId));
	}
});

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
const occurTest = withCollId.extend<{ 
	occurrenceEditor: OccurrenceEditorPage, 
	occurrenceEditorEdit: OccurrenceEditorPage, 
	occurrenceEditorNew: OccurrenceEditorPage,
	occurrenceEditorDet: OccurrenceEditorPage,
	occurrenceSkeletalNew: OccurrenceEditorPage
}>({
	occurrenceEditor: async ({ collId, page }, use) => {
		const occurrenceEditor = OccurrenceEditorPage.make(page);
		occurrenceEditor.collId = collId;
		await use(occurrenceEditor);
	},
	occurrenceEditorEdit: async ({ occurrenceEditor, occId }, use) => {
		occurrenceEditor.occId = occId;
		await occurrenceEditor.gotoRecord(occurrenceEditor.collId, occId)
		await use(occurrenceEditor);
	},
	occurrenceEditorNew: async ({ occurrenceEditor }, use) => {
		await occurrenceEditor.gotoNew(occurrenceEditor.collId);
		await occurrenceEditor.occurForm.set('catalognumber', occurTest.info().workerIndex + '000001');
		await use(occurrenceEditor)
		await occurrenceEditor.setGotoRecord()
		await occurrenceEditor.occurForm.submitNew();
		await occurrenceEditor.checkRecordSuccess();
		await occurrenceEditor.occurForm.checkSetFields()
	},
	occurrenceEditorDet: async ({ occurrenceEditorEdit }, use) => {
		await occurrenceEditorEdit.gotoTab(OccurrenceEditorTab.Determinations)
		await use(occurrenceEditorEdit)
	},
	occurrenceSkeletalNew: async ({ occurrenceEditor }, use) => {
		await occurrenceEditor.gotoSkeletalSubmit(occurrenceEditor.collId);
		await use(occurrenceEditor);
		await occurrenceEditor.occurForm.submitSkeletal();
		const occId = await occurrenceEditor.getSkeletalOccid();
		await occurrenceEditor.gotoRecord(occurrenceEditor.collId, occId)
		await occurrenceEditor.occurForm.checkSetFields();
	},
});

const newOccurrenceTests = {
	'Catalog Number Only': { },
	'Recorded By': { recordedby: 'First Last'}
}

test.describe('Create new occurrence from occurrenceEditor', () => {
	for(let testName in newOccurrenceTests) {
		occurTest(testName, async({ occurrenceEditorNew }) => {
			await occurrenceEditorNew.occurForm.setMany(newOccurrenceTests[testName]);
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

occurTest('From skeletal', async ({ occurrenceSkeletalNew }) => {
	await occurrenceSkeletalNew.occurForm.setMany({
		catalognumber: collId + '00003',
	})
})

/* EDIT OCCURRENCES WITH EDITOR TESTS */
occurTest.describe('Edit Record from Occurrence Editor', () => {
	const tests = {
		'Catalog Number Only': {
			catalognumber: '000004',
		}	
	};

	for(let testName in tests) {
		occurTest(testName, async({ occurrenceEditorEdit, page }) => {
			await occurrenceEditorEdit.occurForm.setMany(tests[testName]);
			await occurrenceEditorEdit.occurForm.submitEdit();
			await expect(page.getByText(occurrenceEditorEdit.occurForm.EDIT_SUCCESS)).toBeVisible();
			await occurrenceEditorEdit.occurForm.checkSetFields();
		})
	}

})

/* DETERMINATIONS TESTS */
occurTest('Add Determination', async ({ occurrenceEditorDet, occurrenceFactory }) => {
	await occurrenceEditorDet.detForm.setToNew();
	await occurrenceEditorDet.detForm.setMany({
		sciname: 'Genus Species',
		identifiedBy: 'CI TESTING',
		dateIdentified: '1/14/2026'
	})
	await occurrenceEditorDet.detForm.submit();
	await occurrenceEditorDet.detForm.checkNewSuccess();
	const dets = await occurrenceFactory.getDeterminations(occurrenceEditorDet.occId);
	expect(dets).toBeDefined();
	expect(dets.length).toBeGreaterThan(0);
	for(let [fieldName, value] of Object.entries(occurrenceEditorDet.detForm.setFields)) {
		expect(dets[0][fieldName]).toBe(value);
	}
});

occurTest('Delete Determination', async({ detId, occurrenceEditorDet, page }) => {
	await occurrenceEditorDet.detForm.openEditForm(detId);
	occurrenceEditorDet.detForm.setToDelete(detId);
	page.on('dialog', dialog => dialog.accept());
	await occurrenceEditorDet.detForm.submit();
	await occurrenceEditorDet.detForm.checkDeleteSuccess();
});

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
