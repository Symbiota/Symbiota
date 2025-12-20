import {test as base} from './db.ts'
import mysql from 'mysql2/promise';

class Collection {
	constructor(public readonly conn: mysql.Connection) {}

	async getByName(collectionName: string) {
		const [ search ] = await this.conn.execute("SELECT collId from omcollections where collectionName = ?", [collectionName]);
		if(search.length > 0) {
			return search[0].collId;
		} else {
			return 0;
		}
	}

	async insertBasic(collectionName: string, managementType: string = 'Live Data') {
		const insert = await this.conn.execute(
			"INSERT omcollections (institutionCode, collectionCode, collectionName, managementType) VALUES (?, ?, ?, ?)",
			['SYMB', collectionName.slice(0, 4), collectionName, managementType]
		);
	}

	async getOrCreate(collectionName) {
		let collid = await this.getByName(collectionName);

		if(!collid) {
			await this.insertBasic(collectionName);
			collid = await this.getByName(collectionName);
		}

		return collid;
	}

	async deleteByCollId(collId) {
		await this.conn.execute('DELETE from omcollectionstats where collId = ?', [ collId ]);
		await this.conn.execute('DELETE from omcollections where collId = ?', [ collId ]);
	}
}

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ collection: Collection}>({
	collection: async ({ DB }, use) => {
		await use(new Collection(DB))
	}
});

export { test, Collection }; 
