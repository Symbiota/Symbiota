import type { Page, Locator } from '@playwright/test';
import { expect } from '@playwright/test';

export class LoginPage {
	private readonly username: Locator;
	private readonly password: Locator;
	private readonly submitButton: Locator;

	constructor(public readonly page: Page) {
		this.username = this.page.locator('input[name=login]');
		this.password = this.page.locator('input[name=password]');
		this.submitButton = this.page.locator('button[value=login]');
	}

	//await expect(this.page).toHaveUrl(url => url.pathname = '/profile/viewprofile.php') 	

	async goto() {
		await this.page.goto('/profile/index.php');
	}

	async fillUsername(username) {
		await this.username.fill(username);
	}

	async fillPassword(password) {
		await this.password.fill(password);
	}

	async attemptLogin() {
		await this.submitButton.click({force: true});
	}

	async expectLoggedIn() {
		// Check if the profile button is populated in the header
		await expect(this.page.getByText('My Profile')).toBeVisible();	
	}
}
