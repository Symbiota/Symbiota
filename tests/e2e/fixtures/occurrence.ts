import {test as base} from './db.ts'
import mysql from 'mysql2/promise';

class OccurrenceFactory {
	constructor(public readonly conn: mysql.Connection) {}

	async getNewRecord(collId: number): Promise<number>{
		await this.conn.execute(
			"INSERT INTO omoccurrences (collId) VALUES (?)",
			[ collId ]
		);

		let result = await this.conn.execute("SELECT LAST_INSERT_ID() as id");

		if(result.length > 0 && result[0].length > 0) {
			return result[0][0].id;
		} else {
			return 0;
		}
	}

	async newDetermination(occId: number): Promise<number>{
		await this.conn.execute(
			"INSERT INTO omoccurdeterminations (occid, identifiedBy, dateIdentified, sciname) VALUES (?,?,?,?)",
			[ occId, 'unknown', 'unknown', 'genus species' ]
		);

		let result = await this.conn.execute("SELECT LAST_INSERT_ID() as id");

		if(result.length > 0 && result[0].length > 0) {
			return result[0][0].id;
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
