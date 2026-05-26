import { test as base } from './collection.ts'
import mysql from 'mysql2/promise';

export type Taxon = {
	sciname: string,
	commons?: Array<string>
}

export type Occurrence = {
	sciname?: string,
	family?: string,
	country?: string,
	locality?: string,
	stateProvince?: string,
	county?: string,
	minimumElevationInMeters?: number,
	maximumElevationInMeters?: number,
	eventDate?: string,
	eventDate2?: string,
	recordedBy?: string,
	recordNumber?: string,
	catalogNumber?: string,
	otherCatalogNumbers?: string,
	decimalLatitude?: number,
	decimalLongitude?: number,
	tidInterpreted?: number,

	// Inserts to external Table
	//taxon?: Taxon,
}

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

	async seedOccurrences(collId: number, occurrences: Array<Occurrence>) {
		for(let occurrence of occurrences) {
			let fields: Array<string> = Object.keys(occurrence);
			fields.push('collId');
			let values: Array<string|number> = Object.values(occurrence);
			values.push(collId)
			await this.getNewBlank(`INSERT INTO omoccurrences (${fields.join(',')}) VALUES (${values.map(v => '?').join(',')})`, values);
		}
	}

	async seedTaxa(taxa: Array<Taxon>): Promise<Array<number>> {
		let tids: Array<number> = [];

		for(let taxon of taxa) {
			const sql = "INSERT INTO taxa (sciName, unitName1) VALUES (?, ?)"
			tids.push(await this.getResult(sql, [taxon.sciname, taxon.sciname]));
		}

		return tids;
	}

	async newDetermination(occId: number): Promise<number>{
		return this.getNewBlank(
			"INSERT INTO omoccurdeterminations (occid, identifiedBy, dateIdentified, sciname) VALUES (?,?,?,?)",
			[ occId, 'unknown', 'unknown', 'genus species' ]
		);
	}

	async newMedia(occId: number): Promise<number>{
		const testUrl = '/ci_media/url.jpg';
		return this.getNewBlank(
			"INSERT INTO media(occid, originalUrl, url, thumbnailUrl) VALUES (?, ?, ?, ?)",
			[ occId, testUrl, testUrl, testUrl]
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
		return this.getResult("SELECT * FROM omoccurrences where occId = ?", [occId]);
	}

	async getMedia(occId: number) {
		return this.getResult("SELECT * FROM media where occId = ?", [occId]);
	}

	async getDeterminations(occId: number) {
		return this.getResult("SELECT * FROM omoccurdeterminations where occId = ?", [occId]);
	}
}

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ occurrenceFactory: OccurrenceFactory, occId: number, detId: number, mediaId: number}>({
	occurrenceFactory: async ({ DB }, use) => {
		await use(new OccurrenceFactory(DB))
	},
	occId: async ({ occurrenceFactory, collId }, use) => {
		const occId = await occurrenceFactory.getNewRecord(collId)
		await use(occId);
	},
	detId: async ({ occurrenceFactory, occId }, use) => {
		await use(await occurrenceFactory.newDetermination(occId));
	},
	mediaId: async ({ occurrenceFactory, occId }, use) => {
		await use(await occurrenceFactory.newMedia(occId));
	}
});

export { test, OccurrenceFactory };
