import {test as base} from '@playwright/test'
import mysql from 'mysql2/promise';

async function getConnection() {
	return await mysql.createConnection({
		host: '127.0.0.1',
		port: 3307,
		user: 'ci_user',
		password: 'ci_password',
		database: 'ci_testing',
	});
}

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ DB: mysql.Connection }>({
	DB: async ({}, use) => {
		let connection = await getConnection();
		await use(connection);
	}
});

export { test, getConnection }; 
