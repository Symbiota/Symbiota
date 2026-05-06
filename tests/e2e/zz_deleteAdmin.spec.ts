import { expect } from '@playwright/test';
import { test } from './fixtures/adminLogin';

test('Delete Admin user', async ({ adminLogin, page }) => {
	// Wait for login redirect to root index.php (not /profile/index.php which is where login starts)
	await page.waitForURL(url => url.pathname === '/index.php');
	await page.goto('/profile/userprofile.php?userid=1');

	// The Delete Profile button lives inside #profileeditdiv which is hidden by default.
	// The toggle JS is not loaded on this page, so reveal the section directly.
	await page.evaluate(() => {
		(document.getElementById('profileeditdiv') as HTMLElement).style.display = 'block';
	});

	// Handle the confirm dialog before clicking the button
	page.on('dialog', dialog => dialog.accept());

	await page.getByRole('button', { name: 'Delete Profile' }).click({force: true});

	// After deleting own profile, redirected to home page
	await expect(page).toHaveURL('/index.php');
});
