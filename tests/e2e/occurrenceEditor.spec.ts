import { expect, mergeTests, request } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
//import { test as testCollection } from './fixtures/collection';
import { test as testOccurrence } from './fixtures/occurrence';
import { OccurrenceEditorPage , OccurrenceEditorTab, test as testOccurrenceEditor } from './pages/OccurrenceEditorPage'
import path from 'node:path';

const test = mergeTests(testWithAdmin, testOccurrenceEditor);
test.beforeEach(async ({ adminLogin }) => {
	await adminLogin.expectLoggedIn()
});

/* CREATE OCCURRENCES */
const newOccurrenceTests = {
	'Catalog Number Only': { },
	'Recorded By': { recordedby: 'First Last'}
}

test.describe('Create Occurrence', () => {
	for(let testName in newOccurrenceTests) {
		test(testName, async({ createOccurrence }) => {
			await createOccurrence.occurForm.setMany(newOccurrenceTests[testName]);
		})
	}
});

/* EDIT OCCURRENCES */
test.describe('Edit Occurrence ', () => {
	const tests = {
		'Catalog Number': {}
	};

	for(let testName in tests) {
		test(testName, async({ submitEditOccurrence }) => {
			await submitEditOccurrence.occurForm.setMany(tests[testName])
		})
	}
})

/* DELETE OCCURRENCES */
test('Delete Occurrence ', async ({ editOccurrence, occurrenceFactory }) => {
	await editOccurrence.deleteOccurrence();
	const occurrence = await occurrenceFactory.getOccurrence(editOccurrence.occId);
	expect(occurrence).toHaveLength(0);
});

/* DETERMINATIONS */
test('Add Determination', async ({ newDet }) => {
	await newDet.detForm.setMany({
		sciname: 'Genus Species',
		identifiedBy: 'CI TESTING',
		dateIdentified: '1/14/2026'
	})
});

test('Delete Determination', async({ detId, editDet, page }) => {
	await editDet.detForm.openEditForm(detId);
	editDet.detForm.setToDelete(detId);
	page.on('dialog', dialog => dialog.accept());
	await editDet.detForm.submit();
	await editDet.detForm.checkDeleteSuccess();
});

/* MEDIA */
test('Add Media (File)', async({ newMedia }) => {
	await newMedia.mediaForm.setFile('imgfile', path.join(__dirname, '../../images/world.png'));
});

const editMediaTests  = {
	'Caption': { caption: 'caption' },
	'Source Url': { sourceUrl: 'someSourceUrl' },
	'Notes': { notes: 'some notes' },
	'Sort': { sortOccurrence: '6' },
	'Tags': { 
		ch_HasOrganism: true,
		ch_HasLabel: true,
		ch_HasIDLabel: true,
		ch_TypedText: true,
		ch_Handwriting: true,
		ch_ShowsHabitat: true,
		ch_HasProblem: true,
		ch_Diagnostic: true,
		ch_ImageOfAdult: true,
		ch_ImageOfImmature: true,
	},
}

test.describe('Edit Media', () => {
	for(let testName in editMediaTests) {
		test(testName, async({ editMedia }) => {
			await editMedia.mediaForm.setMany(editMediaTests[testName])
			await editMedia.mediaForm.submitEdit();	
			await editMedia.mediaForm.openEditForm();
			await editMedia.mediaForm.checkSetFields();
		})
	}
})

test('Delete Media', async ({ editMedia, page}) => {
	page.on('dialog', dialog => dialog.accept());
	await editMedia.mediaForm.set('removeimg', true);
	await editMedia.mediaForm.submitDelete();	
	await expect(page.getByText(editMedia.mediaForm.DELETE_SUCCESS_MSG)).toBeVisible();
})

/* SKELETAL */
test('Skeletal image (Link)', async ({ page, collId }) => {
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
	await occurrenceEditor.occurForm.setMany(inputs);
	await occurrenceEditor.swapToMediaEnterUrl();
	await occurrenceEditor.mediaForm.setMany(mediaInputs);
	await occurrenceEditor.occurForm.submitSkeletalImage();

	const occId = await occurrenceEditor.getSkeletalImageOccid();
	await occurrenceEditor.gotoRecord(collId, occId)
	occurrenceEditor.mediaForm.setScope('[id^=img][id*=editdiv]');
	await occurrenceEditor.occurForm.checkMany(inputs);
	await occurrenceEditor.gotoTab(OccurrenceEditorTab.Media)
	await occurrenceEditor.mediaForm.openEditForm();
	await occurrenceEditor.mediaForm.checkMany({
		originalUrl: url,
		url: url,
		thumbnailUrl: url,
	});
})

test('Skeletal image (File)', async ({ page, collId }) => {
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

test('Create Skeletal', async ({ occurrenceSkeletalNew, collId }) => {
	await occurrenceSkeletalNew.occurForm.setMany({
		catalognumber: collId + '00003',
	})
})
