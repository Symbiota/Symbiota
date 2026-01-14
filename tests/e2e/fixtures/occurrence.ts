import {test as base} from './db.ts'
import mysql from 'mysql2/promise';

class OccurrenceFactory {
	constructor(public readonly conn: mysql.Connection) {}

	async getNewRecord(collId: number): Promise<number>{
		await this.conn.execute(
			"INSERT omoccurrences (collId) VALUES (?)",
			[ collId ]
		);

		let id = await this.conn.execute("SELECT LAST_INSERT_ID() as id");

		if(id.length > 0) {
			return id[0].id;
		} else {
			return 0;
		}
	}
}

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ occurrenceFactory: OccurrenceFactory}>({
	occurrenceFactory: async ({ DB }, use) => {
		await use(new OccurrenceFactory(DB))
	}
});

export { test, OccurrenceFactory };
