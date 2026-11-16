import {test as base} from '@playwright/test'
import { LoginPage } from '../pages/LoginPage';

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ adminLogin: LoginPage}>({
	adminLogin: async ({page}, use) => {
		let loginPage = new LoginPage(page);
		await loginPage.goto();
		await loginPage.fillUsername('Admin');
		await loginPage.fillPassword('admin');
		await loginPage.attemptLogin();
		use(loginPage);
	}
});

export { test }; 
