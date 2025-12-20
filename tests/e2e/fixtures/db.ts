import {test as base} from '@playwright/test'
import mysql from 'mysql2/promise';

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ DB: mysql.Connection }>({
	DB: async ({}, use) => {
		const connection = await mysql.createConnection({
			host: '127.0.0.1',
			user: 'ci_user',
			password: 'ci_password',
			database: 'ci_testing',
		});
		await use(connection);
	}
});

export { test }; 
