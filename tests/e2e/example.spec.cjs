const { test, expect } = require('@playwright/test');

test('Create an Account', async ({ page }) => {
	await page.goto('./');

	//Click Sign In button
	await page.getByText('Sign In').click({force: true});

	// Expects page to have a heading with the name of Installation.
	await expect(page.getByText('Portal Login')).toBeVisible();

	// Nav to create user page
	const createAccountLink = page.getByText('Create an account');
	await expect(createAccountLink).toBeVisible();
	createAccountLink.click();

	// Navigation successful
	await expect(page.getByRole('heading', {name: 'Create New Profile'})).toBeVisible();

	// Text input
	await page.locator('input[name=login]').fill('testUser');
	await page.locator('input[name=pwd]').fill('ciPassword');
	await page.locator('input[name=pwd2]').fill('ciPassword');
	await page.locator('input[name=firstname]').fill('test_first');
	await page.locator('input[name=lastname]').fill('test_last');
	await page.locator('input[name=email]').fill('test_email@symbiota.org');

	await page.getByText('Create Login').click({force: true});

	await expect(page.getByText('Welcome test_first!')).toBeVisible();	
});
