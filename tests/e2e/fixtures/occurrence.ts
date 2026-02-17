import {test as base} from './db.ts'
import mysql from 'mysql2/promise';

class OccurrenceFactory {
	constructor(public readonly conn: mysql.Connection) {}

	// Use this only internally
	async getNewBlank(sql:string, params: any) {
		await this.conn.execute(sql, params);

		let result = await this.conn.execute("SELECT LAST_INSERT_ID() as id");

		if(result.length > 0 && result[0].length > 0) {
			return result[0][0].id;
		} else {
			return 0;
		}
	}

	async getNewRecord(collId: number): Promise<number>{
		return this.getNewBlank("INSERT INTO omoccurrences (collId) VALUES (?)", [ collId ]);
	}

	async newDetermination(occId: number): Promise<number>{
		return this.getNewBlank(
			"INSERT INTO omoccurdeterminations (occid, identifiedBy, dateIdentified, sciname) VALUES (?,?,?,?)",
			[ occId, 'unknown', 'unknown', 'genus species' ]
		);
	}

	async newMedia(occId: number): Promise<number>{
		return this.getNewBlank(
			"INSERT INTO media(occid) VALUES (?)",
			[ occId ]
		);
	}

	private async getResult(sql, params) {
		const result = await this.conn.execute(sql, params)
		if(result && result.length) {
			return result[0];
		} else {
			return [];
		}
	}

	async getOccurrence(occId: number) {
		return this.getResult("SELECT * FROM omoccurrences where occId", [occId]);
	}

	async getMedia(occId: number) {
		return this.getResult("SELECT * FROM media where occId = ?", [occId]);
	}

	async getDeterminations(occId: number) {
		return this.getResult("SELECT * FROM omoccurdeterminations where occId = ?", [occId]);
	}
}

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ occurrenceFactory: OccurrenceFactory}>({
	occurrenceFactory: async ({ DB }, use) => {
		await use(new OccurrenceFactory(DB))
	}
});

export { test, OccurrenceFactory };
