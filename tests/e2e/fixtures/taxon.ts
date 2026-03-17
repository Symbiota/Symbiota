import { test as base } from "./db.ts";
import mysql from "mysql2/promise";

class Taxon {
  constructor(public readonly conn: mysql.Connection) {}

  // Placeholder method until other methods are implemented. 
  // Basically, this is here currently to follow the pattern of how other pages provide a DB connection
  placeholder() {
    return this.conn;
  }
}

const test = base.extend<{ taxon: Taxon; taxonId: number }>({
  taxon: async ({ DB }, use) => {
    await use(new Taxon(DB));
  },
});

export { test, Taxon };
