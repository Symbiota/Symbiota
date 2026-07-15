import { expect, mergeTests } from '@playwright/test';
import { test as testWithAdmin } from './fixtures/adminLogin';
import { test as testTaxonomyEditor } from './pages/TaxonomyEditorPage';
import path from 'node:path';

const test = mergeTests(testWithAdmin, testTaxonomyEditor);
test.beforeEach(async ({ adminLogin }) => {
	await adminLogin.expectLoggedIn()
});

const addMediaTests = {
	'Jpg': '../files/image.jpg',
	'Jpeg': '../files/image.jpeg',
	'Png': '../files/image.png',
	'Bmp': '../files/image.bmp',
	'Uppercase Ext': '../files/uppercase_image.JPG',
	'Mp3': '../files/audio.mp3',
	'Wav': '../files/audio.wav',
	'Ogg': '../files/audio.ogg',
	'Pdf': '../files/text.pdf',
}

test.describe('Upload Taxonomy Media', () => {
	for(let testName in addMediaTests) {
		test(testName, async({ addMedia }) => {
			await addMedia.setFile('imgfile', path.join(__dirname, addMediaTests[testName]));
		});
	}
});
