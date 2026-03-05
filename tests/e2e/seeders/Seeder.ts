import mysql from 'mysql2/promise';
import {test as base} from './../fixtures/db'

export type Taxon = {
	tid?: string,
	sciname: string,
	unitName1: string,
}

export type TaxonVernacular = {
	tid?: number,
	vernacularName: string,
}

export type Occurrence = {
	occId?: number,
	collId?: number,
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
}

export type Media = {
	mediaID?: number,
	occId?: number,
	tid?: number,
	originalUrl?: string,
	url?: string,
	thumbnailUrl?: string,
	archiveUrl?: string,
	sourceUrl?: string,
	referenceUrl?: string,
	caption?: string,
	creatorUid?: number,
	creator?: string,
	mediaType?: string,
	imageType?: string,
	format?: string,
	owner?: string,
	locality?: string,
	anatomy?: string,
	description?: string,
	notes?: string,
	mediaMD5?: string,
	username?: string,
	sourceIdentifier?: string,
	hashFunction?: string,
	hashValue?: string,
	pixelYDimension?: number,
	pixelXDimension?: number,
	dynamicProperties?: string,
	defaultDisplay?: string,
	recordID?: string,
	copyright?: string,
	rights?: string,
	accessRights?: string,
	sortsequence?: number,
	sortOccurrence?: number,
	initialtimestamp?: string,
}
export type Collection = {
	collId?: number,
	institutionCode: string,
	collectionCode: string,
	collectionName: string,
	managementType?: string,
	collType?: string,
}

export type Determination = {
	occId?: number,
	identifiedBy?: string,
	dateIdentified?: string,
	sciname?: string,
}

export enum Tables {
	Occurrence = 'omoccurrences',
	Collection = 'omcollection',
	Determination = 'omoccurdeterminations',
	Taxa = 'taxa',
	TaxaEnumTree = 'taxaenumtree',
	TaxaVernaculars = 'taxavernaculars',
	Media = 'media',
}

export class Seeder {
	static async lastId(conn: mysql.Connection): Promise<number> {
		let result = await conn.execute("SELECT LAST_INSERT_ID() as id");
		if(result.length > 0 && result[0].length > 0) {
			return result[0][0].id;
		} else {
			return 0;
		}
	}

	// Not all tables need this to be filled out since collection being remove
	// will clean up occurrences and related data
	static async remove(id: number, table: Tables, conn: mysql.Connection)  {
		let idField = '';
		switch(table) {
			case Tables.Taxa:
				idField = 'tid';
				break;
			case Tables.TaxaVernaculars:
				idField = 'vid';
				break;
			default:
				return;
		}

		const sql = `DELETE FROM ${table} where ${idField} = ?`
		await conn.execute(sql, [id]);
	}

	private static async executor(model: Object, table: Tables, conn: mysql.Connection) {
		const fields: Array<string> = Object.keys(model);
		const values: Array<string|number|boolean> = Object.values(model);
		const sql = `INSERT INTO ${table} (${fields.join(',')}) VALUES (${'?, '.repeat(fields.length - 1) + '?'})`
		await conn.execute(sql, values);
		return this.lastId(conn);
	}

	static async uniqueCollection(conn: mysql.Connection): Promise<number> {
		const sql = "INSERT omcollections (collectionCode, institutionCode, collectionName, managementType, collType) VALUES (uuid(), 'SYMB', uuid(), 'Live Data', 'Preserved Specimens')";
		await conn.execute(sql);
		return this.lastId(conn);
	}

	static async collection(data: Collection, conn: mysql.Connection) {
		return this.executor(data, Tables.Collection, conn);
	}

	static async deleteByCollId(collId: number, conn: mysql.Connection) {
		await conn.execute('DELETE from media where occid in (select occid from omoccurrences where collId = ?)', [ collId ]);
		await conn.execute('DELETE from omoccurrences where collId = ?', [ collId ]);
		await conn.execute('DELETE from omcollectionstats where collId = ?', [ collId ]);
		await conn.execute('DELETE from omcollections where collId = ?', [ collId ]);
	}

	static async taxon(data: Taxon, conn: mysql.Connection) {
		return this.executor(data, Tables.Taxa, conn);
	}

	static async taxonVernacular(data: TaxonVernacular, conn: mysql.Connection) {
		return this.executor(data, Tables.TaxaVernaculars, conn);
	}

	static async occurrencesWithCollId(collId: number, data: Array<Occurrence>, conn: mysql.Connection) {
		for(let o of data) {
			o.collId = collId;
			this.occurrence(o, conn);
		}
	}

	static async occurrence(data: Occurrence, conn: mysql.Connection) {
		return this.executor(data, Tables.Occurrence, conn);
	}

	static async determination(data: Determination, conn: mysql.Connection) {
		return this.executor(data, Tables.Determination, conn);
	}

	static async media(data: Media,  conn:  mysql.Connection) {
		return this.executor(data, Tables.Media, conn);
	}

	static bind(state): (params: SeederParams) => Promise<void>  {
		return async (params: SeederParams): Promise<void> => {
			if(typeof state === 'function') {
				state = await state(params);
			}

			if(state.occurrences) {
				for(let o of state.occurrences) {
					o.collId = params.collId;
					await Seeder.occurrence(o, params.DB);
				}
			}

			if(state.media) {
				for(let m of state.media) {
					await Seeder.media(m, params.DB);
				}
			}

			if(state.determinations) {
				for(let d of state.determinations) {
					await Seeder.determination(d, params.DB);
				}
			}

			if(state.taxa) {
				for(let t of state.taxa) {
					await Seeder.taxon(t, params.DB);
				}
			}
		}
	}

	private static async getResult(sql, params, conn) {
		const result = await conn.execute(sql, params)
		if(result && result.length) {
			return result[0];
		} else {
			return [];
		}
	}

	static async getOccurrence(occId: number, conn: mysql.Connection) {

		return this.getResult(
			"SELECT * FROM omoccurrences where occId = ?",  
			[occId], 
			conn
		);
	}

	static async getDeterminations(occId: number, conn: mysql.Connection) {
		return this.getResult(
			"SELECT * FROM omoccurdeterminations where occId = ?", 
			[occId],
			conn,
		);
	}
}

export interface SeederParams {
	DB: mysql.Connection,
	collId: number,
}

export const test = base.extend<{collId: number, occId: number, detId: number, mediaId: number }>({
	collId: async ({ DB }, use) => {
		const collId = await Seeder.uniqueCollection(DB);
		await use(collId);
		await Seeder.deleteByCollId(collId, DB);
	},
	occId: async ({ collId, DB }, use) => {
		await use(await Seeder.occurrence({ collId }, DB));
	},
	detId: async ({ occId, DB }, use) => {
		await use(await Seeder.determination({ 
			occId, 
			identifiedBy: 'unknown', 
			dateIdentified: 'unknown', 
			sciname: 'genus species'
		}, DB));
	},
	mediaId: async ({ occId, DB }, use) => {
		await use(await Seeder.media({ occId }, DB));
	}
});
