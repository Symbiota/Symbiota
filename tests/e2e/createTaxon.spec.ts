import { expect, mergeTests } from "@playwright/test";
import { test as testTaxonomyCreation } from "./fixtures/collection";
import { test as testWithAdmin } from "./fixtures/adminLogin";

const test = mergeTests(testCollection, testWithAdmin);
