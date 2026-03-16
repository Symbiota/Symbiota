import { test as base } from "./db.ts";
import mysql from "mysql2/promise";

class Taxon {
  constructor(public readonly conn: mysql.Connection) {}

  async getByName(taxonName: string) {
    const [search] = await this.conn.execute(
      "SELECT collId from omcollections where collectionName = ?",
      [collectionName],
    );
    if (search.length > 0) {
      return search[0].collId;
    } else {
      return 0;
    }
  }

  async insertBasic(
    collectionName: string,
    managementType: string = "Live Data",
    collectionType: string = "Preserved Specimens",
  ): Promise<number> {
    await this.conn.execute(
      "INSERT omcollections (institutionCode, collectionCode, collectionName, managementType, collType) VALUES (?, uuid(), ?, ?, ?)",
      ["SYMB", collectionName, managementType, collectionType],
    );
    let collId = 0;

    let result = await this.conn.execute("SELECT LAST_INSERT_ID() as id");
    if (result.length > 0 && result[0].length > 0) {
      collId = result[0][0].id;
    }

    // Inserting with recordCnt 1 so it displays where expected
    await this.conn.execute(
      "INSERT omcollectionstats(collId, recordCnt) VALUES (?, 1)",
      [collId],
    );

    return collId;
  }

  async getOrCreate(collectionName) {
    let collid = await this.getByName(collectionName);

    if (!collid) {
      await this.insertBasic(collectionName);
      collid = await this.getByName(collectionName);
    }

    return collid;
  }

  async resetCollection(collId) {
    await this.conn.execute(
      "DELETE from media where occid in (select occid from omoccurrences where collId = ?)",
      [collId],
    );
    await this.conn.execute("DELETE from omoccurrences where collId = ?", [
      collId,
    ]);
  }

  async deleteByCollId(collId) {
    await this.conn.execute(
      "DELETE from media where occid in (select occid from omoccurrences where collId = ?)",
      [collId],
    );
    await this.conn.execute("DELETE from omoccurrences where collId = ?", [
      collId,
    ]);
    await this.conn.execute("DELETE from omcollectionstats where collId = ?", [
      collId,
    ]);
    await this.conn.execute("DELETE from omcollections where collId = ?", [
      collId,
    ]);
  }
}

// Extend basic test by providing a "todoPage" fixture.
const test = base.extend<{ collection: Collection; collId: number }>({
  collection: async ({ DB }, use) => {
    await use(new Collection(DB));
  },
});

export { test, Collection };
